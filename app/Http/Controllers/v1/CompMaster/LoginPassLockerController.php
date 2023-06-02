<?php

namespace App\Http\Controllers\v1\CompMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\CompLoginPwdLocker;
use Validator;

class LoginPassLockerController extends Controller
{
    public function index(Request $request)
    {
        try {  
            $search=$request->search;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            if ($search!='') {
                $data=CompLoginPwdLocker::where('bank_name','like', '%' . $search . '%')->get();      
            }else {
                $data=CompLoginPwdLocker::get();      
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
                $data=CompLoginPwdLocker::find($request->id);
                $data->product_id=$request->product_id;
                $data->login_url=$request->login_url;
                $data->login_id=$request->login_id;
                $data->login_pass=$request->login_pass;
                $data->sec_qus_ans=$request->sec_qus_ans;
                $data->save();
            }else{
                $data=CompLoginPwdLocker::create(array(
                    'product_id'=>$request->product_id,
                    'login_url'=>$request->login_url,
                    'login_id'=>$request->login_id,
                    'login_pass'=>$request->login_pass,
                    'sec_qus_ans'=>$request->sec_qus_ans,
                ));      
            }    
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}
