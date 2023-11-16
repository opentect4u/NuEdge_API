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
            $cm_profile_id=$request->cm_profile_id;
            if ($search!='') {
                $data=CompLoginPwdLocker::where('bank_name','like', '%' . $search . '%')->get();      
            }elseif ($cm_profile_id) {
                $data=CompLoginPwdLocker::leftJoin('md_cm_products','md_cm_products.id','=','md_cm_login_pwd_locker.product_id')
                    ->select('md_cm_login_pwd_locker.*','md_cm_products.product_name as product_name','md_cm_products.cm_profile_id as cm_profile_id')
                    ->where('md_cm_products.cm_profile_id',$cm_profile_id)
                    ->get();      
            } else {
                $data=CompLoginPwdLocker::leftJoin('md_cm_products','md_cm_products.id','=','md_cm_login_pwd_locker.product_id')
                    ->select('md_cm_login_pwd_locker.*','md_cm_products.product_name as product_name','md_cm_products.cm_profile_id as cm_profile_id')
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
                $data->updated_by=Helper::modifyUser($request->user());
                $data->save();
            }else{
                $data=CompLoginPwdLocker::create(array(
                    'product_id'=>$request->product_id,
                    'login_url'=>$request->login_url,
                    'login_id'=>$request->login_id,
                    'login_pass'=>$request->login_pass,
                    'sec_qus_ans'=>$request->sec_qus_ans,
                    'created_by'=>Helper::modifyUser($request->user()),
                ));      
            }  
            $data=CompLoginPwdLocker::leftJoin('md_cm_products','md_cm_products.id','=','md_cm_login_pwd_locker.product_id')
                    ->select('md_cm_login_pwd_locker.*','md_cm_products.product_name as product_name','md_cm_products.cm_profile_id as cm_profile_id')
                    ->where('md_cm_login_pwd_locker.id',$data->id)
                    ->first();
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}
