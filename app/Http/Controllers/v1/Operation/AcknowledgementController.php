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
use App\Jobs\AckFinalSubmitJob;

class AcknowledgementController extends Controller
{
    
    public function pending(Request $request)
    {
        try {
            $data=MutualFund::join('md_trans','md_trans.id','=','td_mutual_fund.trans_id')
                ->join('md_trns_type','md_trns_type.id','=','md_trans.trans_type_id')
                ->select('md_trans.trans_type_id as trans_type_id','md_trns_type.trns_type as trns_type_name')
                ->selectRaw('count(td_mutual_fund.id) as total_count')
                ->where('td_mutual_fund.form_status','P')
                ->groupBy('md_trans.trans_type_id')
                ->get();
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
    public function searchDetails(Request $request)
    {
        try {
            // return $request;
            $trans_type_id=$request->trans_type_id;
            $trans_id=$request->trans_id;
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
            $ack_status=json_decode($request->ack_status);
            $client_code=$request->client_code;
            $from_date=$request->from_date;
            $to_date=$request->to_date;

            $brn_cd=json_decode($request->brn_cd);
            $bu_type=json_decode($request->bu_type);
            $rm_id=json_decode($request->rm_id);
            $sub_brk_cd=json_decode($request->sub_brk_cd);
            $euin_no=json_decode($request->euin_no);

            $rawQuery='';
            if (($from_date && $to_date) || $tin_no || $client_code || !empty($amc_name) || !empty($scheme_name) || !empty($rnt_name) 
                || !empty($ack_status) || !empty($brn_cd) || !empty($bu_type) || !empty($rm_id) || !empty($sub_brk_cd) || !empty($euin_no)) {
                if ($from_date && $to_date) {
                    if (strlen($rawQuery) > 0) {
                        $rawQuery.=" AND td_mutual_fund.entry_date >= '". date("Y-m-d",strtotime($from_date))."'";
                    } else {
                        $rawQuery.=" td_mutual_fund.entry_date >= '". date("Y-m-d",strtotime($from_date))."'";
                    }
                    $rawQuery.=" AND td_mutual_fund.entry_date <= '". date("Y-m-d",strtotime($to_date))."'";
                }
                $queryString='td_mutual_fund.tin_no';
                $rawQuery.=Helper::WhereRawQuery($tin_no,$rawQuery,$queryString);
                $queryString='td_mutual_fund.first_client_id';
                $rawQuery.=Helper::WhereRawQuery($client_code,$rawQuery,$queryString);
                $queryString='md_scheme.amc_id';
                $rawQuery.=Helper::WhereRawQuery($amc_name,$rawQuery,$queryString);
                $queryString='td_mutual_fund.trans_scheme_from';
                $rawQuery.=Helper::WhereRawQuery($scheme_name,$rawQuery,$queryString);
                $queryString='td_mutual_fund.rnt_login_at';
                $rawQuery.=Helper::WhereRawQuery($rnt_name,$rawQuery,$queryString);
                $queryString='td_mutual_fund.ack_status';
                $rawQuery.=Helper::WhereRawQuery($ack_status,$rawQuery,$queryString);
                // return $request;
                if (!empty($brn_cd)) {
                    $row_name_string=  "'" .implode("','", $brn_cd). "'";
                    $queryString='md_employee.branch_id';
                    $condition=(strlen($rawQuery) > 0)? " AND ":" ";
                    $rawQuery.=$condition.$queryString." IN (".$row_name_string.")";
                }
                if (!empty($bu_type)) {
                    $row_name_string=  "'" .implode("','", $bu_type). "'";
                    $queryString='(select bu_code from md_business_type where bu_code=md_employee.bu_type_id and branch_id=md_employee.branch_id limit 1)';
                    $condition=(strlen($rawQuery) > 0)? " AND ":" ";
                    $rawQuery.=$condition.$queryString." IN (".$row_name_string.")";
                }
                if (!empty($rm_id)) {
                    $row_name_string=  "'" .implode("','", $rm_id). "'";
                    $queryString="md_employee.euin_no";
                    $condition=(strlen($rawQuery) > 0)? " AND ":" ";
                    $rawQuery.=$condition.$queryString." IN (".$row_name_string.")";
                }
                if (!empty($sub_brk_cd)) {
                    // some logicadd for sub broker checking 
                    // $row_name_string=  "'" .implode("','", $euin_no). "'";
                    // $queryString="(select bu_code from md_sub_broker where bu_code=md_employee.bu_type_id and branch_id=md_employee.branch_id limit 1)";
                    // $condition=(strlen($rawQuery) > 0)? " AND ":" ";
                    // $rawQuery.=$condition.$queryString." IN (".$row_name_string.")";

                    if (!empty($euin_no)) {
                        $row_name_string=  "'" .implode("','", $euin_no). "'";
                        $queryString="md_employee.euin_no";
                        $condition=(strlen($rawQuery) > 0)? " AND ":" ";
                        $rawQuery.=$condition.$queryString." IN (".$row_name_string.")";
                    }
                }
                if (!empty($euin_no)) {
                    $row_name_string=  "'" .implode("','", $euin_no). "'";
                    $queryString="md_employee.euin_no";
                    $condition=(strlen($rawQuery) > 0)? " AND ":" ";
                    $rawQuery.=$condition.$queryString." IN (".$row_name_string.")";
                }
                // return $rawQuery;
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
                    ->leftJoin('md_deposit_bank as ex_md_deposit_bank','ex_md_deposit_bank.id','=','td_mutual_fund.existing_acc_bank_id')
                    ->leftJoin('md_deposit_bank as md_deposit_bank_new','md_deposit_bank_new.id','=','td_mutual_fund.acc_bank_id')
                    ->leftJoin('md_employee','md_employee.euin_no','=','td_form_received.euin_no')
                    ->leftJoin('md_branch','md_branch.id','=','td_form_received.branch_code')
                    ->leftJoin('md_sip_type','md_sip_type.id','=','td_mutual_fund.sip_type')
                    ->leftJoin('md_stp_type','md_stp_type.id','=','td_mutual_fund.stp_type')
                    ->leftJoin('md_swp_type','md_swp_type.id','=','td_mutual_fund.swp_type')
                    ->select('td_mutual_fund.*','md_trans.trns_name as trans_name','md_trans.trans_type_id as trans_type_id','td_form_received.application_no as application_no',
                        'td_form_received.bu_type as bu_type','td_form_received.inv_type as inv_type','md_scheme.scheme_name as scheme_name','md_scheme.id as scheme_id','md_scheme_2.scheme_name as scheme_name_to','md_scheme_2.id as scheme_id_to',
                        'md_client.client_code as first_client_code','md_client.client_name as first_client_name',
                        'td_mutual_fund.kyc_status as first_client_kyc_status',
                        'md_client.pan as first_client_pan','md_client.client_type as first_client_type',
                        'md_client.country_id as first_client_country_id',
                        'md_client.state as first_client_state_id',
                        'md_client.dist as first_client_district_id',
                        'md_client.client_type_mode as change_status_id',
                        'md_client.city as first_client_city_id',
                        'md_client.mobile as first_client_mob',
                        'md_client.email as first_client_email',
                        'md_client.pincode as first_client_pincode',
                        'md_client.add_line_1 as first_client_add_line_1','md_client.add_line_2 as first_client_add_line_2',
                        'md_client_2.client_code as second_client_code','md_client_2.client_name as second_client_name','md_client_2.pan as second_client_pan','md_client_2.client_type as second_client_type',
                        'md_client_3.client_code as third_client_code','md_client_3.client_name as third_client_name','md_client_3.pan as third_client_pan','md_client_3.client_type as third_client_type',
                        'md_plan.plan_name as plan_name','md_option.opt_name as opt_name','md_plan_2.plan_name as plan_name_to','md_option_2.opt_name as opt_name_to',
                        'md_rnt.rnt_name as rnt_name','td_form_received.arn_no as arn_no','td_form_received.euin_no as euin_no','md_branch.brn_name as branch_name',
                        'md_deposit_bank.bank_name as bank_name','md_deposit_bank.ifs_code as ifs_code','md_deposit_bank.micr_code as micr_code','md_deposit_bank.branch_name as chq_branch_name','md_deposit_bank.branch_addr as chq_branch_addr',
                        'ex_md_deposit_bank.bank_name as existing_bank_name','ex_md_deposit_bank.ifs_code as existing_ifsc','ex_md_deposit_bank.micr_code as existing_micr_code','ex_md_deposit_bank.branch_name as existing_branch_name',
                        'md_deposit_bank_new.bank_name as new_bank_name','md_deposit_bank_new.ifs_code as new_ifsc','md_deposit_bank_new.micr_code as new_micr_code','md_deposit_bank_new.branch_name as new_branch_name','td_mutual_fund.acc_no as new_bank_acc_no',
                        'md_branch.brn_name as branch_name','td_form_received.application_no as application_no','md_employee.emp_name as rm_name',
                        'md_sip_type.sip_type_name as sip_type_name','md_stp_type.stp_type_name as stp_type_name','md_swp_type.swp_type_name as swp_type_name',
                        'td_mutual_fund.sip_swp_stp_inst_date as sip_date','td_mutual_fund.sip_swp_stp_start_date as sip_start_date','td_mutual_fund.sip_swp_stp_end_date as sip_end_date','td_mutual_fund.amount as sip_amount',
                        'td_mutual_fund.sip_swp_stp_frequency as swp_frequency','td_mutual_fund.sip_swp_stp_inst_date as swp_date','td_mutual_fund.sip_swp_stp_start_date as swp_start_date','td_mutual_fund.sip_swp_stp_end_date as swp_end_date','td_mutual_fund.amount as swp_amount',
                        'td_mutual_fund.sip_swp_stp_frequency as stp_frequency','td_mutual_fund.sip_swp_stp_inst_date as stp_date','td_mutual_fund.sip_swp_stp_start_date as stp_start_date','td_mutual_fund.sip_swp_stp_end_date as stp_end_date','td_mutual_fund.amount as stp_amount')
                    ->where('md_trans.trans_type_id',$trans_type_id)
                    ->where('td_mutual_fund.trans_id',$trans_id)
                    ->whereRaw($rawQuery)
                    // ->whereDate('td_mutual_fund.entry_date',date('Y-m-d'))
                    ->get();
            }else{
                // return $request;
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
                    ->leftJoin('md_deposit_bank as ex_md_deposit_bank','ex_md_deposit_bank.id','=','td_mutual_fund.existing_acc_bank_id')
                    ->leftJoin('md_deposit_bank as md_deposit_bank_new','md_deposit_bank_new.id','=','td_mutual_fund.acc_bank_id')
                    ->leftJoin('md_employee','md_employee.euin_no','=','td_form_received.euin_no')
                    ->leftJoin('md_branch','md_branch.id','=','td_form_received.branch_code')
                    ->leftJoin('md_sip_type','md_sip_type.id','=','td_mutual_fund.sip_type')
                    ->leftJoin('md_stp_type','md_stp_type.id','=','td_mutual_fund.stp_type')
                    ->leftJoin('md_swp_type','md_swp_type.id','=','td_mutual_fund.swp_type')
                    ->select('td_mutual_fund.*','md_trans.trns_name as trans_name','md_trans.trans_type_id as trans_type_id','td_form_received.application_no as application_no',
                        'td_form_received.bu_type as bu_type','td_form_received.inv_type as inv_type','md_scheme.scheme_name as scheme_name','md_scheme.id as scheme_id','md_scheme_2.scheme_name as scheme_name_to','md_scheme_2.id as scheme_id_to',
                        'md_client.client_code as first_client_code','md_client.client_name as first_client_name',
                        'td_mutual_fund.kyc_status as first_client_kyc_status',
                        'md_client.pan as first_client_pan','md_client.client_type as first_client_type',
                        'md_client.country_id as first_client_country_id',
                        'md_client.state as first_client_state_id',
                        'md_client.dist as first_client_district_id',
                        'md_client.client_type_mode as change_status_id',
                        'md_client.city as first_client_city_id',
                        'md_client.mobile as first_client_mob',
                        'md_client.email as first_client_email',
                        'md_client.pincode as first_client_pincode',
                        'md_client.add_line_1 as first_client_add_line_1','md_client.add_line_2 as first_client_add_line_2',
                        'md_client_2.client_code as second_client_code','md_client_2.client_name as second_client_name','md_client_2.pan as second_client_pan','md_client_2.client_type as second_client_type',
                        'md_client_3.client_code as third_client_code','md_client_3.client_name as third_client_name','md_client_3.pan as third_client_pan','md_client_3.client_type as third_client_type',
                        'md_plan.plan_name as plan_name','md_option.opt_name as opt_name','md_plan_2.plan_name as plan_name_to','md_option_2.opt_name as opt_name_to',
                        'md_rnt.rnt_name as rnt_name','td_form_received.arn_no as arn_no','td_form_received.euin_no as euin_no','md_branch.brn_name as branch_name',
                        'md_deposit_bank.bank_name as bank_name','md_deposit_bank.ifs_code as ifs_code','md_deposit_bank.micr_code as micr_code','md_deposit_bank.branch_name as chq_branch_name','md_deposit_bank.branch_addr as chq_branch_addr',
                        'ex_md_deposit_bank.bank_name as existing_bank_name','ex_md_deposit_bank.ifs_code as existing_ifsc','ex_md_deposit_bank.micr_code as existing_micr_code','ex_md_deposit_bank.branch_name as existing_branch_name',
                        'md_deposit_bank_new.bank_name as new_bank_name','md_deposit_bank_new.ifs_code as new_ifsc','md_deposit_bank_new.micr_code as new_micr_code','md_deposit_bank_new.branch_name as new_branch_name','td_mutual_fund.acc_no as new_bank_acc_no',
                        'md_branch.brn_name as branch_name','td_form_received.application_no as application_no','md_employee.emp_name as rm_name',
                        'md_sip_type.sip_type_name as sip_type_name','md_stp_type.stp_type_name as stp_type_name','md_swp_type.swp_type_name as swp_type_name',
                        'td_mutual_fund.sip_swp_stp_inst_date as sip_date','td_mutual_fund.sip_swp_stp_start_date as sip_start_date','td_mutual_fund.sip_swp_stp_end_date as sip_end_date','td_mutual_fund.amount as sip_amount',
                        'td_mutual_fund.sip_swp_stp_frequency as swp_frequency','td_mutual_fund.sip_swp_stp_inst_date as swp_date','td_mutual_fund.sip_swp_stp_start_date as swp_start_date','td_mutual_fund.sip_swp_stp_end_date as swp_end_date','td_mutual_fund.amount as swp_amount',
                        'td_mutual_fund.sip_swp_stp_frequency as stp_frequency','td_mutual_fund.sip_swp_stp_inst_date as stp_date','td_mutual_fund.sip_swp_stp_start_date as stp_start_date','td_mutual_fund.sip_swp_stp_end_date as stp_end_date','td_mutual_fund.amount as stp_amount')
                    ->where('md_trans.trans_type_id',$trans_type_id)
                    ->where('td_mutual_fund.trans_id',$trans_id)
                    ->whereDate('td_mutual_fund.entry_date',date('Y-m-d'))
                    // ->where('td_mutual_fund.form_status','=','P')
                    ->get();
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
                        ->leftJoin('md_sip_type','md_sip_type.id','=','td_mutual_fund.sip_type')
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
            if ($request->ack_status=='P') {  // for ack process
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
                
                if (Carbon::parse($request->rnt_login_time)->format('H') < 15) {
                    $rnt_login_cutt_off = Carbon::parse($request->rnt_login_dt)->format('Y-m-d');
                }else {
                    $rnt_login_cutt_off = Carbon::parse($request->rnt_login_dt);
                    $rnt_login_cutt_off->addDays(1);
                    $rnt_login_cutt_off->format("Y-m-d");
                }
                // return $rnt_login_cutt_off;
                // return  Carbon::parse($request->rnt_login_dt)->format('Y-m-d').' '.Carbon::parse($request->rnt_login_time)->format('H:i:s');
                MutualFund::where('tin_no',$request->tin_no)->update(array(
                    'rnt_login_dt'=>Carbon::parse($request->rnt_login_dt)->format('Y-m-d').' '.Carbon::parse($request->rnt_login_time)->format('H:i:s'),
                    'rnt_login_cutt_off'=>Carbon::parse($rnt_login_cutt_off)->format('Y-m-d'),
                    'ack_copy_scan'=>$ack_copy_scan_name,
                    'ack_remarks'=>$request->ack_remarks,
                    'ack_status'=>$request->ack_status,
                    'form_status'=>'A',
                    'updated_by'=>Helper::modifyUser($request->user()),
                ));
            }else {
                MutualFund::where('tin_no',$request->tin_no)->update(array(
                    'ack_remarks'=>$request->ack_remarks,
                    'ack_status'=>$request->ack_status,
                    'form_status'=>($request->ack_status=='R')?'R':'P',
                    'updated_by'=>Helper::modifyUser($request->user()),
                ));
            }
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
            $trans_id=$request->trans_id;
            // return $request;
            $data=MutualFund::join('md_client','md_client.id','=','td_mutual_fund.first_client_id')
                ->select('td_mutual_fund.id','td_mutual_fund.first_client_id','td_mutual_fund.app_form_scan','td_mutual_fund.ack_copy_scan',
                'md_client.client_name as first_client_name','md_client.email as first_client_email','md_client.pan as first_client_pan')
                ->where('td_mutual_fund.trans_id',$trans_id)
                ->whereDate('td_mutual_fund.rnt_login_dt',date('Y-m-d'))
                ->where('td_mutual_fund.form_status','=','A')
                ->where('td_mutual_fund.ack_final_submit','=','N')
                ->get();  
            // return $data;
            if (count($data) > 0) {
                foreach ($data as $key => $item) {
                    // return $item;
                    dispatch(new AckFinalSubmitJob($item));
                }
            }
        } catch (\Throwable $th) {
            throw $th;
            $msg="Email Sending Error.";
            return Helper::ErrorResponse($msg);
        }
        return Helper::SuccessResponse($data);
    }

    public function finalSubmit_old(Request $request)
    {
        try {
            $trans_type_id=$request->trans_type_id;
            $trans_id=$request->trans_id;
            // return $request;
            // $data=MutualFund::join('td_form_received','td_form_received.temp_tin_no','=','td_mutual_fund.temp_tin_no')
            //     ->join('md_trans','md_trans.id','=','td_mutual_fund.trans_id')
            //     ->leftJoin('md_deposit_bank','md_deposit_bank.id','=','td_mutual_fund.chq_bank')
            //     ->select('td_mutual_fund.*','md_trans.trns_name as trans_name','md_trans.trans_type_id as trans_type_id',
            //     'td_form_received.arn_no as arn_no','td_form_received.euin_no as euin_no','md_deposit_bank.bank_name as bank_name')
            //     // ->where('md_trans.trans_type_id',$trans_type_id)
            //     ->where('td_mutual_fund.trans_id',$trans_id)
            //     ->whereDate('td_mutual_fund.rnt_login_dt',date('Y-m-d'))
            //     ->where('td_mutual_fund.form_status','=','A')
            //     ->get();  

            $data=MutualFund::join('md_client','md_client.id','=','td_mutual_fund.first_client_id')
                ->select('td_mutual_fund.first_client_id','td_mutual_fund.app_form_scan','td_mutual_fund.ack_copy_scan',
                'md_client.client_name as first_client_name')
                ->where('td_mutual_fund.trans_id',$trans_id)
                ->whereDate('td_mutual_fund.rnt_login_dt',date('Y-m-d'))
                ->where('td_mutual_fund.form_status','=','A')
                ->get();  
            return $data;
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
            throw $th;
            $msg="Email Sending Error.";
            return Helper::ErrorResponse($msg);
        }
        return Helper::SuccessResponse($data);
    }
}