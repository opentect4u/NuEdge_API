<?php

namespace App\Http\Controllers\v1\Operation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{KYC,Client,FormReceived};
use Validator;
use Illuminate\Support\Carbon;
use Mail;
use App\Mail\Master\SendAckEmail;
use App\Models\Email;

class KYCManualUpdateController extends Controller
{
    public function searchDetails(Request $request)
    {
        try {
            $paginate=$request->paginate;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            $trans_type_id=$request->trans_type_id;
            
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

            $order=$request->order;
            $field=$request->field;

            $from_date=$request->from_date;
            $to_date=$request->to_date;
            $login_at=json_decode($request->login_at);
            $login_type=$request->login_type;
            $client_code=$request->client_code;
            $tin_no=$request->tin_no;

            if ($paginate=='A') {
                $paginate=999999999;
            }

            if ($order && $field) {
                $rawOrderBy='';
                if ($order > 0) {
                    $rawOrderBy=$field.' ASC';
                } else {
                    $rawOrderBy=$field.' DESC';
                }
                if (($from_date && $to_date) || $tin_no || $client_code || ($login_type && !empty($login_at))) {
                    $rawQuery='';
                    if ($from_date && $to_date) {
                        if (strlen($rawQuery) > 0) {
                            $rawQuery.=" AND td_kyc.entry_dt"." >= '".date('Y-m-d',strtotime($from_date))."'";
                        } else {
                            $rawQuery.=" td_kyc.entry_dt"." >= '".date('Y-m-d',strtotime($from_date))."'";
                        }
                        $rawQuery.=" AND td_kyc.entry_dt"." <= '".date('Y-m-d',strtotime($to_date))."'";
                    }
                    if ($tin_no) {
                        if (strlen($rawQuery) > 0) {
                            $rawQuery.=" AND td_kyc.tin_no='".$tin_no."'";
                        }else {
                            $rawQuery.=" td_kyc.tin_no='".$tin_no."'";
                        }
                    }
                    if ($client_code) {
                        if (strlen($rawQuery) > 0) {
                            $rawQuery.=" AND td_kyc.client_id='".$client_code."'";
                        }else {
                            $rawQuery.=" td_kyc.client_id='".$client_code."'";
                        }
                    }
                    if ($login_type && $login_type=='A' && !empty($login_at)) {
                        $login_at_string= implode(',', $login_at);
                        if (strlen($rawQuery) > 0) {
                            $rawQuery.=" AND md_amc.id IN (".$login_at_string.")";
                        }else {
                            $rawQuery.=" md_amc.id IN (".$login_at_string.")";
                        }
                    }elseif ($login_type && $login_type!='A' && !empty($login_at)) {
                        $login_at_string= implode(',', $login_at);
                        if (strlen($rawQuery) > 0) {
                            $rawQuery.=" AND md_rnt.id IN (".$login_at_string.")";
                        }else {
                            $rawQuery.=" md_rnt.id IN (".$login_at_string.")";
                        }
                    }
                    // return $rawQuery;
                    $data=KYC::join('md_client','md_client.id','=','td_kyc.client_id')
                        ->leftJoin('md_trans','md_trans.id','=','td_kyc.kyc_type')
                        ->leftJoin('md_rnt','md_rnt.id','=','td_kyc.kyc_login_at')
                        ->leftJoin('md_amc','md_amc.id','=','td_kyc.kyc_login_at')
                        ->leftJoin('md_branch','md_branch.id','=','td_kyc.branch_code')
                        ->leftJoin('md_employee','md_employee.euin_no','=','td_kyc.euin_no')
                        ->select('td_kyc.*','md_client.client_code as client_code','md_client.client_name as client_name','md_client.client_type as client_type','md_client.pan as pan',
                        'md_rnt.rnt_name as rnt_name','md_amc.amc_name as amc_name','md_trans.trns_name as trans_name','md_trans.trans_type_id as trans_type_id','md_branch.brn_name as branch_name'
                        ,'md_employee.emp_name as emp_name')
                        ->whereDate('td_kyc.entry_dt',date('Y-m-d'))
                        ->where('td_kyc.form_status','!=','P')
                        ->where('td_kyc.deleted_flag','N')
                        ->whereRaw($rawQuery)
                        ->orderByRaw($rawOrderBy)
                        ->paginate($paginate); 
                } else {
                    $data=KYC::join('md_client','md_client.id','=','td_kyc.client_id')
                        ->leftJoin('md_trans','md_trans.id','=','td_kyc.kyc_type')
                        ->leftJoin('md_rnt','md_rnt.id','=','td_kyc.kyc_login_at')
                        ->leftJoin('md_amc','md_amc.id','=','td_kyc.kyc_login_at')
                        ->leftJoin('md_branch','md_branch.id','=','td_kyc.branch_code')
                        ->leftJoin('md_employee','md_employee.euin_no','=','td_kyc.euin_no')
                        ->select('td_kyc.*','md_client.client_code as client_code','md_client.client_name as client_name','md_client.client_type as client_type','md_client.pan as pan',
                        'md_rnt.rnt_name as rnt_name','md_amc.amc_name as amc_name','md_trans.trns_name as trans_name','md_trans.trans_type_id as trans_type_id','md_branch.brn_name as branch_name'
                        ,'md_employee.emp_name as emp_name')
                        ->whereDate('td_kyc.entry_dt',date('Y-m-d'))
                        ->where('td_kyc.form_status','!=','P')
                        ->where('td_kyc.deleted_flag','N')
                        ->orderByRaw($rawOrderBy)
                        ->paginate($paginate); 
                }
            } elseif (($from_date && $to_date) || $tin_no || $client_code || ($login_type && !empty($login_at))) {
                $rawQuery='';
                if ($from_date && $to_date) {
                    if (strlen($rawQuery) > 0) {
                        $rawQuery.=" AND td_kyc.entry_dt"." >= '".date('Y-m-d',strtotime($from_date))."'";
                    } else {
                        $rawQuery.=" td_kyc.entry_dt"." >= '".date('Y-m-d',strtotime($from_date))."'";
                    }
                    $rawQuery.=" AND td_kyc.entry_dt"." <= '".date('Y-m-d',strtotime($to_date))."'";
                }
                if ($tin_no) {
                    if (strlen($rawQuery) > 0) {
                        $rawQuery.=" AND td_kyc.tin_no='".$tin_no."'";
                    }else {
                        $rawQuery.=" td_kyc.tin_no='".$tin_no."'";
                    }
                }
                if ($client_code) {
                    if (strlen($rawQuery) > 0) {
                        $rawQuery.=" AND td_kyc.client_id='".$client_code."'";
                    }else {
                        $rawQuery.=" td_kyc.client_id='".$client_code."'";
                    }
                }
                if ($login_type && $login_type=='A' && !empty($login_at)) {
                    $login_at_string= implode(',', $login_at);
                    if (strlen($rawQuery) > 0) {
                        $rawQuery.=" AND md_amc.id IN (".$login_at_string.")";
                    }else {
                        $rawQuery.=" md_amc.id IN (".$login_at_string.")";
                    }
                }elseif ($login_type && $login_type!='A' && !empty($login_at)) {
                    $login_at_string= implode(',', $login_at);
                    if (strlen($rawQuery) > 0) {
                        $rawQuery.=" AND md_rnt.id IN (".$login_at_string.")";
                    }else {
                        $rawQuery.=" md_rnt.id IN (".$login_at_string.")";
                    }
                }
                // return $rawQuery;
                $data=KYC::join('md_client','md_client.id','=','td_kyc.client_id')
                    ->leftJoin('md_trans','md_trans.id','=','td_kyc.kyc_type')
                    ->leftJoin('md_rnt','md_rnt.id','=','td_kyc.kyc_login_at')
                    ->leftJoin('md_amc','md_amc.id','=','td_kyc.kyc_login_at')
                    ->leftJoin('md_branch','md_branch.id','=','td_kyc.branch_code')
                    ->leftJoin('md_employee','md_employee.euin_no','=','td_kyc.euin_no')
                    ->select('td_kyc.*','md_client.client_code as client_code','md_client.client_name as client_name','md_client.client_type as client_type','md_client.pan as pan',
                    'md_rnt.rnt_name as rnt_name','md_amc.amc_name as amc_name','md_trans.trns_name as trans_name','md_trans.trans_type_id as trans_type_id','md_branch.brn_name as branch_name'
                    ,'md_employee.emp_name as emp_name')
                    ->whereDate('td_kyc.entry_dt',date('Y-m-d'))
                    ->where('td_kyc.deleted_flag','N')
                    ->where('td_kyc.form_status','!=','P')
                    ->whereRaw($rawQuery)
                    ->orderBy('td_kyc.entry_dt','DESC')
                    ->paginate($paginate); 
            } else {
                $data=KYC::join('md_client','md_client.id','=','td_kyc.client_id')
                    ->leftJoin('md_trans','md_trans.id','=','td_kyc.kyc_type')
                    ->leftJoin('md_rnt','md_rnt.id','=','td_kyc.kyc_login_at')
                    ->leftJoin('md_amc','md_amc.id','=','td_kyc.kyc_login_at')
                    ->leftJoin('md_branch','md_branch.id','=','td_kyc.branch_code')
                    ->leftJoin('md_employee','md_employee.euin_no','=','td_kyc.euin_no')
                    ->select('td_kyc.*','md_client.client_code as client_code','md_client.client_name as client_name','md_client.client_type as client_type','md_client.pan as pan',
                    'md_rnt.rnt_name as rnt_name','md_amc.amc_name as amc_name','md_trans.trns_name as trans_name','md_trans.trans_type_id as trans_type_id','md_branch.brn_name as branch_name'
                    ,'md_employee.emp_name as emp_name')
                    ->whereDate('td_kyc.entry_dt',date('Y-m-d'))
                    ->where('td_kyc.form_status','!=','P')
                    ->where('td_kyc.deleted_flag','N')
                    ->orderBy('td_kyc.entry_dt','DESC')
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

            $order=$request->order;
            $field=$request->field;

            $from_date=$request->from_date;
            $to_date=$request->to_date;
            $login_at=json_decode($request->login_at);
            $login_type=$request->login_type;
            $client_code=$request->client_code;
            $tin_no=$request->tin_no;

            if ($order && $field) {
                $rawOrderBy='';
                if ($order > 0) {
                    $rawOrderBy=$field.' ASC';
                } else {
                    $rawOrderBy=$field.' DESC';
                }
                if (($from_date && $to_date) || $tin_no || $client_code || ($login_type && !empty($login_at))) {
                    $rawQuery='';
                    if ($from_date && $to_date) {
                        if (strlen($rawQuery) > 0) {
                            $rawQuery.=" AND td_kyc.entry_dt"." >= '".date('Y-m-d',strtotime($from_date))."'";
                        } else {
                            $rawQuery.=" td_kyc.entry_dt"." >= '".date('Y-m-d',strtotime($from_date))."'";
                        }
                        $rawQuery.=" AND td_kyc.entry_dt"." <= '".date('Y-m-d',strtotime($to_date))."'";
                    }
                    if ($tin_no) {
                        if (strlen($rawQuery) > 0) {
                            $rawQuery.=" AND td_kyc.tin_no='".$tin_no."'";
                        }else {
                            $rawQuery.=" td_kyc.tin_no='".$tin_no."'";
                        }
                    }
                    if ($client_code) {
                        if (strlen($rawQuery) > 0) {
                            $rawQuery.=" AND td_kyc.client_id='".$client_code."'";
                        }else {
                            $rawQuery.=" td_kyc.client_id='".$client_code."'";
                        }
                    }
                    if ($login_type && $login_type=='A' && !empty($login_at)) {
                        $login_at_string= implode(',', $login_at);
                        if (strlen($rawQuery) > 0) {
                            $rawQuery.=" AND md_amc.id IN (".$login_at_string.")";
                        }else {
                            $rawQuery.=" md_amc.id IN (".$login_at_string.")";
                        }
                    }elseif ($login_type && $login_type!='A' && !empty($login_at)) {
                        $login_at_string= implode(',', $login_at);
                        if (strlen($rawQuery) > 0) {
                            $rawQuery.=" AND md_rnt.id IN (".$login_at_string.")";
                        }else {
                            $rawQuery.=" md_rnt.id IN (".$login_at_string.")";
                        }
                    }
                    // return $rawQuery;
                    $data=KYC::join('md_client','md_client.id','=','td_kyc.client_id')
                        ->leftJoin('md_trans','md_trans.id','=','td_kyc.kyc_type')
                        ->leftJoin('md_rnt','md_rnt.id','=','td_kyc.kyc_login_at')
                        ->leftJoin('md_amc','md_amc.id','=','td_kyc.kyc_login_at')
                        ->leftJoin('md_branch','md_branch.id','=','td_kyc.branch_code')
                        ->leftJoin('md_employee','md_employee.euin_no','=','td_kyc.euin_no')
                        ->select('td_kyc.*','md_client.client_code as client_code','md_client.client_name as client_name','md_client.client_type as client_type','md_client.pan as pan',
                        'md_rnt.rnt_name as rnt_name','md_amc.amc_name as amc_name','md_trans.trns_name as trans_name','md_trans.trans_type_id as trans_type_id','md_branch.brn_name as branch_name'
                        ,'md_employee.emp_name as emp_name')
                        ->whereDate('td_kyc.entry_dt',date('Y-m-d'))
                        ->where('td_kyc.form_status','!=','P')
                        ->where('td_kyc.deleted_flag','N')
                        ->whereRaw($rawQuery)
                        ->orderByRaw($rawOrderBy)
                        ->get(); 
                } else {
                    $data=KYC::join('md_client','md_client.id','=','td_kyc.client_id')
                        ->leftJoin('md_trans','md_trans.id','=','td_kyc.kyc_type')
                        ->leftJoin('md_rnt','md_rnt.id','=','td_kyc.kyc_login_at')
                        ->leftJoin('md_amc','md_amc.id','=','td_kyc.kyc_login_at')
                        ->leftJoin('md_branch','md_branch.id','=','td_kyc.branch_code')
                        ->leftJoin('md_employee','md_employee.euin_no','=','td_kyc.euin_no')
                        ->select('td_kyc.*','md_client.client_code as client_code','md_client.client_name as client_name','md_client.client_type as client_type','md_client.pan as pan',
                        'md_rnt.rnt_name as rnt_name','md_amc.amc_name as amc_name','md_trans.trns_name as trans_name','md_trans.trans_type_id as trans_type_id','md_branch.brn_name as branch_name'
                        ,'md_employee.emp_name as emp_name')
                        ->whereDate('td_kyc.entry_dt',date('Y-m-d'))
                        ->where('td_kyc.form_status','!=','P')
                        ->where('td_kyc.deleted_flag','N')
                        ->orderByRaw($rawOrderBy)
                        ->get(); 
                }
            } elseif (($from_date && $to_date) || $tin_no || $client_code || ($login_type && !empty($login_at))) {
                $rawQuery='';
                if ($from_date && $to_date) {
                    if (strlen($rawQuery) > 0) {
                        $rawQuery.=" AND td_kyc.entry_dt"." >= '".date('Y-m-d',strtotime($from_date))."'";
                    } else {
                        $rawQuery.=" td_kyc.entry_dt"." >= '".date('Y-m-d',strtotime($from_date))."'";
                    }
                    $rawQuery.=" AND td_kyc.entry_dt"." <= '".date('Y-m-d',strtotime($to_date))."'";
                }
                if ($tin_no) {
                    if (strlen($rawQuery) > 0) {
                        $rawQuery.=" AND td_kyc.tin_no='".$tin_no."'";
                    }else {
                        $rawQuery.=" td_kyc.tin_no='".$tin_no."'";
                    }
                }
                if ($client_code) {
                    if (strlen($rawQuery) > 0) {
                        $rawQuery.=" AND td_kyc.client_id='".$client_code."'";
                    }else {
                        $rawQuery.=" td_kyc.client_id='".$client_code."'";
                    }
                }
                if ($login_type && $login_type=='A' && !empty($login_at)) {
                    $login_at_string= implode(',', $login_at);
                    if (strlen($rawQuery) > 0) {
                        $rawQuery.=" AND md_amc.id IN (".$login_at_string.")";
                    }else {
                        $rawQuery.=" md_amc.id IN (".$login_at_string.")";
                    }
                }elseif ($login_type && $login_type!='A' && !empty($login_at)) {
                    $login_at_string= implode(',', $login_at);
                    if (strlen($rawQuery) > 0) {
                        $rawQuery.=" AND md_rnt.id IN (".$login_at_string.")";
                    }else {
                        $rawQuery.=" md_rnt.id IN (".$login_at_string.")";
                    }
                }
                // return $rawQuery;
                $data=KYC::join('md_client','md_client.id','=','td_kyc.client_id')
                    ->leftJoin('md_trans','md_trans.id','=','td_kyc.kyc_type')
                    ->leftJoin('md_rnt','md_rnt.id','=','td_kyc.kyc_login_at')
                    ->leftJoin('md_amc','md_amc.id','=','td_kyc.kyc_login_at')
                    ->leftJoin('md_branch','md_branch.id','=','td_kyc.branch_code')
                    ->leftJoin('md_employee','md_employee.euin_no','=','td_kyc.euin_no')
                    ->select('td_kyc.*','md_client.client_code as client_code','md_client.client_name as client_name','md_client.client_type as client_type','md_client.pan as pan',
                    'md_rnt.rnt_name as rnt_name','md_amc.amc_name as amc_name','md_trans.trns_name as trans_name','md_trans.trans_type_id as trans_type_id','md_branch.brn_name as branch_name'
                    ,'md_employee.emp_name as emp_name')
                    ->whereDate('td_kyc.entry_dt',date('Y-m-d'))
                    ->where('td_kyc.deleted_flag','N')
                    ->where('td_kyc.form_status','!=','P')
                    ->whereRaw($rawQuery)
                    ->orderBy('td_kyc.entry_dt','DESC')
                    ->get(); 
            } else {
                $data=KYC::join('md_client','md_client.id','=','td_kyc.client_id')
                    ->leftJoin('md_trans','md_trans.id','=','td_kyc.kyc_type')
                    ->leftJoin('md_rnt','md_rnt.id','=','td_kyc.kyc_login_at')
                    ->leftJoin('md_amc','md_amc.id','=','td_kyc.kyc_login_at')
                    ->leftJoin('md_branch','md_branch.id','=','td_kyc.branch_code')
                    ->leftJoin('md_employee','md_employee.euin_no','=','td_kyc.euin_no')
                    ->select('td_kyc.*','md_client.client_code as client_code','md_client.client_name as client_name','md_client.client_type as client_type','md_client.pan as pan',
                    'md_rnt.rnt_name as rnt_name','md_amc.amc_name as amc_name','md_trans.trns_name as trans_name','md_trans.trans_type_id as trans_type_id','md_branch.brn_name as branch_name'
                    ,'md_employee.emp_name as emp_name')
                    ->whereDate('td_kyc.entry_dt',date('Y-m-d'))
                    ->where('td_kyc.form_status','!=','P')
                    ->where('td_kyc.deleted_flag','N')
                    ->orderBy('td_kyc.entry_dt','DESC')
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
            return $request;
            $data1=KYC::where('tin_no',$request->tin_no)->first();
            
            if ($request->manual_trans_status=="P") { //process
                $upload_soa=$request->upload_scan;
                if ($upload_soa) {
                    $path_extension=$upload_soa->getClientOriginalExtension();
                    $upload_soa_name=$request->tin_no.'_'.date('YmdHis').".".$path_extension;
                    $upload_soa->move(public_path('kyc-scan-copy/'),$upload_soa_name);
                }else{
                    $upload_soa_name=$data1->upload_soa;
                    // return $doc_name;
                }
                KYC::where('tin_no',$request->tin_no)->update(array(
                    'manual_trans_status'=>$request->manual_trans_status,
                    'process_date'=>$request->process_date,
                    'ckyc_no'=>$request->ckyc_no,
                    'upload_scan'=>$upload_soa_name,
                    'manual_update_remarks'=>$request->manual_update_remarks,
                    'form_status'=>'M',
                    // 'updated_at'
                ));   
            }elseif ($request->manual_trans_status=="R") { // reject
                $reject_memo=$request->reject_memo;
                if ($reject_memo) {
                    $path_extension=$reject_memo->getClientOriginalExtension();
                    $reject_memo_name=$request->tin_no.'_'.date('YmdHis').".".$path_extension;
                    $reject_memo->move(public_path('kyc-reject-memo/'),$reject_memo_name);
                }else{
                    $reject_memo_name=$data1->reject_memo;
                    // return $doc_name;
                }
                KYC::where('tin_no',$request->tin_no)->update(array(
                    'manual_trans_status'=>$request->manual_trans_status,
                    'contact_to_amc'=>$request->contact_to_amc,
                    'contact_via'=>$request->contact_via,
                    'contact_per_name'=>isset($request->contact_per_name)?$request->contact_per_name:NULL,
                    'contact_per_phone'=>isset($request->contact_per_phone)?$request->contact_per_phone:NULL,
                    'contact_per_email'=>isset($request->contact_per_email)?$request->contact_per_email:NULL,
                    'reject_reason_id'=>isset($request->reject_reason_id)?$request->reject_reason_id:NULL,
                    'reject_memo'=>$reject_memo_name,
                    'manual_update_remarks'=>$request->manual_update_remarks,
                    'form_status'=>'M',
                ));   
            } elseif ($request->manual_trans_status=="N") { // pending
                KYC::where('tin_no',$request->tin_no)->update(array(
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
            $data=KYC::where('tin_no',$request->tin_no)->first();
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

}
