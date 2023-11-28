<?php

namespace App\Http\Controllers\V1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\UploadFileHelp;
use Validator;

class UploadFileHelpController extends Controller
{
    public function index(Request $request)
    {
        try {  
            $search=$request->search;
            $paginate=$request->paginate;
            $product_id=$request->product_id;
            if ($search!='') {
                $data=UploadFileHelp::orWhere('event','like', '%' . $search . '%')
                ->get();      
            }elseif ($paginate!='') {
                $data=UploadFileHelp::paginate($paginate);      
            } else {
                $data=UploadFileHelp::leftJoin('md_rnt','md_rnt.id','=','md_file_upload_help.rnt_id')
                    // ->leftJoin('md_mailback_filetype','md_mailback_filetype.id','=','md_file_upload_help.file_type_id')
                    // ->leftJoin('md_mailback_filename','md_mailback_filename.id','=','md_file_upload_help.file_id')
                    ->select('md_file_upload_help.*','md_rnt.rnt_name as rnt_name')
                    ->get();      
            }
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function createUpdate(Request $request)
    {
        $validator = Validator::make(request()->all(),[
            'rnt_id' =>'required',
            'file_type_id' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            // return $request;
            if ($request->id > 0) {
                $data=UploadFileHelp::find($request->id);
                $data->rnt_id=$request->rnt_id;
                $data->file_type_id=$request->file_type_id;
                $data->file_id=$request->file_id;
                $data->file_format_id=$request->file_format_id;
                $data->uploaded_mode_id=$request->uploaded_mode_id;
                $data->rec_upload_freq=$request->rec_upload_freq;
                $data->updated_by=Helper::modifyUser($request->user());
                $data->save();
            }else{
                $is_has=UploadFileHelp::where('rnt_id',$request->rnt_id)
                    ->where('file_type_id',$request->file_type_id)
                    ->where('file_id',$request->file_id)
                    ->where('file_format_id',$request->file_format_id)
                    ->where('uploaded_mode_id',$request->uploaded_mode_id)
                    ->count();
                if ($is_has>0) {
                    return Helper::ErrorResponse('Already exist');
                }else {
                    $data=UploadFileHelp::create(array(
                        'rnt_id'=>$request->rnt_id,
                        'file_type_id'=>$request->file_type_id,
                        'file_id'=>$request->file_id,
                        'file_format_id'=>$request->file_format_id,
                        'uploaded_mode_id'=>$request->uploaded_mode_id,
                        'rec_upload_freq'=>$request->rec_upload_freq,
                        'created_by'=>Helper::modifyUser($request->user()),
                    ));     
                }
            } 
            $mydata=UploadFileHelp::leftJoin('md_rnt','md_rnt.id','=','md_file_upload_help.rnt_id')
                // ->leftJoin('md_mailback_filetype','md_mailback_filetype.id','=','md_file_upload_help.file_type_id')
                // ->leftJoin('md_mailback_filename','md_mailback_filename.id','=','md_file_upload_help.file_id')
                ->select('md_file_upload_help.*','md_rnt.rnt_name as rnt_name')
                ->where('md_file_upload_help.id',$data->id)
                ->first();   
        } catch (\Throwable $th) {
            // throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($mydata);
    }

}
