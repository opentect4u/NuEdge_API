<?php

namespace App\Http\Controllers\v1\FDOperation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{InsProduct,FixedDeposit,FDFormReceived};
use Validator;

class FormEntryController extends Controller
{
    //
    public function searchDetails(Request $request)
    {
        try {
            $paginate=$request->paginate;
            $field=$request->field;
            $order=$request->order;

            $from_date=$request->from_date;
            $to_date=$request->to_date;
            $investor_code=$request->investor_code;
            $fd_bu_type=json_decode($request->fd_bu_type);
            $euin_no=json_decode($request->euin_no);
            $brn_cd=json_decode($request->brn_cd);
            $rm_id=json_decode($request->rm_id);
            $sub_brk_cd=json_decode($request->sub_brk_cd);
            
            $tin_no=$request->tin_no;
            $option=$request->option;
            $investor_name=$request->investor_name;
            $bu_type=json_decode($request->bu_type);
            $ins_type_id=json_decode($request->ins_type_id);
            $insured_bu_type=json_decode($request->insured_bu_type);
            $company_id=json_decode($request->company_id);
            $comp_type_id=json_decode($request->comp_type_id);
            $scheme_id=json_decode($request->scheme_id);

            $login_status=$request->login_status;
            $date_status=$request->date_status;
            $start_date=$request->start_date;
            $end_date=$request->end_date;

            if ($paginate=='A') {
                $paginate=999999999;
            }

            if ($option==3) {
                if ($login_status=='L') {
                    $login_status="!=";
                }else{
                    $login_status="=";
                }

                if ($date_status=='T') {
                    if ($field && $order) {
                        $rawOrderBy='';
                        if ($order > 0) {
                            $rawOrderBy=$field.' ASC';
                        } else {
                            $rawOrderBy=$field.' DESC';
                        }
                        $data=FixedDeposit::join('td_fd_form_received','td_fd_form_received.temp_tin_no','=','td_fixed_deposit.temp_tin_no')
                            ->leftJoin('md_fd_scheme','md_fd_scheme.id','=','td_fixed_deposit.scheme_id')
                            ->leftJoin('md_fd_type_of_company','md_fd_type_of_company.id','=','md_fd_scheme.comp_type_id')
                            ->leftJoin('md_fd_company','md_fd_company.id','=','md_fd_scheme.comp_id')
                            ->leftJoin('md_fd_company as md_fd_company_2','md_fd_company_2.id','=','td_fixed_deposit.comp_login_at')
                            ->leftJoin('md_client','md_client.id','=','td_fixed_deposit.first_client_id')
                            ->leftJoin('md_client as md_client_2','md_client_2.id','=','td_fixed_deposit.second_client_id')
                            ->leftJoin('md_client as md_client_3','md_client_3.id','=','td_fixed_deposit.third_client_id')
                            ->leftJoin('md_deposit_bank','md_deposit_bank.id','=','td_fixed_deposit.chq_bank')
                            ->leftJoin('md_employee','md_employee.euin_no','=','td_fd_form_received.euin_no')
                            ->leftJoin('md_branch','md_branch.id','=','td_fd_form_received.branch_code')
                            ->select('td_fixed_deposit.*','td_fd_form_received.bu_type as bu_type','td_fd_form_received.arn_no as arn_no','td_fd_form_received.euin_no as euin_no','td_fd_form_received.fd_bu_type as fd_bu_type','td_fd_form_received.sub_brk_cd as sub_brk_cd','md_employee.emp_name as emp_name',
                            'md_fd_scheme.scheme_name as scheme_name','md_fd_type_of_company.comp_type as comp_type_name','md_fd_company.comp_short_name as comp_short_name','md_fd_company.comp_full_name as comp_full_name',
                            'md_client.client_code as investor_code','md_client.client_name as investor_name','md_client.pan as investor_pan','md_client.dob as investor_dob',
                            'md_client_2.client_code as investor_code_2','md_client_2.client_name as investor_name_2','md_client_2.pan as investor_pan_2','md_client_2.dob as investor_dob_2',
                            'md_client_3.client_code as investor_code_3','md_client_3.client_name as investor_name_3','md_client_3.pan as investor_pan_3','md_client_3.dob as investor_dob_3',
                            'md_deposit_bank.bank_name as chq_bank','md_fd_company_2.comp_short_name as comp_login_name','md_branch.brn_name as branch_name')
                            ->where('td_fixed_deposit.delete_flag','N')
                            ->whereDate('td_fixed_deposit.entry_date',date('Y-m-d'))
                            ->where('td_fixed_deposit.comp_login_dt',$login_status,NULL)
                            ->where('td_fixed_deposit.comp_login_cutt_off',$login_status,NULL)
                            ->orderByRaw($rawOrderBy)
                            ->paginate($paginate);
                    }else {
                        $data=FixedDeposit::join('td_fd_form_received','td_fd_form_received.temp_tin_no','=','td_fixed_deposit.temp_tin_no')
                            ->leftJoin('md_fd_scheme','md_fd_scheme.id','=','td_fixed_deposit.scheme_id')
                            ->leftJoin('md_fd_type_of_company','md_fd_type_of_company.id','=','md_fd_scheme.comp_type_id')
                            ->leftJoin('md_fd_company','md_fd_company.id','=','md_fd_scheme.comp_id')
                            ->leftJoin('md_fd_company as md_fd_company_2','md_fd_company_2.id','=','td_fixed_deposit.comp_login_at')
                            ->leftJoin('md_client','md_client.id','=','td_fixed_deposit.first_client_id')
                            ->leftJoin('md_client as md_client_2','md_client_2.id','=','td_fixed_deposit.second_client_id')
                            ->leftJoin('md_client as md_client_3','md_client_3.id','=','td_fixed_deposit.third_client_id')
                            ->leftJoin('md_deposit_bank','md_deposit_bank.id','=','td_fixed_deposit.chq_bank')
                            ->leftJoin('md_employee','md_employee.euin_no','=','td_fd_form_received.euin_no')
                            ->leftJoin('md_branch','md_branch.id','=','td_fd_form_received.branch_code')
                            ->select('td_fixed_deposit.*','td_fd_form_received.bu_type as bu_type','td_fd_form_received.arn_no as arn_no','td_fd_form_received.euin_no as euin_no','td_fd_form_received.fd_bu_type as fd_bu_type','td_fd_form_received.sub_brk_cd as sub_brk_cd','md_employee.emp_name as emp_name',
                            'md_fd_scheme.scheme_name as scheme_name','md_fd_type_of_company.comp_type as comp_type_name','md_fd_company.comp_short_name as comp_short_name','md_fd_company.comp_full_name as comp_full_name',
                            'md_client.client_code as investor_code','md_client.client_name as investor_name','md_client.pan as investor_pan','md_client.dob as investor_dob',
                            'md_client_2.client_code as investor_code_2','md_client_2.client_name as investor_name_2','md_client_2.pan as investor_pan_2','md_client_2.dob as investor_dob_2',
                            'md_client_3.client_code as investor_code_3','md_client_3.client_name as investor_name_3','md_client_3.pan as investor_pan_3','md_client_3.dob as investor_dob_3',
                            'md_deposit_bank.bank_name as chq_bank','md_fd_company_2.comp_short_name as comp_login_name','md_branch.brn_name as branch_name')
                            ->where('td_fixed_deposit.delete_flag','N')
                            ->whereDate('td_fixed_deposit.entry_date',date('Y-m-d'))
                            ->where('td_fixed_deposit.comp_login_dt',$login_status,NULL)
                            ->where('td_fixed_deposit.comp_login_cutt_off',$login_status,NULL)
                            ->orderBy('td_fixed_deposit.updated_at','desc')
                            ->paginate($paginate);
                    }
                }else {
                    if ($field && $order) {
                        $rawOrderBy='';
                        if ($order > 0) {
                            $rawOrderBy=$field.' ASC';
                        } else {
                            $rawOrderBy=$field.' DESC';
                        }
                        $data=FixedDeposit::join('td_fd_form_received','td_fd_form_received.temp_tin_no','=','td_fixed_deposit.temp_tin_no')
                            ->leftJoin('md_fd_scheme','md_fd_scheme.id','=','td_fixed_deposit.scheme_id')
                            ->leftJoin('md_fd_type_of_company','md_fd_type_of_company.id','=','md_fd_scheme.comp_type_id')
                            ->leftJoin('md_fd_company','md_fd_company.id','=','md_fd_scheme.comp_id')
                            ->leftJoin('md_fd_company as md_fd_company_2','md_fd_company_2.id','=','td_fixed_deposit.comp_login_at')
                            ->leftJoin('md_client','md_client.id','=','td_fixed_deposit.first_client_id')
                            ->leftJoin('md_client as md_client_2','md_client_2.id','=','td_fixed_deposit.second_client_id')
                            ->leftJoin('md_client as md_client_3','md_client_3.id','=','td_fixed_deposit.third_client_id')
                            ->leftJoin('md_deposit_bank','md_deposit_bank.id','=','td_fixed_deposit.chq_bank')
                            ->leftJoin('md_employee','md_employee.euin_no','=','td_fd_form_received.euin_no')
                            ->leftJoin('md_branch','md_branch.id','=','td_fd_form_received.branch_code')
                            ->select('td_fixed_deposit.*','td_fd_form_received.bu_type as bu_type','td_fd_form_received.arn_no as arn_no','td_fd_form_received.euin_no as euin_no','td_fd_form_received.fd_bu_type as fd_bu_type','td_fd_form_received.sub_brk_cd as sub_brk_cd','md_employee.emp_name as emp_name',
                            'md_fd_scheme.scheme_name as scheme_name','md_fd_type_of_company.comp_type as comp_type_name','md_fd_company.comp_short_name as comp_short_name','md_fd_company.comp_full_name as comp_full_name',
                            'md_client.client_code as investor_code','md_client.client_name as investor_name','md_client.pan as investor_pan','md_client.dob as investor_dob',
                            'md_client_2.client_code as investor_code_2','md_client_2.client_name as investor_name_2','md_client_2.pan as investor_pan_2','md_client_2.dob as investor_dob_2',
                            'md_client_3.client_code as investor_code_3','md_client_3.client_name as investor_name_3','md_client_3.pan as investor_pan_3','md_client_3.dob as investor_dob_3',
                            'md_deposit_bank.bank_name as chq_bank','md_fd_company_2.comp_short_name as comp_login_name','md_branch.brn_name as branch_name')
                            ->where('td_fixed_deposit.delete_flag','N')
                            ->whereDate('td_fixed_deposit.entry_date','>=',date('Y-m-d',strtotime($start_date)))
                            ->whereDate('td_fixed_deposit.entry_date','<=',date('Y-m-d',strtotime($end_date)))
                            ->where('td_fixed_deposit.comp_login_dt',$login_status,NULL)
                            ->where('td_fixed_deposit.comp_login_cutt_off',$login_status,NULL)
                            ->orderByRaw($rawOrderBy)
                            ->paginate($paginate);
                    }else {
                        $data=FixedDeposit::join('td_fd_form_received','td_fd_form_received.temp_tin_no','=','td_fixed_deposit.temp_tin_no')
                            ->leftJoin('md_fd_scheme','md_fd_scheme.id','=','td_fixed_deposit.scheme_id')
                            ->leftJoin('md_fd_type_of_company','md_fd_type_of_company.id','=','md_fd_scheme.comp_type_id')
                            ->leftJoin('md_fd_company','md_fd_company.id','=','md_fd_scheme.comp_id')
                            ->leftJoin('md_fd_company as md_fd_company_2','md_fd_company_2.id','=','td_fixed_deposit.comp_login_at')
                            ->leftJoin('md_client','md_client.id','=','td_fixed_deposit.first_client_id')
                            ->leftJoin('md_client as md_client_2','md_client_2.id','=','td_fixed_deposit.second_client_id')
                            ->leftJoin('md_client as md_client_3','md_client_3.id','=','td_fixed_deposit.third_client_id')
                            ->leftJoin('md_deposit_bank','md_deposit_bank.id','=','td_fixed_deposit.chq_bank')
                            ->leftJoin('md_employee','md_employee.euin_no','=','td_fd_form_received.euin_no')
                            ->leftJoin('md_branch','md_branch.id','=','td_fd_form_received.branch_code')
                            ->select('td_fixed_deposit.*','td_fd_form_received.bu_type as bu_type','td_fd_form_received.arn_no as arn_no','td_fd_form_received.euin_no as euin_no','td_fd_form_received.fd_bu_type as fd_bu_type','td_fd_form_received.sub_brk_cd as sub_brk_cd','md_employee.emp_name as emp_name',
                            'md_fd_scheme.scheme_name as scheme_name','md_fd_type_of_company.comp_type as comp_type_name','md_fd_company.comp_short_name as comp_short_name','md_fd_company.comp_full_name as comp_full_name',
                            'md_client.client_code as investor_code','md_client.client_name as investor_name','md_client.pan as investor_pan','md_client.dob as investor_dob',
                            'md_client_2.client_code as investor_code_2','md_client_2.client_name as investor_name_2','md_client_2.pan as investor_pan_2','md_client_2.dob as investor_dob_2',
                            'md_client_3.client_code as investor_code_3','md_client_3.client_name as investor_name_3','md_client_3.pan as investor_pan_3','md_client_3.dob as investor_dob_3',
                            'md_deposit_bank.bank_name as chq_bank','md_fd_company_2.comp_short_name as comp_login_name','md_branch.brn_name as branch_name')
                            ->where('td_fixed_deposit.delete_flag','N')
                            ->whereDate('td_fixed_deposit.entry_date','>=',date('Y-m-d',strtotime($start_date)))
                            ->whereDate('td_fixed_deposit.entry_date','<=',date('Y-m-d',strtotime($end_date)))
                            ->where('td_fixed_deposit.comp_login_dt',$login_status,NULL)
                            ->where('td_fixed_deposit.comp_login_cutt_off',$login_status,NULL)
                            ->orderBy('td_fixed_deposit.updated_at','desc')
                            ->paginate($paginate);
                    }
                }
            }else {
                if ($order && $field) {
                    $rawOrderBy='';
                    if ($order > 0) {
                        $rawOrderBy=$field.' ASC';
                    } else {
                        $rawOrderBy=$field.' DESC';
                    }
                    if (($from_date && $to_date) || $tin_no || $investor_code || $company_id || $comp_type_id || $scheme_id) {
                        $rawQuery='';
                        $queryString='td_fixed_deposit.entry_date';
                        $rawQuery.=Helper::FrmToDateRawQuery($from_date,$to_date,$rawQuery,$queryString);
                        $queryString1='td_fixed_deposit.tin_no';
                        $rawQuery.=Helper::WhereRawQuery($tin_no,$rawQuery,$queryString1);
                        // return $rawQuery;
                        $queryString2='td_fixed_deposit.first_client_id';
                        $rawQuery.=Helper::WhereRawQuery($investor_code,$rawQuery,$queryString2);
                        $queryString3='md_fd_scheme.comp_id';
                        $rawQuery.=Helper::WhereRawQuery($company_id,$rawQuery,$queryString3);
                        $queryString4='md_fd_scheme.comp_type_id';
                        $rawQuery.=Helper::WhereRawQuery($comp_type_id,$rawQuery,$queryString4);
                        $queryString5='td_fixed_deposit.scheme_id';
                        $rawQuery.=Helper::WhereRawQuery($scheme_id,$rawQuery,$queryString5);
    
                        $data=FixedDeposit::join('td_fd_form_received','td_fd_form_received.temp_tin_no','=','td_fixed_deposit.temp_tin_no')
                            ->leftJoin('md_fd_scheme','md_fd_scheme.id','=','td_fixed_deposit.scheme_id')
                            ->leftJoin('md_fd_type_of_company','md_fd_type_of_company.id','=','md_fd_scheme.comp_type_id')
                            ->leftJoin('md_fd_company','md_fd_company.id','=','md_fd_scheme.comp_id')
                            ->leftJoin('md_fd_company as md_fd_company_2','md_fd_company_2.id','=','td_fixed_deposit.comp_login_at')
                            ->leftJoin('md_client','md_client.id','=','td_fixed_deposit.first_client_id')
                            ->leftJoin('md_client as md_client_2','md_client_2.id','=','td_fixed_deposit.second_client_id')
                            ->leftJoin('md_client as md_client_3','md_client_3.id','=','td_fixed_deposit.third_client_id')
                            ->leftJoin('md_deposit_bank','md_deposit_bank.id','=','td_fixed_deposit.chq_bank')
                            ->leftJoin('md_employee','md_employee.euin_no','=','td_fd_form_received.euin_no')
                            ->leftJoin('md_branch','md_branch.id','=','td_fd_form_received.branch_code')
                            ->select('td_fixed_deposit.*','td_fd_form_received.bu_type as bu_type','td_fd_form_received.arn_no as arn_no','td_fd_form_received.euin_no as euin_no','td_fd_form_received.fd_bu_type as fd_bu_type','td_fd_form_received.sub_brk_cd as sub_brk_cd','md_employee.emp_name as emp_name',
                            'md_fd_scheme.scheme_name as scheme_name','md_fd_type_of_company.comp_type as comp_type_name','md_fd_company.comp_short_name as comp_short_name','md_fd_company.comp_full_name as comp_full_name',
                            'md_client.client_code as investor_code','md_client.client_name as investor_name','md_client.pan as investor_pan','md_client.dob as investor_dob',
                            'md_client_2.client_code as investor_code_2','md_client_2.client_name as investor_name_2','md_client_2.pan as investor_pan_2','md_client_2.dob as investor_dob_2',
                            'md_client_3.client_code as investor_code_3','md_client_3.client_name as investor_name_3','md_client_3.pan as investor_pan_3','md_client_3.dob as investor_dob_3',
                            'md_deposit_bank.bank_name as chq_bank','md_fd_company_2.comp_short_name as comp_login_name','md_branch.brn_name as branch_name')
                            ->where('td_fixed_deposit.delete_flag','N')
                            ->whereRaw($rawQuery)
                            ->orderByRaw($rawOrderBy)
                            ->paginate($paginate);
                    } else {
                        $data=FixedDeposit::join('td_fd_form_received','td_fd_form_received.temp_tin_no','=','td_fixed_deposit.temp_tin_no')
                            ->leftJoin('md_fd_scheme','md_fd_scheme.id','=','td_fixed_deposit.scheme_id')
                            ->leftJoin('md_fd_type_of_company','md_fd_type_of_company.id','=','md_fd_scheme.comp_type_id')
                            ->leftJoin('md_fd_company','md_fd_company.id','=','md_fd_scheme.comp_id')
                            ->leftJoin('md_fd_company as md_fd_company_2','md_fd_company_2.id','=','td_fixed_deposit.comp_login_at')
                            ->leftJoin('md_client','md_client.id','=','td_fixed_deposit.first_client_id')
                            ->leftJoin('md_client as md_client_2','md_client_2.id','=','td_fixed_deposit.second_client_id')
                            ->leftJoin('md_client as md_client_3','md_client_3.id','=','td_fixed_deposit.third_client_id')
                            ->leftJoin('md_deposit_bank','md_deposit_bank.id','=','td_fixed_deposit.chq_bank')
                            ->leftJoin('md_employee','md_employee.euin_no','=','td_fd_form_received.euin_no')
                            ->leftJoin('md_branch','md_branch.id','=','td_fd_form_received.branch_code')
                            ->select('td_fixed_deposit.*','td_fd_form_received.bu_type as bu_type','td_fd_form_received.arn_no as arn_no','td_fd_form_received.euin_no as euin_no','td_fd_form_received.fd_bu_type as fd_bu_type','td_fd_form_received.sub_brk_cd as sub_brk_cd','md_employee.emp_name as emp_name',
                            'md_fd_scheme.scheme_name as scheme_name','md_fd_type_of_company.comp_type as comp_type_name','md_fd_company.comp_short_name as comp_short_name','md_fd_company.comp_full_name as comp_full_name',
                            'md_client.client_code as investor_code','md_client.client_name as investor_name','md_client.pan as investor_pan','md_client.dob as investor_dob',
                            'md_client_2.client_code as investor_code_2','md_client_2.client_name as investor_name_2','md_client_2.pan as investor_pan_2','md_client_2.dob as investor_dob_2',
                            'md_client_3.client_code as investor_code_3','md_client_3.client_name as investor_name_3','md_client_3.pan as investor_pan_3','md_client_3.dob as investor_dob_3',
                            'md_deposit_bank.bank_name as chq_bank','md_fd_company_2.comp_short_name as comp_login_name','md_branch.brn_name as branch_name')
                            ->where('td_fixed_deposit.delete_flag','N')
                            ->orderByRaw($rawOrderBy)
                            ->paginate($paginate);
                    }
                }elseif (($from_date && $to_date) || $tin_no || $investor_code || $company_id || $comp_type_id || $scheme_id) {
                    $rawQuery='';
                    $queryString='td_fixed_deposit.entry_date';
                    $rawQuery.=Helper::FrmToDateRawQuery($from_date,$to_date,$rawQuery,$queryString);
                    $queryString1='td_fixed_deposit.tin_no';
                    $rawQuery.=Helper::WhereRawQuery($tin_no,$rawQuery,$queryString1);
                    // return $rawQuery;
                    $queryString2='td_fixed_deposit.first_client_id';
                    $rawQuery.=Helper::WhereRawQuery($investor_code,$rawQuery,$queryString2);
                    $queryString3='md_fd_scheme.comp_id';
                    $rawQuery.=Helper::WhereRawQuery($company_id,$rawQuery,$queryString3);
                    $queryString4='md_fd_scheme.comp_type_id';
                    $rawQuery.=Helper::WhereRawQuery($comp_type_id,$rawQuery,$queryString4);
                    $queryString5='td_fixed_deposit.scheme_id';
                    $rawQuery.=Helper::WhereRawQuery($scheme_id,$rawQuery,$queryString5);

                    $data=FixedDeposit::join('td_fd_form_received','td_fd_form_received.temp_tin_no','=','td_fixed_deposit.temp_tin_no')
                        ->leftJoin('md_fd_scheme','md_fd_scheme.id','=','td_fixed_deposit.scheme_id')
                        ->leftJoin('md_fd_type_of_company','md_fd_type_of_company.id','=','md_fd_scheme.comp_type_id')
                        ->leftJoin('md_fd_company','md_fd_company.id','=','md_fd_scheme.comp_id')
                        ->leftJoin('md_fd_company as md_fd_company_2','md_fd_company_2.id','=','td_fixed_deposit.comp_login_at')
                        ->leftJoin('md_client','md_client.id','=','td_fixed_deposit.first_client_id')
                        ->leftJoin('md_client as md_client_2','md_client_2.id','=','td_fixed_deposit.second_client_id')
                        ->leftJoin('md_client as md_client_3','md_client_3.id','=','td_fixed_deposit.third_client_id')
                        ->leftJoin('md_deposit_bank','md_deposit_bank.id','=','td_fixed_deposit.chq_bank')
                        ->leftJoin('md_employee','md_employee.euin_no','=','td_fd_form_received.euin_no')
                        ->leftJoin('md_branch','md_branch.id','=','td_fd_form_received.branch_code')
                        ->select('td_fixed_deposit.*','td_fd_form_received.bu_type as bu_type','td_fd_form_received.arn_no as arn_no','td_fd_form_received.euin_no as euin_no','td_fd_form_received.fd_bu_type as fd_bu_type','td_fd_form_received.sub_brk_cd as sub_brk_cd','md_employee.emp_name as emp_name',
                        'md_fd_scheme.scheme_name as scheme_name','md_fd_type_of_company.comp_type as comp_type_name','md_fd_company.comp_short_name as comp_short_name','md_fd_company.comp_full_name as comp_full_name',
                        'md_client.client_code as investor_code','md_client.client_name as investor_name','md_client.pan as investor_pan','md_client.dob as investor_dob',
                        'md_client_2.client_code as investor_code_2','md_client_2.client_name as investor_name_2','md_client_2.pan as investor_pan_2','md_client_2.dob as investor_dob_2',
                        'md_client_3.client_code as investor_code_3','md_client_3.client_name as investor_name_3','md_client_3.pan as investor_pan_3','md_client_3.dob as investor_dob_3',
                        'md_deposit_bank.bank_name as chq_bank','md_fd_company_2.comp_short_name as comp_login_name','md_branch.brn_name as branch_name')
                        ->where('td_fixed_deposit.delete_flag','N')
                        ->whereRaw($rawQuery)
                        ->orderBy('td_fixed_deposit.updated_at','desc')
                        ->paginate($paginate);
                } else {
                    $data=FixedDeposit::join('td_fd_form_received','td_fd_form_received.temp_tin_no','=','td_fixed_deposit.temp_tin_no')
                        ->leftJoin('md_fd_scheme','md_fd_scheme.id','=','td_fixed_deposit.scheme_id')
                        ->leftJoin('md_fd_type_of_company','md_fd_type_of_company.id','=','md_fd_scheme.comp_type_id')
                        ->leftJoin('md_fd_company','md_fd_company.id','=','md_fd_scheme.comp_id')
                        ->leftJoin('md_fd_company as md_fd_company_2','md_fd_company_2.id','=','td_fixed_deposit.comp_login_at')
                        ->leftJoin('md_client','md_client.id','=','td_fixed_deposit.first_client_id')
                        ->leftJoin('md_client as md_client_2','md_client_2.id','=','td_fixed_deposit.second_client_id')
                        ->leftJoin('md_client as md_client_3','md_client_3.id','=','td_fixed_deposit.third_client_id')
                        ->leftJoin('md_deposit_bank','md_deposit_bank.id','=','td_fixed_deposit.chq_bank')
                        ->leftJoin('md_employee','md_employee.euin_no','=','td_fd_form_received.euin_no')
                        ->leftJoin('md_branch','md_branch.id','=','td_fd_form_received.branch_code')
                        ->select('td_fixed_deposit.*','td_fd_form_received.bu_type as bu_type','td_fd_form_received.arn_no as arn_no','td_fd_form_received.euin_no as euin_no','td_fd_form_received.fd_bu_type as fd_bu_type','td_fd_form_received.sub_brk_cd as sub_brk_cd','md_employee.emp_name as emp_name',
                        'md_fd_scheme.scheme_name as scheme_name','md_fd_type_of_company.comp_type as comp_type_name','md_fd_company.comp_short_name as comp_short_name','md_fd_company.comp_full_name as comp_full_name',
                        'md_client.client_code as investor_code','md_client.client_name as investor_name','md_client.pan as investor_pan','md_client.dob as investor_dob',
                        'md_client_2.client_code as investor_code_2','md_client_2.client_name as investor_name_2','md_client_2.pan as investor_pan_2','md_client_2.dob as investor_dob_2',
                        'md_client_3.client_code as investor_code_3','md_client_3.client_name as investor_name_3','md_client_3.pan as investor_pan_3','md_client_3.dob as investor_dob_3',
                        'md_deposit_bank.bank_name as chq_bank','md_fd_company_2.comp_short_name as comp_login_name','md_branch.brn_name as branch_name')
                        ->where('td_fixed_deposit.delete_flag','N')
                        ->orderBy('td_fixed_deposit.updated_at','desc')
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
            $paginate=$request->paginate;
            $field=$request->field;
            $order=$request->order;

            $from_date=$request->from_date;
            $to_date=$request->to_date;
            $investor_code=$request->investor_code;
            $fd_bu_type=json_decode($request->fd_bu_type);
            $euin_no=json_decode($request->euin_no);
            $brn_cd=json_decode($request->brn_cd);
            $rm_id=json_decode($request->rm_id);
            $sub_brk_cd=json_decode($request->sub_brk_cd);
            
            $tin_no=$request->tin_no;
            $option=$request->option;
            $investor_name=$request->investor_name;
            $bu_type=json_decode($request->bu_type);
            $ins_type_id=json_decode($request->ins_type_id);
            $insured_bu_type=json_decode($request->insured_bu_type);
            $company_id=json_decode($request->company_id);
            $comp_type_id=json_decode($request->comp_type_id);
            $scheme_id=json_decode($request->scheme_id);

            $login_status=$request->login_status;
            $date_status=$request->date_status;
            $start_date=$request->start_date;
            $end_date=$request->end_date;

            if ($paginate=='A') {
                $paginate=999999999;
            }

            if ($option==3) {
                if ($login_status=='L') {
                    $login_status="!=";
                }else{
                    $login_status="=";
                }

                if ($date_status=='T') {
                    if ($field && $order) {
                        $rawOrderBy='';
                        if ($order > 0) {
                            $rawOrderBy=$field.' ASC';
                        } else {
                            $rawOrderBy=$field.' DESC';
                        }
                        $data=FixedDeposit::join('td_fd_form_received','td_fd_form_received.temp_tin_no','=','td_fixed_deposit.temp_tin_no')
                            ->leftJoin('md_fd_scheme','md_fd_scheme.id','=','td_fixed_deposit.scheme_id')
                            ->leftJoin('md_fd_type_of_company','md_fd_type_of_company.id','=','md_fd_scheme.comp_type_id')
                            ->leftJoin('md_fd_company','md_fd_company.id','=','md_fd_scheme.comp_id')
                            ->leftJoin('md_fd_company as md_fd_company_2','md_fd_company_2.id','=','td_fixed_deposit.comp_login_at')
                            ->leftJoin('md_client','md_client.id','=','td_fixed_deposit.first_client_id')
                            ->leftJoin('md_client as md_client_2','md_client_2.id','=','td_fixed_deposit.second_client_id')
                            ->leftJoin('md_client as md_client_3','md_client_3.id','=','td_fixed_deposit.third_client_id')
                            ->leftJoin('md_deposit_bank','md_deposit_bank.id','=','td_fixed_deposit.chq_bank')
                            ->leftJoin('md_employee','md_employee.euin_no','=','td_fd_form_received.euin_no')
                            ->leftJoin('md_branch','md_branch.id','=','td_fd_form_received.branch_code')
                            ->select('td_fixed_deposit.*','td_fd_form_received.bu_type as bu_type','td_fd_form_received.arn_no as arn_no','td_fd_form_received.euin_no as euin_no','td_fd_form_received.fd_bu_type as fd_bu_type','td_fd_form_received.sub_brk_cd as sub_brk_cd','md_employee.emp_name as emp_name',
                            'md_fd_scheme.scheme_name as scheme_name','md_fd_type_of_company.comp_type as comp_type_name','md_fd_company.comp_short_name as comp_short_name','md_fd_company.comp_full_name as comp_full_name',
                            'md_client.client_code as investor_code','md_client.client_name as investor_name','md_client.pan as investor_pan','md_client.dob as investor_dob',
                            'md_client_2.client_code as investor_code_2','md_client_2.client_name as investor_name_2','md_client_2.pan as investor_pan_2','md_client_2.dob as investor_dob_2',
                            'md_client_3.client_code as investor_code_3','md_client_3.client_name as investor_name_3','md_client_3.pan as investor_pan_3','md_client_3.dob as investor_dob_3',
                            'md_deposit_bank.bank_name as chq_bank','md_fd_company_2.comp_short_name as comp_login_name','md_branch.brn_name as branch_name')
                            ->where('td_fixed_deposit.delete_flag','N')
                            ->whereDate('td_fixed_deposit.entry_date',date('Y-m-d'))
                            ->where('td_fixed_deposit.comp_login_dt',$login_status,NULL)
                            ->where('td_fixed_deposit.comp_login_cutt_off',$login_status,NULL)
                            ->orderByRaw($rawOrderBy)
                            ->get();
                    }else {
                        $data=FixedDeposit::join('td_fd_form_received','td_fd_form_received.temp_tin_no','=','td_fixed_deposit.temp_tin_no')
                            ->leftJoin('md_fd_scheme','md_fd_scheme.id','=','td_fixed_deposit.scheme_id')
                            ->leftJoin('md_fd_type_of_company','md_fd_type_of_company.id','=','md_fd_scheme.comp_type_id')
                            ->leftJoin('md_fd_company','md_fd_company.id','=','md_fd_scheme.comp_id')
                            ->leftJoin('md_fd_company as md_fd_company_2','md_fd_company_2.id','=','td_fixed_deposit.comp_login_at')
                            ->leftJoin('md_client','md_client.id','=','td_fixed_deposit.first_client_id')
                            ->leftJoin('md_client as md_client_2','md_client_2.id','=','td_fixed_deposit.second_client_id')
                            ->leftJoin('md_client as md_client_3','md_client_3.id','=','td_fixed_deposit.third_client_id')
                            ->leftJoin('md_deposit_bank','md_deposit_bank.id','=','td_fixed_deposit.chq_bank')
                            ->leftJoin('md_employee','md_employee.euin_no','=','td_fd_form_received.euin_no')
                            ->leftJoin('md_branch','md_branch.id','=','td_fd_form_received.branch_code')
                            ->select('td_fixed_deposit.*','td_fd_form_received.bu_type as bu_type','td_fd_form_received.arn_no as arn_no','td_fd_form_received.euin_no as euin_no','td_fd_form_received.fd_bu_type as fd_bu_type','td_fd_form_received.sub_brk_cd as sub_brk_cd','md_employee.emp_name as emp_name',
                            'md_fd_scheme.scheme_name as scheme_name','md_fd_type_of_company.comp_type as comp_type_name','md_fd_company.comp_short_name as comp_short_name','md_fd_company.comp_full_name as comp_full_name',
                            'md_client.client_code as investor_code','md_client.client_name as investor_name','md_client.pan as investor_pan','md_client.dob as investor_dob',
                            'md_client_2.client_code as investor_code_2','md_client_2.client_name as investor_name_2','md_client_2.pan as investor_pan_2','md_client_2.dob as investor_dob_2',
                            'md_client_3.client_code as investor_code_3','md_client_3.client_name as investor_name_3','md_client_3.pan as investor_pan_3','md_client_3.dob as investor_dob_3',
                            'md_deposit_bank.bank_name as chq_bank','md_fd_company_2.comp_short_name as comp_login_name','md_branch.brn_name as branch_name')
                            ->where('td_fixed_deposit.delete_flag','N')
                            ->whereDate('td_fixed_deposit.entry_date',date('Y-m-d'))
                            ->where('td_fixed_deposit.comp_login_dt',$login_status,NULL)
                            ->where('td_fixed_deposit.comp_login_cutt_off',$login_status,NULL)
                            ->orderBy('td_fixed_deposit.updated_at','desc')
                            ->get();
                    }
                }else {
                    if ($field && $order) {
                        $rawOrderBy='';
                        if ($order > 0) {
                            $rawOrderBy=$field.' ASC';
                        } else {
                            $rawOrderBy=$field.' DESC';
                        }
                        $data=FixedDeposit::join('td_fd_form_received','td_fd_form_received.temp_tin_no','=','td_fixed_deposit.temp_tin_no')
                            ->leftJoin('md_fd_scheme','md_fd_scheme.id','=','td_fixed_deposit.scheme_id')
                            ->leftJoin('md_fd_type_of_company','md_fd_type_of_company.id','=','md_fd_scheme.comp_type_id')
                            ->leftJoin('md_fd_company','md_fd_company.id','=','md_fd_scheme.comp_id')
                            ->leftJoin('md_fd_company as md_fd_company_2','md_fd_company_2.id','=','td_fixed_deposit.comp_login_at')
                            ->leftJoin('md_client','md_client.id','=','td_fixed_deposit.first_client_id')
                            ->leftJoin('md_client as md_client_2','md_client_2.id','=','td_fixed_deposit.second_client_id')
                            ->leftJoin('md_client as md_client_3','md_client_3.id','=','td_fixed_deposit.third_client_id')
                            ->leftJoin('md_deposit_bank','md_deposit_bank.id','=','td_fixed_deposit.chq_bank')
                            ->leftJoin('md_employee','md_employee.euin_no','=','td_fd_form_received.euin_no')
                            ->leftJoin('md_branch','md_branch.id','=','td_fd_form_received.branch_code')
                            ->select('td_fixed_deposit.*','td_fd_form_received.bu_type as bu_type','td_fd_form_received.arn_no as arn_no','td_fd_form_received.euin_no as euin_no','td_fd_form_received.fd_bu_type as fd_bu_type','td_fd_form_received.sub_brk_cd as sub_brk_cd','md_employee.emp_name as emp_name',
                            'md_fd_scheme.scheme_name as scheme_name','md_fd_type_of_company.comp_type as comp_type_name','md_fd_company.comp_short_name as comp_short_name','md_fd_company.comp_full_name as comp_full_name',
                            'md_client.client_code as investor_code','md_client.client_name as investor_name','md_client.pan as investor_pan','md_client.dob as investor_dob',
                            'md_client_2.client_code as investor_code_2','md_client_2.client_name as investor_name_2','md_client_2.pan as investor_pan_2','md_client_2.dob as investor_dob_2',
                            'md_client_3.client_code as investor_code_3','md_client_3.client_name as investor_name_3','md_client_3.pan as investor_pan_3','md_client_3.dob as investor_dob_3',
                            'md_deposit_bank.bank_name as chq_bank','md_fd_company_2.comp_short_name as comp_login_name','md_branch.brn_name as branch_name')
                            ->where('td_fixed_deposit.delete_flag','N')
                            ->whereDate('td_fixed_deposit.entry_date','>=',date('Y-m-d',strtotime($start_date)))
                            ->whereDate('td_fixed_deposit.entry_date','<=',date('Y-m-d',strtotime($end_date)))
                            ->where('td_fixed_deposit.comp_login_dt',$login_status,NULL)
                            ->where('td_fixed_deposit.comp_login_cutt_off',$login_status,NULL)
                            ->orderByRaw($rawOrderBy)
                            ->get();
                    }else {
                        $data=FixedDeposit::join('td_fd_form_received','td_fd_form_received.temp_tin_no','=','td_fixed_deposit.temp_tin_no')
                            ->leftJoin('md_fd_scheme','md_fd_scheme.id','=','td_fixed_deposit.scheme_id')
                            ->leftJoin('md_fd_type_of_company','md_fd_type_of_company.id','=','md_fd_scheme.comp_type_id')
                            ->leftJoin('md_fd_company','md_fd_company.id','=','md_fd_scheme.comp_id')
                            ->leftJoin('md_fd_company as md_fd_company_2','md_fd_company_2.id','=','td_fixed_deposit.comp_login_at')
                            ->leftJoin('md_client','md_client.id','=','td_fixed_deposit.first_client_id')
                            ->leftJoin('md_client as md_client_2','md_client_2.id','=','td_fixed_deposit.second_client_id')
                            ->leftJoin('md_client as md_client_3','md_client_3.id','=','td_fixed_deposit.third_client_id')
                            ->leftJoin('md_deposit_bank','md_deposit_bank.id','=','td_fixed_deposit.chq_bank')
                            ->leftJoin('md_employee','md_employee.euin_no','=','td_fd_form_received.euin_no')
                            ->leftJoin('md_branch','md_branch.id','=','td_fd_form_received.branch_code')
                            ->select('td_fixed_deposit.*','td_fd_form_received.bu_type as bu_type','td_fd_form_received.arn_no as arn_no','td_fd_form_received.euin_no as euin_no','td_fd_form_received.fd_bu_type as fd_bu_type','td_fd_form_received.sub_brk_cd as sub_brk_cd','md_employee.emp_name as emp_name',
                            'md_fd_scheme.scheme_name as scheme_name','md_fd_type_of_company.comp_type as comp_type_name','md_fd_company.comp_short_name as comp_short_name','md_fd_company.comp_full_name as comp_full_name',
                            'md_client.client_code as investor_code','md_client.client_name as investor_name','md_client.pan as investor_pan','md_client.dob as investor_dob',
                            'md_client_2.client_code as investor_code_2','md_client_2.client_name as investor_name_2','md_client_2.pan as investor_pan_2','md_client_2.dob as investor_dob_2',
                            'md_client_3.client_code as investor_code_3','md_client_3.client_name as investor_name_3','md_client_3.pan as investor_pan_3','md_client_3.dob as investor_dob_3',
                            'md_deposit_bank.bank_name as chq_bank','md_fd_company_2.comp_short_name as comp_login_name','md_branch.brn_name as branch_name')
                            ->where('td_fixed_deposit.delete_flag','N')
                            ->whereDate('td_fixed_deposit.entry_date','>=',date('Y-m-d',strtotime($start_date)))
                            ->whereDate('td_fixed_deposit.entry_date','<=',date('Y-m-d',strtotime($end_date)))
                            ->where('td_fixed_deposit.comp_login_dt',$login_status,NULL)
                            ->where('td_fixed_deposit.comp_login_cutt_off',$login_status,NULL)
                            ->orderBy('td_fixed_deposit.updated_at','desc')
                            ->get();
                    }
                }
            }else {
                if ($order && $field) {
                    $rawOrderBy='';
                    if ($order > 0) {
                        $rawOrderBy=$field.' ASC';
                    } else {
                        $rawOrderBy=$field.' DESC';
                    }
                    if (($from_date && $to_date) || $tin_no || $investor_code || $company_id || $comp_type_id || $scheme_id) {
                        $rawQuery='';
                        $queryString='td_fixed_deposit.entry_date';
                        $rawQuery.=Helper::FrmToDateRawQuery($from_date,$to_date,$rawQuery,$queryString);
                        $queryString1='td_fixed_deposit.tin_no';
                        $rawQuery.=Helper::WhereRawQuery($tin_no,$rawQuery,$queryString1);
                        // return $rawQuery;
                        $queryString2='td_fixed_deposit.first_client_id';
                        $rawQuery.=Helper::WhereRawQuery($investor_code,$rawQuery,$queryString2);
                        $queryString3='md_fd_scheme.comp_id';
                        $rawQuery.=Helper::WhereRawQuery($company_id,$rawQuery,$queryString3);
                        $queryString4='md_fd_scheme.comp_type_id';
                        $rawQuery.=Helper::WhereRawQuery($comp_type_id,$rawQuery,$queryString4);
                        $queryString5='td_fixed_deposit.scheme_id';
                        $rawQuery.=Helper::WhereRawQuery($scheme_id,$rawQuery,$queryString5);
    
                        $data=FixedDeposit::join('td_fd_form_received','td_fd_form_received.temp_tin_no','=','td_fixed_deposit.temp_tin_no')
                            ->leftJoin('md_fd_scheme','md_fd_scheme.id','=','td_fixed_deposit.scheme_id')
                            ->leftJoin('md_fd_type_of_company','md_fd_type_of_company.id','=','md_fd_scheme.comp_type_id')
                            ->leftJoin('md_fd_company','md_fd_company.id','=','md_fd_scheme.comp_id')
                            ->leftJoin('md_fd_company as md_fd_company_2','md_fd_company_2.id','=','td_fixed_deposit.comp_login_at')
                            ->leftJoin('md_client','md_client.id','=','td_fixed_deposit.first_client_id')
                            ->leftJoin('md_client as md_client_2','md_client_2.id','=','td_fixed_deposit.second_client_id')
                            ->leftJoin('md_client as md_client_3','md_client_3.id','=','td_fixed_deposit.third_client_id')
                            ->leftJoin('md_deposit_bank','md_deposit_bank.id','=','td_fixed_deposit.chq_bank')
                            ->leftJoin('md_employee','md_employee.euin_no','=','td_fd_form_received.euin_no')
                            ->leftJoin('md_branch','md_branch.id','=','td_fd_form_received.branch_code')
                            ->select('td_fixed_deposit.*','td_fd_form_received.bu_type as bu_type','td_fd_form_received.arn_no as arn_no','td_fd_form_received.euin_no as euin_no','td_fd_form_received.fd_bu_type as fd_bu_type','td_fd_form_received.sub_brk_cd as sub_brk_cd','md_employee.emp_name as emp_name',
                            'md_fd_scheme.scheme_name as scheme_name','md_fd_type_of_company.comp_type as comp_type_name','md_fd_company.comp_short_name as comp_short_name','md_fd_company.comp_full_name as comp_full_name',
                            'md_client.client_code as investor_code','md_client.client_name as investor_name','md_client.pan as investor_pan','md_client.dob as investor_dob',
                            'md_client_2.client_code as investor_code_2','md_client_2.client_name as investor_name_2','md_client_2.pan as investor_pan_2','md_client_2.dob as investor_dob_2',
                            'md_client_3.client_code as investor_code_3','md_client_3.client_name as investor_name_3','md_client_3.pan as investor_pan_3','md_client_3.dob as investor_dob_3',
                            'md_deposit_bank.bank_name as chq_bank','md_fd_company_2.comp_short_name as comp_login_name','md_branch.brn_name as branch_name')
                            ->where('td_fixed_deposit.delete_flag','N')
                            ->whereRaw($rawQuery)
                            ->orderByRaw($rawOrderBy)
                            ->get();
                    } else {
                        $data=FixedDeposit::join('td_fd_form_received','td_fd_form_received.temp_tin_no','=','td_fixed_deposit.temp_tin_no')
                            ->leftJoin('md_fd_scheme','md_fd_scheme.id','=','td_fixed_deposit.scheme_id')
                            ->leftJoin('md_fd_type_of_company','md_fd_type_of_company.id','=','md_fd_scheme.comp_type_id')
                            ->leftJoin('md_fd_company','md_fd_company.id','=','md_fd_scheme.comp_id')
                            ->leftJoin('md_fd_company as md_fd_company_2','md_fd_company_2.id','=','td_fixed_deposit.comp_login_at')
                            ->leftJoin('md_client','md_client.id','=','td_fixed_deposit.first_client_id')
                            ->leftJoin('md_client as md_client_2','md_client_2.id','=','td_fixed_deposit.second_client_id')
                            ->leftJoin('md_client as md_client_3','md_client_3.id','=','td_fixed_deposit.third_client_id')
                            ->leftJoin('md_deposit_bank','md_deposit_bank.id','=','td_fixed_deposit.chq_bank')
                            ->leftJoin('md_employee','md_employee.euin_no','=','td_fd_form_received.euin_no')
                            ->leftJoin('md_branch','md_branch.id','=','td_fd_form_received.branch_code')
                            ->select('td_fixed_deposit.*','td_fd_form_received.bu_type as bu_type','td_fd_form_received.arn_no as arn_no','td_fd_form_received.euin_no as euin_no','td_fd_form_received.fd_bu_type as fd_bu_type','td_fd_form_received.sub_brk_cd as sub_brk_cd','md_employee.emp_name as emp_name',
                            'md_fd_scheme.scheme_name as scheme_name','md_fd_type_of_company.comp_type as comp_type_name','md_fd_company.comp_short_name as comp_short_name','md_fd_company.comp_full_name as comp_full_name',
                            'md_client.client_code as investor_code','md_client.client_name as investor_name','md_client.pan as investor_pan','md_client.dob as investor_dob',
                            'md_client_2.client_code as investor_code_2','md_client_2.client_name as investor_name_2','md_client_2.pan as investor_pan_2','md_client_2.dob as investor_dob_2',
                            'md_client_3.client_code as investor_code_3','md_client_3.client_name as investor_name_3','md_client_3.pan as investor_pan_3','md_client_3.dob as investor_dob_3',
                            'md_deposit_bank.bank_name as chq_bank','md_fd_company_2.comp_short_name as comp_login_name','md_branch.brn_name as branch_name')
                            ->where('td_fixed_deposit.delete_flag','N')
                            ->orderByRaw($rawOrderBy)
                            ->get();
                    }
                }elseif (($from_date && $to_date) || $tin_no || $investor_code || $company_id || $comp_type_id || $scheme_id) {
                    $rawQuery='';
                    $queryString='td_fixed_deposit.entry_date';
                    $rawQuery.=Helper::FrmToDateRawQuery($from_date,$to_date,$rawQuery,$queryString);
                    $queryString1='td_fixed_deposit.tin_no';
                    $rawQuery.=Helper::WhereRawQuery($tin_no,$rawQuery,$queryString1);
                    // return $rawQuery;
                    $queryString2='td_fixed_deposit.first_client_id';
                    $rawQuery.=Helper::WhereRawQuery($investor_code,$rawQuery,$queryString2);
                    $queryString3='md_fd_scheme.comp_id';
                    $rawQuery.=Helper::WhereRawQuery($company_id,$rawQuery,$queryString3);
                    $queryString4='md_fd_scheme.comp_type_id';
                    $rawQuery.=Helper::WhereRawQuery($comp_type_id,$rawQuery,$queryString4);
                    $queryString5='td_fixed_deposit.scheme_id';
                    $rawQuery.=Helper::WhereRawQuery($scheme_id,$rawQuery,$queryString5);

                    $data=FixedDeposit::join('td_fd_form_received','td_fd_form_received.temp_tin_no','=','td_fixed_deposit.temp_tin_no')
                        ->leftJoin('md_fd_scheme','md_fd_scheme.id','=','td_fixed_deposit.scheme_id')
                        ->leftJoin('md_fd_type_of_company','md_fd_type_of_company.id','=','md_fd_scheme.comp_type_id')
                        ->leftJoin('md_fd_company','md_fd_company.id','=','md_fd_scheme.comp_id')
                        ->leftJoin('md_fd_company as md_fd_company_2','md_fd_company_2.id','=','td_fixed_deposit.comp_login_at')
                        ->leftJoin('md_client','md_client.id','=','td_fixed_deposit.first_client_id')
                        ->leftJoin('md_client as md_client_2','md_client_2.id','=','td_fixed_deposit.second_client_id')
                        ->leftJoin('md_client as md_client_3','md_client_3.id','=','td_fixed_deposit.third_client_id')
                        ->leftJoin('md_deposit_bank','md_deposit_bank.id','=','td_fixed_deposit.chq_bank')
                        ->leftJoin('md_employee','md_employee.euin_no','=','td_fd_form_received.euin_no')
                        ->leftJoin('md_branch','md_branch.id','=','td_fd_form_received.branch_code')
                        ->select('td_fixed_deposit.*','td_fd_form_received.bu_type as bu_type','td_fd_form_received.arn_no as arn_no','td_fd_form_received.euin_no as euin_no','td_fd_form_received.fd_bu_type as fd_bu_type','td_fd_form_received.sub_brk_cd as sub_brk_cd','md_employee.emp_name as emp_name',
                        'md_fd_scheme.scheme_name as scheme_name','md_fd_type_of_company.comp_type as comp_type_name','md_fd_company.comp_short_name as comp_short_name','md_fd_company.comp_full_name as comp_full_name',
                        'md_client.client_code as investor_code','md_client.client_name as investor_name','md_client.pan as investor_pan','md_client.dob as investor_dob',
                        'md_client_2.client_code as investor_code_2','md_client_2.client_name as investor_name_2','md_client_2.pan as investor_pan_2','md_client_2.dob as investor_dob_2',
                        'md_client_3.client_code as investor_code_3','md_client_3.client_name as investor_name_3','md_client_3.pan as investor_pan_3','md_client_3.dob as investor_dob_3',
                        'md_deposit_bank.bank_name as chq_bank','md_fd_company_2.comp_short_name as comp_login_name','md_branch.brn_name as branch_name')
                        ->where('td_fixed_deposit.delete_flag','N')
                        ->whereRaw($rawQuery)
                        ->orderBy('td_fixed_deposit.updated_at','desc')
                        ->get();
                } else {
                    $data=FixedDeposit::join('td_fd_form_received','td_fd_form_received.temp_tin_no','=','td_fixed_deposit.temp_tin_no')
                        ->leftJoin('md_fd_scheme','md_fd_scheme.id','=','td_fixed_deposit.scheme_id')
                        ->leftJoin('md_fd_type_of_company','md_fd_type_of_company.id','=','md_fd_scheme.comp_type_id')
                        ->leftJoin('md_fd_company','md_fd_company.id','=','md_fd_scheme.comp_id')
                        ->leftJoin('md_fd_company as md_fd_company_2','md_fd_company_2.id','=','td_fixed_deposit.comp_login_at')
                        ->leftJoin('md_client','md_client.id','=','td_fixed_deposit.first_client_id')
                        ->leftJoin('md_client as md_client_2','md_client_2.id','=','td_fixed_deposit.second_client_id')
                        ->leftJoin('md_client as md_client_3','md_client_3.id','=','td_fixed_deposit.third_client_id')
                        ->leftJoin('md_deposit_bank','md_deposit_bank.id','=','td_fixed_deposit.chq_bank')
                        ->leftJoin('md_employee','md_employee.euin_no','=','td_fd_form_received.euin_no')
                        ->leftJoin('md_branch','md_branch.id','=','td_fd_form_received.branch_code')
                        ->select('td_fixed_deposit.*','td_fd_form_received.bu_type as bu_type','td_fd_form_received.arn_no as arn_no','td_fd_form_received.euin_no as euin_no','td_fd_form_received.fd_bu_type as fd_bu_type','td_fd_form_received.sub_brk_cd as sub_brk_cd','md_employee.emp_name as emp_name',
                        'md_fd_scheme.scheme_name as scheme_name','md_fd_type_of_company.comp_type as comp_type_name','md_fd_company.comp_short_name as comp_short_name','md_fd_company.comp_full_name as comp_full_name',
                        'md_client.client_code as investor_code','md_client.client_name as investor_name','md_client.pan as investor_pan','md_client.dob as investor_dob',
                        'md_client_2.client_code as investor_code_2','md_client_2.client_name as investor_name_2','md_client_2.pan as investor_pan_2','md_client_2.dob as investor_dob_2',
                        'md_client_3.client_code as investor_code_3','md_client_3.client_name as investor_name_3','md_client_3.pan as investor_pan_3','md_client_3.dob as investor_dob_3',
                        'md_deposit_bank.bank_name as chq_bank','md_fd_company_2.comp_short_name as comp_login_name','md_branch.brn_name as branch_name')
                        ->where('td_fixed_deposit.delete_flag','N')
                        ->orderBy('td_fixed_deposit.updated_at','desc')
                        ->get();
                }
            }
        }  catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function index(Request $request)
    {
        try {
            $tin_no=$request->tin_no;
            if ($tin_no) {
                $data=FixedDeposit::join('td_fd_form_received','td_fd_form_received.temp_tin_no','=','td_fixed_deposit.temp_tin_no')
                        ->leftJoin('md_fd_scheme','md_fd_scheme.id','=','td_fixed_deposit.scheme_id')
                        ->leftJoin('md_fd_type_of_company','md_fd_type_of_company.id','=','md_fd_scheme.comp_type_id')
                        ->leftJoin('md_fd_company','md_fd_company.id','=','md_fd_scheme.comp_id')
                        ->leftJoin('md_fd_company as md_fd_company_2','md_fd_company_2.id','=','td_fixed_deposit.comp_login_at')
                        ->leftJoin('md_client','md_client.id','=','td_fixed_deposit.first_client_id')
                        ->leftJoin('md_client as md_client_2','md_client_2.id','=','td_fixed_deposit.second_client_id')
                        ->leftJoin('md_client as md_client_3','md_client_3.id','=','td_fixed_deposit.third_client_id')
                        ->leftJoin('md_deposit_bank','md_deposit_bank.id','=','td_fixed_deposit.chq_bank')
                        ->leftJoin('md_employee','md_employee.euin_no','=','td_fd_form_received.euin_no')
                        ->select('td_fixed_deposit.*','td_fd_form_received.bu_type as bu_type','td_fd_form_received.arn_no as arn_no','td_fd_form_received.euin_no as euin_no','td_fd_form_received.fd_bu_type as fd_bu_type','td_fd_form_received.sub_brk_cd as sub_brk_cd','md_employee.emp_name as emp_name',
                        'md_fd_scheme.scheme_name as scheme_name','md_fd_type_of_company.comp_type as comp_type_name','md_fd_company.comp_short_name as comp_short_name','md_fd_company.comp_full_name as comp_full_name',
                        'md_client.client_code as investor_code','md_client.client_name as investor_name','md_client.pan as investor_pan','md_client.dob as investor_dob',
                        'md_client_2.client_code as investor_code_2','md_client_2.client_name as investor_name_2','md_client_2.pan as investor_pan_2','md_client_2.dob as investor_dob_2',
                        'md_client_3.client_code as investor_code_3','md_client_3.client_name as investor_name_3','md_client_3.pan as investor_pan_3','md_client_3.dob as investor_dob_3',
                        'md_deposit_bank.bank_name as chq_bank','md_fd_company_2.comp_short_name as comp_login_name')
                        ->where('td_fixed_deposit.delete_flag','N')
                        ->where('td_fixed_deposit.tin_no','like', '%' . $tin_no . '%')
                        ->orderBy('td_fixed_deposit.updated_at','desc')
                        ->get();
            } else {
                $data=FixedDeposit::join('td_fd_form_received','td_fd_form_received.temp_tin_no','=','td_fixed_deposit.temp_tin_no')
                        ->leftJoin('md_fd_scheme','md_fd_scheme.id','=','td_fixed_deposit.scheme_id')
                        ->leftJoin('md_fd_type_of_company','md_fd_type_of_company.id','=','md_fd_scheme.comp_type_id')
                        ->leftJoin('md_fd_company','md_fd_company.id','=','md_fd_scheme.comp_id')
                        ->leftJoin('md_fd_company as md_fd_company_2','md_fd_company_2.id','=','td_fixed_deposit.comp_login_at')
                        ->leftJoin('md_client','md_client.id','=','td_fixed_deposit.first_client_id')
                        ->leftJoin('md_client as md_client_2','md_client_2.id','=','td_fixed_deposit.second_client_id')
                        ->leftJoin('md_client as md_client_3','md_client_3.id','=','td_fixed_deposit.third_client_id')
                        ->leftJoin('md_deposit_bank','md_deposit_bank.id','=','td_fixed_deposit.chq_bank')
                        ->leftJoin('md_employee','md_employee.euin_no','=','td_fd_form_received.euin_no')
                        ->select('td_fixed_deposit.*','td_fd_form_received.bu_type as bu_type','td_fd_form_received.arn_no as arn_no','td_fd_form_received.euin_no as euin_no','td_fd_form_received.fd_bu_type as fd_bu_type','td_fd_form_received.sub_brk_cd as sub_brk_cd','md_employee.emp_name as emp_name',
                        'md_fd_scheme.scheme_name as scheme_name','md_fd_type_of_company.comp_type as comp_type_name','md_fd_company.comp_short_name as comp_short_name','md_fd_company.comp_full_name as comp_full_name',
                        'md_client.client_code as investor_code','md_client.client_name as investor_name','md_client.pan as investor_pan','md_client.dob as investor_dob',
                        'md_client_2.client_code as investor_code_2','md_client_2.client_name as investor_name_2','md_client_2.pan as investor_pan_2','md_client_2.dob as investor_dob_2',
                        'md_client_3.client_code as investor_code_3','md_client_3.client_name as investor_name_3','md_client_3.pan as investor_pan_3','md_client_3.dob as investor_dob_3',
                        'md_deposit_bank.bank_name as chq_bank','md_fd_company_2.comp_short_name as comp_login_name')
                        ->where('td_fixed_deposit.delete_flag','N')
                        ->orderBy('td_fixed_deposit.updated_at','desc')
                        ->get();
            }
            
        } catch (\Throwable $th) {
            // throw $th;
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
            $product_id=4;
            $tin_no='';
            $trans_type_id='';
            $is_has=FixedDeposit::get();
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
                $has=FixedDeposit::where('temp_tin_no',$request->temp_tin_no)->get();
                if (count($has)>0) {
                    $error='Temporary TIN no already exist.';
                    return Helper::ErrorResponse($error);
                }else {
                    
                    FDFormReceived::where('temp_tin_no',$request->temp_tin_no)->update(array(
                        'bu_type'=>$request->bu_type,
                        'euin_no'=>$request->euin_no,
                        'sub_arn_no'=>isset($request->sub_arn_no)?$request->sub_arn_no:NULL,
                        'sub_brk_cd'=>isset($request->sub_brk_cd)?$request->sub_brk_cd:NULL,
                        'investor_id'=>$request->investor_id,
                        'fd_bu_type'=>$request->fd_bu_type,
                        'comp_id'=>$request->company_id,
                        'scheme_id'=>$request->scheme_id,
                        // 'branch_code'=>$branch_code,
                        // 'created_by'=>'',
                    ));       
                    
                    $second_client_id=$request->second_client_id;
                    $second_client_name=$request->second_client_name;
                    $second_client_pan=$request->second_client_pan;
                    $second_client_dob=$request->second_client_dob;
                    if ($second_client_id=='' && $second_client_name!='' && $second_client_pan!='' && $second_client_dob!='') {
                        $s_c_data=Client::create(array(
                            'client_name'=>$second_client_name,
                            'pan'=>$second_client_pan,
                            'dob'=>date('Y-m-d',strtotime($second_client_dob)),
                            'client_type'=>'E',
                            // 'created_by'=>'',
                        ));  
                        $second_client_id=$s_c_data->id;
                    }

                    $third_client_id=$request->third_client_id;
                    $third_client_name=$request->third_client_name;
                    $third_client_pan=$request->third_client_pan;
                    $third_client_dob=$request->third_client_dob;
                    if ($third_client_id=='' && $third_client_name!='' && $third_client_pan!='' && $third_client_dob!='') {
                        $t_c_data=Client::create(array(
                            'client_name'=>$third_client_name,
                            'pan'=>$third_client_pan,
                            'dob'=>date('Y-m-d',strtotime($third_client_dob)),
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
                        $app_form_scan->move(public_path('fd-application-form/'),$doc_name);
                    }
                    $data=FixedDeposit::create(array(
                        'temp_tin_no'=>$request->temp_tin_no,
                        'tin_no'=>$tin_no,
                        'entry_tin_status'=>$request->tin_status,
                        'entry_date'=>date('Y-m-d'),
                        'mode_of_holding'=>$request->mode_of_holding,
                        'kyc_status'=>$request->kyc_status,
                        'first_client_id'=>$request->investor_id,
                        'first_kyc'=>$request->investor_kyc,
                        'second_client_id'=>isset($second_client_id)?$second_client_id:NULL,
                        'second_kyc'=>isset($request->second_kyc)?$request->second_kyc:NULL,
                        'third_client_id'=>isset($third_client_id)?$third_client_id:NULL,
                        'third_kyc'=>isset($request->third_kyc)?$request->third_kyc:NULL,
                        'scheme_id'=>$request->scheme_id,
                        'investment_type'=>$request->investment_type,
                        'application_no'=>$request->application_no,
                        'fdr_no'=>$request->fdr_no,
                        'option'=>$request->option,
                        'sub_option'=>$request->sub_option,
                        'tenure_type'=>$request->tenure_type,
                        'tenure'=>$request->tenure,
                        'interest_rate'=>$request->interest_rate,
                        'maturity_instruction'=>$request->maturity_instruction,
                        'maturity_date'=>$request->maturity_date,
                        'acc_no'=>isset($request->acc_no)?$request->acc_no:NULL,
                        'amount'=>$request->amount,
                        'mode_of_payment'=>$request->mode_of_payment,
                        'chq_bank'=>isset($request->chq_bank)?$request->chq_bank:NULL,
                        'payment_ref_no'=>$request->payment_ref_no,
                        'chq_no'=>$request->chq_no,
                        'certificate_delivery_opt'=>$request->certificate_delivery_opt,
                        'tds_info_id'=>$request->tds_info,
                        'app_form_scan'=>$doc_name,
                        'comp_login_at'=>$request->comp_login_at,
                        'remarks'=>isset($request->remarks)?$request->remarks:NULL,
                        'form_status'=>'P',
                        // 'created_by'=>'',
                    ));    

                }
            } else {  // Without TTIN not exist
                // return $request; // if temp tin not exist
                // return $request->sip_start_date;
                // return $tin_no;
                // craete TTIN no
                $is_has=FDFormReceived::orderBy('created_at','desc')->get();
                if (count($is_has)>0) {
                    $temp_tin_no=Helper::TempTINGen((count($is_has)+1),$product_id); // generate temp tin no
                }else{
                    $temp_tin_no=Helper::TempTINGen(1,$product_id); // generate temp tin no
                }
                $arn_no=Helper::CommonParamValue(1);
                $branch_code=1;
                $fr_data=FDFormReceived::create(array(
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
                    'proposal_no'=>isset($request->proposal_no)?$request->proposal_no:NULL,
                    'branch_code'=>$branch_code,
                    // 'created_by'=>'',
                ));      
                // return $fr_data;
                $ttin_no=$fr_data->temp_tin_no;

                $second_client_id=$request->second_client_id;
                $second_client_name=$request->second_client_name;
                $second_client_pan=$request->second_client_pan;
                $second_client_dob=$request->second_client_dob;
                if ($second_client_id=='' && $second_client_name!='' && $second_client_pan!='' && $second_client_dob!='') {
                    $s_c_data=Client::create(array(
                        'client_name'=>$second_client_name,
                        'pan'=>$second_client_pan,
                        'dob'=>date('Y-m-d',strtotime($second_client_dob)),
                        'client_type'=>'E',
                        // 'created_by'=>'',
                    ));  
                    $second_client_id=$s_c_data->id;
                }

                $third_client_id=$request->third_client_id;
                $third_client_name=$request->third_client_name;
                $third_client_pan=$request->third_client_pan;
                $third_client_dob=$request->third_client_dob;
                if ($third_client_id=='' && $third_client_name!='' && $third_client_pan!='' && $third_client_dob!='') {
                    $t_c_data=Client::create(array(
                        'client_name'=>$third_client_name,
                        'pan'=>$third_client_pan,
                        'dob'=>date('Y-m-d',strtotime($third_client_dob)),
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
                    $app_form_scan->move(public_path('ins-application-form/'),$doc_name);
                }
                $data=FixedDeposit::create(array(
                    'temp_tin_no' =>$ttin_no,
                    'tin_no'=> $tin_no,
                    'entry_tin_status'=>$request->tin_status,
                    'entry_date'=>date('Y-m-d'),
                    'mode_of_holding'=>$request->mode_of_holding,
                    'kyc_status'=>$request->kyc_status,
                    'first_client_id'=>$request->investor_id,
                    'first_kyc'=>$request->investor_kyc,
                    'second_client_id'=>isset($second_client_id)?$second_client_id:NULL,
                    'second_kyc'=>isset($request->second_kyc)?$request->second_kyc:NULL,
                    'third_client_id'=>isset($third_client_id)?$third_client_id:NULL,
                    'third_kyc'=>isset($request->third_kyc)?$request->third_kyc:NULL,
                    'scheme_id'=>$request->scheme_id,
                    'investment_type'=>$request->investment_type,
                    'application_no'=>$request->application_no,
                    'fdr_no'=>$request->fdr_no,
                    'option'=>$request->option,
                    'sub_option'=>$request->sub_option,
                    'tenure_type'=>$request->tenure_type,
                    'tenure'=>$request->tenure,
                    'interest_rate'=>$request->interest_rate,
                    'maturity_instruction'=>$request->maturity_instruction,
                    'maturity_date'=>$request->maturity_date,
                    'acc_no'=>isset($request->acc_no)?$request->acc_no:NULL,
                    'amount'=>$request->amount,
                    'mode_of_payment'=>$request->mode_of_payment,
                    'chq_bank'=>isset($request->chq_bank)?$request->chq_bank:NULL,
                    'payment_ref_no'=>$request->payment_ref_no,
                    'chq_no'=>$request->chq_no,
                    'certificate_delivery_opt'=>$request->certificate_delivery_opt,
                    'tds_info_id'=>$request->tds_info,
                    'app_form_scan'=>$doc_name,
                    'comp_login_at'=>$request->comp_login_at,
                    'remarks'=>isset($request->remarks)?$request->remarks:NULL,
                    'form_status'=>'P',
                    // 'created_by'=>'',
                ));   
            }
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

}
