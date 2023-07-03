<?php

namespace App\Http\Controllers\v1\FDOperation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{InsProduct,FDFormReceived,FixedDeposit};
use Validator;
use DB;

class FormReceivedController extends Controller
{
    public function searchDetails(Request $request)
    {
        try {
            // return $request;
            $paginate=$request->paginate;
            $field=$request->field;
            $order=$request->order;

            $from_date=$request->from_date;
            $to_date=$request->to_date;
            $investor_code=$request->investor_code;
            $temp_tin_no=$request->temp_tin_no;
            $fd_bu_type=json_decode($request->fd_bu_type);
            $recv_from=$request->recv_from;
            $euin_no=json_decode($request->euin_no);
            $brn_cd=json_decode($request->brn_cd);
            $bu_type=json_decode($request->bu_type);
            $rm_id=json_decode($request->rm_id);
            $sub_brk_cd=json_decode($request->sub_brk_cd);

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
                if (($from_date && $to_date) || $temp_tin_no || $investor_code || $fd_bu_type || $recv_from) {
                    $rawQuery='';
                    if ($from_date && $to_date) {
                        if (strlen($rawQuery) > 0) {
                            $rawQuery.=' AND td_fd_form_received.rec_datetime'.' >= '. $from_date;
                        } else {
                            $rawQuery.=' td_fd_form_received.rec_datetime'.' >= '. $from_date;
                        }
                        $rawQuery.=' AND td_fd_form_received.rec_datetime'.' <= '. $to_date;
                    }
                    if ($temp_tin_no) {
                        if (strlen($rawQuery) > 0) {
                            $rawQuery.=" AND td_fd_form_received.temp_tin_no='".$temp_tin_no."'";
                        }else {
                            $rawQuery.=" td_fd_form_received.temp_tin_no='".$temp_tin_no."'";
                        }
                    }
                    if ($investor_code) {
                        if (strlen($rawQuery) > 0) {
                            $rawQuery.=" AND td_fd_form_received.investor_id='".$investor_code."'";
                        }else {
                            $rawQuery.=" td_fd_form_received.investor_id='".$investor_code."'";
                        }
                    }
                    if (!empty($fd_bu_type)) {
                        $fd_bu_type_string= implode(',', $fd_bu_type);
                        if (strlen($rawQuery) > 0) {
                            $rawQuery.=" AND td_fd_form_received.fd_bu_type IN (".$fd_bu_type_string.")";
                        }else {
                            $rawQuery.=" td_fd_form_received.fd_bu_type IN (".$fd_bu_type_string.")";
                        }
                    }
                    if ($recv_from) {
                        if (strlen($rawQuery) > 0) {
                            $rawQuery.=" AND td_fd_form_received.recv_from LIKE '%".$recv_from."%'";
                        }else {
                            $rawQuery.=" td_fd_form_received.recv_from LIKE '%".$recv_from."%'";
                        }
                    }
                    $data=FDFormReceived::leftJoin('md_client','md_client.id','=','td_fd_form_received.investor_id')
                        ->leftJoin('md_fd_company','md_fd_company.id','=','td_fd_form_received.comp_id')
                        ->leftJoin('md_fd_scheme','md_fd_scheme.id','=','td_fd_form_received.scheme_id')
                        ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_fd_form_received.sub_brk_cd')
                        ->leftJoin('md_employee','md_employee.euin_no','=','td_fd_form_received.euin_no')
                        ->leftJoin('md_branch','md_branch.id','=','td_fd_form_received.branch_code')
                        ->select('td_fd_form_received.*','td_fd_form_received.rec_datetime as entry_date','md_client.client_name as investor_name','md_client.client_code as investor_code','md_client.dob as dob','md_client.pan as pan',
                        'md_fd_company.comp_short_name as comp_short_name','md_fd_company.comp_full_name as comp_full_name','md_fd_scheme.scheme_name as scheme_name','md_sub_broker.bro_name as broker_name',
                        'md_employee.emp_name as emp_name','md_branch.brn_name as branch_name')
                        ->where('td_fd_form_received.deleted_flag','N')
                        ->whereRaw($rawQuery)
                        ->orderByRaw($rawOrderBy)
                        ->paginate($paginate);
                }else {
                    $data=FDFormReceived::leftJoin('md_client','md_client.id','=','td_fd_form_received.investor_id')
                        ->leftJoin('md_fd_company','md_fd_company.id','=','td_fd_form_received.comp_id')
                        ->leftJoin('md_fd_scheme','md_fd_scheme.id','=','td_fd_form_received.scheme_id')
                        ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_fd_form_received.sub_brk_cd')
                        ->leftJoin('md_employee','md_employee.euin_no','=','td_fd_form_received.euin_no')
                        ->leftJoin('md_branch','md_branch.id','=','td_fd_form_received.branch_code')
                        ->select('td_fd_form_received.*','td_fd_form_received.rec_datetime as entry_date','md_client.client_name as investor_name','md_client.client_code as investor_code','md_client.dob as dob','md_client.pan as pan',
                        'md_fd_company.comp_short_name as comp_short_name','md_fd_company.comp_full_name as comp_full_name','md_fd_scheme.scheme_name as scheme_name','md_sub_broker.bro_name as broker_name',
                        'md_employee.emp_name as emp_name','md_branch.brn_name as branch_name')
                        ->where('td_fd_form_received.deleted_flag','N')
                        ->orderByRaw($rawOrderBy)
                        ->paginate($paginate);
                }
            }elseif (($from_date && $to_date) || $temp_tin_no || $investor_code || $fd_bu_type || $recv_from) {
                $rawQuery='';
                $queryString='td_fd_form_received.rec_datetime';
                $rawQuery.=Helper::FrmToDateRawQuery($from_date,$to_date,$rawQuery,$queryString);
                // return $rawQuery;
                // if ($from_date && $to_date) {
                //     if (strlen($rawQuery) > 0) {
                //         // date(`td_fd_form_received`.`rec_datetime`)
                //         $rawQuery.=" AND date(td_fd_form_received.rec_datetime)"." >= '". $from_date."'";
                //     } else {
                //         $rawQuery.=" date(td_fd_form_received.rec_datetime)"." >= '". $from_date."'";
                //     }
                //     $rawQuery.=" AND date(td_fd_form_received.rec_datetime)"." <= '". $to_date."'";
                // }
                $queryString1='td_fd_form_received.temp_tin_no';
                $rawQuery.=Helper::WhereRawQuery($temp_tin_no,$rawQuery,$queryString1);

                // if ($temp_tin_no) {
                //     if (strlen($rawQuery) > 0) {
                //         $rawQuery.=" AND td_fd_form_received.temp_tin_no='".$temp_tin_no."'";
                //     }else {
                //         $rawQuery.=" td_fd_form_received.temp_tin_no='".$temp_tin_no."'";
                //     }
                // }
                if ($investor_code) {
                    if (strlen($rawQuery) > 0) {
                        $rawQuery.=" AND td_fd_form_received.investor_id='".$investor_code."'";
                    }else {
                        $rawQuery.=" td_fd_form_received.investor_id='".$investor_code."'";
                    }
                }
                if (!empty($fd_bu_type)) {
                    $fd_bu_type_string= implode(',', $fd_bu_type);
                    if (strlen($rawQuery) > 0) {
                        $rawQuery.=" AND td_fd_form_received.fd_bu_type IN (".$fd_bu_type_string.")";
                    }else {
                        $rawQuery.=" td_fd_form_received.fd_bu_type IN (".$fd_bu_type_string.")";
                    }
                }
                if ($recv_from) {
                    if (strlen($rawQuery) > 0) {
                        $rawQuery.=" AND td_fd_form_received.recv_from LIKE '%".$recv_from."%'";
                    }else {
                        $rawQuery.=" td_fd_form_received.recv_from LIKE '%".$recv_from."%'";
                    }
                }
                // DB::enableQueryLog();
                $data=FDFormReceived::leftJoin('md_client','md_client.id','=','td_fd_form_received.investor_id')
                    ->leftJoin('md_fd_company','md_fd_company.id','=','td_fd_form_received.comp_id')
                    ->leftJoin('md_fd_scheme','md_fd_scheme.id','=','td_fd_form_received.scheme_id')
                    ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_fd_form_received.sub_brk_cd')
                    ->leftJoin('md_employee','md_employee.euin_no','=','td_fd_form_received.euin_no')
                    ->leftJoin('md_branch','md_branch.id','=','td_fd_form_received.branch_code')
                    ->select('td_fd_form_received.*','td_fd_form_received.rec_datetime as entry_date','md_client.client_name as investor_name','md_client.client_code as investor_code','md_client.dob as dob','md_client.pan as pan',
                    'md_fd_company.comp_short_name as comp_short_name','md_fd_company.comp_full_name as comp_full_name','md_fd_scheme.scheme_name as scheme_name','md_sub_broker.bro_name as broker_name',
                    'md_employee.emp_name as emp_name','md_branch.brn_name as branch_name')
                    ->where('td_fd_form_received.deleted_flag','N')
                    ->whereRaw($rawQuery)
                    ->paginate($paginate);
                // dd(DB::getQueryLog());
                // return DB::getQueryLog($data);
            }else {
                // DB::enableQueryLog();
                $data=FDFormReceived::leftJoin('md_client','md_client.id','=','td_fd_form_received.investor_id')
                    ->leftJoin('md_fd_company','md_fd_company.id','=','td_fd_form_received.comp_id')
                    ->leftJoin('md_fd_scheme','md_fd_scheme.id','=','td_fd_form_received.scheme_id')
                    ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_fd_form_received.sub_brk_cd')
                    ->leftJoin('md_employee','md_employee.euin_no','=','td_fd_form_received.euin_no')
                    ->leftJoin('md_branch','md_branch.id','=','td_fd_form_received.branch_code')
                    ->select('td_fd_form_received.*','td_fd_form_received.rec_datetime as entry_date','md_client.client_name as investor_name','md_client.client_code as investor_code','md_client.dob as dob','md_client.pan as pan',
                    'md_fd_company.comp_short_name as comp_short_name','md_fd_company.comp_full_name as comp_full_name','md_fd_scheme.scheme_name as scheme_name','md_sub_broker.bro_name as broker_name',
                    'md_employee.emp_name as emp_name','md_branch.brn_name as branch_name')
                    ->where('td_fd_form_received.deleted_flag','N')
                    // ->whereDate('td_fd_form_received.rec_datetime',date('Y-m-d'))
                    ->orderBy('td_fd_form_received.rec_datetime','desc')
                    ->paginate($paginate);
                
                // dd(DB::getQueryLog());
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
            // return $request;
            $paginate=$request->paginate;
            $field=$request->field;
            $order=$request->order;

            $from_date=$request->from_date;
            $to_date=$request->to_date;
            $investor_code=$request->investor_code;
            $temp_tin_no=$request->temp_tin_no;
            $fd_bu_type=json_decode($request->fd_bu_type);
            $recv_from=$request->recv_from;
            $euin_no=json_decode($request->euin_no);
            $brn_cd=json_decode($request->brn_cd);
            $bu_type=json_decode($request->bu_type);
            $rm_id=json_decode($request->rm_id);
            $sub_brk_cd=json_decode($request->sub_brk_cd);

            if ($order && $field) {
                $rawOrderBy='';
                if ($order > 0) {
                    $rawOrderBy=$field.' ASC';
                } else {
                    $rawOrderBy=$field.' DESC';
                }
                if (($from_date && $to_date) || $temp_tin_no || $investor_code || $fd_bu_type || $recv_from) {
                    $rawQuery='';
                    if ($from_date && $to_date) {
                        if (strlen($rawQuery) > 0) {
                            $rawQuery.=' AND td_fd_form_received.rec_datetime'.' >= '. $from_date;
                        } else {
                            $rawQuery.=' td_fd_form_received.rec_datetime'.' >= '. $from_date;
                        }
                        $rawQuery.=' AND td_fd_form_received.rec_datetime'.' <= '. $to_date;
                    }
                    if ($temp_tin_no) {
                        if (strlen($rawQuery) > 0) {
                            $rawQuery.=" AND td_fd_form_received.temp_tin_no='".$temp_tin_no."'";
                        }else {
                            $rawQuery.=" td_fd_form_received.temp_tin_no='".$temp_tin_no."'";
                        }
                    }
                    if ($investor_code) {
                        if (strlen($rawQuery) > 0) {
                            $rawQuery.=" AND td_fd_form_received.investor_id='".$investor_code."'";
                        }else {
                            $rawQuery.=" td_fd_form_received.investor_id='".$investor_code."'";
                        }
                    }
                    if (!empty($fd_bu_type)) {
                        $fd_bu_type_string= implode(',', $fd_bu_type);
                        if (strlen($rawQuery) > 0) {
                            $rawQuery.=" AND td_fd_form_received.fd_bu_type IN (".$fd_bu_type_string.")";
                        }else {
                            $rawQuery.=" td_fd_form_received.fd_bu_type IN (".$fd_bu_type_string.")";
                        }
                    }
                    if ($recv_from) {
                        if (strlen($rawQuery) > 0) {
                            $rawQuery.=" AND td_fd_form_received.recv_from LIKE '%".$recv_from."%'";
                        }else {
                            $rawQuery.=" td_fd_form_received.recv_from LIKE '%".$recv_from."%'";
                        }
                    }
                    $data=FDFormReceived::leftJoin('md_client','md_client.id','=','td_fd_form_received.investor_id')
                        ->leftJoin('md_fd_company','md_fd_company.id','=','td_fd_form_received.comp_id')
                        ->leftJoin('md_fd_scheme','md_fd_scheme.id','=','td_fd_form_received.scheme_id')
                        ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_fd_form_received.sub_brk_cd')
                        ->leftJoin('md_employee','md_employee.euin_no','=','td_fd_form_received.euin_no')
                        ->leftJoin('md_branch','md_branch.id','=','td_fd_form_received.branch_code')
                        ->select('td_fd_form_received.*','td_fd_form_received.rec_datetime as entry_date','md_client.client_name as investor_name','md_client.client_code as investor_code','md_client.dob as dob','md_client.pan as pan',
                        'md_fd_company.comp_short_name as comp_short_name','md_fd_company.comp_full_name as comp_full_name','md_fd_scheme.scheme_name as scheme_name','md_sub_broker.bro_name as broker_name',
                        'md_employee.emp_name as emp_name','md_branch.brn_name as branch_name')
                        ->where('td_fd_form_received.deleted_flag','N')
                        ->whereRaw($rawQuery)
                        ->orderByRaw($rawOrderBy)
                        ->get();
                }else {
                    $data=FDFormReceived::leftJoin('md_client','md_client.id','=','td_fd_form_received.investor_id')
                        ->leftJoin('md_fd_company','md_fd_company.id','=','td_fd_form_received.comp_id')
                        ->leftJoin('md_fd_scheme','md_fd_scheme.id','=','td_fd_form_received.scheme_id')
                        ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_fd_form_received.sub_brk_cd')
                        ->leftJoin('md_employee','md_employee.euin_no','=','td_fd_form_received.euin_no')
                        ->leftJoin('md_branch','md_branch.id','=','td_fd_form_received.branch_code')
                        ->select('td_fd_form_received.*','td_fd_form_received.rec_datetime as entry_date','md_client.client_name as investor_name','md_client.client_code as investor_code','md_client.dob as dob','md_client.pan as pan',
                        'md_fd_company.comp_short_name as comp_short_name','md_fd_company.comp_full_name as comp_full_name','md_fd_scheme.scheme_name as scheme_name','md_sub_broker.bro_name as broker_name',
                        'md_employee.emp_name as emp_name','md_branch.brn_name as branch_name')
                        ->where('td_fd_form_received.deleted_flag','N')
                        ->orderByRaw($rawOrderBy)
                        ->orderBy('td_fd_form_received.rec_datetime','desc')
                        ->get();
                }
            }elseif (($from_date && $to_date) || $temp_tin_no || $investor_code || $fd_bu_type || $recv_from) {
                $rawQuery='';
                if ($from_date && $to_date) {
                    if (strlen($rawQuery) > 0) {
                        $rawQuery.=' AND td_fd_form_received.rec_datetime'.' >= '. $from_date;
                    } else {
                        $rawQuery.=' td_fd_form_received.rec_datetime'.' >= '. $from_date;
                    }
                    $rawQuery.=' AND td_fd_form_received.rec_datetime'.' <= '. $to_date;
                }
                if ($temp_tin_no) {
                    if (strlen($rawQuery) > 0) {
                        $rawQuery.=" AND td_fd_form_received.temp_tin_no='".$temp_tin_no."'";
                    }else {
                        $rawQuery.=" td_fd_form_received.temp_tin_no='".$temp_tin_no."'";
                    }
                }
                if ($investor_code) {
                    if (strlen($rawQuery) > 0) {
                        $rawQuery.=" AND td_fd_form_received.investor_id='".$investor_code."'";
                    }else {
                        $rawQuery.=" td_fd_form_received.investor_id='".$investor_code."'";
                    }
                }
                if (!empty($fd_bu_type)) {
                    $fd_bu_type_string= implode(',', $fd_bu_type);
                    if (strlen($rawQuery) > 0) {
                        $rawQuery.=" AND td_fd_form_received.fd_bu_type IN (".$fd_bu_type_string.")";
                    }else {
                        $rawQuery.=" td_fd_form_received.fd_bu_type IN (".$fd_bu_type_string.")";
                    }
                }
                if ($recv_from) {
                    if (strlen($rawQuery) > 0) {
                        $rawQuery.=" AND td_fd_form_received.recv_from LIKE '%".$recv_from."%'";
                    }else {
                        $rawQuery.=" td_fd_form_received.recv_from LIKE '%".$recv_from."%'";
                    }
                }
                $data=FDFormReceived::leftJoin('md_client','md_client.id','=','td_fd_form_received.investor_id')
                    ->leftJoin('md_fd_company','md_fd_company.id','=','td_fd_form_received.comp_id')
                    ->leftJoin('md_fd_scheme','md_fd_scheme.id','=','td_fd_form_received.scheme_id')
                    ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_fd_form_received.sub_brk_cd')
                    ->leftJoin('md_employee','md_employee.euin_no','=','td_fd_form_received.euin_no')
                    ->leftJoin('md_branch','md_branch.id','=','td_fd_form_received.branch_code')
                    ->select('td_fd_form_received.*','td_fd_form_received.rec_datetime as entry_date','md_client.client_name as investor_name','md_client.client_code as investor_code','md_client.dob as dob','md_client.pan as pan',
                    'md_fd_company.comp_short_name as comp_short_name','md_fd_company.comp_full_name as comp_full_name','md_fd_scheme.scheme_name as scheme_name','md_sub_broker.bro_name as broker_name',
                    'md_employee.emp_name as emp_name','md_branch.brn_name as branch_name')
                    ->where('td_fd_form_received.deleted_flag','N')
                    ->whereRaw($rawQuery)
                    ->get();
            }else {
                $data=FDFormReceived::leftJoin('md_client','md_client.id','=','td_fd_form_received.investor_id')
                    ->leftJoin('md_fd_company','md_fd_company.id','=','td_fd_form_received.comp_id')
                    ->leftJoin('md_fd_scheme','md_fd_scheme.id','=','td_fd_form_received.scheme_id')
                    ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_fd_form_received.sub_brk_cd')
                    ->leftJoin('md_employee','md_employee.euin_no','=','td_fd_form_received.euin_no')
                    ->leftJoin('md_branch','md_branch.id','=','td_fd_form_received.branch_code')
                    ->select('td_fd_form_received.*','td_fd_form_received.rec_datetime as entry_date','md_client.client_name as investor_name','md_client.client_code as investor_code','md_client.dob as dob','md_client.pan as pan',
                    'md_fd_company.comp_short_name as comp_short_name','md_fd_company.comp_full_name as comp_full_name','md_fd_scheme.scheme_name as scheme_name','md_sub_broker.bro_name as broker_name',
                    'md_employee.emp_name as emp_name','md_branch.brn_name as branch_name')
                    ->where('td_fd_form_received.deleted_flag','N')
                    // ->whereDate('td_fd_form_received.rec_datetime',date('Y-m-d'))
                    ->orderBy('td_fd_form_received.rec_datetime','desc')
                    ->get();
            }
        } catch (\Throwable $th) {
            // throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function show(Request $request)
    {
        try {
            $temp_tin_no=$request->temp_tin_no;
            $flag=$request->flag;

            if ($temp_tin_no && $flag=='C') {
                // return 'Hii';
                $data=FDFormReceived::leftJoin('md_client','md_client.id','=','td_fd_form_received.investor_id')
                    ->leftJoin('md_fd_company','md_fd_company.id','=','td_fd_form_received.comp_id')
                    ->leftJoin('md_fd_scheme','md_fd_scheme.id','=','td_fd_form_received.scheme_id')
                    ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_fd_form_received.sub_brk_cd')
                    ->leftJoin('md_employee','md_employee.euin_no','=','td_fd_form_received.euin_no')
                    ->select('td_fd_form_received.*','md_client.client_name as investor_name','md_client.client_code as investor_code','md_client.dob as dob','md_client.pan as pan',
                    'md_fd_company.comp_short_name as comp_short_name','md_fd_company.comp_full_name as comp_full_name','md_fd_scheme.scheme_name as scheme_name','md_fd_scheme.comp_type_id as comp_type_id',
                    'md_sub_broker.bro_name as broker_name',
                    'md_employee.emp_name as emp_name')
                    ->where('td_fd_form_received.deleted_flag','N')
                    ->where('td_fd_form_received.temp_tin_no',$temp_tin_no)
                    ->get();
                // return $data;
                if (count($data)>0) {
                    $data1=FixedDeposit::where('delete_flag','N')
                        ->where('temp_tin_no', $temp_tin_no)
                        ->get();
                        // return $data1;
                    if (count($data1)>0) {
                        $data=[];
                        return Helper::SuccessResponse($data);
                    }
                }   
            }elseif ($temp_tin_no) {
                $data=FDFormReceived::leftJoin('md_client','md_client.id','=','td_fd_form_received.investor_id')
                    ->leftJoin('md_fd_company','md_fd_company.id','=','td_fd_form_received.comp_id')
                    ->leftJoin('md_fd_scheme','md_fd_scheme.id','=','td_fd_form_received.scheme_id')
                    ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_fd_form_received.sub_brk_cd')
                    ->leftJoin('md_employee','md_employee.euin_no','=','td_fd_form_received.euin_no')
                    ->select('td_fd_form_received.*','md_client.client_name as investor_name','md_client.client_code as investor_code','md_client.dob as dob','md_client.pan as pan',
                    'md_fd_company.comp_short_name as comp_short_name','md_fd_company.comp_full_name as comp_full_name','md_fd_scheme.scheme_name as scheme_name','md_fd_scheme.comp_type_id as comp_type_id',
                    'md_sub_broker.bro_name as broker_name',
                    'md_employee.emp_name as emp_name')
                    ->where('td_fd_form_received.deleted_flag','N')
                    ->where('td_fd_form_received.temp_tin_no','like', '%' . $temp_tin_no . '%')
                    ->get();
            }else {
                $data=FDFormReceived::leftJoin('md_client','md_client.id','=','td_fd_form_received.investor_id')
                    ->leftJoin('md_fd_company','md_fd_company.id','=','td_fd_form_received.comp_id')
                    ->leftJoin('md_fd_scheme','md_fd_scheme.id','=','td_fd_form_received.scheme_id')
                    ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_fd_form_received.sub_brk_cd')
                    ->leftJoin('md_employee','md_employee.euin_no','=','td_fd_form_received.euin_no')
                    ->select('td_fd_form_received.*','md_client.client_name as investor_name','md_client.client_code as investor_code','md_client.dob as dob','md_client.pan as pan',
                    'md_fd_company.comp_short_name as comp_short_name','md_fd_company.comp_full_name as comp_full_name','md_fd_scheme.scheme_name as scheme_name','md_sub_broker.bro_name as broker_name',
                    'md_employee.emp_name as emp_name')
                    ->where('td_fd_form_received.deleted_flag','N')
                    ->get();
            }
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function create(Request $request)
    {
        $validator = Validator::make(request()->all(),[
            'euin_no' =>'required',
            'bu_type' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            // return $request;
            $is_has=FDFormReceived::orderBy('created_at','desc')->get();
            if (count($is_has)>0) {
                $temp_tin_no=Helper::TempTINGen((count($is_has)+1),4); // generate temp tin no
            }else{
                $temp_tin_no=Helper::TempTINGen(1,4); // generate temp tin no
            }
            
                // $bu_type='D';
                $arn_no=Helper::CommonParamValue(1);
                // $euin_to=Helper::CommonParamValue(2);
                $branch_code=1;
                $data=FDFormReceived::create(array(
                    'rec_datetime'=>date('Y-m-d H:i:s'),
                    'temp_tin_no'=>$temp_tin_no,
                    'bu_type'=>$request->bu_type,
                    'arn_no'=>$arn_no,
                    'euin_no'=>$request->euin_no,
                    'sub_arn_no'=>isset($request->sub_arn_no)?$request->sub_arn_no:NULL,
                    'sub_brk_cd'=>isset($request->sub_brk_cd)?$request->sub_brk_cd:NULL,
                    'investor_id'=>$request->investor_id,
                    'fd_bu_type'=>$request->fd_bu_type,
                    'comp_id'=>$request->company_id,
                    'scheme_id'=>$request->scheme_id,
                    'recv_from'=>$request->recv_from,
                    'branch_code'=>$branch_code,
                    // 'created_by'=>'',
                ));      
                $data=FDFormReceived::leftJoin('md_client','md_client.id','=','td_fd_form_received.investor_id')
                    ->leftJoin('md_fd_company','md_fd_company.id','=','td_fd_form_received.comp_id')
                    ->leftJoin('md_fd_scheme','md_fd_scheme.id','=','td_fd_form_received.scheme_id')
                    ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_fd_form_received.sub_brk_cd')
                    ->leftJoin('md_employee','md_employee.euin_no','=','td_fd_form_received.euin_no')
                    ->leftJoin('md_branch','md_branch.id','=','td_fd_form_received.branch_code')
                    ->select('td_fd_form_received.*','td_fd_form_received.rec_datetime as entry_date','md_client.client_name as investor_name','md_client.client_code as investor_code','md_client.dob as dob','md_client.pan as pan',
                    'md_fd_company.comp_short_name as comp_short_name','md_fd_company.comp_full_name as comp_full_name','md_fd_scheme.scheme_name as scheme_name','md_sub_broker.bro_name as broker_name',
                    'md_employee.emp_name as emp_name','md_branch.brn_name as branch_name')
                    ->where('td_fd_form_received.temp_tin_no',$data->temp_tin_no)
                    ->first(); 
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function update(Request $request)
    {
        $validator = Validator::make(request()->all(),[
            'temp_tin_no' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
    
        try {
            // return $request;
                // $bu_type='D';
                $arn_no=Helper::CommonParamValue(1);
                // $euin_to=Helper::CommonParamValue(2);
                $branch_code=1;
                $da=FDFormReceived::where('temp_tin_no',$request->temp_tin_no)->update([
                    'bu_type'=>$request->bu_type,
                    'arn_no'=>$arn_no,
                    'euin_no'=>$request->euin_no,
                    'sub_arn_no'=>isset($request->sub_arn_no)?$request->sub_arn_no:NULL,
                    'sub_brk_cd'=>isset($request->sub_brk_cd)?$request->sub_brk_cd:NULL,
                    'investor_id'=>$request->investor_id,
                    'fd_bu_type'=>$request->fd_bu_type,
                    'comp_id'=>$request->company_id,
                    'scheme_id'=>$request->scheme_id,
                    'recv_from'=>$request->recv_from,
                    'branch_code'=>$branch_code,
                    // 'created_by'=>'',
                ]);      
                $data=FDFormReceived::leftJoin('md_client','md_client.id','=','td_fd_form_received.investor_id')
                    ->leftJoin('md_fd_company','md_fd_company.id','=','td_fd_form_received.comp_id')
                    ->leftJoin('md_fd_scheme','md_fd_scheme.id','=','td_fd_form_received.scheme_id')
                    ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_fd_form_received.sub_brk_cd')
                    ->leftJoin('md_employee','md_employee.euin_no','=','td_fd_form_received.euin_no')
                    ->leftJoin('md_branch','md_branch.id','=','td_fd_form_received.branch_code')
                    ->select('td_fd_form_received.*','td_fd_form_received.rec_datetime as entry_date','md_client.client_name as investor_name','md_client.client_code as investor_code','md_client.dob as dob','md_client.pan as pan',
                    'md_fd_company.comp_short_name as comp_short_name','md_fd_company.comp_full_name as comp_full_name','md_fd_scheme.scheme_name as scheme_name','md_sub_broker.bro_name as broker_name',
                    'md_employee.emp_name as emp_name','md_branch.brn_name as branch_name')
                    ->where('td_fd_form_received.temp_tin_no',$request->temp_tin_no)
                    ->first(); 
        } catch (\Throwable $th) {
            // throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function delete(Request $request){
        $validator = Validator::make(request()->all(),[
            'id' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            // return $request;
            $data=FixedDeposit::where('temp_tin_no',$request->id)->get();
            if (count($data)>0) {
                $msg='Not delete';
                return Helper::ErrorResponse($msg);
            }else {
                $data=FDFormReceived::where('temp_tin_no',$request->id)->update([
                    'deleted_at'=>date('Y-m-d H:i:s'),
                    'deleted_by'=>1,
                    'deleted_flag'=>'Y',
                ]);
            }
              
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}

