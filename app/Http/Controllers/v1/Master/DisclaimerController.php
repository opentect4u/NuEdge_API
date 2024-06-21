<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{Disclaimer,Scheme};
use Validator;
use Excel;
use App\Imports\AMCImport;

class DisclaimerController extends Controller
{
    public function index(Request $request)
    {
        try {  
            $data=Disclaimer::get();  
        } catch (\Throwable $th) {
            // throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function createUpdate(Request $request)
    {
        $validator = Validator::make(request()->all(),[
            'dis_for' =>'required',
            'dis_des' =>'required',
        ]);
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            // return $request;
            if ($request->id > 0) {
                $data=Disclaimer::find($request->id);
                $data->dis_for=$request->dis_for;
                $data->dis_des=$request->dis_des;
                $data->updated_by=Helper::modifyUser($request->user());
                $data->save();
            }else{
                $data=Disclaimer::create(array(
                    'dis_for'=>$request->dis_for,
                    'dis_des'=>$request->dis_des,
                    'created_by'=>Helper::modifyUser($request->user()),
                ));  
            }    
        } catch (\Throwable $th) {
            // throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function delete(Request $request)
    {
        try {
            $id=$request->id;
            $is_has=Scheme::where('amc_id',$id)->get();
            // return $is_has;
            if (count($is_has)>0) {
                return Helper::WarningResponse(parent::DELETE_NOT_ALLOW_ERROR);
            }else {
                $data=Disclaimer::find($id);
                $data->delete_flag='Y';
                $data->deleted_date=date('Y-m-d H:i:s');
                $data->deleted_by=Helper::modifyUser($request->user());
                $data->save();
            }
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DELETE_FAIL_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

 
}