<?php

namespace App\Http\Controllers\v1\Operation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{KYC,Client};
use Validator;
use Illuminate\Support\Carbon;

class KYCAckController extends Controller
{
    public function searchDetails(Request $request)
    {
        try {
            $paginate=$request->paginate;
            $option=$request->option;
            $login_status_id=json_decode($request->login_status_id);

            $trans_type_id=$request->trans_type_id;

            $recv_from=$request->recv_from;
            $sub_brk_cd=$request->sub_brk_cd;
            $euin_no=$request->euin_no;
            $inv_type=$request->inv_type;
            $trans_type=$request->trans_type;
            $date_status=$request->date_status;
            $bu_type=json_decode($request->bu_type);
            $kyc_status=json_decode($request->kyc_status);
            $login_status=$request->login_status;
            
            $start_date=$request->start_date;
            $end_date=$request->end_date;
            // return $bu_type;

            $order=$request->order;
            $field=$request->field;

            $from_date=$request->from_date;
            $to_date=$request->to_date;
            $login_at=json_decode($request->login_at);
            $login_type=$request->login_type;
            $client_code=$request->client_code;
            $tin_no=$request->tin_no;
            if ($paginate=='A' || $paginate=='undefined') {
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
                        ->where('td_kyc.deleted_flag','N')
                        // ->where('td_kyc.tin_no',$tin_no)
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
                    ->where('td_kyc.deleted_flag','N')
                    // ->where('td_kyc.tin_no',$tin_no)
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
                    ->where('td_kyc.deleted_flag','N')
                    ->orderBy('td_kyc.entry_dt','DESC')
                    ->paginate($paginate); 
            }
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
    public function export(Request $request)
    {
        try {
            $paginate=$request->paginate;
            $option=$request->option;
            $login_status_id=json_decode($request->login_status_id);

            $trans_type_id=$request->trans_type_id;

            $recv_from=$request->recv_from;
            $sub_brk_cd=$request->sub_brk_cd;
            $euin_no=$request->euin_no;
            $inv_type=$request->inv_type;
            $trans_type=$request->trans_type;
            $date_status=$request->date_status;
            $bu_type=json_decode($request->bu_type);
            $kyc_status=json_decode($request->kyc_status);
            $login_status=$request->login_status;
            
            $start_date=$request->start_date;
            $end_date=$request->end_date;
            // return $bu_type;

            $order=$request->order;
            $field=$request->field;

            $from_date=$request->from_date;
            $to_date=$request->to_date;
            $login_at=json_decode($request->login_at);
            $login_type=$request->login_type;
            $client_code=$request->client_code;
            $tin_no=$request->tin_no;
            if ($paginate=='A' || $paginate=='undefined') {
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
                        ->where('td_kyc.deleted_flag','N')
                        // ->where('td_kyc.tin_no',$tin_no)
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
                    ->where('td_kyc.deleted_flag','N')
                    // ->where('td_kyc.tin_no',$tin_no)
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
                    ->where('td_kyc.deleted_flag','N')
                    ->orderBy('td_kyc.entry_dt','DESC')
                    ->get(); 
            }
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function update(Request $request)
    {
        // return $request;
        // return Carbon::parse($request->rnt_login_cutt_off)->format('Y-m-d H:i:s');
        try {
            // return $request;
            $data1=KYC::where('tin_no',$request->tin_no)->first();
            $ack_copy_scan=$request->ack_copy_scan;
            if ($ack_copy_scan) {
                $path_extension=$ack_copy_scan->getClientOriginalExtension();
                // $ack_copy_scan_name=microtime(true).".".$path_extension;
                $ack_copy_scan_name="ack_".$request->tin_no.".".$path_extension;
                $ack_copy_scan->move(public_path('kyc-acknowledgement-copy/'),$ack_copy_scan_name);
            }else{
                $ack_copy_scan_name=$data1->ack_copy_scan;
                // return $doc_name;
            }
            // ack_copy_scan
                // rnt_login_time

                if (Carbon::parse($request->rnt_login_time)->format('H') < 15) {
                    $kyc_login_cutt_off = Carbon::parse($request->rnt_login_date)->format('Y-m-d');
                }else {
                    $kyc_login_cutt_off = Carbon::parse($request->rnt_login_date);
                    $kyc_login_cutt_off->addDays(1);
                    $kyc_login_cutt_off->format("Y-m-d");
                }
                // return $kyc_login_cutt_off;
                KYC::where('tin_no',$request->tin_no)->update(array(
                    'kyc_login_dt'=>Carbon::parse($request->kyc_login_dt)->format('Y-m-d').' '.Carbon::parse($request->kyc_login_dt)->format('H:i:s'),
                    'kyc_login_cutt_off'=>Carbon::parse($kyc_login_cutt_off)->format('Y-m-d'),
                    // 'kyc_login_cutt_off'=>$kyc_login_cutt_off,
                    'ack_copy_scan'=>$ack_copy_scan_name,
                    'ack_remarks'=>$request->ack_remarks,
                    'form_status'=>'A',
                    'updated_by'=>Helper::modifyUser($request->user()),
                    // 'updated_at'
                ));   
            // return $data1;
            // return $request->tin_no;
            $data=KYC::where('tin_no',$request->tin_no)->first();
            // return $data;
        } catch (\Throwable $th) {
            // throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
    
}
