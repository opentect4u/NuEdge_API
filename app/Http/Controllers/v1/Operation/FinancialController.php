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
            $trans_type_id=$request->trans_type_id;
            if ($search!='') {
                $data=MutualFund::join('md_trans','md_trans.id','=','td_mutual_fund.trans_type')
                    ->select('td_mutual_fund.*','md_trans.trans_type_id as trans_type_id')
                    ->where('md_trans.trans_type_id',$trans_type_id)
                    ->where('td_mutual_fund.tin_no',$search)
                    // ->whereDate('td_mutual_fund.entry_date',date('Y-m-d'))
                    ->get();     
            }else{
                $data=MutualFund::join('md_trans','md_trans.id','=','td_mutual_fund.trans_type')
                ->select('td_mutual_fund.*','md_trans.trans_type_id as trans_type_id')
                ->where('md_trans.trans_type_id',$trans_type_id)
                ->whereDate('td_mutual_fund.entry_date',date('Y-m-d'))
                ->get();      
            }
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function create(Request $request)
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
        try {
            
           
            // return $request;
            // return $request->app_form_scan;
            
            // return $request->app_form_scan;
            // return $request;

            $product_id=1;
            $tin_no='';
            $trans_type_id=$request->trans_type_id;
            if ($trans_type_id==1) { // Financial
                $is_has=MutualFund::join('md_trans','md_trans.id','=','td_mutual_fund.trans_type')
                    ->select('td_mutual_fund.*','md_trans.trans_type_id as trans_type_id')
                    ->where('md_trans.trans_type_id',$trans_type_id)
                    ->get();
                // return $is_has; 
                if (count($is_has)>0) {
                    $tin_no=Helper::GenTIN($product_id,$trans_type_id,(count($is_has)+1));
                } else {
                    $tin_no=Helper::GenTIN($product_id,$trans_type_id,1);
                }
            }else if ($trans_type_id==3) { // Non Financial
                $is_has=MutualFund::join('md_trans','md_trans.id','=','td_mutual_fund.trans_type')
                    ->select('td_mutual_fund.*','md_trans.trans_type_id as trans_type_id')
                    ->where('md_trans.trans_type_id',$trans_type_id)
                    ->get();
                // return $is_has; 
                if (count($is_has)>0) {
                    $tin_no=Helper::GenTIN($product_id,$trans_type_id,(count($is_has)+1));
                } else {
                    $tin_no=Helper::GenTIN($product_id,$trans_type_id,1);
                }
            }else if ($trans_type_id==4) {  // NFO
                $is_has=MutualFund::join('md_trans','md_trans.id','=','td_mutual_fund.trans_type')
                    ->select('td_mutual_fund.*','md_trans.trans_type_id as trans_type_id')
                    ->where('md_trans.trans_type_id',$trans_type_id)
                    ->get();
                // return $is_has; 
                if (count($is_has)>0) {
                    $tin_no=Helper::GenTIN($product_id,$trans_type_id,(count($is_has)+1));
                } else {
                    $tin_no=Helper::GenTIN($product_id,$trans_type_id,1);
                }
            }
            // return $tin_no;

            // return $request;
            
            
            $has=MutualFund::where('temp_tin_id',$request->temp_tin_id)->get();
            if (count($has)>0) {
                $error='Temporary TIN no already exist.';
                return Helper::ErrorResponse($error);
            }else {
                $app_form_scan=$request->app_form_scan;
                $doc_name='';
                if ($app_form_scan) {
                    $cv_path_extension=$app_form_scan->getClientOriginalExtension();
                    $doc_name=microtime().".".$cv_path_extension;
                    $app_form_scan->move(public_path('application-form/'),$doc_name);
                }
                $data=MutualFund::create(array(
                    'temp_tin_id' =>$request->temp_tin_id,
                    'tin_no'=> $tin_no,
                    'entry_date'=> date('Y-m-d',strtotime($request->entry_date)),
                    'first_client_code'=>$request->first_client_code,
                    'first_pan'=>$request->first_pan,
                    'first_kyc'=>$request->first_kyc,
                    'second_client_code'=>isset($request->second_client_code)?$request->second_client_code:'',
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
                    'rnt_login_at'=>$request->rnt_login_at,
                    'app_form_scan'=>$doc_name,
                    'form_scan_status'=>$request->form_scan_status,
                    'remarks'=>$request->remarks,
                    'form_status'=>'P',
                    // 'created_by'=>'',
                ));    
            }
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function update(Request $request)
    {
        // return $request;
        try {
            // return $request;
            $data1=MutualFund::where('tin_no',$request->tin_no)->first();
            $app_form_scan=$request->app_form_scan;
            if ($app_form_scan) {
                $cv_path_extension=$app_form_scan->getClientOriginalExtension();
                $doc_name=microtime().".".$cv_path_extension;
                $app_form_scan->move(public_path('application-form/'),$doc_name);
            }else{
                $doc_name=$data1->app_form_scan;
                // return $doc_name;
            }
                MutualFund::where('tin_no',$request->tin_no)->update(array(
                    // 'temp_tin_id' =>$request->temp_tin_id,
                    // 'tin_no'=> $tin_no,
                    // 'entry_date'=> date('Y-m-d',strtotime($request->entry_date)),
                    'first_client_code'=>$request->first_client_code,
                    'first_pan'=>$request->first_pan,
                    'first_kyc'=>$request->first_kyc,
                    'second_client_code'=>isset($request->second_client_code)?$request->second_client_code:'',
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
                    'rnt_login_at'=>$request->rnt_login_at,
                    'app_form_scan'=>$doc_name,
                    'form_scan_status'=>$request->form_scan_status,
                    'remarks'=>$request->remarks,
                    // 'created_by'=>'',
                ));   
            // return $data1;
            $data=MutualFund::where('tin_no',$request->tin_no)->first();
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}