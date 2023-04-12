<?php

namespace App\Http\Controllers\v1\Operation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{MutualFund,Client,FormReceived};
use Validator;
use Illuminate\Support\Carbon;
use Mail;
use App\Mail\Master\SendAckEmail;
use App\Models\Email;

class ManualUpdateController extends Controller
{
    public function searchDetails(Request $request)
    {
        try {
            $paginate=$request->paginate;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            $trans_type_id=$request->trans_type_id;
            
            $tin_no=$request->tin_no;
            $option=$request->option;
            $sub_brk_cd=$request->sub_brk_cd;
            $ins_type_id=$request->ins_type_id;
            $insured_bu_type=$request->insured_bu_type;
            $proposer_name=$request->proposer_name;
            $euin_no=$request->euin_no;
            $bu_type=json_decode($request->bu_type);
            
            $login_status=$request->login_status;
            $date_status=$request->date_status;
            $start_date=$request->start_date;
            $end_date=$request->end_date;

            if ($paginate=='A') {
                $paginate=999999999;
            }

            
                if ($tin_no) {
                    $data=MutualFund::join('td_form_received','td_form_received.temp_tin_no','=','td_mutual_fund.temp_tin_no')
                        ->join('md_trans','md_trans.id','=','td_mutual_fund.trans_id')
                        ->join('md_scheme','md_scheme.id','=','td_mutual_fund.trans_scheme_from')
                        ->leftJoin('md_scheme as md_scheme_2','md_scheme_2.id','=','td_mutual_fund.trans_scheme_to')
                        ->join('md_client','md_client.id','=','td_mutual_fund.first_client_id')
                        ->leftJoin('md_client as md_client_2','md_client_2.id','=','td_mutual_fund.second_client_id')
                        ->leftJoin('md_client as md_client_3','md_client_3.id','=','td_mutual_fund.third_client_id')
                        ->join('md_plan','md_plan.id','=','td_mutual_fund.plan_id')
                        ->join('md_option','md_option.id','=','td_mutual_fund.option_id')
                        ->leftJoin('md_plan as md_plan_2','md_plan_2.id','=','td_mutual_fund.plan_id_to')
                        ->leftJoin('md_option as md_option_2','md_option_2.id','=','td_mutual_fund.option_id_to')
                        ->leftJoin('md_rnt','md_rnt.id','=','td_mutual_fund.rnt_login_at')
                        ->select('td_mutual_fund.*','md_trans.trns_name as trans_name','md_trans.trans_type_id as trans_type_id','md_scheme.scheme_name as scheme_name',
                        'td_form_received.bu_type as bu_type','md_scheme.scheme_name as scheme_name','md_scheme_2.scheme_name as scheme_name_to',
                        'md_client.client_code as first_client_code','md_client.client_name as first_client_name','md_client.pan as first_client_pan','md_client.client_type as first_client_type',
                        'md_client_2.client_code as second_client_code','md_client_2.client_name as second_client_name','md_client_2.pan as second_client_pan','md_client_2.client_type as second_client_type',
                        'md_plan.plan_name as plan_name','md_option.opt_name as opt_name','md_plan_2.plan_name as plan_name_to','md_option_2.opt_name as opt_name_to',
                        'md_rnt.rnt_name as rnt_name','td_form_received.arn_no as arn_no','td_form_received.euin_no as euin_no'
                        )
                        ->where('td_mutual_fund.delete_flag','N')
                        ->where('md_trans.trans_type_id',$trans_type_id)
                        ->where('td_mutual_fund.form_status','!=','P')
                        ->where('td_mutual_fund.tin_no',$tin_no)
                        ->orderBy('td_mutual_fund.updated_at','desc')
                        ->paginate($paginate);
                }elseif (!empty($bu_type)) {
                    # code...
                } else {
                    // return 'Hii';
                    $data=MutualFund::join('td_form_received','td_form_received.temp_tin_no','=','td_mutual_fund.temp_tin_no')
                        ->join('md_trans','md_trans.id','=','td_mutual_fund.trans_id')
                        ->join('md_scheme','md_scheme.id','=','td_mutual_fund.trans_scheme_from')
                        ->leftJoin('md_scheme as md_scheme_2','md_scheme_2.id','=','td_mutual_fund.trans_scheme_to')
                        ->join('md_client','md_client.id','=','td_mutual_fund.first_client_id')
                        ->leftJoin('md_client as md_client_2','md_client_2.id','=','td_mutual_fund.second_client_id')
                        ->leftJoin('md_client as md_client_3','md_client_3.id','=','td_mutual_fund.third_client_id')
                        ->join('md_plan','md_plan.id','=','td_mutual_fund.plan_id')
                        ->join('md_option','md_option.id','=','td_mutual_fund.option_id')
                        ->leftJoin('md_plan as md_plan_2','md_plan_2.id','=','td_mutual_fund.plan_id_to')
                        ->leftJoin('md_option as md_option_2','md_option_2.id','=','td_mutual_fund.option_id_to')
                        ->leftJoin('md_rnt','md_rnt.id','=','td_mutual_fund.rnt_login_at')
                        ->select('td_mutual_fund.*','md_trans.trns_name as trans_name','md_trans.trans_type_id as trans_type_id','md_scheme.scheme_name as scheme_name',
                        'td_form_received.bu_type as bu_type','md_scheme.scheme_name as scheme_name','md_scheme_2.scheme_name as scheme_name_to',
                        'md_client.client_code as first_client_code','md_client.client_name as first_client_name','md_client.pan as first_client_pan','md_client.client_type as first_client_type',
                        'md_client_2.client_code as second_client_code','md_client_2.client_name as second_client_name','md_client_2.pan as second_client_pan','md_client_2.client_type as second_client_type',
                        'md_plan.plan_name as plan_name','md_option.opt_name as opt_name','md_plan_2.plan_name as plan_name_to','md_option_2.opt_name as opt_name_to',
                        'md_rnt.rnt_name as rnt_name','td_form_received.arn_no as arn_no','td_form_received.euin_no as euin_no'
                        )
                        ->where('td_mutual_fund.delete_flag','N')
                        ->where('md_trans.trans_type_id',$trans_type_id)
                        ->where('td_mutual_fund.form_status','!=','P')
                        ->orderBy('td_mutual_fund.updated_at','desc')
                        ->paginate($paginate);   
                }
            
        } catch (\Throwable $th) {
            // throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function export(Request $request)
    {
        try {
            $paginate=$request->paginate;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            
            $tin_no=$request->tin_no;
            $option=$request->option;
            $sub_brk_cd=$request->sub_brk_cd;
            $ins_type_id=$request->ins_type_id;
            $insured_bu_type=$request->insured_bu_type;
            $proposer_name=$request->proposer_name;
            $euin_no=$request->euin_no;
            $bu_type=json_decode($request->bu_type);
            
            $login_status=$request->login_status;
            $date_status=$request->date_status;
            $start_date=$request->start_date;
            $end_date=$request->end_date;

           
                if ($tin_no) {
                    
                }elseif (!empty($bu_type)) {
                    
                } else {
                    $data=MutualFund::join('td_form_received','td_form_received.temp_tin_no','=','td_mutual_fund.temp_tin_no')
                        ->join('md_trans','md_trans.id','=','td_mutual_fund.trans_id')
                        ->join('md_scheme','md_scheme.id','=','td_mutual_fund.trans_scheme_from')
                        ->leftJoin('md_scheme as md_scheme_2','md_scheme_2.id','=','td_mutual_fund.trans_scheme_to')
                        ->join('md_client','md_client.id','=','td_mutual_fund.first_client_id')
                        ->leftJoin('md_client as md_client_2','md_client_2.id','=','td_mutual_fund.second_client_id')
                        ->leftJoin('md_client as md_client_3','md_client_3.id','=','td_mutual_fund.third_client_id')
                        ->join('md_plan','md_plan.id','=','td_mutual_fund.plan_id')
                        ->join('md_option','md_option.id','=','td_mutual_fund.option_id')
                        ->leftJoin('md_plan as md_plan_2','md_plan_2.id','=','td_mutual_fund.plan_id_to')
                        ->leftJoin('md_option as md_option_2','md_option_2.id','=','td_mutual_fund.option_id_to')
                        ->leftJoin('md_rnt','md_rnt.id','=','td_mutual_fund.rnt_login_at')
                        ->select('td_mutual_fund.*','md_trans.trns_name as trans_name','md_trans.trans_type_id as trans_type_id','md_scheme.scheme_name as scheme_name',
                        'td_form_received.bu_type as bu_type','md_scheme.scheme_name as scheme_name','md_scheme_2.scheme_name as scheme_name_to',
                        'md_client.client_code as first_client_code','md_client.client_name as first_client_name','md_client.pan as first_client_pan','md_client.client_type as first_client_type',
                        'md_client_2.client_code as second_client_code','md_client_2.client_name as second_client_name','md_client_2.pan as second_client_pan','md_client_2.client_type as second_client_type',
                        'md_plan.plan_name as plan_name','md_option.opt_name as opt_name','md_plan_2.plan_name as plan_name_to','md_option_2.opt_name as opt_name_to',
                        'md_rnt.rnt_name as rnt_name','td_form_received.arn_no as arn_no','td_form_received.euin_no as euin_no'
                        )
                        ->where('td_mutual_fund.delete_flag','N')
                        ->where('md_trans.trans_type_id',$trans_type_id)
                        ->where('td_mutual_fund.form_status','!=','P')
                        ->orderBy('td_mutual_fund.updated_at','desc')
                        ->get();   
                }
            
        } catch (\Throwable $th) {
            // throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function update(Request $request)
    {
        try {
            // return $request;
            $data1=MutualFund::where('tin_no',$request->tin_no)->first();
            
            if ($request->manual_trans_status=="P") { //process
                $upload_soa=$request->upload_soa;
                if ($upload_soa) {
                    $path_extension=$upload_soa->getClientOriginalExtension();
                    $upload_soa_name=$request->tin_no.'_'.date('YmdHis').".".$path_extension;
                    $upload_soa->move(public_path('soa-copy/'),$upload_soa_name);
                }else{
                    $upload_soa_name=$data1->upload_soa;
                    // return $doc_name;
                }
                MutualFund::where('tin_no',$request->tin_no)->update(array(
                    'manual_trans_status'=>$request->manual_trans_status,
                    'process_date'=>$request->process_date,
                    'folio_no'=>$request->folio_no,
                    'upload_soa'=>$upload_soa_name,
                    'manual_update_remarks'=>$request->manual_update_remarks,
                    'form_status'=>'M',
                    // 'updated_at'
                ));   
            }elseif ($request->manual_trans_status=="R") { // reject
                $reject_memo=$request->reject_memo;
                if ($reject_memo) {
                    $path_extension=$reject_memo->getClientOriginalExtension();
                    $reject_memo_name=$request->tin_no.'_'.date('YmdHis').".".$path_extension;
                    $reject_memo->move(public_path('reject-memo/'),$reject_memo_name);
                }else{
                    $reject_memo_name=$data1->reject_memo;
                    // return $doc_name;
                }
                MutualFund::where('tin_no',$request->tin_no)->update(array(
                    'manual_trans_status'=>$request->manual_trans_status,
                    'contact_to_amc'=>$request->contact_to_amc,
                    'contact_via'=>$request->contact_via,
                    'contact_per_name'=>isset($request->contact_per_name)?$request->contact_per_name:NULL,
                    'contact_per_phone'=>isset($request->contact_per_phone)?$request->contact_per_phone:NULL,
                    'contact_per_email'=>isset($request->contact_per_email)?$request->contact_per_email:NULL,
                    'reject_reason_id'=>isset($request->reject_reason_id)?$request->reject_reason_id:NULL,
                    'reject_memo'=>$reject_memo_name,
                    'manual_update_remarks'=>$request->manual_update_remarks,
                ));   
            } elseif ($request->manual_trans_status=="P") { // pending
                MutualFund::where('tin_no',$request->tin_no)->update(array(
                    'manual_trans_status'=>$request->manual_trans_status,
                    'contact_to_amc'=>$request->contact_to_amc,
                    'contact_via'=>$request->contact_via,
                    'contact_per_name'=>isset($request->contact_per_name)?$request->contact_per_name:NULL,
                    'contact_per_phone'=>isset($request->contact_per_phone)?$request->contact_per_phone:NULL,
                    'contact_per_email'=>isset($request->contact_per_email)?$request->contact_per_email:NULL,
                    'reject_reason_id'=>isset($request->reject_reason_id)?$request->reject_reason_id:NULL,
                    'pending_reason'=>isset($request->pending_reason)?$request->pending_reason:NULL,
                    'manual_update_remarks'=>$request->manual_update_remarks,
                ));   
            }
            $data=MutualFund::where('tin_no',$request->tin_no)->first();
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function finalSubmit(Request $request)
    {
        try {
            return Helper::SuccessResponse($data);

            $trans_type_id=$request->trans_type_id;
            // return $request;
            $data=MutualFund::join('td_form_received','td_form_received.temp_tin_no','=','td_mutual_fund.temp_tin_no')
                ->join('md_trans','md_trans.id','=','td_mutual_fund.trans_id')
                ->leftJoin('md_employee','md_employee.euin_no','=','td_ins_form_received.euin_no')
                ->select('td_mutual_fund.*','md_trans.trns_name as trans_name','md_trans.trans_type_id as trans_type_id',
                'td_form_received.arn_no as arn_no','td_form_received.euin_no as euin_no')
                ->where('md_trans.trans_type_id',$trans_type_id)
                ->whereDate('td_mutual_fund.updated_at',date('Y-m-d'))
                ->where('td_mutual_fund.form_status','=','A')
                ->get();   

            // return $data;
            $sort_arr=[];
            foreach ($data as $key => $item) {
                $sort_arr[$item['first_client_id']][$key] = $item;
            }
            ksort($sort_arr, SORT_NUMERIC);
            // return $sort_arr;

            $data1["email"] = "cmaity905@gmail.com";
            $data1["title"] = "From NuEdge Testing";
            $data1["body"] = "This is Demo";
     
            foreach ($sort_arr as $key => $value) {
                // return $value;
                $files = [];
                foreach ($value as $key => $value1) {
                    // return $value1;
                    $filePath=public_path('ins-policy-copy/'.$value1->ack_copy_scan);
                    // $filePath = public_path('test1.pdf');
                    $filePath1 = public_path($value1->first_client_id.'_'.$key.'_'.'encrypt_documented.pdf');
                    // return $filePath1;
                    $password='1234';
                    Helper::encrypt($filePath, $filePath1, $password);
                    // return $filePath1;
                    array_push($files,$filePath1);
                }
                // return $files;
                // ================= start mail send code =================
                // $email=Email::find(2);
                // Mail::to($request->email)->send(new SendAckEmail($client_name,$email->subject,$email->body));

                // Mail::send('emails.operation.ack-copy', $data1, function($message)use($data1, $files) {
                //     $message->to($data1["email"], $data1["email"])
                //             ->subject($data1["title"]);
                //     foreach ($files as $file){
                //         $message->attach($file);
                //     }
                    
                // });
                // ==========end mail send code ==========================
                // start remove file
                foreach ($files as $file){
                    if (file_exists($file) != null) {
                        unlink($file);
                    }
                }
                // end remove file
            }
            
        } catch (\Throwable $th) {
            //throw $th;
           $msg="Email Sending Error.";
            return Helper::ErrorResponse($msg);
        }
        return Helper::SuccessResponse($data);
    }
}
