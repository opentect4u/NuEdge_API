<?php

namespace App\Http\Controllers\v1\Operation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\MutualFund;
use Validator;

class FinancialController extends Controller
{
    public function index(Request $request)
    {
        try {  
            $search=$request->search;
            if ($search!='') {
                $data=MutualFund::orWhere('client_name','like', '%' . $search . '%')
                    ->orWhere('client_code','like', '%' . $search . '%')
                    ->orWhere('pan','like', '%' . $search . '%')
                    ->orWhere('mobile','like', '%' . $search . '%')
                    ->orWhere('email','like', '%' . $search . '%')
                    ->get();      
            }else{
                $data=MutualFund::
                whereDate('updated_at',date('Y-m-d'))->
                get();      
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
        //     'client_name'=>'required',
        //     'dob'=>'required',
        //     'add_line_1'=>'required',
        //     'city'=>'required',
        //     'dist'=>'required',
        //     'state'=>'required',
        //     'pincode'=>'required',
        //     'pan'=>'required',
        //     'mobile'=>'required',
        //     'email'=>'required',
        // ]);
    
        // if($validator->fails()) {
        //     $errors = $validator->errors();
        //     return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        // }
        // try {
           
            // return $request;
            $product_id=1;
            $trans_type_id=1;
            // 1
            $is_has=MutualFund::get();
            if (count($is_has)>0) {
                $tin_no=Helper::GenTIN($product_id,$trans_type_id,(count($is_has)+1));
            } else {
                $tin_no=Helper::GenTIN($product_id,$trans_type_id,1);
            }

                $data=MutualFund::create(array(
                    'temp_tin_id' =>$request->temp_tin_id,
                    'tin_no'=> $tin_no,
                    'entry_date'=> date('Y-m-d',strtotime($request->entry_date)),
                    'first_client_code'=>$request->first_client_code,
                    'first_pan'=>$request->first_pan,
                    'first_kyc'=>$request->first_kyc,
                    'second_client_code'=>isset($request->first_client_code)?$request->first_client_code:'',
                    'second_pan'=>isset($request->second_pan)?$request->second_pan:'',
                    'second_kyc'=>isset($request->second_kyc)?$request->second_kyc:'',
                    'third_client_code'=>isset($request->third_client_code)?$request->third_client_code:'',
                    'third_pan'=>isset($request->third_pan)?$request->third_pan:'',
                    'third_kyc'=>isset($request->third_kyc)?$request->third_kyc:'',
                    'amc_id'=>$request->amc_id,
                    'trans_catg'=>$request->trans_catg,
                    'trans_subcat'=>$request->trans_subcatg,
                    'trans_scheme_from'=>isset($request->trans_scheme_from)?$request->trans_scheme_from:'',
                    'trans_scheme_to'=>isset($request->scheme_name)?$request->scheme_name:$request->trans_scheme_to,
                    // 'folio_no',
                    'amount'=>$request->amount,
                    // 'unit',
                    'trans_type'=>$request->trans_type,
                    'sip_start_date'=>isset($request->sip_start_date)?date('Y-m-d',strtotime($request->sip_start_date)):'',
                    'sip_end_date'=>isset($request->sip_end_date)?date('Y-m-d',strtotime($request->sip_end_date)):'',
                    'chq_no'=>$request->chq_no,
                    'chq_bank'=>$request->chq_bank,
                    // 'created_by'=>'',
                ));    
            
        // } catch (\Throwable $th) {
        //     //throw $th;
        //     return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        // }
        return Helper::SuccessResponse($data);
    }
}
