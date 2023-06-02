<?php

namespace App\Http\Controllers\v1\CompMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\CompBank;
use Validator;

class BankController extends Controller
{
    public function index(Request $request)
    {
        try {  
            $search=$request->search;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            if ($search!='') {
                $data=CompBank::where('bank_name','like', '%' . $search . '%')->get();      
            }else {
                $data=CompBank::get();      
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
            $all_details=json_decode($request->bank_dtls);
            // return $all_details;
            $data=[];    
            foreach ($all_details as $key => $value) {
                // return $value;
                if ($value->id >0) {
                    $dt=CompBank::find($value->id);
                    $dt->cm_profile_id=$value->cm_profile_id;
                    $dt->acc_no=$value->acc_no;
                    $dt->bank_name=$value->bank_name;
                    $dt->ifsc=$value->ifsc;
                    $dt->micr=$value->micr;
                    $dt->branch_name=$value->branch_name;
                    $dt->branch_add=$value->branch_add;
                    $dt->save();
                }else {
                    $dt=CompBank::create(array(
                        'cm_profile_id'=>$value->cm_profile_id,
                        'acc_no'=>$value->acc_no,
                        'bank_name'=>$value->bank_name,
                        'ifsc'=>$value->ifsc,
                        'micr'=>$value->micr,
                        'branch_name'=>$value->branch_name,
                        'branch_add'=>$value->branch_add,
                    ));    
                }
                array_push($data,$dt);
            }
        } catch (\Throwable $th) {
            // throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}
