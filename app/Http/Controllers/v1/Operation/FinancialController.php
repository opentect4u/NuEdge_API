<?php

namespace App\Http\Controllers\v1\Operation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{MutualFund,Client,FormReceived};
use Validator;
use Illuminate\Support\Carbon;

class FinancialController extends Controller
{
    public function searchDetails(Request $request)
    {
        try {
            $paginate=$request->paginate;
            $trans_type_id=$request->trans_type_id;
            $trans_id=$request->trans_id;
            $tin_no=$request->tin_no;
            $cat_name=$request->cat_name;
            $option=$request->option;
            $login_status=$request->login_status;
            $date_status=$request->date_status;
            $start_date=$request->start_date;
            $end_date=$request->end_date;

            $amc_name=json_decode($request->amc_name);
            $scheme_name=json_decode($request->scheme_name);
            $rnt_name=json_decode($request->rnt_name);
            $client_code=$request->client_code;

            $order=$request->order;
            $field=$request->field;

            $from_date=$request->from_date;
            $to_date=$request->to_date;

            $brn_cd=json_decode($request->brn_cd);
            $bu_type=json_decode($request->bu_type);
            $rm_id=json_decode($request->rm_id);
            $euin_no=json_decode($request->euin_no);
            $sub_brk_cd=json_decode($request->sub_brk_cd);

            if ($paginate=='A') {
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
                        ->leftJoin('md_employee','md_employee.euin_no','=','td_form_received.euin_no')
                        ->leftJoin('md_branch','md_branch.id','=','td_form_received.branch_code')
                        ->select('td_mutual_fund.*','md_trans.trns_name as trans_name','md_trans.trans_type_id as trans_type_id','td_form_received.application_no as application_no',
                        'td_form_received.bu_type as bu_type','td_form_received.inv_type as inv_type','md_scheme.scheme_name as scheme_name','md_scheme.id as scheme_id','md_scheme_2.scheme_name as scheme_name_to','md_scheme_2.id as scheme_id_to',
                        'md_client.client_code as first_client_code',
                        'md_client.mobile as first_client_mob',
                        'md_client.email as first_client_email',
                        'md_client.client_type_mode as change_status_id',
                        'md_client.client_name as first_client_name',
                        'md_client.country_id as first_client_country_id',
                        'md_client.state as first_client_state_id',
                        'md_client.dist as first_client_district_id',
                        'md_client.city as first_client_city_id',
                        'md_client.pincode as first_client_pincode',
                        'md_client.mobile as first_client_mob',
                        'md_client.add_line_1 as first_client_add_line_1',
                        'md_client.add_line_2 as first_client_add_line_2',
                        'md_client.pan as first_client_pan','md_client.client_type as first_client_type',
                        'md_client_2.client_code as second_client_code','md_client_2.client_name as second_client_name','md_client_2.pan as second_client_pan','md_client_2.client_type as second_client_type',
                        'md_plan.plan_name as plan_name','md_option.opt_name as opt_name','md_plan_2.plan_name as plan_name_to','md_option_2.opt_name as opt_name_to',
                        'md_rnt.rnt_name as rnt_name','td_form_received.arn_no as arn_no','td_form_received.euin_no as euin_no','md_branch.brn_name as branch_name',
                        'md_deposit_bank.bank_name as bank_name','md_deposit_bank.ifs_code as ifs_code','md_deposit_bank.micr_code as micr_code','md_deposit_bank.branch_name as chq_branch_name','md_deposit_bank.branch_addr as chq_branch_addr',
                        'md_employee.emp_name as emp_name')
                        ->where('md_trans.trans_type_id',$trans_type_id)
                        ->whereDate('td_mutual_fund.entry_date',date('Y-m-d'))
                        ->where('td_mutual_fund.rnt_login_dt',$login_status,NULL)
                        ->where('td_mutual_fund.rnt_login_cutt_off',$login_status,NULL)
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
                        ->leftJoin('md_employee','md_employee.euin_no','=','td_form_received.euin_no')
                        ->leftJoin('md_branch','md_branch.id','=','td_form_received.branch_code')
                        ->select('td_mutual_fund.*','md_trans.trns_name as trans_name','md_trans.trans_type_id as trans_type_id','td_form_received.application_no as application_no',
                        'td_form_received.bu_type as bu_type','td_form_received.inv_type as inv_type','md_scheme.scheme_name as scheme_name','md_scheme.id as scheme_id','md_scheme_2.scheme_name as scheme_name_to','md_scheme_2.id as scheme_id_to',
                        'md_client.client_code as first_client_code','md_client.client_name as first_client_name','md_client.pan as first_client_pan','md_client.client_type as first_client_type',
                        'md_client_2.client_code as second_client_code',
                        'md_client.country_id as first_client_country_id',
                        'md_client.state as first_client_state_id',
                        'md_client.dist as first_client_district_id',
                        'md_client.client_type_mode as change_status_id',
                        'md_client.city as first_client_city_id',
                        'md_client.pincode as first_client_pincode',
                        'md_client.mobile as first_client_mob',
                        'md_client.email as first_client_email',
                        'md_client.add_line_1 as first_client_add_line_1',
                        'md_client.add_line_2 as first_client_add_line_2',
                        'md_client_2.client_name as second_client_name','md_client_2.pan as second_client_pan','md_client_2.client_type as second_client_type',
                        'md_plan.plan_name as plan_name','md_option.opt_name as opt_name','md_plan_2.plan_name as plan_name_to','md_option_2.opt_name as opt_name_to',
                        'md_rnt.rnt_name as rnt_name','td_form_received.arn_no as arn_no','td_form_received.euin_no as euin_no','md_branch.brn_name as branch_name',
                        'md_deposit_bank.bank_name as bank_name','md_deposit_bank.ifs_code as ifs_code','md_deposit_bank.micr_code as micr_code','md_deposit_bank.branch_name as chq_branch_name','md_deposit_bank.branch_addr as chq_branch_addr',
                        'md_employee.emp_name as emp_name')
                        ->where('md_trans.trans_type_id',$trans_type_id)
                        // ->whereBetween('td_mutual_fund.entry_date',[date('Y-m-d',strtotime($start_date)),date('Y-m-d',strtotime($end_date))])
                        ->whereDate('td_mutual_fund.entry_date','>=',date('Y-m-d',strtotime($start_date)))
                        ->whereDate('td_mutual_fund.entry_date','<=',date('Y-m-d',strtotime($end_date)))
                        ->where('td_mutual_fund.rnt_login_dt',$login_status,NULL)
                        ->where('td_mutual_fund.rnt_login_cutt_off',$login_status,NULL)
                        ->paginate($paginate);
                }
            }else {
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
                        $rawQuery=$this->filterCriteria($rawQuery,$from_date,$to_date,$tin_no,$client_code,$amc_name,$scheme_name,$rnt_name);
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
                            ->leftJoin('md_employee','md_employee.euin_no','=','td_form_received.euin_no')
                            ->leftJoin('md_branch','md_branch.id','=','td_form_received.branch_code')
                            ->select('td_mutual_fund.*','md_trans.trns_name as trans_name','md_trans.trans_type_id as trans_type_id','td_form_received.application_no as application_no',
                            'td_form_received.bu_type as bu_type','td_form_received.inv_type as inv_type','md_scheme.scheme_name as scheme_name','md_scheme.id as scheme_id','md_scheme_2.scheme_name as scheme_name_to','md_scheme_2.id as scheme_id_to',
                            'md_client.client_code as first_client_code','md_client.client_name as first_client_name',
                            'md_client.pan as first_client_pan','md_client.client_type as first_client_type',
                            'md_client.country_id as first_client_country_id',
                            'md_client.state as first_client_state_id',
                            'md_client.dist as first_client_district_id',
                        'md_client.client_type_mode as change_status_id',
                        'md_client.city as first_client_city_id',
                            'md_client.pincode as first_client_pincode',
                            'md_client.mobile as first_client_mob',
                            'md_client.email as first_client_email',
                            'md_client.add_line_1 as first_client_add_line_1',
                            'md_client.add_line_2 as first_client_add_line_2',
                            'md_client_2.client_code as second_client_code','md_client_2.client_name as second_client_name','md_client_2.pan as second_client_pan','md_client_2.client_type as second_client_type',
                            'md_plan.plan_name as plan_name','md_option.opt_name as opt_name','md_plan_2.plan_name as plan_name_to','md_option_2.opt_name as opt_name_to',
                            'md_rnt.rnt_name as rnt_name','td_form_received.arn_no as arn_no','td_form_received.euin_no as euin_no','md_branch.brn_name as branch_name',
                            'md_deposit_bank.bank_name as bank_name','md_deposit_bank.ifs_code as ifs_code','md_deposit_bank.micr_code as micr_code','md_deposit_bank.branch_name as chq_branch_name','md_deposit_bank.branch_addr as chq_branch_addr',
                            'md_employee.emp_name as emp_name')
                            ->where('md_trans.trans_type_id',$trans_type_id)
                            ->where('td_mutual_fund.trans_id',$trans_id)
                            ->whereRaw($rawQuery)
                            ->orderByRaw($rawOrderBy)
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
                            ->leftJoin('md_employee','md_employee.euin_no','=','td_form_received.euin_no')
                            ->leftJoin('md_branch','md_branch.id','=','td_form_received.branch_code')
                            ->select('td_mutual_fund.*','md_trans.trns_name as trans_name','md_trans.trans_type_id as trans_type_id','td_form_received.application_no as application_no',
                            'td_form_received.bu_type as bu_type','td_form_received.inv_type as inv_type','md_scheme.scheme_name as scheme_name','md_scheme.id as scheme_id','md_scheme_2.scheme_name as scheme_name_to','md_scheme_2.id as scheme_id_to',
                            'md_client.client_code as first_client_code','md_client.client_name as first_client_name',
                            'md_client.pan as first_client_pan','md_client.client_type as first_client_type',
                            'md_client.country_id as first_client_country_id',
                            'md_client.state as first_client_state_id',
                            'md_client.dist as first_client_district_id',
                            'md_client.city as first_client_city_id',
                        'md_client.client_type_mode as change_status_id',
                        'md_client.mobile as first_client_mob',
                            'md_client.email as first_client_email',
                            'md_client.pincode as first_client_pincode',
                            'md_client.add_line_1 as first_client_add_line_1',
                            'md_client.add_line_2 as first_client_add_line_2',
                            'md_client_2.client_code as second_client_code','md_client_2.client_name as second_client_name','md_client_2.pan as second_client_pan','md_client_2.client_type as second_client_type',
                            'md_plan.plan_name as plan_name','md_option.opt_name as opt_name','md_plan_2.plan_name as plan_name_to','md_option_2.opt_name as opt_name_to',
                            'md_rnt.rnt_name as rnt_name','td_form_received.arn_no as arn_no','td_form_received.euin_no as euin_no','md_branch.brn_name as branch_name',
                            'md_deposit_bank.bank_name as bank_name','md_deposit_bank.ifs_code as ifs_code','md_deposit_bank.micr_code as micr_code','md_deposit_bank.branch_name as chq_branch_name','md_deposit_bank.branch_addr as chq_branch_addr',
                            'md_employee.emp_name as emp_name')
                            ->where('md_trans.trans_type_id',$trans_type_id)
                            ->where('td_mutual_fund.trans_id',$trans_id)
                            // ->whereDate('td_mutual_fund.entry_date',date('Y-m-d'))
                            ->orderByRaw($rawOrderBy)
                            ->paginate($paginate);
                    }
                } elseif (($from_date && $to_date) || $tin_no || $client_code || $amc_name || $scheme_name || $rnt_name || $brn_cd || $bu_type || $euin_no || $sub_brk_cd) {
                    // return $request;
                    $rawQuery='';

                    $queryString='td_form_received.branch_code';
                    $rawQuery.=Helper::WhereRawQuery($brn_cd,$rawQuery,$queryString);
                    $queryString='td_form_received.bu_type';
                    $rawQuery.=Helper::WhereRawQuery($bu_type,$rawQuery,$queryString);
                    $queryString='td_form_received.euin_no';
                    $rawQuery.=Helper::WhereRawQuery($euin_no,$rawQuery,$queryString);
                    $queryString='td_form_received.sub_brk_cd';
                    $rawQuery.=Helper::WhereRawQuery($sub_brk_cd,$rawQuery,$queryString);

                    $rawQuery=$this->filterCriteria($rawQuery,$from_date,$to_date,$tin_no,$client_code,$amc_name,$scheme_name,$rnt_name);
                    // return $rawQuery;
                    // \DB::enableQueryLog();
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
                        ->leftJoin('md_employee','md_employee.euin_no','=','td_form_received.euin_no')
                        ->leftJoin('md_branch','md_branch.id','=','td_form_received.branch_code')
                        ->select('td_mutual_fund.*','md_trans.trns_name as trans_name','md_trans.trans_type_id as trans_type_id','td_form_received.application_no as application_no',
                        'td_form_received.bu_type as bu_type','td_form_received.inv_type as inv_type','md_scheme.scheme_name as scheme_name','md_scheme.id as scheme_id','md_scheme_2.scheme_name as scheme_name_to','md_scheme_2.id as scheme_id_to',
                        'md_client.client_code as first_client_code','md_client.client_name as first_client_name',
                        'md_client.pan as first_client_pan','md_client.client_type as first_client_type',
                        'md_client.country_id as first_client_country_id',
                        'md_client.state as first_client_state_id',
                        'md_client.dist as first_client_district_id',
                        'md_client.client_type_mode as change_status_id',
                        'md_client.city as first_client_city_id',
                        'md_client.mobile as first_client_mob',
                        'md_client.email as first_client_email',
                        'md_client.pincode as first_client_pincode',
                        'md_client.add_line_1 as first_client_add_line_1',
                        'md_client.add_line_2 as first_client_add_line_2',
                        'md_client_2.client_code as second_client_code','md_client_2.client_name as second_client_name','md_client_2.pan as second_client_pan','md_client_2.client_type as second_client_type',
                        'md_plan.plan_name as plan_name','md_option.opt_name as opt_name','md_plan_2.plan_name as plan_name_to','md_option_2.opt_name as opt_name_to',
                        'md_rnt.rnt_name as rnt_name','td_form_received.arn_no as arn_no','td_form_received.euin_no as euin_no','md_branch.brn_name as branch_name',
                        'md_deposit_bank.bank_name as bank_name','md_deposit_bank.ifs_code as ifs_code','md_deposit_bank.micr_code as micr_code','md_deposit_bank.branch_name as chq_branch_name','md_deposit_bank.branch_addr as chq_branch_addr',
                        'md_employee.emp_name as emp_name')
                        ->where('md_trans.trans_type_id',$trans_type_id)
                        ->where('td_mutual_fund.trans_id',$trans_id)
                        ->whereRaw($rawQuery)
                        ->paginate($paginate);
                    // dd(\DB::getQueryLog());
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
                        ->leftJoin('md_employee','md_employee.euin_no','=','td_form_received.euin_no')
                        ->leftJoin('md_branch','md_branch.id','=','td_form_received.branch_code')
                        ->select('td_mutual_fund.*','md_trans.trns_name as trans_name','md_trans.trans_type_id as trans_type_id','td_form_received.application_no as application_no',
                        'td_form_received.bu_type as bu_type','td_form_received.inv_type as inv_type','md_scheme.scheme_name as scheme_name','md_scheme.id as scheme_id','md_scheme_2.scheme_name as scheme_name_to','md_scheme_2.id as scheme_id_to',
                        'md_client.client_code as first_client_code','md_client.client_name as first_client_name',
                        'md_client.pan as first_client_pan','md_client.client_type as first_client_type',
                        'md_client.country_id as first_client_country_id',
                        'md_client.state as first_client_state_id',
                        'md_client.dist as first_client_district_id',
                        'md_client.client_type_mode as change_status_id',
                        'md_client.city as first_client_city_id',
                        'md_client.mobile as first_client_mob',
                        'md_client.email as first_client_email',
                        'md_client.pincode as first_client_pincode',
                        'md_client.add_line_1 as first_client_add_line_1',
                        'md_client.add_line_2 as first_client_add_line_2',
                        'md_client_2.client_code as second_client_code','md_client_2.client_name as second_client_name','md_client_2.pan as second_client_pan','md_client_2.client_type as second_client_type',
                        'md_plan.plan_name as plan_name','md_option.opt_name as opt_name','md_plan_2.plan_name as plan_name_to','md_option_2.opt_name as opt_name_to',
                        'md_rnt.rnt_name as rnt_name','td_form_received.arn_no as arn_no','td_form_received.euin_no as euin_no','md_branch.brn_name as branch_name',
                        'md_deposit_bank.bank_name as bank_name','md_deposit_bank.ifs_code as ifs_code','md_deposit_bank.micr_code as micr_code','md_deposit_bank.branch_name as chq_branch_name','md_deposit_bank.branch_addr as chq_branch_addr',
                        'md_employee.emp_name as emp_name')
                        ->where('md_trans.trans_type_id',$trans_type_id)
                        ->where('td_mutual_fund.trans_id',$trans_id)
                        // ->whereDate('td_mutual_fund.entry_date',date('Y-m-d'))
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
            $trans_type_id=$request->trans_type_id;
            $trans_id=$request->trans_id;
            $tin_no=$request->tin_no;
            $cat_name=$request->cat_name;
            $option=$request->option;
            $login_status=$request->login_status;
            $date_status=$request->date_status;
            $start_date=$request->start_date;
            $end_date=$request->end_date;

            $amc_name=json_decode($request->amc_name);
            $scheme_name=json_decode($request->scheme_name);
            $rnt_name=json_decode($request->rnt_name);
            $client_code=$request->client_code;

            $order=$request->order;
            $field=$request->field;

            $from_date=$request->from_date;
            $to_date=$request->to_date;


            if ($paginate=='A') {
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
                        ->leftJoin('md_employee','md_employee.euin_no','=','td_form_received.euin_no')
                        ->leftJoin('md_branch','md_branch.id','=','td_form_received.branch_code')
                        ->select('td_mutual_fund.*','md_trans.trns_name as trans_name','md_trans.trans_type_id as trans_type_id','td_form_received.application_no as application_no',
                        'td_form_received.bu_type as bu_type','td_form_received.inv_type as inv_type','md_scheme.scheme_name as scheme_name','md_scheme.id as scheme_id','md_scheme_2.scheme_name as scheme_name_to','md_scheme_2.id as scheme_id_to',
                        'md_client.client_code as first_client_code','md_client.client_name as first_client_name',
                        'md_client.country_id as first_client_country_id',
                        'md_client.state as first_client_state_id',
                        'md_client.dist as first_client_district_id',
                        'md_client.city as first_client_city_id',
                        'md_client.client_type_mode as change_status_id',
                        'md_client.pincode as first_client_pincode',
                        'md_client.add_line_1 as first_client_add_line_1',
                        'md_client.add_line_2 as first_client_add_line_2',
                        'md_client.mobile as first_client_mob',
                        'md_client.email as first_client_email',
                        'md_client.pan as first_client_pan','md_client.client_type as first_client_type',
                        'md_client_2.client_code as second_client_code','md_client_2.client_name as second_client_name','md_client_2.pan as second_client_pan','md_client_2.client_type as second_client_type',
                        'md_plan.plan_name as plan_name','md_option.opt_name as opt_name','md_plan_2.plan_name as plan_name_to','md_option_2.opt_name as opt_name_to',
                        'md_rnt.rnt_name as rnt_name','td_form_received.arn_no as arn_no','td_form_received.euin_no as euin_no','md_branch.brn_name as branch_name',
                        'md_deposit_bank.bank_name as bank_name','md_deposit_bank.ifs_code as ifs_code','md_deposit_bank.micr_code as micr_code','md_deposit_bank.branch_name as chq_branch_name','md_deposit_bank.branch_addr as chq_branch_addr',
                        'md_employee.emp_name as emp_name')
                        ->where('md_trans.trans_type_id',$trans_type_id)
                        ->whereDate('td_mutual_fund.entry_date',date('Y-m-d'))
                        ->where('td_mutual_fund.rnt_login_dt',$login_status,NULL)
                        ->where('td_mutual_fund.rnt_login_cutt_off',$login_status,NULL)
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
                        ->leftJoin('md_employee','md_employee.euin_no','=','td_form_received.euin_no')
                        ->leftJoin('md_branch','md_branch.id','=','td_form_received.branch_code')
                        ->select('td_mutual_fund.*','md_trans.trns_name as trans_name','md_trans.trans_type_id as trans_type_id','td_form_received.application_no as application_no',
                        'td_form_received.bu_type as bu_type','td_form_received.inv_type as inv_type','md_scheme.scheme_name as scheme_name','md_scheme.id as scheme_id','md_scheme_2.scheme_name as scheme_name_to','md_scheme_2.id as scheme_id_to',
                        'md_client.client_code as first_client_code',
                        'md_client.country_id as first_client_country_id',
                        'md_client.state as first_client_state_id',
                        'md_client.dist as first_client_district_id',
                        'md_client.city as first_client_city_id',
                        'md_client.client_type_mode as change_status_id',
                        'md_client.mobile as first_client_mob',
                        'md_client.email as first_client_email',
                        'md_client.pincode as first_client_pincode',
                        'md_client.add_line_1 as first_client_add_line_1',
                        'md_client.add_line_2 as first_client_add_line_2',
                        'md_client.client_name as first_client_name','md_client.pan as first_client_pan','md_client.client_type as first_client_type',
                        'md_client_2.client_code as second_client_code','md_client_2.client_name as second_client_name','md_client_2.pan as second_client_pan','md_client_2.client_type as second_client_type',
                        'md_plan.plan_name as plan_name','md_option.opt_name as opt_name','md_plan_2.plan_name as plan_name_to','md_option_2.opt_name as opt_name_to',
                        'md_rnt.rnt_name as rnt_name','td_form_received.arn_no as arn_no','td_form_received.euin_no as euin_no','md_branch.brn_name as branch_name',
                        'md_deposit_bank.bank_name as bank_name','md_deposit_bank.ifs_code as ifs_code','md_deposit_bank.micr_code as micr_code','md_deposit_bank.branch_name as chq_branch_name','md_deposit_bank.branch_addr as chq_branch_addr',
                        'md_employee.emp_name as emp_name')
                        ->where('md_trans.trans_type_id',$trans_type_id)
                        // ->whereBetween('td_mutual_fund.entry_date',[date('Y-m-d',strtotime($start_date)),date('Y-m-d',strtotime($end_date))])
                        ->whereDate('td_mutual_fund.entry_date','>=',date('Y-m-d',strtotime($start_date)))
                        ->whereDate('td_mutual_fund.entry_date','<=',date('Y-m-d',strtotime($end_date)))
                        ->where('td_mutual_fund.rnt_login_dt',$login_status,NULL)
                        ->where('td_mutual_fund.rnt_login_cutt_off',$login_status,NULL)
                        ->get();
                }
            } else {
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
                                $rawQuery.=" AND td_mutual_fund.entry_date"." >= '".date('Y-m-d',strtotime($from_date))."'";
                            } else {
                                $rawQuery.=" td_mutual_fund.entry_date"." >= '".date('Y-m-d',strtotime($from_date))."'";
                            }
                            $rawQuery.=" AND td_mutual_fund.entry_date"." <= '".date('Y-m-d',strtotime($to_date))."'";
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
                            ->leftJoin('md_employee','md_employee.euin_no','=','td_form_received.euin_no')
                            ->leftJoin('md_branch','md_branch.id','=','td_form_received.branch_code')
                            ->select('td_mutual_fund.*','md_trans.trns_name as trans_name','md_trans.trans_type_id as trans_type_id','td_form_received.application_no as application_no',
                            'td_form_received.bu_type as bu_type','td_form_received.inv_type as inv_type','md_scheme.scheme_name as scheme_name','md_scheme.id as scheme_id','md_scheme_2.scheme_name as scheme_name_to','md_scheme_2.id as scheme_id_to',
                            'md_client.client_code as first_client_code',
                            'md_client.country_id as first_client_country_id',
                            'md_client.state as first_client_state_id',
                        'md_client.client_type_mode as change_status_id',
                        'md_client.dist as first_client_district_id',
                            'md_client.city as first_client_city_id',
                            'md_client.pincode as first_client_pincode',
                            'md_client.mobile as first_client_mob',
                            'md_client.email as first_client_email',
                            'md_client.add_line_1 as first_client_add_line_1',
                            'md_client.add_line_2 as first_client_add_line_2',
                            'md_client.client_name as first_client_name','md_client.pan as first_client_pan','md_client.client_type as first_client_type',
                            'md_client_2.client_code as second_client_code','md_client_2.client_name as second_client_name','md_client_2.pan as second_client_pan','md_client_2.client_type as second_client_type',
                            'md_plan.plan_name as plan_name','md_option.opt_name as opt_name','md_plan_2.plan_name as plan_name_to','md_option_2.opt_name as opt_name_to',
                            'md_rnt.rnt_name as rnt_name','td_form_received.arn_no as arn_no','td_form_received.euin_no as euin_no','md_branch.brn_name as branch_name',
                            'md_deposit_bank.bank_name as bank_name','md_deposit_bank.ifs_code as ifs_code','md_deposit_bank.micr_code as micr_code','md_deposit_bank.branch_name as chq_branch_name','md_deposit_bank.branch_addr as chq_branch_addr',
                            'md_employee.emp_name as emp_name')
                            ->where('md_trans.trans_type_id',$trans_type_id)
                            ->where('td_mutual_fund.trans_id',$trans_id)
                            ->whereRaw($rawQuery)
                            ->orderByRaw($rawOrderBy)
                            ->get();
                            // ->get();
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
                            ->leftJoin('md_employee','md_employee.euin_no','=','td_form_received.euin_no')
                            ->leftJoin('md_branch','md_branch.id','=','td_form_received.branch_code')
                            ->select('td_mutual_fund.*','md_trans.trns_name as trans_name','md_trans.trans_type_id as trans_type_id','td_form_received.application_no as application_no',
                            'td_form_received.bu_type as bu_type','td_form_received.inv_type as inv_type','md_scheme.scheme_name as scheme_name','md_scheme.id as scheme_id','md_scheme_2.scheme_name as scheme_name_to','md_scheme_2.id as scheme_id_to',
                            'md_client.client_code as first_client_code',
                            'md_client.country_id as first_client_country_id',
                            'md_client.state as first_client_state_id',
                            'md_client.dist as first_client_district_id',
                            'md_client.city as first_client_city_id',
                        'md_client.client_type_mode as change_status_id',
                        'md_client.pincode as first_client_pincode',
                            'md_client.mobile as first_client_mob',
                            'md_client.email as first_client_email',
                            'md_client.add_line_1 as first_client_add_line_1',
                            'md_client.add_line_2 as first_client_add_line_2',
                            'md_client.client_name as first_client_name','md_client.pan as first_client_pan','md_client.client_type as first_client_type',
                            'md_client_2.client_code as second_client_code','md_client_2.client_name as second_client_name','md_client_2.pan as second_client_pan','md_client_2.client_type as second_client_type',
                            'md_plan.plan_name as plan_name','md_option.opt_name as opt_name','md_plan_2.plan_name as plan_name_to','md_option_2.opt_name as opt_name_to',
                            'md_rnt.rnt_name as rnt_name','td_form_received.arn_no as arn_no','td_form_received.euin_no as euin_no','md_branch.brn_name as branch_name',
                            'md_deposit_bank.bank_name as bank_name','md_deposit_bank.ifs_code as ifs_code','md_deposit_bank.micr_code as micr_code','md_deposit_bank.branch_name as chq_branch_name','md_deposit_bank.branch_addr as chq_branch_addr',
                            'md_employee.emp_name as emp_name')
                            ->where('md_trans.trans_type_id',$trans_type_id)
                            ->where('td_mutual_fund.trans_id',$trans_id)
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
                    // return $rawQuery;

