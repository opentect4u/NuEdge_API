<?php

namespace App\Http\Controllers\v1\Operation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\FormReceived;
use Validator;

class FormReceivedController extends Controller
{
    public function index(Request $request)
    {
        try {  
            $search=$request->search;
            if ($search!='') {
                $data=FormReceived::where('FormReceived_name','like', '%' . $search . '%')->get();      
            }else {
                $data=FormReceived::get();      
            }
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function create(Request $request)
    {
        $validator = Validator::make(request()->all(),[
            'euin_from' =>'required',
            'product_id' =>'required',
            'form_type_id' =>'required',
            'pan_no' =>'required',
            'mobile' =>'required|min:10|numeric',
            'email' =>'required|email',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            
                $temp_tin_id='TIN'.date('ymdHis'); // generate temp tin no
                $bu_type='D';
                $arn_no=Helper::CommonParamValue(1);
                $euin_to=Helper::CommonParamValue(2);
                $branch_code=1;
                $data=FormReceived::create(array(
                    'rec_datetime'=>date('Y-m-d H:i:s'),
                    'temp_tin_id'=>$temp_tin_id,
                    'bu_type'=>$bu_type,
                    'arn_no'=>$arn_no,
                    'euin_from'=>$request->euin_from,
                    'euin_to'=>$euin_to,
                    // 'sub_arn_no'=>,
                    // 'sub_brk_cd'=>,
                    'product_id'=>$request->product_id,
                    'form_type_id'=>$request->form_type_id,
                    'application_no'=>isset($request->application_no)?$request->application_no:'NA',
                    'pan_no'=>$request->pan_no,
                    'mobile'=>$request->mobile,
                    'email'=>$request->email,
                    'branch_code'=>$branch_code,
                    // 'created_by'=>'',
                ));      
              
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}
