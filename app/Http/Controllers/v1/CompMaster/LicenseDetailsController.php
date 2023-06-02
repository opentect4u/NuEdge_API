<?php

namespace App\Http\Controllers\v1\CompMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\CompLicenseDetails;
use Validator;

class LicenseDetailsController extends Controller
{
    public function index(Request $request)
    {
        try {  
            $search=$request->search;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            if ($search!='') {
                $data=CompLicenseDetails::where('bank_name','like', '%' . $search . '%')->get();      
            }else {
                $data=CompLicenseDetails::get();      
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
            'product_id' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            if ($request->id > 0) {
                $data=CompLicenseDetails::find($request->id);

                if ($request->upload_file) {
                    $upload_file_path_extension=$request->upload_file->getClientOriginalExtension();
                    $upload_file=(microtime(true) * 100).".".$upload_file_path_extension;
                    $request->upload_file->move(public_path('company/license/'),$upload_file);
                
                    if($data->upload_file!=null){
                        $filelogo = public_path('company/license/') . $data->upload_file;
                        if (file_exists($filelogo) != null) {
                            unlink($filelogo);
                        }
                    } 
                }else {
                    $upload_file=$data->upload_file;
                }

                $data->product_id=$request->product_id;
                $data->licence_no=$request->licence_no;
                $data->valid_from=$request->valid_from;
                $data->valid_to=$request->valid_to;
                $data->upload_file=$upload_file;
                $data->save();
            }else{

                $upload_file='';
                if ($request->upload_file) {
                    $upload_file_path_extension=$request->upload_file->getClientOriginalExtension();
                    $upload_file=(microtime(true) * 100).".".$upload_file_path_extension;
                    $request->upload_file->move(public_path('company/license/'),$upload_file);
                }

                $data=CompLicenseDetails::create(array(
                    'product_id'=>$request->product_id,
                    'licence_no'=>$request->licence_no,
                    'valid_from'=>$request->valid_from,
                    'valid_to'=>$request->valid_to,
                    'upload_file'=>$upload_file,
                ));      
            }    
        } catch (\Throwable $th) {
            // throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}