                    // \DB::enableQueryLog();
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
                        ->leftJoin('md_employee','md_employee.euin_no','=','td_form_received.euin_no')
                        ->leftJoin('md_branch','md_branch.id','=','td_form_received.branch_code')
                        ->select('td_mutual_fund.*','md_trans.trns_name as trans_name','md_trans.trans_type_id as trans_type_id','td_form_received.application_no as application_no',
                        'td_form_received.bu_type as bu_type','td_form_received.inv_type as inv_type','md_scheme.scheme_name as scheme_name','md_scheme.id as scheme_id','md_scheme_2.scheme_name as scheme_name_to','md_scheme_2.id as scheme_id_to',
                        'md_client.client_code as first_client_code',
                        'md_client.country_id as first_client_country_id',
                        'md_client.state as first_client_state_id',
                        'md_client.client_type_mode as change_status_id',
                        'md_client.dist as first_client_district_id',
                        'md_client.city as first_client_city_id',
                        'md_client.pincode as first_client_pincode',
                        'md_client.mobile as first_client_mob',
                        'md_client.email as first_client_email',
                        'md_client.add_line_1 as first_client_add_line_1',
                        'md_client.add_line_2 as first_client_add_line_2',
                        'md_client.client_name as first_client_name','md_client.pan as first_client_pan','md_client.client_type as first_client_type',
                        'md_client_2.client_code as second_client_code','md_client_2.client_name as second_client_name','md_client_2.pan as second_client_pan','md_client_2.client_type as second_client_type',
                        'md_plan.plan_name as plan_name','md_option.opt_name as opt_name','md_plan_2.plan_name as plan_name_to','md_option_2.opt_name as opt_name_to',
                        'md_rnt.rnt_name as rnt_name','td_form_received.arn_no as arn_no','td_form_received.euin_no as euin_no','md_branch.brn_name as branch_name',
                        'md_deposit_bank.bank_name as bank_name','md_deposit_bank.ifs_code as ifs_code','md_deposit_bank.micr_code as micr_code','md_deposit_bank.branch_name as chq_branch_name','md_deposit_bank.branch_addr as chq_branch_addr',
                        'md_employee.emp_name as emp_name')
                        ->where('md_trans.trans_type_id',$trans_type_id)
                        ->where('td_mutual_fund.trans_id',$trans_id)
                        ->whereRaw($rawQuery)
                        ->get();
                    // dd(\DB::getQueryLog());
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
                        ->leftJoin('md_employee','md_employee.euin_no','=','td_form_received.euin_no')
                        ->leftJoin('md_branch','md_branch.id','=','td_form_received.branch_code')
                        ->select('td_mutual_fund.*','md_trans.trns_name as trans_name','md_trans.trans_type_id as trans_type_id','td_form_received.application_no as application_no',
                        'td_form_received.bu_type as bu_type','td_form_received.inv_type as inv_type','md_scheme.scheme_name as scheme_name','md_scheme.id as scheme_id','md_scheme_2.scheme_name as scheme_name_to','md_scheme_2.id as scheme_id_to',
                        'md_client.client_code as first_client_code',
                        'md_client.country_id as first_client_country_id',
                        'md_client.state as first_client_state_id',
                        'md_client.dist as first_client_district_id',
                        'md_client.city as first_client_city_id',
                        'md_client.client_type_mode as change_status_id',
                        'md_client.mobile as first_client_mob',
                        'md_client.email as first_client_email',
                        'md_client.pincode as first_client_pincode',
                        'md_client.add_line_1 as first_client_add_line_1',
                        'md_client.add_line_2 as first_client_add_line_2',
                        'md_client.client_name as first_client_name','md_client.pan as first_client_pan','md_client.client_type as first_client_type',
                        'md_client_2.client_code as second_client_code','md_client_2.client_name as second_client_name','md_client_2.pan as second_client_pan','md_client_2.client_type as second_client_type',
                        'md_plan.plan_name as plan_name','md_option.opt_name as opt_name','md_plan_2.plan_name as plan_name_to','md_option_2.opt_name as opt_name_to',
                        'md_rnt.rnt_name as rnt_name','td_form_received.arn_no as arn_no','td_form_received.euin_no as euin_no','md_branch.brn_name as branch_name',
                        'md_deposit_bank.bank_name as bank_name','md_deposit_bank.ifs_code as ifs_code','md_deposit_bank.micr_code as micr_code','md_deposit_bank.branch_name as chq_branch_name','md_deposit_bank.branch_addr as chq_branch_addr',
                        'md_employee.emp_name as emp_name')
                        ->where('md_trans.trans_type_id',$trans_type_id)
                        ->where('td_mutual_fund.trans_id',$trans_id)
                        ->whereDate('td_mutual_fund.entry_date',date('Y-m-d'))
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
            $trans_type_id=$request->trans_type_id;
            $trans_id=$request->trans_id;
            $paginate=$request->paginate;
            $tin_no=$request->tin_no;
            if ($search!='') {
                $data=MutualFund::join('md_trans','md_trans.id','=','td_mutual_fund.trans_id')
                    ->leftJoin('md_deposit_bank','md_deposit_bank.id','=','td_mutual_fund.chq_bank')
                    ->select('td_mutual_fund.*','md_trans.trans_type_id as trans_type_id')
                    ->where('md_trans.trans_type_id',$trans_type_id)
                    ->where('td_mutual_fund.tin_no',$search)
                    // ->whereDate('td_mutual_fund.entry_date',date('Y-m-d'))
                    ->get();
            }elseif ($tin_no && $trans_type_id && $trans_id) {
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
                    ->leftJoin('md_employee','md_employee.euin_no','=','td_form_received.euin_no')
                    ->leftJoin('md_branch','md_branch.id','=','td_form_received.branch_code')
                    ->select('td_mutual_fund.*','md_trans.trns_name as trans_name','md_trans.trans_type_id as trans_type_id','td_form_received.application_no as application_no',
                    'td_form_received.bu_type as bu_type','td_form_received.inv_type as inv_type','md_scheme.scheme_name as scheme_name','md_scheme.id as scheme_id','md_scheme_2.scheme_name as scheme_name_to','md_scheme_2.id as scheme_id_to',
                    'md_client.client_code as first_client_code','md_client.client_name as first_client_name',
                    'md_client.pan as first_client_pan','md_client.client_type as first_client_type',
                    'md_client_2.client_code as second_client_code','md_client_2.client_name as second_client_name','md_client_2.pan as second_client_pan','md_client_2.client_type as second_client_type',
                    'md_plan.plan_name as plan_name','md_option.opt_name as opt_name','md_plan_2.plan_name as plan_name_to','md_option_2.opt_name as opt_name_to',
                    'md_rnt.rnt_name as rnt_name','td_form_received.arn_no as arn_no','td_form_received.euin_no as euin_no','md_branch.brn_name as branch_name',
                    'md_deposit_bank.bank_name as bank_name','md_deposit_bank.ifs_code as ifs_code','md_deposit_bank.micr_code as micr_code','md_deposit_bank.branch_name as chq_branch_name','md_deposit_bank.branch_addr as chq_branch_addr',
                    'md_employee.emp_name as emp_name')
                    ->where('md_trans.trans_type_id',$trans_type_id)
                    ->where('td_mutual_fund.trans_id',$trans_id)
                    ->where('td_mutual_fund.tin_no','like', '%' . $tin_no . '%')
                    ->get();
            }elseif ($paginate!='' && $trans_id!='') {
                $data=MutualFund::join('md_trans','md_trans.id','=','td_mutual_fund.trans_id')
                    ->leftJoin('md_deposit_bank','md_deposit_bank.id','=','td_mutual_fund.chq_bank')
                    ->select('td_mutual_fund.*','md_trans.trans_type_id as trans_type_id')
                    ->where('td_mutual_fund.trans_id',$trans_id)
                    ->whereDate('td_mutual_fund.entry_date',date('Y-m-d'))
                    ->paginate($paginate);
            }elseif ($paginate!='') {
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
                    ->leftJoin('md_employee','md_employee.euin_no','=','td_form_received.euin_no')
                    ->leftJoin('md_branch','md_branch.id','=','td_form_received.branch_code')
                    ->select('td_mutual_fund.*','md_trans.trns_name as trans_name','md_trans.trans_type_id as trans_type_id','td_form_received.application_no as application_no',
                    'td_form_received.bu_type as bu_type','td_form_received.inv_type as inv_type','md_scheme.scheme_name as scheme_name','md_scheme.id as scheme_id','md_scheme_2.scheme_name as scheme_name_to','md_scheme_2.id as scheme_id_to',
                    'md_client.client_code as first_client_code','md_client.client_name as first_client_name',
                    'md_client.pan as first_client_pan','md_client.client_type as first_client_type',
                    'md_client_2.client_code as second_client_code','md_client_2.client_name as second_client_name','md_client_2.pan as second_client_pan','md_client_2.client_type as second_client_type',
                    'md_plan.plan_name as plan_name','md_option.opt_name as opt_name','md_plan_2.plan_name as plan_name_to','md_option_2.opt_name as opt_name_to',
                    'md_rnt.rnt_name as rnt_name','td_form_received.arn_no as arn_no','td_form_received.euin_no as euin_no','md_branch.brn_name as branch_name',
                    'md_deposit_bank.bank_name as bank_name','md_deposit_bank.ifs_code as ifs_code','md_deposit_bank.micr_code as micr_code','md_deposit_bank.branch_name as chq_branch_name','md_deposit_bank.branch_addr as chq_branch_addr',
                    'md_employee.emp_name as emp_name')
                    ->where('md_trans.trans_type_id',$trans_type_id)
                    // ->whereDate('td_mutual_fund.entry_date',date('Y-m-d'))
                    ->paginate($paginate);
            }elseif ($tin_no!='') {
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
                    ->leftJoin('md_employee','md_employee.euin_no','=','td_form_received.euin_no')
                    ->leftJoin('md_branch','md_branch.id','=','td_form_received.branch_code')
                    ->select('td_mutual_fund.*','md_trans.trns_name as trans_name','md_trans.trans_type_id as trans_type_id','td_form_received.application_no as application_no',
                    'td_form_received.bu_type as bu_type','td_form_received.inv_type as inv_type','md_scheme.scheme_name as scheme_name','md_scheme.id as scheme_id','md_scheme_2.scheme_name as scheme_name_to','md_scheme_2.id as scheme_id_to',
                    'md_client.client_code as first_client_code','md_client.client_name as first_client_name','md_client.pan as first_client_pan','md_client.client_type as first_client_type',
                    'md_client_2.client_code as second_client_code','md_client_2.client_name as second_client_name',
                    'md_client_2.pan as second_client_pan','md_client_2.client_type as second_client_type',

                    'md_plan.plan_name as plan_name','md_option.opt_name as opt_name','md_plan_2.plan_name as plan_name_to','md_option_2.opt_name as opt_name_to',
                    'md_rnt.rnt_name as rnt_name','td_form_received.arn_no as arn_no','td_form_received.euin_no as euin_no','md_branch.brn_name as branch_name',
                    'md_deposit_bank.bank_name as bank_name','md_deposit_bank.ifs_code as ifs_code','md_deposit_bank.micr_code as micr_code','md_deposit_bank.branch_name as chq_branch_name','md_deposit_bank.branch_addr as chq_branch_addr',
                    'md_employee.emp_name as emp_name')
                    ->where('td_mutual_fund.tin_no','like', '%' . $tin_no . '%')
                    ->get();
            } else{
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
                    ->leftJoin('md_employee','md_employee.euin_no','=','td_form_received.euin_no')
                    ->leftJoin('md_branch','md_branch.id','=','td_form_received.branch_code')
                    ->select('td_mutual_fund.*','md_trans.trns_name as trans_name','md_trans.trans_type_id as trans_type_id','td_form_received.application_no as application_no',
                    'td_form_received.bu_type as bu_type','td_form_received.inv_type as inv_type','md_scheme.scheme_name as scheme_name','md_scheme.id as scheme_id','md_scheme_2.scheme_name as scheme_name_to','md_scheme_2.id as scheme_id_to',
                    'md_client.client_code as first_client_code','md_client.client_name as first_client_name','md_client.pan as first_client_pan','md_client.client_type as first_client_type',
                    'md_client_2.client_code as second_client_code','md_client_2.client_name as second_client_name',
                    'md_client_2.pan as second_client_pan','md_client_2.client_type as second_client_type',
                    'md_plan.plan_name as plan_name','md_option.opt_name as opt_name','md_plan_2.plan_name as plan_name_to','md_option_2.opt_name as opt_name_to',
                    'md_rnt.rnt_name as rnt_name','td_form_received.arn_no as arn_no','td_form_received.euin_no as euin_no','md_branch.brn_name as branch_name',
                    'md_deposit_bank.bank_name as bank_name','md_deposit_bank.ifs_code as ifs_code','md_deposit_bank.micr_code as micr_code','md_deposit_bank.branch_name as chq_branch_name','md_deposit_bank.branch_addr as chq_branch_addr',
                    'md_employee.emp_name as emp_name')
                    ->where('md_trans.trans_type_id',$trans_type_id)
                    ->whereDate('td_mutual_fund.entry_date',date('Y-m-d'))
                    ->get();
            }
        } catch (\Throwable $th) {
            // throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function createShow(Request $request)
    {
        try {
            $datas=MutualFund::join('md_trans','md_trans.id','=','td_mutual_fund.trans_id')
                    ->leftJoin('md_deposit_bank','md_deposit_bank.id','=','td_mutual_fund.chq_bank')
                    ->select('td_mutual_fund.*','md_trans.trns_name as trans_name')
                    ->where('td_mutual_fund.delete_flag','N')
                    ->whereDate('td_mutual_fund.entry_date',date('Y-m-d'))
                    ->where('md_trans.trans_type_id',$request->trans_type_id)
                    ->get();

            $data=[];
            $trans_id_1_count=0;
            $trans_id_2_count=0;
            $trans_id_3_count=0;
            if ($request->trans_type_id==4) {
                foreach($datas as $dd){
                    if($dd->trans_id==4){
                        $trans_id_1_count=$trans_id_1_count+1;
                    }elseif ($dd->trans_id==5) {
                        $trans_id_2_count=$trans_id_2_count+1;
                    }elseif ($dd->trans_id==6) {
                        $trans_id_3_count=$trans_id_3_count+1;
                    }
                }
                $trans_data_1['id']=4;
                $trans_data_1['name']='PIP';
                $trans_data_1['count']=$trans_id_1_count;
                array_push($data,$trans_data_1);
                $trans_data_2['id']=5;
                $trans_data_2['name']='SIP';
                $trans_data_2['count']=$trans_id_2_count;
                array_push($data,$trans_data_2);
                $trans_data_3['id']=6;
                $trans_data_3['name']='Switch';
                $trans_data_3['count']=$trans_id_3_count;
                array_push($data,$trans_data_3);
            } elseif ($request->trans_type_id==5) {
                # code...
            } else {
                foreach($datas as $dd){
                    if($dd->trans_id==1){
                        $trans_id_1_count=$trans_id_1_count+1;
                    }elseif ($dd->trans_id==2) {
                        $trans_id_2_count=$trans_id_2_count+1;
                    }elseif ($dd->trans_id==3) {
                        $trans_id_3_count=$trans_id_3_count+1;
                    }
                }
                $trans_data_1['id']=1;
                $trans_data_1['name']='PIP';
                $trans_data_1['count']=$trans_id_1_count;
                array_push($data,$trans_data_1);
                $trans_data_2['id']=2;
                $trans_data_2['name']='SIP';
                $trans_data_2['count']=$trans_id_2_count;
                array_push($data,$trans_data_2);
                $trans_data_3['id']=3;
                $trans_data_3['name']='Switch';
                $trans_data_3['count']=$trans_id_3_count;
                array_push($data,$trans_data_3);
            }
            // return $data;
        } catch (\Throwable $th) {
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
            $product_id=1;
            $tin_no='';
            $trans_type_id=$request->trans_type_id;
            if ($trans_type_id==1) { // Financial
                $is_has=MutualFund::join('md_trans','md_trans.id','=','td_mutual_fund.trans_id')
                        ->leftJoin('md_deposit_bank','md_deposit_bank.id','=','td_mutual_fund.chq_bank')
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
                $is_has=MutualFund::join('md_trans','md_trans.id','=','td_mutual_fund.trans_id')
                        ->leftJoin('md_deposit_bank','md_deposit_bank.id','=','td_mutual_fund.chq_bank')
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
                $is_has=MutualFund::join('md_trans','md_trans.id','=','td_mutual_fund.trans_id')
                        ->leftJoin('md_deposit_bank','md_deposit_bank.id','=','td_mutual_fund.chq_bank')
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

            if($request->temp_tin_no!='' && $request->tin_status=='Y'){  // with TTIN NO
                // return $request;
                $has=MutualFund::where('temp_tin_no',$request->temp_tin_no)->get();
                if (count($has)>0) {
                    $error='Temporary TIN no already exist.';
                    return Helper::ErrorResponse($error);
                }else {
                    $second_client_id=$request->second_client_id;
                    $second_client_name=$request->second_client_name;
                    $second_client_pan=$request->second_client_pan;
                    if ($second_client_id=='' && $second_client_name!='' && $second_client_pan!='') {
                        $s_c_data=Client::create(array(
                            'client_name'=>$second_client_name,
                            'pan'=>$second_client_pan,
                            'client_type'=>'E',
                            // 'created_by'=>'',
                        ));
                        $second_client_id=$s_c_data->id;
                    }

                    $third_client_id=$request->third_client_id;
                    $third_client_name=$request->third_client_name;
                    $third_client_pan=$request->third_client_pan;
                    if ($second_client_id=='' && $third_client_name!='' && $third_client_pan!='') {
                        $t_c_data=Client::create(array(
                            'client_name'=>$third_client_name,
                            'pan'=>$third_client_pan,
                            'client_type'=>'E',
                            // 'created_by'=>'',
                        ));
                        $third_client_id=$t_c_data->id;
                    }

                    $app_form_scan=$request->app_form_scan;
                    $doc_name='';
                    if ($app_form_scan) {
                        $cv_path_extension=$app_form_scan->getClientOriginalExtension();
                        $doc_name=microtime(true).".".$cv_path_extension;
                        $app_form_scan->move(public_path('application-form/'),$doc_name);
                    }
                    FormReceived::where('temp_tin_no',$request->temp_tin_no)->update(array(
                        'bu_type'=>$request->bu_type,
                        'euin_no'=>$request->euin_no,
                        'sub_arn_no'=>isset($request->sub_arn_no)?$request->sub_arn_no:NULL,
                        'sub_brk_cd'=>isset($request->sub_brk_cd)?$request->sub_brk_cd:NULL,
                        'client_id'=>$request->first_client_id,
                        'trans_id'=>$request->trans_id,
                        'scheme_id'=>$request->scheme_id,
                        'scheme_id_to'=>isset($request->scheme_id_to)?$request->scheme_id_to:NULL,
                        'inv_type'=>isset($request->inv_type)?$request->inv_type:'N',
                        'application_no'=>isset($request->application_no)?$request->application_no:NULL,
                        'folio_no'=>isset($request->folio_no)?$request->folio_no:NULL,
                    ));
                    $data=MutualFund::create(array(
                        'temp_tin_no' =>$request->temp_tin_no,
                        'tin_no'=> $tin_no,
                        'entry_tin_status'=>$request->tin_status,
                        'entry_date'=> date('Y-m-d'),
                        'first_client_id'=>$request->first_client_id,
                        'first_kyc'=>$request->first_kyc,
                        'mode_of_holding'=>(($request->mode_of_holding)?$request->mode_of_holding:(($request->change_existing_mode_of_holding)?$request->change_existing_mode_of_holding:NULL)),

                        'second_client_id'=>isset($second_client_id)?$second_client_id:NULL,
                        'second_kyc'=>isset($request->second_kyc)?$request->second_kyc:NULL,
                        'third_client_id'=>isset($third_client_id)?$third_client_id:NULL,
                        'third_kyc'=>isset($request->third_kyc)?$request->third_kyc:NULL,
                        'trans_scheme_from'=>$request->scheme_id,
                        'trans_scheme_to'=>isset($request->scheme_id_to)?$request->scheme_id_to:NULL,
                        'option_id'=>isset($request->option)?$request->option:NULL,
                        'plan_id'=>isset($request->plan)?$request->plan:NULL,
                        'option_id_to'=>isset($request->option_to)?$request->option_to:NULL,
                        'plan_id_to'=>isset($request->plan_to)?$request->plan_to:NULL,
                        'folio_no'=>isset($request->folio_no)?$request->folio_no:NULL,
                        'amount'=>(($request->amount)?$request->amount:(($request->redemp_amount)?$request->redemp_amount:(($request->swp_stp_amount)?$request->swp_stp_amount:''))),
                        'unit'=>(($request->unit)?$request->unit:(($request->redemp_unit)?$request->redemp_unit:'')),
                        'switch_by'=>isset($request->switch_by)?$request->switch_by:NULL,
                        'trans_id'=>$request->trans_id,
                        'first_inv_amount'=>isset($request->first_inv_amount)?$request->first_inv_amount:NULL,
                        'sip_type'=>isset($request->sip_type)?$request->sip_type:NULL,

                        'sip_swp_stp_duration_type'=>((isset($request->sip_duration_type))?$request->sip_duration_type:((isset($request->swp_stp_duration_type))?$request->swp_stp_duration_type:NULL)),
                        'sip_swp_stp_duration'=>(($request->sip_duration)?$request->sip_duration:(($request->swp_stp_duration)?$request->swp_stp_duration:NULL)),
                        'sip_swp_stp_frequency'=>(($request->sip_frequency)?$request->sip_frequency:(($request->swp_stp_frequency)?$request->swp_stp_frequency:NULL)),

                        'sip_swp_stp_inst_date'=>(($request->sip_date)?$request->sip_date:(($request->installment_dt)?$request->installment_dt:NULL)),

                        // 'sip_swp_stp_start_date'=>isset($request->sip_start_date)?date('Y-m-d',strtotime($request->sip_start_date)):isset($request->swp_stp_start_date)?date('Y-m-d',strtotime($request->swp_stp_start_date)):NULL,
                        'sip_swp_stp_start_date'=>(($request->sip_start_date) ? date('Y-m-d',strtotime($request->sip_start_date)):(($request->swp_stp_start_date)? date('Y-m-d',strtotime($request->swp_stp_start_date)):NULL)),
                        'sip_swp_stp_end_date'=>(($request->sip_end_date)?date('Y-m-d',strtotime($request->sip_end_date)):(($request->swp_stp_end_date)?date('Y-m-d',strtotime($request->swp_stp_end_date)):NULL)),

                        'chq_no'=>$request->chq_no,
                        'chq_bank'=>$request->chq_bank,
                        // 'rnt_login_at'=>$request->rnt_login_at,
                        'app_form_scan'=>$doc_name,
                        'form_scan_status'=>$request->form_scan_status,
                        'remarks'=>$request->remarks,
                        'form_status'=>'P',
                        'rnt_login_at'=>$request->rnt_login_at,
                        'cancel_eff_dt'=>isset($request->cancel_eff_dt)?date('Y-m-d',strtotime($request->cancel_eff_dt)):NULL,
                        'change_contact_type'=>isset($request->change_contact_type)?$request->change_contact_type:NULL,
                        'reason_for_change'=>isset($request->reason_for_change)?$request->reason_for_change:NULL,
                        'nominee_opt_out'=>isset($request->nominee_opt_out)?$request->nominee_opt_out:NULL,
                        'redemp_type'=>isset($request->redemp_type)?$request->redemp_type:NULL,
                        'redemp_unit_type'=>isset($request->redemp_unit_type)?$request->redemp_unit_type:NULL,
                        'acc_no'=>isset($request->acc_no)?$request->acc_no:NULL,
                        'acc_bank_id'=>isset($request->acc_bank_id)?$request->acc_bank_id:NULL,
                        'swp_type'=>isset($request->swp_type)?$request->swp_type:NULL,
                        'stp_type'=>isset($request->stp_type)?$request->stp_type:NULL,
                        'kyc_status'=>isset($request->kyc_status)?$request->kyc_status:NULL,
                        'transmission_type'=>isset($request->transmission_type)?$request->transmission_type:NULL,

                        'change_new_mode_of_holding'=>isset($request->change_new_mode_of_holding)?$request->change_new_mode_of_holding:NULL,
                        'mob_declaration_flag'=>isset($request->mob_dec)?$request->mob_dec:NULL,
                        'email_declaration_flag'=>isset($request->email_dec)?$request->email_dec:NULL,
                        'merge_folio'=>isset($request->merge_folio)?$request->merge_folio:NULL,
                        'new_nominee'=>isset($request->new_nominee)?$request->new_nominee:NULL,

                        // for special sip
                        'swp_frequency'=>isset($request->swp_frequency)?$request->swp_frequency:NULL,
                        'swp_start_date'=>isset($request->swp_start_date)?date('Y-m-d',strtotime($request->swp_start_date)):NULL,
                        'swp_duration'=>isset($request->swp_duration)?$request->swp_duration:NULL,
                        'swp_end_date'=>isset($request->swp_end_date)?date('Y-m-d',strtotime($request->swp_end_date)):NULL,
                        'swp_inst_amount'=>isset($request->swp_inst_amount)?$request->swp_inst_amount:NULL,
                        // for step up sip
                        'step_up_by'=>isset($request->step_up_by)?$request->step_up_by:'N',
                        'step_up_amount'=>isset($request->step_up_amount)?$request->step_up_amount:NULL,
                        'step_up_percentage'=>isset($request->step_up_percentage)?$request->step_up_percentage:NULL,
                        // 'created_by'=>'',
                    ));

                    // START only for non financial changes
                    if ($request->change_contact_type!='') {  // client update
                        $first_client_id=$data->first_client_id;
                        $up_data=Client::find($first_client_id);
                        if ($request->email) {
                            $up_data->email=$request->email;
                            $up_data->save();
                        }
                        if ($request->mobile) {
                            $up_data->mobile=$request->mobile;
                            $up_data->save();
                        }
                    }

                    if ($data->trans_id==22) {  // address change
                        $first_client_id=$data->first_client_id;
                        $up_data=Client::find($first_client_id);
                        $up_data->add_line_1=$request->add_line_1;
                        $up_data->add_line_2=$request->add_line_2;
                        $up_data->city=$request->city;
                        $up_data->dist=$request->dist;
                        $up_data->state=$request->state;
                        $up_data->pincode=$request->pincode;
                        $up_data->country_id=$request->country_id;
                        $up_data->save();
                    }

                    if ($data->trans_id==23) {  // name change
                        $first_client_id=$data->first_client_id;
                        $up_data=Client::find($first_client_id);

                        $client_name=ucwords($request->new_name);
                        $words = explode(" ",$client_name);
                        $client_code="";
                        $client_code_1 = mb_substr($words[0], 0, 1).mb_substr($words[(count($words)-1)], 0, 1);;

                        $is_has=Client::where('client_code',$client_code_1)->get();
                        if (count($is_has)>0) {
                            $client_code=$client_code_1.date('dmy',strtotime($up_data->dob)).count($is_has);
                        }else {
                            $client_code=$client_code_1.date('dmy',strtotime($up_data->dob));
                        }
                        $up_data->client_code=$client_code;
                        $up_data->client_name=$client_name;
                        $up_data->save();
                    }

                    if ($data->trans_id==24) {  // change status
                        $first_client_id=$data->first_client_id;
                        $up_data=Client::find($first_client_id);
                        $up_data->client_type_mode=$request->change_status;
                        $up_data->save();
                    }

                    if ($data->trans_id==25) {  // Nominee Opt-Out

                        # code...
                    }

                    if ($data->trans_id==28) {  // Folio PAN Updation
                        $first_client_id=$data->first_client_id;
                        $up_data=Client::find($first_client_id);
                        $up_data->pan=$request->folio_pan;
                        $up_data->client_type='P';
                        $up_data->save();
                    }
                    if ($data->trans_id==29) {  // Redemption

                        # code...
                    }
                    if ($data->trans_id==20) {  // minor to major
                        $first_client_id=$data->first_client_id;
                        $up_data=Client::find($first_client_id);
                        $up_data->pan=$request->minor_to_major_pan;
                        $up_data->client_type='P';
                        $up_data->save();
                    }

                    if ($request->transmission_type!='' && $data->trans_id==19) {
                        $fetch_data=MutualFund::where('folio_no',$data->folio_no)
                        ->orderBy('td_mutual_fund.created_at','ASC')
                        ->get();
                        // return $fetch_data;
                        if ($request->transmission_type==1) {
                            # code...
                            // $data=MutualFund::
                        }elseif ($request->transmission_type==2) {
                            # code...
                        }

                    }

                    if ($data->trans_id==32) {  // change mode of holding
                        $fetch_folio_data=MutualFund::where('folio_no',$data->folio_no)
                            ->orderBy('td_mutual_fund.created_at','ASC')
                            ->get();

                    }

                    if ($data->trans_id==11 || $data->trans_id==21) {  // nominee 11 change & 21 addition
                        $fetch_folio_data=MutualFund::where('folio_no',$data->folio_no)
                            ->orderBy('td_mutual_fund.created_at','ASC')
                            ->get();
                    }

                    // END only for non financial changes
                }
            } else {  // Without TTIN not exist
                // return $request; // if temp tin not exist
                // return $request->sip_start_date;
                // return $tin_no;
                // craete TTIN no
                $is_has=FormReceived::orderBy('created_at','desc')->get();
                if (count($is_has)>0) {
                    $temp_tin_no=Helper::TempTINGen((count($is_has)+1),1); // generate temp tin no
                }else{
                    $temp_tin_no=Helper::TempTINGen(1,1); // generate temp tin no
                }
                $arn_no=Helper::CommonParamValue(1);
                $branch_code=1;
                $fr_data=FormReceived::create(array(
                    'rec_datetime'=>date('Y-m-d H:i:s'),
                    'temp_tin_no'=>$temp_tin_no,
                    'bu_type'=>$request->bu_type,
                    'arn_no'=>$arn_no,
                    'euin_no'=>$request->euin_no,
                    'sub_arn_no'=>isset($request->sub_arn_no)?$request->sub_arn_no:NULL,
                    'sub_brk_cd'=>isset($request->sub_brk_cd)?$request->sub_brk_cd:NULL,
                    'client_id'=>$request->first_client_id,
                    'product_id'=>1,
                    'trans_id'=>$request->trans_id,
                    'scheme_id'=>$request->scheme_id,
                    'scheme_id_to'=>isset($request->scheme_id_to)?$request->scheme_id_to:NULL,
                    'recv_from'=>$request->recv_from,
                    'inv_type'=>isset($request->inv_type)?$request->inv_type:'N',
                    'application_no'=>isset($request->application_no)?$request->application_no:NULL,
                    'kyc_status'=>isset($request->kyc_status)?$request->kyc_status:'A',
                    'branch_code'=>Helper::getBranchCode(),
                    // 'created_by'=>'',
                ));
                // return $fr_data;
                $ttin_no=$fr_data->temp_tin_no;

                    $second_client_id=$request->second_client_id;
                    $second_client_name=$request->second_client_name;
                    $second_client_pan=$request->second_client_pan;
                    if ($second_client_id=='' && $second_client_name!='' && $second_client_pan!='') {
                        $s_c_data=Client::create(array(
                            'client_name'=>$second_client_name,
                            'pan'=>$second_client_pan,
                            'client_type'=>'E',
                            // 'created_by'=>'',
                        ));
                        $second_client_id=$s_c_data->id;
                    }

                    $third_client_id=$request->third_client_id;
                    $third_client_name=$request->third_client_name;
                    $third_client_pan=$request->third_client_pan;
                    if ($third_client_id=='' && $third_client_name!='' && $third_client_pan!='') {
                        $t_c_data=Client::create(array(
                            'client_name'=>$third_client_name,
                            'pan'=>$third_client_pan,
                            'client_type'=>'E',
                            // 'created_by'=>'',
                        ));
                        $third_client_id=$t_c_data->id;
                    }

                    $app_form_scan=$request->app_form_scan;
                    $doc_name='';
                    if ($app_form_scan) {
                        $cv_path_extension=$app_form_scan->getClientOriginalExtension();
                        $doc_name=microtime(true).".".$cv_path_extension;
                        $app_form_scan->move(public_path('application-form/'),$doc_name);
                    }
                    $data=MutualFund::create(array(
                        'temp_tin_no' =>$ttin_no,
                        'tin_no'=> $tin_no,
                        'entry_tin_status'=>$request->tin_status,
                        'entry_date'=> date('Y-m-d'),
                        'first_client_id'=>$request->first_client_id,
                        'first_kyc'=>isset($request->first_kyc)?$request->first_kyc:NULL,
                        'mode_of_holding'=>(($request->mode_of_holding)?$request->mode_of_holding:(($request->change_existing_mode_of_holding)?$request->change_existing_mode_of_holding:NULL)),

                        'second_client_id'=>isset($second_client_id)?$second_client_id:NULL,
                        'second_kyc'=>isset($request->second_kyc)?$request->second_kyc:NULL,
                        'third_client_id'=>isset($third_client_id)?$third_client_id:NULL,
                        'third_kyc'=>isset($request->third_kyc)?$request->third_kyc:NULL,
                        'trans_scheme_from'=>$request->scheme_id,
                        'trans_scheme_to'=>isset($request->scheme_id_to)?$request->scheme_id_to:NULL,
                        'option_id'=>isset($request->option)?$request->option:NULL,
                        'plan_id'=>isset($request->plan)?$request->plan:NULL,
                        'option_id_to'=>isset($request->option_to)?$request->option_to:NULL,
                        'plan_id_to'=>isset($request->plan_to)?$request->plan_to:NULL,
                        'folio_no'=>isset($request->folio_no)?$request->folio_no:NULL,
                        'amount'=>(($request->amount)?$request->amount:(($request->redemp_amount)?$request->redemp_amount:(($request->swp_stp_amount)?$request->swp_stp_amount:''))),
                        'unit'=>(($request->unit)?$request->unit:(($request->redemp_unit)?$request->redemp_unit:'')),
                        'switch_by'=>isset($request->switch_by)?$request->switch_by:NULL,
                        'trans_id'=>$request->trans_id,
                        'first_inv_amount'=>isset($request->first_inv_amount)?$request->first_inv_amount:NULL,
                        'sip_type'=>isset($request->sip_type)?$request->sip_type:NULL,

                        'sip_swp_stp_duration_type'=>((isset($request->sip_duration_type))?$request->sip_duration_type:((isset($request->swp_stp_duration_type))?$request->swp_stp_duration_type:NULL)),
                        'sip_swp_stp_duration'=>(($request->sip_duration)?$request->sip_duration:(($request->swp_stp_duration)?$request->swp_stp_duration:NULL)),
                        'sip_swp_stp_frequency'=>(($request->sip_frequency)?$request->sip_frequency:(($request->swp_stp_frequency)?$request->swp_stp_frequency:NULL)),

                        'sip_swp_stp_inst_date'=>(($request->sip_date)?$request->sip_date:(($request->installment_dt)?$request->installment_dt:NULL)),

                        // 'sip_swp_stp_start_date'=>isset($request->sip_start_date)?date('Y-m-d',strtotime($request->sip_start_date)):isset($request->swp_stp_start_date)?date('Y-m-d',strtotime($request->swp_stp_start_date)):NULL,
                        'sip_swp_stp_start_date'=>(($request->sip_start_date) ? date('Y-m-d',strtotime($request->sip_start_date)):(($request->swp_stp_start_date)? date('Y-m-d',strtotime($request->swp_stp_start_date)):NULL)),
                        'sip_swp_stp_end_date'=>(($request->sip_end_date)?date('Y-m-d',strtotime($request->sip_end_date)):(($request->swp_stp_end_date)?date('Y-m-d',strtotime($request->swp_stp_end_date)):NULL)),

                        'chq_no'=>$request->chq_no,
                        'chq_bank'=>$request->chq_bank,
                        // 'rnt_login_at'=>$request->rnt_login_at,
                        'app_form_scan'=>$doc_name,
                        'form_scan_status'=>$request->form_scan_status,
                        'remarks'=>$request->remarks,
                        'form_status'=>'P',
                        'rnt_login_at'=>$request->rnt_login_at,
                        'cancel_eff_dt'=>isset($request->cancel_eff_dt)?date('Y-m-d',strtotime($request->cancel_eff_dt)):NULL,
                        'change_contact_type'=>isset($request->change_contact_type)?$request->change_contact_type:NULL,
                        'reason_for_change'=>isset($request->reason_for_change)?$request->reason_for_change:NULL,
                        'nominee_opt_out'=>isset($request->nominee_opt_out)?$request->nominee_opt_out:NULL,
                        'redemp_type'=>isset($request->redemp_type)?$request->redemp_type:NULL,
                        'redemp_unit_type'=>isset($request->redemp_unit_type)?$request->redemp_unit_type:NULL,
                        'acc_no'=>isset($request->acc_no)?$request->acc_no:NULL,
                        'acc_bank_id'=>isset($request->acc_bank_id)?$request->acc_bank_id:NULL,
                        'swp_type'=>isset($request->swp_type)?$request->swp_type:NULL,
                        'stp_type'=>isset($request->stp_type)?$request->stp_type:NULL,
                        'kyc_status'=>isset($request->kyc_status)?$request->kyc_status:NULL,
                        'transmission_type'=>isset($request->transmission_type)?$request->transmission_type:NULL,

                        'change_new_mode_of_holding'=>isset($request->change_new_mode_of_holding)?$request->change_new_mode_of_holding:NULL,
                        'mob_declaration_flag'=>isset($request->mob_dec)?$request->mob_dec:NULL,
                        'email_declaration_flag'=>isset($request->email_dec)?$request->email_dec:NULL,
                        'merge_folio'=>isset($request->merge_folio)?$request->merge_folio:NULL,
                        'new_nominee'=>isset($request->new_nominee)?$request->new_nominee:NULL,

                        // for special sip
                        'swp_frequency'=>isset($request->swp_frequency)?$request->swp_frequency:NULL,
                        'swp_start_date'=>isset($request->swp_start_date)?date('Y-m-d',strtotime($request->swp_start_date)):NULL,
                        'swp_duration'=>isset($request->swp_duration)?$request->swp_duration:NULL,
                        'swp_end_date'=>isset($request->swp_end_date)?date('Y-m-d',strtotime($request->swp_end_date)):NULL,
                        'swp_inst_amount'=>isset($request->swp_inst_amount)?$request->swp_inst_amount:NULL,
                        // for step up sip
                        'step_up_by'=>isset($request->step_up_by)?$request->step_up_by:'N',
                        'step_up_amount'=>isset($request->step_up_amount)?$request->step_up_amount:NULL,
                        'step_up_percentage'=>isset($request->step_up_percentage)?$request->step_up_percentage:NULL,

                    ));

                // START only for non financial changes
                if ($request->change_contact_type!='') {  // client update
                    $first_client_id=$data->first_client_id;
                    $up_data=Client::find($first_client_id);
                    if ($request->email) {
                        $up_data->email=$request->email;
                        $up_data->save();
                    }
                    if ($request->mobile) {
                        $up_data->mobile=$request->mobile;
                        $up_data->save();
                    }
                }

                if ($data->trans_id==22) {  // address change
                    $first_client_id=$data->first_client_id;
                    $up_data=Client::find($first_client_id);
                    $up_data->add_line_1=$request->add_line_1;
                    $up_data->add_line_2=$request->add_line_2;
                    $up_data->city=$request->city;
                    $up_data->dist=$request->dist;
                    $up_data->state=$request->state;
                    $up_data->pincode=$request->pincode;
                    $up_data->country_id=$request->country_id;
                    $up_data->save();
                }

                if ($data->trans_id==23) {  // name change
                    $first_client_id=$data->first_client_id;
                    $up_data=Client::find($first_client_id);
                    $client_name=ucwords($request->new_name);
                    $words = explode(" ",$client_name);
                    $client_code="";
                    $client_code_1 = mb_substr($words[0], 0, 1).mb_substr($words[(count($words)-1)], 0, 1);;
                    $is_has=Client::where('client_code',$client_code_1)->get();
                    if (count($is_has)>0) {
                        $client_code=$client_code_1.date('dmy',strtotime($up_data->dob)).count($is_has);
                    }else {
                        $client_code=$client_code_1.date('dmy',strtotime($up_data->dob));
                    }
                    $up_data->client_code=$client_code;
                    $up_data->client_name=$client_name;
                    $up_data->save();
                }

                if ($data->trans_id==24) {  // change status
                    $first_client_id=$data->first_client_id;
                    $up_data=Client::find($first_client_id);
                    $up_data->client_type_mode=$request->change_status;
                    $up_data->save();
                }

                if ($data->trans_id==25) {  // Nominee Opt-Out

                    # code...
                }

                if ($data->trans_id==28) {  // Folio PAN Updation
                    $first_client_id=$data->first_client_id;
                    $up_data=Client::find($first_client_id);
                    $up_data->pan=$request->folio_pan;
                    $up_data->client_type='P';
                    $up_data->save();
                }
                if ($data->trans_id==29) {  // Redemption

                    # code...
                }
                if ($data->trans_id==20) {  // minor to major
                    $first_client_id=$data->first_client_id;
                    $up_data=Client::find($first_client_id);
                    $up_data->pan=$request->minor_to_major_pan;
                    $up_data->client_type='P';
                }

                if ($request->transmission_type!='' && $data->trans_id==19) {
                    $fetch_data=MutualFund::where('folio_no',$data->folio_no)
                    ->orderBy('td_mutual_fund.created_at','ASC')
                    ->get();
                    // return $fetch_data;
                    if ($request->transmission_type==1) {
                        # code...
                        // $data=MutualFund::
                    }elseif ($request->transmission_type==2) {
                        # code...
                    }

                }

                if ($data->trans_id==32) {  // change mode of holding
                    $fetch_folio_data=MutualFund::where('folio_no',$data->folio_no)
                        ->orderBy('td_mutual_fund.created_at','ASC')
                        ->get();

                }

                if ($data->trans_id==11 || $data->trans_id==21) {  // nominee 11 change & 21 addition
                    $fetch_folio_data=MutualFund::where('folio_no',$data->folio_no)
                        ->orderBy('td_mutual_fund.created_at','ASC')
                        ->get();
                }

                if($data->trans_id == 18){
                    $up_data=Client::find($first_client_id);
                    $up_data->email = ($request->change_contact_type == 'B' || $request->change_contact_type == 'E') ? $request->email : $up_data->email;
                    $up_data->mobile = ($request->change_contact_type == 'B' || $request->change_contact_type == 'M') ? $request->mobile : $up_data->mobile;
                    $up_data->save();
                }




                // END only for non financial changes
            }
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function getFolioDetails(Request $request)
    {
        try {
            $folio_no=$request->folio_no;
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
                        ->leftJoin('md_employee','md_employee.euin_no','=','td_form_received.euin_no')
                        ->leftJoin('md_branch','md_branch.id','=','td_form_received.branch_code')
                        ->select('td_mutual_fund.*','md_trans.trns_name as trans_name','md_trans.trans_type_id as trans_type_id','td_form_received.application_no as application_no',
                        'td_form_received.bu_type as bu_type','td_form_received.inv_type as inv_type','md_scheme.scheme_name as scheme_name','md_scheme.id as scheme_id','md_scheme_2.scheme_name as scheme_name_to','md_scheme_2.id as scheme_id_to',
                        'md_client.client_code as first_client_code','md_client.client_name as first_client_name','md_client.pan as first_client_pan','md_client.client_type as first_client_type',
                        'md_client_2.client_code as second_client_code','md_client_2.client_name as second_client_name','md_client_2.pan as second_client_pan','md_client_2.client_type as second_client_type',
                        'md_plan.plan_name as plan_name','md_option.opt_name as opt_name','md_plan_2.plan_name as plan_name_to','md_option_2.opt_name as opt_name_to',
                        'md_rnt.rnt_name as rnt_name','td_form_received.arn_no as arn_no','td_form_received.euin_no as euin_no','md_branch.brn_name as branch_name',
                        'md_deposit_bank.bank_name as bank_name','md_deposit_bank.ifs_code as ifs_code','md_deposit_bank.micr_code as micr_code','md_deposit_bank.branch_name as chq_branch_name','md_deposit_bank.branch_addr as chq_branch_addr',
                        'md_employee.emp_name as emp_name')
                        ->where('td_mutual_fund.folio_no',$folio_no)
                        // ->orderBy('td_mutual_fund.created_at','ASC')
                        ->get();
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
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
                $ack_copy_scan_name=microtime(true).".".$path_extension;
                $ack_copy_scan->move(public_path('acknowledgement-copy/'),$ack_copy_scan_name);
            }else{
                $ack_copy_scan_name=$data1->ack_copy_scan;
                // return $doc_name;
            }
            // ack_copy_scan
                MutualFund::where('tin_no',$request->tin_no)->update(array(
                    'rnt_login_dt'=>date('Y-m-d',strtotime($request->rnt_login_date)),
                    'rnt_login_cutt_off'=>Carbon::parse($request->rnt_login_cutt_off)->format('Y-m-d H:i:s'),
                    'ack_copy_scan'=>$ack_copy_scan_name,
                    'form_status'=>'A',
                ));
            // return $data1;
            $data=MutualFund::where('tin_no',$request->tin_no)->first();
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function updateOld(Request $request)
    {
        return $request;
        try {
            // return $request;
            $data1=MutualFund::where('tin_no',$request->tin_no)->first();
            $app_form_scan=$request->app_form_scan;
            if ($app_form_scan) {
                $cv_path_extension=$app_form_scan->getClientOriginalExtension();
                $doc_name=microtime(true).".".$cv_path_extension;
                $app_form_scan->move(public_path('acknowledgement-copy/'),$doc_name);
            }else{
                $doc_name=$data1->app_form_scan;
                // return $doc_name;
            }
            // ack_copy_scan
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

    public function filterCriteria($rawQuery,$from_date,$to_date,$tin_no,$client_code,$amc_name,$scheme_name,$rnt_name)
    {
        $queryString='td_mutual_fund.entry_date';
        $rawQuery.=Helper::FrmToDateRawQuery($from_date,$to_date,$rawQuery,$queryString);
        $queryString1='td_mutual_fund.tin_no';
        $rawQuery.=Helper::WhereRawQuery($tin_no,$rawQuery,$queryString1);
        $queryString2='td_mutual_fund.first_client_id';
        $rawQuery.=Helper::WhereRawQuery($client_code,$rawQuery,$queryString2);
        $queryString3='md_scheme.amc_id';
        $rawQuery.=Helper::WhereRawQuery($amc_name,$rawQuery,$queryString3);
        $queryString4='td_mutual_fund.trans_scheme_from';
        $rawQuery.=Helper::WhereRawQuery($scheme_name,$rawQuery,$queryString4);
        $queryString5='td_mutual_fund.rnt_login_at';
        $rawQuery.=Helper::WhereRawQuery($rnt_name,$rawQuery,$queryString5);
        return $rawQuery;
    }
}
