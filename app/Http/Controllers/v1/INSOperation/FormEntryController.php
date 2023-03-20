<?php

namespace App\Http\Controllers\v1\INSOperation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{InsProduct,Insurance,InsFormReceived};
use Validator;

class FormEntryController extends Controller
{
    //
    public function searchDetails(Request $request)
    {
        try {
            $paginate=$request->paginate;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            
            $tin_no=$request->tin_no;
            $option=$request->option;
            $date_status=$request->date_status;
            $start_date=$request->start_date;
            $end_date=$request->end_date;
            $login_status=$request->login_status;
            
            if ($paginate=='A') {
                $paginate=999999999;
            }

            if ($option==3) {
                if ($date_status=='T') {
                    # code...
                }else {
                    # code...
                }
            }else {
                if ($sort_by && $column_name) {
                    # code...
                } elseif ($tin_no) {
                    # code...
                } else {
                    $data=Insurance::join('td_ins_form_received','td_ins_form_received.temp_tin_no','=','td_insurance.temp_tin_no')
                        ->leftJoin('md_ins_company','md_ins_company.id','=','td_insurance.company_id')
                        ->leftJoin('md_ins_product_type','md_ins_product_type.id','=','td_insurance.product_type_id')
                        ->leftJoin('md_ins_products','md_ins_products.id','=','td_insurance.product_id')
                        ->leftJoin('md_client','md_client.id','=','td_insurance.proposer_id')
                        ->leftJoin('md_client as md_client_2','md_client_2.id','=','td_insurance.insured_person_id')
                        ->leftJoin('md_ins_type','md_ins_type.id','=','td_ins_form_received.ins_type_id')
                        ->select('td_insurance.*','td_ins_form_received.bu_type as bu_type','td_ins_form_received.arn_no as arn_no','td_ins_form_received.euin_no as euin_no','td_ins_form_received.insure_bu_type as insure_bu_type',
                        'md_ins_company.comp_short_name as comp_short_name','md_ins_company.comp_full_name as comp_full_name','md_ins_product_type.product_type as product_type','md_ins_products.product_name as product_name',
                        'md_client.client_code as proposer_code','md_client.client_name as proposer_name','md_client.pan as proposer_pan','md_client.client_type as proposer_type',
                        'md_client_2.client_code as insured_person_code','md_client_2.client_name as insured_person_name','md_client_2.pan as insured_person_pan','md_client_2.client_type as insured_person_type',
                        'md_ins_type.type as ins_type')
                        ->where('td_insurance.delete_flag','N')
                        ->orderBy('td_insurance.updated_at','desc')
                        ->paginate($paginate);
                }
            }
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function export(Request $request)
    {
        try {
            //code...
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
            $product_id=3;
            $tin_no='';
            $trans_type_id='';
            $is_has=Insurance::get();
            // return $is_has; 
            if (count($is_has)>0) {
                $tin_no=Helper::GenTIN($product_id,$trans_type_id,(count($is_has)+1));
            } else {
                $tin_no=Helper::GenTIN($product_id,$trans_type_id,1);
            }
            // return $tin_no;
            // return $request;
            
            if($request->temp_tin_no!='' && $request->tin_status=='Y'){  // with TTIN NO
                // return $request;
                $has=Insurance::where('temp_tin_no',$request->temp_tin_no)->get();
                if (count($has)>0) {
                    $error='Temporary TIN no already exist.';
                    return Helper::ErrorResponse($error);
                }else {
                    
                    InsFormReceived::where('temp_tin_no',$request->temp_tin_no)->update(array(
                        'bu_type'=>$request->bu_type,
                        'euin_no'=>$request->euin_no,
                        'sub_arn_no'=>isset($request->sub_arn_no)?$request->sub_arn_no:NULL,
                        'sub_brk_cd'=>isset($request->sub_brk_cd)?$request->sub_brk_cd:NULL,
                        'proposer_id'=>$request->proposer_id,
                        'insure_bu_type'=>$request->insure_bu_type,
                        'ins_type_id'=>$request->ins_type_id,
                        'recv_from'=>$request->recv_from,
                        // 'branch_code'=>$branch_code,
                        // 'created_by'=>'',
                    ));       
                    
                    $insured_person_id=$request->insured_person_id;
                    $insured_person_name=$request->insured_person_name;
                    $insured_person_pan=$request->insured_person_pan;
                    $insured_person_dob=$request->insured_person_dob;
                    if ($insured_person_id=='' && $insured_person!='' && $insured_person_pan!='' && $insured_person_dob!='') {
                        $t_c_data=Client::create(array(
                            'client_name'=>$insured_person,
                            'pan'=>$insured_person_pan,
                            'dob'=>$insured_person_dob,
                            'client_type'=>'E',
                            // 'created_by'=>'',
                        ));  
                        $insured_person_id=$t_c_data->id;
                    }

                    $app_form_scan=$request->app_form_scan;
                    $doc_name='';
                    if ($app_form_scan) {
                        $cv_path_extension=$app_form_scan->getClientOriginalExtension();
                        $doc_name=microtime().".".$cv_path_extension;
                        $app_form_scan->move(public_path('ins-application-form/'),$doc_name);
                    }
                    $data=Insurance::create(array(
                        'temp_tin_no' =>$request->temp_tin_no,
                        'tin_no'=> $tin_no,
                        'entry_date'=> date('Y-m-d'),
                        'proposer_id'=>$request->proposer_id,
                        'insured_person_id'=>$insured_person_id,
                        'company_id'=>$request->company_id,
                        'product_type_id'=>$request->product_type_id,
                        'product_id'=>$request->product_id,
                        'proposal_no'=>$request->proposal_no,
                        'sum_assured'=>$request->sum_assured,
                        'mode_of_premium'=>$request->mode_of_premium,
                        'gross_premium'=>$request->gross_premium,
                        'net_premium'=>$request->net_premium,
                        'mode_of_payment'=>$request->mode_of_payment,
                        'chq_bank'=>$request->chq_bank,
                        'acc_no'=>$request->acc_no,
                        'payment_ref_no'=>$request->payment_ref_no,
                        'chq_no'=>$request->chq_no,
                        'policy_term'=>$request->policy_term,
                        'policy_pre_pay_term'=>$request->policy_pre_pay_term,
                        'app_form_scan'=>$doc_name,
                        'comp_login_at'=>$request->comp_login_at,
                        'remarks'=>$request->remarks,
                        'form_status'=>'P',
                        // 'created_by'=>'',
                    ));    
                   

                }
            } else {  // Without TTIN not exist
                // return $request; // if temp tin not exist
                // return $request->sip_start_date;
                // return $tin_no;
                // craete TTIN no
                $is_has=InsFormReceived::orderBy('created_at','desc')->get();
                if (count($is_has)>0) {
                    $temp_tin_no=Helper::TempTINGen((count($is_has)+1),3); // generate temp tin no
                }else{
                    $temp_tin_no=Helper::TempTINGen(1,3); // generate temp tin no
                }
                $arn_no=Helper::CommonParamValue(1);
                $branch_code=1;
                $fr_data=InsFormReceived::create(array(
                    'rec_datetime'=>date('Y-m-d H:i:s'),
                    'temp_tin_no'=>$temp_tin_no,
                    'bu_type'=>$request->bu_type,
                    'arn_no'=>$arn_no,
                    'euin_no'=>$request->euin_no,
                    'sub_arn_no'=>isset($request->sub_arn_no)?$request->sub_arn_no:NULL,
                    'sub_brk_cd'=>isset($request->sub_brk_cd)?$request->sub_brk_cd:NULL,
                    'proposer_id'=>$request->proposer_id,
                    'insure_bu_type'=>$request->insure_bu_type,
                    'ins_type_id'=>$request->ins_type_id,
                    'recv_from'=>$request->recv_from,
                    'proposal_no'=>isset($request->proposal_no)?$request->proposal_no:NULL,
                    'branch_code'=>$branch_code,
                    // 'created_by'=>'',
                ));   
                // return $fr_data;
                $ttin_no=$fr_data->temp_tin_no;

                $insured_person_id=$request->insured_person_id;
                $insured_person_name=$request->insured_person_name;
                $insured_person_pan=$request->insured_person_pan;
                $insured_person_dob=$request->insured_person_dob;
                if ($insured_person_id=='' && $insured_person!='' && $insured_person_pan!='' && $insured_person_dob!='') {
                    $t_c_data=Client::create(array(
                        'client_name'=>$insured_person,
                        'pan'=>$insured_person_pan,
                        'dob'=>$insured_person_dob,
                        'client_type'=>'E',
                        // 'created_by'=>'',
                    ));  
                    $insured_person_id=$t_c_data->id;
                }

                $app_form_scan=$request->app_form_scan;
                $doc_name='';
                if ($app_form_scan) {
                    $cv_path_extension=$app_form_scan->getClientOriginalExtension();
                    $doc_name=microtime().".".$cv_path_extension;
                    $app_form_scan->move(public_path('ins-application-form/'),$doc_name);
                }
                $data=Insurance::create(array(
                    'temp_tin_no' =>$request->temp_tin_no,
                    'tin_no'=> $tin_no,
                    'entry_date'=> date('Y-m-d'),
                    'proposer_id'=>$request->proposer_id,
                    'insured_person_id'=>$request->insured_person_id,
                    'company_id'=>$request->company_id,
                    'Product_type_id'=>$request->Product_type_id,
                    'Product_id'=>$request->Product_id,
                    'proposal_no'=>$request->proposal_no,
                    'sum_assured'=>$request->sum_assured,
                    'mode_of_premium'=>$request->mode_of_premium,
                    'gross_premium'=>$request->gross_premium,
                    'net_premium'=>$request->net_premium,
                    'mode_of_payment'=>$request->mode_of_payment,
                    'chq_bank'=>$request->chq_bank,
                    'acc_no'=>$request->acc_no,
                    'payment_ref_no'=>$request->payment_ref_no,
                    'chq_no'=>$request->chq_no,
                    'policy_term'=>$request->policy_term,
                    'policy_pre_pay_term'=>$request->policy_pre_pay_term,
                    'app_form_scan'=>$app_form_scan,
                    'comp_login_at'=>$request->comp_login_at,
                    'remarks'=>$request->remarks,
                    'form_status'=>'P',
                    // 'created_by'=>'',
                ));  

            }
        } catch (\Throwable $th) {
            // throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

}
