<?php

namespace App\Http\Controllers\v1\Operation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{KYC,Client};
use Validator;

class KYCController extends Controller
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
            if ($option==3) {
                if ($login_status=='L') {
                    $login_status="!=";
                }else{
                    $login_status="=";
                }
                // return $login_status;
                if ($date_status=='T') {
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
                        ->whereDate('td_kyc.entry_dt',date('Y-m-d'))
                        ->orderBy('td_kyc.entry_dt','DESC')
                        ->paginate($paginate);   
                }else{
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
                        ->whereDate('td_kyc.entry_dt','>=',date('Y-m-d',strtotime($start_date)))
                        ->whereDate('td_kyc.entry_dt','<=',date('Y-m-d',strtotime($end_date)))
                        ->orderBy('td_kyc.entry_dt','DESC')
                        ->paginate($paginate);   
                }
            }else {
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
            if ($option==3) {
                if ($login_status=='L') {
                    $login_status="!=";
                }else{
                    $login_status="=";
                }
                // return $login_status;
                if ($date_status=='T') {
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
                        ->whereDate('td_kyc.entry_dt',date('Y-m-d'))
                        ->orderBy('td_kyc.entry_dt','DESC')
                        ->get();   
                }else{
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
                        ->whereDate('td_kyc.entry_dt','>=',date('Y-m-d',strtotime($start_date)))
                        ->whereDate('td_kyc.entry_dt','<=',date('Y-m-d',strtotime($end_date)))
                        ->orderBy('td_kyc.entry_dt','DESC')
                        ->get();   
                }
            }else {
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
            }
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
    public function index(Request $request)
    {
        try {  
            $search=$request->search;
            $tin_no=$request->tin_no;
            if ($search!='') {
                $data=KYC::where('client_code',$search)
                    // ->orWhere('mobile','like', '%' . $search . '%')
                    // ->orWhere('email','like', '%' . $search . '%')
                    ->get();      
            }elseif ($tin_no) {
                $data=KYC::where('tin_no','like', '%' . $tin_no . '%')
                    // ->orWhere('mobile','like', '%' . $search . '%')
                    // ->orWhere('email','like', '%' . $search . '%')
                    ->get();   
            } else{
                $data=KYC::whereDate('updated_at',date('Y-m-d'))->get();      
            }
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function showAdd(Request $request)
    {
        try {  
            $search=$request->search;
            $data=Client::with('ClientDoc')->where('client_code',$search)
                // ->orWhere('mobile','like', '%' . $search . '%')
                // ->orWhere('email','like', '%' . $search . '%')
                ->get();      
           
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function createUpdate(Request $request)
    {
        // $validator = Validator::make(request()->all(),[
        //     'kyc_type'=>'required',
        //     'kyc_login_type'=>'required',
        //     'kyc_login_at'=>'required',
        // ]);
    
        // if($validator->fails()) {
        //     $errors = $validator->errors();
        //     return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        // }
        try {
            $is_has=KYC::where('client_id',$request->client_id)->get();
            // if (count($is_has) > 0) {
            //     return Helper::WarningResponse('Client already exist.');
            // }else{
                // return $request;
                $product_id=1;
                $trans_type_id=2;
                // 2
                $is_has=KYC::get();
                if (count($is_has)>0) {
                    $tin_no=Helper::GenTIN($product_id,$trans_type_id,(count($is_has)+1));
                } else {
                    $tin_no=Helper::GenTIN($product_id,$trans_type_id,1);
                }
                $arn_no=Helper::CommonParamValue(1);

                $scaned_form=$request->scaned_form;
                    $doc_name='';
                    if ($scaned_form) {
                        $cv_path_extension=$scaned_form->getClientOriginalExtension();
                        $doc_name=microtime(true).".".$cv_path_extension;
                        $scaned_form->move(public_path('kyc-form/'),$doc_name);
                    }

                $data=KYC::create(array(
                    'tin_no'=>$tin_no,
                    'entry_dt'=>date('Y-m-d'),
                    'bu_type'=>$request->bu_type,
                    'arn_no'=>$arn_no,
                    'sub_arn_no'=>isset($request->sub_arn_no)?$request->sub_arn_no:NULL,
                    'euin_no'=>isset($request->euin_no)?$request->euin_no:NULL,
                    'sub_brk_cd'=>isset($request->sub_brk_cd)?$request->sub_brk_cd:NULL,
                    'client_id'=>$request->client_id,
                    'present_kyc_status'=>$request->present_kyc_status,
                    'kyc_type'=>$request->kyc_type,
                    'kyc_login_type'=>$request->kyc_login_type,
                    'kyc_login_at'=>$request->kyc_login_at,
                    'scaned_form'=>$doc_name,
                    'remarks'=>$request->remarks,
                    'form_scan_status'=>'A',
                    'final_kyc_status'=>'U',
                    'branch_code'=>1,
                    'deleted_flag'=>'N',
                    'created_by'=>Helper::modifyUser($request->user()),
                    // 'created_by'=>'',
                    // modification_type
                    // fresh_type
                ));    
            // }  
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    
}