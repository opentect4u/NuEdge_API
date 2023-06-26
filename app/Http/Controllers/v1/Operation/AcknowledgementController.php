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

class AcknowledgementController extends Controller
{
    public function searchDetails(Request $request)
    {
        try {
            $paginate=$request->paginate;
            $trans_type_id=$request->trans_type_id;
            $start_date=$request->start_date;
            $end_date=$request->end_date;

            $tin_no=$request->tin_no;
            $option=$request->option;
            $login_status=$request->login_status;
            $date_status=$request->date_status;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;

            $amc_name=json_decode($request->amc_name);
            $scheme_name=json_decode($request->scheme_name);
            $rnt_name=json_decode($request->rnt_name);
            $login_status_id=json_decode($request->login_status_id);
            $client_code=$request->client_code;
            
            $order=$request->order;
            $field=$request->field;

            $from_date=$request->from_date;
            $to_date=$request->to_date;
            
            
            if ($paginate=='A') {
                $paginate=999999999;
            }
          
                // return $request;
            if ($order && $field) {
                $rawOrderBy='';
                if ($order > 0) {
                    $rawOrderBy=$field.' ASC';
                } else {
                    $rawOrderBy=$field.' DESC';
                }
                if (($from_date && $to_date) || $tin_no || $client_code || $amc_name || $scheme_name || $rnt_name) {
                    $rawQuery='';
                    if ($from_date && $to_date) {
                        if (strlen($rawQuery) > 0) {
                            $rawQuery.=' AND td_mutual_fund.entry_date'.' >= '. $from_date;
                        } else {
                            $rawQuery.=' td_mutual_fund.entry_date'.' >= '. $from_date;
                        }
                        $rawQuery.=' AND td_mutual_fund.entry_date'.' <= '. $to_date;
                    }
                    if ($tin_no) {
                        if (strlen($rawQuery) > 0) {
                            $rawQuery.=" AND td_mutual_fund.tin_no='".$tin_no."'";
                        }else {
                            $rawQuery.=" td_mutual_fund.tin_no='".$tin_no."'";
                        }
                    }
                    if ($client_code) {
                        if (strlen($rawQuery) > 0) {
                            $rawQuery.=" AND td_mutual_fund.first_client_id='".$client_code."'";
                        }else {
                            $rawQuery.=" td_mutual_fund.first_client_id='".$client_code."'";
                        }
                    }
                    if (!empty($amc_name)) {
                        $amc_name_string= implode(',', $amc_name);
                        if (strlen($rawQuery) > 0) {
                            $rawQuery.=" AND md_scheme.amc_id IN (".$amc_name_string.")";
                        }else {
                            $rawQuery.=" md_scheme.amc_id IN (".$amc_name_string.")";
                        }
                    }
                    if (!empty($scheme_name)) {
                        $scheme_name_string= implode(',', $scheme_name);
                        if (strlen($rawQuery) > 0) {
                            $rawQuery.=" AND td_mutual_fund.trans_scheme_from IN (".$scheme_name_string.")";
                        }else {
                            $rawQuery.=" td_mutual_fund.trans_scheme_from IN (".$scheme_name_string.")";
                        }
                    }

                    if (!empty($rnt_name)) {
                        $rnt_name_string= implode(',', $rnt_name);
                        if (strlen($rawQuery) > 0) {
                            $rawQuery.=" AND td_mutual_fund.trans_scheme_from IN (".$rnt_name_string.")";
                        }else {
                            $rawQuery.=" td_mutual_fund.trans_scheme_from IN (".$rnt_name_string.")";
                        }
                    }
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
                        ->leftJoin('md_deposit_bank','md_deposit_bank.id','=','td_mutual_fund.chq_bank')
                        ->select('td_mutual_fund.*','md_trans.trns_name as trans_name','md_trans.trans_type_id as trans_type_id','md_scheme.scheme_name as scheme_name',
                        'td_form_received.bu_type as bu_type','td_form_received.inv_type as inv_type','md_scheme.scheme_name as scheme_name','md_scheme_2.scheme_name as scheme_name_to',
                        'md_client.client_code as first_client_code','md_client.client_name as first_client_name','md_client.pan as first_client_pan','md_client.client_type as first_client_type',
                        'md_client_2.client_code as second_client_code','md_client_2.client_name as second_client_name','md_client_2.pan as second_client_pan','md_client_2.client_type as second_client_type',
                        'md_plan.plan_name as plan_name','md_option.opt_name as opt_name','md_plan_2.plan_name as plan_name_to','md_option_2.opt_name as opt_name_to',
                        'md_rnt.rnt_name as rnt_name','td_form_received.arn_no as arn_no','td_form_received.euin_no as euin_no','md_deposit_bank.bank_name as bank_name'
                        )
                        ->where('md_trans.trans_type_id',$trans_type_id)
                        ->whereRaw($rawQuery)
                        ->orderByRaw($rawOrderBy)
                        ->paginate($paginate);  
                }else {
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
                        ->leftJoin('md_deposit_bank','md_deposit_bank.id','=','td_mutual_fund.chq_bank')
                        ->select('td_mutual_fund.*','md_trans.trns_name as trans_name','md_trans.trans_type_id as trans_type_id','md_scheme.scheme_name as scheme_name',
                        'td_form_received.bu_type as bu_type','td_form_received.inv_type as inv_type','md_scheme.scheme_name as scheme_name','md_scheme_2.scheme_name as scheme_name_to',
                        'md_client.client_code as first_client_code','md_client.client_name as first_client_name','md_client.pan as first_client_pan','md_client.client_type as first_client_type',
                        'md_client_2.client_code as second_client_code','md_client_2.client_name as second_client_name','md_client_2.pan as second_client_pan','md_client_2.client_type as second_client_type',
                        'md_plan.plan_name as plan_name','md_option.opt_name as opt_name','md_plan_2.plan_name as plan_name_to','md_option_2.opt_name as opt_name_to',
                        'md_rnt.rnt_name as rnt_name','td_form_received.arn_no as arn_no','td_form_received.euin_no as euin_no','md_deposit_bank.bank_name as bank_name'
                        )
                        ->where('md_trans.trans_type_id',$trans_type_id)
                        ->whereDate('td_mutual_fund.entry_date',date('Y-m-d'))
                        ->orderByRaw($rawOrderBy)
                        ->paginate($paginate);   
                }
            } elseif (($from_date && $to_date) || $tin_no || $client_code || $amc_name || $scheme_name || $rnt_name) {
                $rawQuery='';
                if ($from_date && $to_date) {
                    if (strlen($rawQuery) > 0) {
                        $rawQuery.=' AND td_mutual_fund.entry_date'.' >= '. $from_date;
                    } else {
                        $rawQuery.=' td_mutual_fund.entry_date'.' >= '. $from_date;
                    }
                    $rawQuery.=' AND td_mutual_fund.entry_date'.' <= '. $to_date;
                }
                if ($tin_no) {
                    if (strlen($rawQuery) > 0) {
                        $rawQuery.=" AND td_mutual_fund.tin_no='".$tin_no."'";
                    }else {
                        $rawQuery.=" td_mutual_fund.tin_no='".$tin_no."'";
                    }
                }
                if ($client_code) {
                    if (strlen($rawQuery) > 0) {
                        $rawQuery.=" AND td_mutual_fund.first_client_id='".$client_code."'";
                    }else {
                        $rawQuery.=" td_mutual_fund.first_client_id='".$client_code."'";
                    }
                }
                if (!empty($amc_name)) {
                    $amc_name_string= implode(',', $amc_name);
                    if (strlen($rawQuery) > 0) {
                        $rawQuery.=" AND md_scheme.amc_id IN (".$amc_name_string.")";
                    }else {
                        $rawQuery.=" md_scheme.amc_id IN (".$amc_name_string.")";
                    }
                }
                if (!empty($scheme_name)) {
                    $scheme_name_string= implode(',', $scheme_name);
                    if (strlen($rawQuery) > 0) {
                        $rawQuery.=" AND td_mutual_fund.trans_scheme_from IN (".$scheme_name_string.")";
                    }else {
                        $rawQuery.=" td_mutual_fund.trans_scheme_from IN (".$scheme_name_string.")";
                    }
                }

                if (!empty($rnt_name)) {
                    $rnt_name_string= implode(',', $rnt_name);
                    if (strlen($rawQuery) > 0) {
                        $rawQuery.=" AND td_mutual_fund.trans_scheme_from IN (".$rnt_name_string.")";
                    }else {
                        $rawQuery.=" td_mutual_fund.trans_scheme_from IN (".$rnt_name_string.")";
                    }
                }
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
                        ->leftJoin('md_deposit_bank','md_deposit_bank.id','=','td_mutual_fund.chq_bank')
                        ->select('td_mutual_fund.*','md_trans.trns_name as trans_name','md_trans.trans_type_id as trans_type_id','md_scheme.scheme_name as scheme_name',
                        'td_form_received.bu_type as bu_type','td_form_received.inv_type as inv_type','md_scheme.scheme_name as scheme_name','md_scheme_2.scheme_name as scheme_name_to',
                        'md_client.client_code as first_client_code','md_client.client_name as first_client_name','md_client.pan as first_client_pan','md_client.client_type as first_client_type',
                        'md_client_2.client_code as second_client_code','md_client_2.client_name as second_client_name','md_client_2.pan as second_client_pan','md_client_2.client_type as second_client_type',
                        'md_plan.plan_name as plan_name','md_option.opt_name as opt_name','md_plan_2.plan_name as plan_name_to','md_option_2.opt_name as opt_name_to',
                        'md_rnt.rnt_name as rnt_name','td_form_received.arn_no as arn_no','td_form_received.euin_no as euin_no','md_deposit_bank.bank_name as bank_name'
                        )
                        ->where('md_trans.trans_type_id',$trans_type_id)
                        ->whereRaw($rawQuery)
                        // ->whereDate('td_mutual_fund.entry_date',date('Y-m-d'))
                        ->paginate($paginate);  
                
            }else{
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
                        ->leftJoin('md_deposit_bank','md_deposit_bank.id','=','td_mutual_fund.chq_bank')
                        ->select('td_mutual_fund.*','md_trans.trns_name as trans_name','md_trans.trans_type_id as trans_type_id','md_scheme.scheme_name as scheme_name',
                        'td_form_received.bu_type as bu_type','td_form_received.inv_type as inv_type','md_scheme.scheme_name as scheme_name','md_scheme_2.scheme_name as scheme_name_to',
                        'md_client.client_code as first_client_code','md_client.client_name as first_client_name','md_client.pan as first_client_pan','md_client.client_type as first_client_type',
                        'md_client_2.client_code as second_client_code','md_client_2.client_name as second_client_name','md_client_2.pan as second_client_pan','md_client_2.client_type as second_client_type',
                        'md_plan.plan_name as plan_name','md_option.opt_name as opt_name','md_plan_2.plan_name as plan_name_to','md_option_2.opt_name as opt_name_to',
                        'md_rnt.rnt_name as rnt_name','td_form_received.arn_no as arn_no','td_form_received.euin_no as euin_no','md_deposit_bank.bank_name as bank_name'
                        )
                        ->where('md_trans.trans_type_id',$trans_type_id)
                        ->whereDate('td_mutual_fund.entry_date',date('Y-m-d'))
                        // ->where('td_mutual_fund.form_status','=','P')
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
            $trans_type_id=$request->trans_type_id;
            $start_date=$request->start_date;
            $end_date=$request->end_date;

            $tin_no=$request->tin_no;
            $option=$request->option;
            $login_status=$request->login_status;
            $date_status=$request->date_status;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;

            $amc_name=json_decode($request->amc_name);
            $scheme_name=json_decode($request->scheme_name);
            $rnt_name=json_decode($request->rnt_name);
            $login_status_id=json_decode($request->login_status_id);
            $client_code=$request->client_code;
            
            $order=$request->order;
            $field=$request->field;

            $from_date=$request->from_date;
            $to_date=$request->to_date;
            
                // return $request;
            if ($order && $field) {
                $rawOrderBy='';
                if ($order > 0) {
                    $rawOrderBy=$field.' ASC';
                } else {
                    $rawOrderBy=$field.' DESC';
                }
                if (($from_date && $to_date) || $tin_no || $client_code || $amc_name || $scheme_name || $rnt_name) {
                    $rawQuery='';
                    if ($from_date && $to_date) {
                        if (strlen($rawQuery) > 0) {
                            $rawQuery.=' AND td_mutual_fund.entry_date'.' >= '. $from_date;
                        } else {
                            $rawQuery.=' td_mutual_fund.entry_date'.' >= '. $from_date;
                        }
                        $rawQuery.=' AND td_mutual_fund.entry_date'.' <= '. $to_date;
                    }
                    if ($tin_no) {
                        if (strlen($rawQuery) > 0) {
                            $rawQuery.=" AND td_mutual_fund.tin_no='".$tin_no."'";
                        }else {
                            $rawQuery.=" td_mutual_fund.tin_no='".$tin_no."'";
                        }
                    }
                    if ($client_code) {
                        if (strlen($rawQuery) > 0) {
                            $rawQuery.=" AND td_mutual_fund.first_client_id='".$client_code."'";
                        }else {
                            $rawQuery.=" td_mutual_fund.first_client_id='".$client_code."'";
                        }
                    }
                    if (!empty($amc_name)) {
                        $amc_name_string= implode(',', $amc_name);
                        if (strlen($rawQuery) > 0) {
                            $rawQuery.=" AND md_scheme.amc_id IN (".$amc_name_string.")";
                        }else {
                            $rawQuery.=" md_scheme.amc_id IN (".$amc_name_string.")";
                        }
                    }
                    if (!empty($scheme_name)) {
                        $scheme_name_string= implode(',', $scheme_name);
                        if (strlen($rawQuery) > 0) {
                            $rawQuery.=" AND td_mutual_fund.trans_scheme_from IN (".$scheme_name_string.")";
                        }else {
                            $rawQuery.=" td_mutual_fund.trans_scheme_from IN (".$scheme_name_string.")";
                        }
                    }

                    if (!empty($rnt_name)) {
                        $rnt_name_string= implode(',', $rnt_name);
                        if (strlen($rawQuery) > 0) {
                            $rawQuery.=" AND td_mutual_fund.trans_scheme_from IN (".$rnt_name_string.")";
                        }else {
                            $rawQuery.=" td_mutual_fund.trans_scheme_from IN (".$rnt_name_string.")";
                        }
                    }
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
                        ->leftJoin('md_deposit_bank','md_deposit_bank.id','=','td_mutual_fund.chq_bank')
                        ->select('td_mutual_fund.*','md_trans.trns_name as trans_name','md_trans.trans_type_id as trans_type_id','md_scheme.scheme_name as scheme_name',
                        'td_form_received.bu_type as bu_type','td_form_received.inv_type as inv_type','md_scheme.scheme_name as scheme_name','md_scheme_2.scheme_name as scheme_name_to',
                        'md_client.client_code as first_client_code','md_client.client_name as first_client_name','md_client.pan as first_client_pan','md_client.client_type as first_client_type',
                        'md_client_2.client_code as second_client_code','md_client_2.client_name as second_client_name','md_client_2.pan as second_client_pan','md_client_2.client_type as second_client_type',
                        'md_plan.plan_name as plan_name','md_option.opt_name as opt_name','md_plan_2.plan_name as plan_name_to','md_option_2.opt_name as opt_name_to',
                        'md_rnt.rnt_name as rnt_name','td_form_received.arn_no as arn_no','td_form_received.euin_no as euin_no','md_deposit_bank.bank_name as bank_name'
                        )
                        ->where('md_trans.trans_type_id',$trans_type_id)
                        ->whereRaw($rawQuery)
                        ->orderByRaw($rawOrderBy)
                        ->get();  
                }else {
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
                        ->leftJoin('md_deposit_bank','md_deposit_bank.id','=','td_mutual_fund.chq_bank')
                        ->select('td_mutual_fund.*','md_trans.trns_name as trans_name','md_trans.trans_type_id as trans_type_id','md_scheme.scheme_name as scheme_name',
                        'td_form_received.bu_type as bu_type','td_form_received.inv_type as inv_type','md_scheme.scheme_name as scheme_name','md_scheme_2.scheme_name as scheme_name_to',
                        'md_client.client_code as first_client_code','md_client.client_name as first_client_name','md_client.pan as first_client_pan','md_client.client_type as first_client_type',
                        'md_client_2.client_code as second_client_code','md_client_2.client_name as second_client_name','md_client_2.pan as second_client_pan','md_client_2.client_type as second_client_type',
                        'md_plan.plan_name as plan_name','md_option.opt_name as opt_name','md_plan_2.plan_name as plan_name_to','md_option_2.opt_name as opt_name_to',
                        'md_rnt.rnt_name as rnt_name','td_form_received.arn_no as arn_no','td_form_received.euin_no as euin_no','md_deposit_bank.bank_name as bank_name'
                        )
                        ->where('md_trans.trans_type_id',$trans_type_id)
                        ->whereDate('td_mutual_fund.entry_date',date('Y-m-d'))
                        ->orderByRaw($rawOrderBy)
                        ->get();   
                }
            } elseif (($from_date && $to_date) || $tin_no || $client_code || $amc_name || $scheme_name || $rnt_name) {
                $rawQuery='';
                if ($from_date && $to_date) {
                    if (strlen($rawQuery) > 0) {
                        $rawQuery.=' AND td_mutual_fund.entry_date'.' >= '. $from_date;
                    } else {
                        $rawQuery.=' td_mutual_fund.entry_date'.' >= '. $from_date;
                    }
                    $rawQuery.=' AND td_mutual_fund.entry_date'.' <= '. $to_date;
                }
                if ($tin_no) {
                    if (strlen($rawQuery) > 0) {
                        $rawQuery.=" AND td_mutual_fund.tin_no='".$tin_no."'";
                    }else {
                        $rawQuery.=" td_mutual_fund.tin_no='".$tin_no."'";
                    }
                }
                if ($client_code) {
                    if (strlen($rawQuery) > 0) {
                        $rawQuery.=" AND td_mutual_fund.first_client_id='".$client_code."'";
                    }else {
                        $rawQuery.=" td_mutual_fund.first_client_id='".$client_code."'";
                    }
                }
                if (!empty($amc_name)) {
                    $amc_name_string= implode(',', $amc_name);
                    if (strlen($rawQuery) > 0) {
                        $rawQuery.=" AND md_scheme.amc_id IN (".$amc_name_string.")";
                    }else {
                        $rawQuery.=" md_scheme.amc_id IN (".$amc_name_string.")";
                    }
                }
                if (!empty($scheme_name)) {
                    $scheme_name_string= implode(',', $scheme_name);
                    if (strlen($rawQuery) > 0) {
                        $rawQuery.=" AND td_mutual_fund.trans_scheme_from IN (".$scheme_name_string.")";
                    }else {
                        $rawQuery.=" td_mutual_fund.trans_scheme_from IN (".$scheme_name_string.")";
                    }
                }

                if (!empty($rnt_name)) {
                    $rnt_name_string= implode(',', $rnt_name);
                    if (strlen($rawQuery) > 0) {
                        $rawQuery.=" AND td_mutual_fund.trans_scheme_from IN (".$rnt_name_string.")";
                    }else {
                        $rawQuery.=" td_mutual_fund.trans_scheme_from IN (".$rnt_name_string.")";
                    }
                }
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
                        ->leftJoin('md_deposit_bank','md_deposit_bank.id','=','td_mutual_fund.chq_bank')
                        ->select('td_mutual_fund.*','md_trans.trns_name as trans_name','md_trans.trans_type_id as trans_type_id','md_scheme.scheme_name as scheme_name',
                        'td_form_received.bu_type as bu_type','td_form_received.inv_type as inv_type','md_scheme.scheme_name as scheme_name','md_scheme_2.scheme_name as scheme_name_to',
                        'md_client.client_code as first_client_code','md_client.client_name as first_client_name','md_client.pan as first_client_pan','md_client.client_type as first_client_type',
                        'md_client_2.client_code as second_client_code','md_client_2.client_name as second_client_name','md_client_2.pan as second_client_pan','md_client_2.client_type as second_client_type',
                        'md_plan.plan_name as plan_name','md_option.opt_name as opt_name','md_plan_2.plan_name as plan_name_to','md_option_2.opt_name as opt_name_to',
                        'md_rnt.rnt_name as rnt_name','td_form_received.arn_no as arn_no','td_form_received.euin_no as euin_no','md_deposit_bank.bank_name as bank_name'
                        )
                        ->where('md_trans.trans_type_id',$trans_type_id)
                        ->whereRaw($rawQuery)
                        // ->whereDate('td_mutual_fund.entry_date',date('Y-m-d'))
                        ->get();  
                
            }else{
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
                        ->leftJoin('md_deposit_bank','md_deposit_bank.id','=','td_mutual_fund.chq_bank')
                        ->select('td_mutual_fund.*','md_trans.trns_name as trans_name','md_trans.trans_type_id as trans_type_id','md_scheme.scheme_name as scheme_name',
                        'td_form_received.bu_type as bu_type','td_form_received.inv_type as inv_type','md_scheme.scheme_name as scheme_name','md_scheme_2.scheme_name as scheme_name_to',
                        'md_client.client_code as first_client_code','md_client.client_name as first_client_name','md_client.pan as first_client_pan','md_client.client_type as first_client_type',
                        'md_client_2.client_code as second_client_code','md_client_2.client_name as second_client_name','md_client_2.pan as second_client_pan','md_client_2.client_type as second_client_type',
                        'md_plan.plan_name as plan_name','md_option.opt_name as opt_name','md_plan_2.plan_name as plan_name_to','md_option_2.opt_name as opt_name_to',
                        'md_rnt.rnt_name as rnt_name','td_form_received.arn_no as arn_no','td_form_received.euin_no as euin_no','md_deposit_bank.bank_name as bank_name'
                        )
                        ->where('md_trans.trans_type_id',$trans_type_id)
                        ->whereDate('td_mutual_fund.entry_date',date('Y-m-d'))
                        // ->where('td_mutual_fund.form_status','=','P')
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
            $data1=MutualFund::where('tin_no',$request->tin_no)->first();
            $ack_copy_scan=$request->ack_copy_scan;
            if ($ack_copy_scan) {
                $path_extension=$ack_copy_scan->getClientOriginalExtension();
                // $ack_copy_scan_name=microtime(true).".".$path_extension;
                $ack_copy_scan_name="ack_".$request->tin_no.".".$path_extension;
                $ack_copy_scan->move(public_path('acknowledgement-copy/'),$ack_copy_scan_name);
            }else{
                $ack_copy_scan_name=$data1->ack_copy_scan;
                // return $doc_name;
            }
            // ack_copy_scan
                // rnt_login_time

                if (Carbon::parse($request->rnt_login_time)->format('H') < 15) {
                    $rnt_login_cutt_off = Carbon::parse($request->rnt_login_date)->format('Y-m-d');
                }else {
                    $rnt_login_cutt_off = Carbon::parse($request->rnt_login_date);
                    $rnt_login_cutt_off->addDays(1);
                    $rnt_login_cutt_off->format("Y-m-d");
                }
                // return $rnt_login_cutt_off;
                MutualFund::where('tin_no',$request->tin_no)->update(array(
                    'rnt_login_dt'=>Carbon::parse($request->rnt_login_date)->format('Y-m-d').' '.Carbon::parse($request->rnt_login_time)->format('H:i:s'),
                    'rnt_login_cutt_off'=>Carbon::parse($rnt_login_cutt_off)->format('Y-m-d'),
                    // 'rnt_login_cutt_off'=>$rnt_login_cutt_off,
                    'ack_copy_scan'=>$ack_copy_scan_name,
                    'ack_remarks'=>$request->ack_remarks,
                    'form_status'=>'A',
                    // 'updated_at'
                ));   
            // return $data1;
            // return $request->tin_no;
            $data=MutualFund::where('tin_no',$request->tin_no)->first();
            // return $data;
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function finalSubmit(Request $request)
    {
        try {
            $trans_type_id=$request->trans_type_id;
            // return $request;
            $data=MutualFund::join('td_form_received','td_form_received.temp_tin_no','=','td_mutual_fund.temp_tin_no')
                ->join('md_trans','md_trans.id','=','td_mutual_fund.trans_id')
                        ->leftJoin('md_deposit_bank','md_deposit_bank.id','=','td_mutual_fund.chq_bank')
                ->select('td_mutual_fund.*','md_trans.trns_name as trans_name','md_trans.trans_type_id as trans_type_id',
                'td_form_received.arn_no as arn_no','td_form_received.euin_no as euin_no','md_deposit_bank.bank_name as bank_name')
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
                    $filePath=public_path('acknowledgement-copy/'.$value1->ack_copy_scan);
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