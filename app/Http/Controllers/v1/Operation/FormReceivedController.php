<?php

namespace App\Http\Controllers\v1\Operation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{FormReceived,MutualFund};
use Validator;

class FormReceivedController extends Controller
{
    public function index(Request $request)
    {
        try {  
            $search=$request->search;
            $temp_tin_id=$request->temp_tin_id;
            $trans_type_id=$request->trans_type_id;
            $flag=$request->flag;

            if ($search!='') {
                $data=FormReceived::join('md_products','md_products.id','=','td_form_received.product_id')
                    ->select('td_form_received.*','md_products.product_name as product_name')
                    ->where('td_form_received.temp_tin_id','like', '%' . $search . '%')
                    ->orWhere('td_form_received.pan_no','like', '%' . $search . '%')
                    ->orWhere('td_form_received.mobile','like', '%' . $search . '%')
                    ->orWhere('td_form_received.email','like', '%' . $search . '%')
                    ->orWhere('td_form_received.application_no','like', '%' . $search . '%')
                    ->get();      
            }else if ($temp_tin_id!='' && $trans_type_id!='' && $flag=='C') {
                // return $temp_tin_id;
                $data=FormReceived::join('md_products','md_products.id','=','td_form_received.product_id')
                    ->join('md_employee','md_employee.emp_code','=','td_form_received.euin_from')
                    ->join('md_employee as md_employee1','md_employee1.emp_code','=','td_form_received.euin_to')
                    ->join('md_trans','md_trans.id','=','td_form_received.trans_id')
                    ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_form_received.sub_brk_cd')
                    ->select('td_form_received.*','md_products.product_name as product_name','md_employee.emp_name as euin_from_name','md_employee1.emp_name as euin_to_name','md_sub_broker.bro_name as sub_bro_name')
                    ->where('td_form_received.temp_tin_id', $temp_tin_id)
                    ->where('md_trans.trans_type_id',$trans_type_id)
                    ->get(); 
                // return $data;
                if (count($data)>0) {
                    $data1=MutualFund::where('temp_tin_id', $temp_tin_id)->get();
                    // return $data1;
                    if (count($data1)>0) {
                        $data=[];
                        return Helper::SuccessResponse($data);
                    }
                }   
            }else if ($temp_tin_id!='' && $trans_type_id!='' && $flag=='U') {
                // return $temp_tin_id;
                $data=FormReceived::join('md_products','md_products.id','=','td_form_received.product_id')
                    ->join('md_employee','md_employee.emp_code','=','td_form_received.euin_from')
                    ->join('md_employee as md_employee1','md_employee1.emp_code','=','td_form_received.euin_to')
                    ->join('md_trans','md_trans.id','=','td_form_received.trans_id')
                    ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_form_received.sub_brk_cd')
                    ->select('td_form_received.*','md_products.product_name as product_name','md_employee.emp_name as euin_from_name','md_employee1.emp_name as euin_to_name','md_sub_broker.bro_name as sub_bro_name')
                    ->where('td_form_received.temp_tin_id', $temp_tin_id)
                    ->where('md_trans.trans_type_id',$trans_type_id)
                    ->get(); 
            } else {
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
            'trans_id' =>'required',
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
                    'trans_id'=>$request->trans_id,
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