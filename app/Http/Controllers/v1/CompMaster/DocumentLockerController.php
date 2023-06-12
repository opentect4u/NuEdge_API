<?php

namespace App\Http\Controllers\v1\CompMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\CompDocumentLocker;
use Validator;

class DocumentLockerController extends Controller
{
    public function index(Request $request)
    {
        try {  
            $search=$request->search;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;

            $cm_profile_id=$request->cm_profile_id;
            if ($search!='') {
                $data=CompDocumentLocker::where('bank_name','like', '%' . $search . '%')->get();      
            }elseif ($cm_profile_id) {
                $data=CompDocumentLocker::where('cm_profile_id',$cm_profile_id)->get();      
            } else {
                $data=CompDocumentLocker::get();      
            }
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function createUpdate(Request $request)
    {
        // $validator = Validator::make(request()->all(),[
        //     'product_name' =>'required',
        // ]);
    
        // if($validator->fails()) {
        //     $errors = $validator->errors();
        //     return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        // }
        try {
            // return $request;
            // return count($request->upload_file);

            $doc_dtls=json_decode($request->doc_dtls);
            $upload_file=$request->upload_file;
            // return $upload_file;
            $data=[];
                foreach ($doc_dtls as $key => $value) {
                    // return $value;
                    $logo='';
                    // $file=$request->upload_file[$key];
                    $key='file_'.$key;
                    $file=$request->$key;
                    if ($value->id > 0) {
                        $dat=CompDocumentLocker::find($value->id);
                        if ($file) {
                            $logo_path_extension=$file->getClientOriginalExtension();
                            $logo=(microtime(true) * 100).".".$logo_path_extension;
                            $file->move(public_path('company/document/'),$logo);
                            if($dat->upload_file!=null){
                                $filelogo = public_path('company/document/') . $dat->upload_file;
                                if (file_exists($filelogo) != null) {
                                    unlink($filelogo);
                                }
                            } 
                        }else {
                            $logo=$dat->upload_file;
                        }
                        $dat->cm_profile_id=$value->cm_profile_id;
                        $dat->doc_name=$value->doc_name;
                        $dat->doc_no=$value->doc_no;
                        $dat->valid_from=$value->valid_from;
                        $dat->valid_to=$value->valid_to;
                        $dat->upload_file=$logo;
                        $dat->save();
                    }else {
                        // return $file;
                        if ($file) {
                            $logo_path_extension=$file->getClientOriginalExtension();
                            $logo=(microtime(true) * 100).".".$logo_path_extension;
                            $file->move(public_path('company/document/'),$logo);
                        }
                        // return $value;
                        $dat=CompDocumentLocker::create(array(
                            'cm_profile_id'=>$value->cm_profile_id,
                            'doc_name'=>$value->doc_name,
                            'doc_no'=>$value->doc_no,
                            'valid_from'=>$value->valid_from,
                            'valid_to'=>$value->valid_to,
                            'upload_file'=>$logo,
                        ));    
                    }
                    array_push($data,$dat) ;
                }
               
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}
