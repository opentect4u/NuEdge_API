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
                $data=FormReceived::join('md_products','md_products.id','=','td_form_received.product_id')
                    ->select('td_form_received.*','md_products.product_name as product_name')
                    ->where('td_form_received.temp_tin_id','like', '%' . $search . '%')
                    ->orWhere('td_form_received.pan_no','like', '%' . $search . '%')
                    ->orWhere('td_form_received.mobile','like', '%' . $search . '%')
                    ->orWhere('td_form_received.email','like', '%' . $search . '%')
                    ->orWhere('td_form_received.application_no','like', '%' . $search . '%')
                    ->get();      
            }else {
                $data=FormReceived::whereDate('updated_at',date('Y-m-d'))->get();      
                // $data=FormReceived::get();      
            }
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function createShow(Request $request)
    {
        try {
            $datetime=date('d-m-Y H:i:s');
            $arn_no=Helper::CommonParamValue(1);
            $data=['datetime'=>$datetime,'arn_no'=>$arn_no];
        } catch (\Throwable $th) {
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

            $is_has=FormReceived::get();
            if (count($is_has)>0) {
                $temp_tin_id=Helper::TempTINGen((count($is_has)+1)); // generate temp tin no
            }else{
                $temp_tin_id=Helper::TempTINGen(1); // generate temp tin no
            }
            
                // $bu_type='D';
                $arn_no=Helper::CommonParamValue(1);
                // $euin_to=Helper::CommonParamValue(2);
                $branch_code=1;
                $data=FormReceived::create(array(
                    'rec_datetime'=>date('Y-m-d H:i:s'),
                    'temp_tin_id'=>$temp_tin_id,
                    'bu_type'=>$request->bu_type,
                    'arn_no'=>$request->arn_no,
                    'euin_from'=>$request->euin_from,
                    'euin_to'=>$request->euin_to,
                    'sub_arn_no'=>isset($request->sub_arn_no)?$request->sub_arn_no:'',
                    'sub_brk_cd'=>isset($request->sub_brk_cd)?$request->sub_brk_cd:'',
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