<?php

namespace App\Http\Controllers\v1\FDOperation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{FixedDeposit,Client,InsFormReceived};
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
            $field=$request->field;
            $order=$request->order;
            
            $tin_no=$request->tin_no;
            $option=$request->option;
            $sub_brk_cd=$request->sub_brk_cd;
            $proposer_name=$request->proposer_name;
            $euin_no=$request->euin_no;
            $bu_type=json_decode($request->bu_type);
            $ins_type_id=json_decode($request->ins_type_id);
            $insured_bu_type=json_decode($request->insured_bu_type);
            $ack_status=json_decode($request->ack_status);
            $from_date=$request->from_date;
            $to_date=$request->to_date;
            $company_id=json_decode($request->company_id);
            $comp_type_id=json_decode($request->comp_type_id);
            $scheme_id=json_decode($request->scheme_id);
            $investor_code=$request->investor_code;
            $login_status_id=json_decode($request->login_status_id);

            $login_status=$request->login_status;
            $date_status=$request->date_status;
            $start_date=$request->start_date;
            $end_date=$request->end_date;

            if ($paginate=='A') {
                $paginate=999999999;
            }

            if ($field && $order) {
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
                        ->where('td_fixed_deposit.form_status','!=','P')
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
                        ->where('td_fixed_deposit.form_status','!=','P')
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
                    ->where('td_fixed_deposit.form_status','!=','P')
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
                    ->where('td_fixed_deposit.form_status','!=','P')
                    ->orderBy('td_fixed_deposit.updated_at','desc')
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
            $field=$request->field;
            $order=$request->order;
            
            $tin_no=$request->tin_no;
            $option=$request->option;
            $sub_brk_cd=$request->sub_brk_cd;
            $proposer_name=$request->proposer_name;
            $euin_no=$request->euin_no;
            $bu_type=json_decode($request->bu_type);
            $ins_type_id=json_decode($request->ins_type_id);
            $insured_bu_type=json_decode($request->insured_bu_type);
            $ack_status=json_decode($request->ack_status);
            $from_date=$request->from_date;
            $to_date=$request->to_date;
            $company_id=json_decode($request->company_id);
            $comp_type_id=json_decode($request->comp_type_id);
            $scheme_id=json_decode($request->scheme_id);
            $investor_code=$request->investor_code;
            $login_status_id=json_decode($request->login_status_id);

            $login_status=$request->login_status;
            $date_status=$request->date_status;
            $start_date=$request->start_date;
            $end_date=$request->end_date;

            if ($paginate=='A') {
                $paginate=999999999;
            }

            if ($field && $order) {
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
                        ->where('td_fixed_deposit.form_status','!=','P')
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
                        ->where('td_fixed_deposit.form_status','!=','P')
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
                    ->where('td_fixed_deposit.form_status','!=','P')
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
                    ->where('td_fixed_deposit.form_status','!=','P')
                    ->orderBy('td_fixed_deposit.updated_at','desc')
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
            $data1=FixedDeposit::where('tin_no',$request->tin_no)->first();
            
            if ($request->manual_trans_status=="P") {
                $fdr_scan=$request->fdr_scan;
                if ($fdr_scan) {
                    $path_extension=$fdr_scan->getClientOriginalExtension();
                    $fdr_scan_name=$request->tin_no.'_'.date('YmdHis').".".$path_extension;
                    $fdr_scan->move(public_path('fd-policy-copy/'),$fdr_scan_name);
                }else{
                    $fdr_scan_name=$data1->fdr_scan;
                    // return $doc_name;
                }
                FixedDeposit::where('tin_no',$request->tin_no)->update(array(
                    'manual_trans_status'=>$request->manual_trans_status,
                    'logged_in'=>$request->logged_in,
                    'fdr_no'=>$request->fdr_no,
                    'fdr_scan'=>$fdr_scan_name,
                    'manual_update_remarks'=>$request->manual_update_remarks,
                    'form_status'=>'M',
                    // 'updated_at'
                ));   
            }elseif ($request->manual_trans_status=="R") {
                # code...
                $reject_memo=$request->reject_memo;
                if ($reject_memo) {
                    $path_extension=$reject_memo->getClientOriginalExtension();
                    $reject_memo_name=$request->tin_no.'_'.date('YmdHis').".".$path_extension;
                    $reject_memo->move(public_path('fd-reject-memo/'),$reject_memo_name);
                }else{
                    $reject_memo_name=$data1->reject_memo;
                    // return $doc_name;
                }
                FixedDeposit::where('tin_no',$request->tin_no)->update(array(
                    'manual_trans_status'=>$request->manual_trans_status,
                    'logged_in'=>$request->logged_in,
                    'contact_to_comp'=>$request->contact_to_comp,
                    'contact_via'=>$request->contact_via,
                    'contact_per_name'=>isset($request->contact_per_name)?$request->contact_per_name:NULL,
                    'contact_per_phone'=>isset($request->contact_per_phone)?$request->contact_per_phone:NULL,
                    'contact_per_email'=>isset($request->contact_per_email)?$request->contact_per_email:NULL,
                    'reject_reason_id'=>isset($request->contact_per_email)?$request->contact_per_email:NULL,
                    'reject_memo'=>$reject_memo_name,
                    'pending_reason'=>isset($request->pending_reason)?$request->pending_reason:NULL,
                    'manual_update_remarks'=>$request->manual_update_remarks,
                ));   
            } else {
                FixedDeposit::where('tin_no',$request->tin_no)->update(array(
                    'manual_trans_status'=>$request->manual_trans_status,
                    'logged_in'=>$request->logged_in,
                    'contact_to_comp'=>$request->contact_to_comp,
                    'contact_via'=>$request->contact_via,
                    'contact_per_name'=>isset($request->contact_per_name)?$request->contact_per_name:NULL,
                    'contact_per_phone'=>isset($request->contact_per_phone)?$request->contact_per_phone:NULL,
                    'contact_per_email'=>isset($request->contact_per_email)?$request->contact_per_email:NULL,
                    'reject_reason_id'=>isset($request->contact_per_email)?$request->contact_per_email:NULL,
                    'pending_reason'=>isset($request->pending_reason)?$request->pending_reason:NULL,
                    'manual_update_remarks'=>$request->manual_update_remarks,
                ));   
            }
            $data=FixedDeposit::where('tin_no',$request->tin_no)->first();
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
            $data=FixedDeposit::join('td_form_received','td_form_received.temp_tin_no','=','td_mutual_fund.temp_tin_no')
                ->join('md_trans','md_trans.id','=','td_mutual_fund.trans_id')
                        ->leftJoin('md_employee','md_employee.euin_no','=','td_ins_form_received.euin_no')
                        ->leftJoin('md_branch','md_branch.id','=','td_fd_form_received.branch_code')
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
