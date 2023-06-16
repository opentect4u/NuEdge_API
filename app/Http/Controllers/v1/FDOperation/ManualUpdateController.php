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
                    if ($sort_by && $column_name) {
                        if ($column_name="insure_bu_type") {
                            $data=FixedDeposit::join('td_ins_form_received','td_ins_form_received.temp_tin_no','=','td_insurance.temp_tin_no')
                                ->leftJoin('md_ins_company','md_ins_company.id','=','td_insurance.company_id')
                                ->leftJoin('md_ins_company as company_2','company_2.id','=','td_insurance.comp_login_at')
                                ->leftJoin('md_ins_product_type','md_ins_product_type.id','=','td_insurance.product_type_id')
                                ->leftJoin('md_ins_products','md_ins_products.id','=','td_insurance.product_id')
                                ->leftJoin('md_client','md_client.id','=','td_insurance.proposer_id')
                                ->leftJoin('md_client as md_client_2','md_client_2.id','=','td_insurance.insured_person_id')
                                ->leftJoin('md_ins_type','md_ins_type.id','=','td_ins_form_received.ins_type_id')
                                ->leftJoin('md_deposit_bank','md_deposit_bank.id','=','td_insurance.chq_bank')
                        ->leftJoin('md_employee','md_employee.euin_no','=','td_ins_form_received.euin_no')
                                ->select('td_insurance.*','td_ins_form_received.bu_type as bu_type','td_ins_form_received.arn_no as arn_no','td_ins_form_received.euin_no as euin_no','td_ins_form_received.insure_bu_type as insure_bu_type','td_ins_form_received.ins_type_id as ins_type_id',
                                'md_ins_company.comp_short_name as comp_short_name','md_ins_company.comp_full_name as comp_full_name','md_ins_product_type.product_type as product_type','md_ins_products.product_name as product_name',
                                'md_client.client_code as proposer_code','md_client.client_name as proposer_name','md_client.pan as proposer_pan','md_client.client_type as proposer_type','md_client.dob as proposer_dob',
                                'md_client_2.client_code as insured_person_code','md_client_2.client_name as insured_person_name','md_client_2.pan as insured_person_pan','md_client_2.client_type as insured_person_type','md_client_2.dob as insured_person_dob',
                                'md_ins_type.type as ins_type','company_2.comp_short_name as comp_login_short_name','company_2.comp_full_name as comp_login_full_name','md_deposit_bank.bank_name as bank_name','md_deposit_bank.micr_code as micr_code','md_deposit_bank.ifs_code as ifs_code','md_deposit_bank.branch_name as branch_name','md_deposit_bank.branch_addr as branch_addr','md_employee.emp_name as emp_name')
                                ->where('td_insurance.delete_flag','N')
                                ->where('td_insurance.form_status','!=','P')
                                ->whereDate('td_insurance.entry_date',date('Y-m-d'))
                                ->where('td_insurance.comp_login_dt',$login_status,NULL)
                                ->where('td_insurance.comp_login_cutt_off',$login_status,NULL)
                                ->orderBy('td_ins_form_received.bu_type',$sort_by)
                                ->paginate($paginate);
                        }else {
                            $data=FixedDeposit::join('td_ins_form_received','td_ins_form_received.temp_tin_no','=','td_insurance.temp_tin_no')
                                ->leftJoin('md_ins_company','md_ins_company.id','=','td_insurance.company_id')
                                ->leftJoin('md_ins_company as company_2','company_2.id','=','td_insurance.comp_login_at')
                                ->leftJoin('md_ins_product_type','md_ins_product_type.id','=','td_insurance.product_type_id')
                                ->leftJoin('md_ins_products','md_ins_products.id','=','td_insurance.product_id')
                                ->leftJoin('md_client','md_client.id','=','td_insurance.proposer_id')
                                ->leftJoin('md_client as md_client_2','md_client_2.id','=','td_insurance.insured_person_id')
                                ->leftJoin('md_ins_type','md_ins_type.id','=','td_ins_form_received.ins_type_id')
                                ->leftJoin('md_deposit_bank','md_deposit_bank.id','=','td_insurance.chq_bank')
                        ->leftJoin('md_employee','md_employee.euin_no','=','td_ins_form_received.euin_no')
                                ->select('td_insurance.*','td_ins_form_received.bu_type as bu_type','td_ins_form_received.arn_no as arn_no','td_ins_form_received.euin_no as euin_no','td_ins_form_received.insure_bu_type as insure_bu_type','td_ins_form_received.ins_type_id as ins_type_id',
                                'md_ins_company.comp_short_name as comp_short_name','md_ins_company.comp_full_name as comp_full_name','md_ins_product_type.product_type as product_type','md_ins_products.product_name as product_name',
                                'md_client.client_code as proposer_code','md_client.client_name as proposer_name','md_client.pan as proposer_pan','md_client.client_type as proposer_type','md_client.dob as proposer_dob',
                                'md_client_2.client_code as insured_person_code','md_client_2.client_name as insured_person_name','md_client_2.pan as insured_person_pan','md_client_2.client_type as insured_person_type','md_client_2.dob as insured_person_dob',
                                'md_ins_type.type as ins_type','company_2.comp_short_name as comp_login_short_name','company_2.comp_full_name as comp_login_full_name','md_deposit_bank.bank_name as bank_name','md_deposit_bank.micr_code as micr_code','md_deposit_bank.ifs_code as ifs_code','md_deposit_bank.branch_name as branch_name','md_deposit_bank.branch_addr as branch_addr','md_employee.emp_name as emp_name')
                                ->where('td_insurance.delete_flag','N')
                                                        ->where('td_insurance.form_status','!=','P')
                                ->whereDate('td_insurance.entry_date',date('Y-m-d'))
                                ->where('td_insurance.comp_login_dt',$login_status,NULL)
                                ->where('td_insurance.comp_login_cutt_off',$login_status,NULL)
                                ->orderBy('td_insurance.'.$column_name,$sort_by)
                                ->paginate($paginate);
                        }
                    }else {
                        $data=FixedDeposit::join('td_ins_form_received','td_ins_form_received.temp_tin_no','=','td_insurance.temp_tin_no')
                            ->leftJoin('md_ins_company','md_ins_company.id','=','td_insurance.company_id')
                            ->leftJoin('md_ins_company as company_2','company_2.id','=','td_insurance.comp_login_at')
                            ->leftJoin('md_ins_product_type','md_ins_product_type.id','=','td_insurance.product_type_id')
                            ->leftJoin('md_ins_products','md_ins_products.id','=','td_insurance.product_id')
                            ->leftJoin('md_client','md_client.id','=','td_insurance.proposer_id')
                            ->leftJoin('md_client as md_client_2','md_client_2.id','=','td_insurance.insured_person_id')
                            ->leftJoin('md_ins_type','md_ins_type.id','=','td_ins_form_received.ins_type_id')
                            ->leftJoin('md_deposit_bank','md_deposit_bank.id','=','td_insurance.chq_bank')
                        ->leftJoin('md_employee','md_employee.euin_no','=','td_ins_form_received.euin_no')
                            ->select('td_insurance.*','td_ins_form_received.bu_type as bu_type','td_ins_form_received.arn_no as arn_no','td_ins_form_received.euin_no as euin_no','td_ins_form_received.insure_bu_type as insure_bu_type','td_ins_form_received.ins_type_id as ins_type_id',
                            'md_ins_company.comp_short_name as comp_short_name','md_ins_company.comp_full_name as comp_full_name','md_ins_product_type.product_type as product_type','md_ins_products.product_name as product_name',
                            'md_client.client_code as proposer_code','md_client.client_name as proposer_name','md_client.pan as proposer_pan','md_client.client_type as proposer_type','md_client.dob as proposer_dob',
                            'md_client_2.client_code as insured_person_code','md_client_2.client_name as insured_person_name','md_client_2.pan as insured_person_pan','md_client_2.client_type as insured_person_type','md_client_2.dob as insured_person_dob',
                            'md_ins_type.type as ins_type','company_2.comp_short_name as comp_login_short_name','company_2.comp_full_name as comp_login_full_name','md_deposit_bank.bank_name as bank_name','md_deposit_bank.micr_code as micr_code','md_deposit_bank.ifs_code as ifs_code','md_deposit_bank.branch_name as branch_name','md_deposit_bank.branch_addr as branch_addr','md_employee.emp_name as emp_name')
                            ->where('td_insurance.delete_flag','N')
                            ->where('td_insurance.form_status','!=','P')
                            ->whereDate('td_insurance.entry_date',date('Y-m-d'))
                            ->where('td_insurance.comp_login_dt',$login_status,NULL)
                            ->where('td_insurance.comp_login_cutt_off',$login_status,NULL)
                            ->orderBy('td_insurance.updated_at','desc')
                            ->paginate($paginate);
                    }
                }else {
                    if ($sort_by && $column_name) {
                        if ($column_name="insure_bu_type") {
                            $data=FixedDeposit::join('td_ins_form_received','td_ins_form_received.temp_tin_no','=','td_insurance.temp_tin_no')
                            ->leftJoin('md_ins_company','md_ins_company.id','=','td_insurance.company_id')
                            ->leftJoin('md_ins_company as company_2','company_2.id','=','td_insurance.comp_login_at')
                            ->leftJoin('md_ins_product_type','md_ins_product_type.id','=','td_insurance.product_type_id')
                            ->leftJoin('md_ins_products','md_ins_products.id','=','td_insurance.product_id')
                            ->leftJoin('md_client','md_client.id','=','td_insurance.proposer_id')
                            ->leftJoin('md_client as md_client_2','md_client_2.id','=','td_insurance.insured_person_id')
                            ->leftJoin('md_ins_type','md_ins_type.id','=','td_ins_form_received.ins_type_id')
                            ->leftJoin('md_deposit_bank','md_deposit_bank.id','=','td_insurance.chq_bank')
                        ->leftJoin('md_employee','md_employee.euin_no','=','td_ins_form_received.euin_no')
                            ->select('td_insurance.*','td_ins_form_received.bu_type as bu_type','td_ins_form_received.arn_no as arn_no','td_ins_form_received.euin_no as euin_no','td_ins_form_received.insure_bu_type as insure_bu_type','td_ins_form_received.ins_type_id as ins_type_id',
                            'md_ins_company.comp_short_name as comp_short_name','md_ins_company.comp_full_name as comp_full_name','md_ins_product_type.product_type as product_type','md_ins_products.product_name as product_name',
                            'md_client.client_code as proposer_code','md_client.client_name as proposer_name','md_client.pan as proposer_pan','md_client.client_type as proposer_type','md_client.dob as proposer_dob',
                            'md_client_2.client_code as insured_person_code','md_client_2.client_name as insured_person_name','md_client_2.pan as insured_person_pan','md_client_2.client_type as insured_person_type','md_client_2.dob as insured_person_dob',
                            'md_ins_type.type as ins_type','company_2.comp_short_name as comp_login_short_name','company_2.comp_full_name as comp_login_full_name','md_deposit_bank.bank_name as bank_name','md_deposit_bank.micr_code as micr_code','md_deposit_bank.ifs_code as ifs_code','md_deposit_bank.branch_name as branch_name','md_deposit_bank.branch_addr as branch_addr','md_employee.emp_name as emp_name')
                            ->where('td_insurance.delete_flag','N')
                                                    ->where('td_insurance.form_status','!=','P')
                            ->whereDate('td_insurance.entry_date','>=',date('Y-m-d',strtotime($start_date)))
                            ->whereDate('td_insurance.entry_date','<=',date('Y-m-d',strtotime($end_date)))
                            ->where('td_insurance.comp_login_dt',$login_status,NULL)
                            ->where('td_insurance.comp_login_cutt_off',$login_status,NULL)
                            ->orderBy('td_ins_form_received.bu_type',$sort_by)
                            ->paginate($paginate);
                        }else {
                            $data=FixedDeposit::join('td_ins_form_received','td_ins_form_received.temp_tin_no','=','td_insurance.temp_tin_no')
                            ->leftJoin('md_ins_company','md_ins_company.id','=','td_insurance.company_id')
                            ->leftJoin('md_ins_company as company_2','company_2.id','=','td_insurance.comp_login_at')
                            ->leftJoin('md_ins_product_type','md_ins_product_type.id','=','td_insurance.product_type_id')
                            ->leftJoin('md_ins_products','md_ins_products.id','=','td_insurance.product_id')
                            ->leftJoin('md_client','md_client.id','=','td_insurance.proposer_id')
                            ->leftJoin('md_client as md_client_2','md_client_2.id','=','td_insurance.insured_person_id')
                            ->leftJoin('md_ins_type','md_ins_type.id','=','td_ins_form_received.ins_type_id')
                            ->leftJoin('md_deposit_bank','md_deposit_bank.id','=','td_insurance.chq_bank')
                        ->leftJoin('md_employee','md_employee.euin_no','=','td_ins_form_received.euin_no')
                            ->select('td_insurance.*','td_ins_form_received.bu_type as bu_type','td_ins_form_received.arn_no as arn_no','td_ins_form_received.euin_no as euin_no','td_ins_form_received.insure_bu_type as insure_bu_type','td_ins_form_received.ins_type_id as ins_type_id',
                            'md_ins_company.comp_short_name as comp_short_name','md_ins_company.comp_full_name as comp_full_name','md_ins_product_type.product_type as product_type','md_ins_products.product_name as product_name',
                            'md_client.client_code as proposer_code','md_client.client_name as proposer_name','md_client.pan as proposer_pan','md_client.client_type as proposer_type','md_client.dob as proposer_dob',
                            'md_client_2.client_code as insured_person_code','md_client_2.client_name as insured_person_name','md_client_2.pan as insured_person_pan','md_client_2.client_type as insured_person_type','md_client_2.dob as insured_person_dob',
                            'md_ins_type.type as ins_type','company_2.comp_short_name as comp_login_short_name','company_2.comp_full_name as comp_login_full_name','md_deposit_bank.bank_name as bank_name','md_deposit_bank.micr_code as micr_code','md_deposit_bank.ifs_code as ifs_code','md_deposit_bank.branch_name as branch_name','md_deposit_bank.branch_addr as branch_addr','md_employee.emp_name as emp_name')
                            ->where('td_insurance.delete_flag','N')
                                                    ->where('td_insurance.form_status','!=','P')
                            ->whereDate('td_insurance.entry_date','>=',date('Y-m-d',strtotime($start_date)))
                            ->whereDate('td_insurance.entry_date','<=',date('Y-m-d',strtotime($end_date)))
                            ->where('td_insurance.comp_login_dt',$login_status,NULL)
                            ->where('td_insurance.comp_login_cutt_off',$login_status,NULL)
                            ->orderBy('td_insurance.'.$column_name,$sort_by)
                            ->paginate($paginate);
                        }
                    }else {
                        $data=FixedDeposit::join('td_ins_form_received','td_ins_form_received.temp_tin_no','=','td_insurance.temp_tin_no')
                            ->leftJoin('md_ins_company','md_ins_company.id','=','td_insurance.company_id')
                            ->leftJoin('md_ins_company as company_2','company_2.id','=','td_insurance.comp_login_at')
                            ->leftJoin('md_ins_product_type','md_ins_product_type.id','=','td_insurance.product_type_id')
                            ->leftJoin('md_ins_products','md_ins_products.id','=','td_insurance.product_id')
                            ->leftJoin('md_client','md_client.id','=','td_insurance.proposer_id')
                            ->leftJoin('md_client as md_client_2','md_client_2.id','=','td_insurance.insured_person_id')
                            ->leftJoin('md_ins_type','md_ins_type.id','=','td_ins_form_received.ins_type_id')
                            ->leftJoin('md_deposit_bank','md_deposit_bank.id','=','td_insurance.chq_bank')
                        ->leftJoin('md_employee','md_employee.euin_no','=','td_ins_form_received.euin_no')
                            ->select('td_insurance.*','td_ins_form_received.bu_type as bu_type','td_ins_form_received.arn_no as arn_no','td_ins_form_received.euin_no as euin_no','td_ins_form_received.insure_bu_type as insure_bu_type','td_ins_form_received.ins_type_id as ins_type_id',
                            'md_ins_company.comp_short_name as comp_short_name','md_ins_company.comp_full_name as comp_full_name','md_ins_product_type.product_type as product_type','md_ins_products.product_name as product_name',
                            'md_client.client_code as proposer_code','md_client.client_name as proposer_name','md_client.pan as proposer_pan','md_client.client_type as proposer_type','md_client.dob as proposer_dob',
                            'md_client_2.client_code as insured_person_code','md_client_2.client_name as insured_person_name','md_client_2.pan as insured_person_pan','md_client_2.client_type as insured_person_type','md_client_2.dob as insured_person_dob',
                            'md_ins_type.type as ins_type','company_2.comp_short_name as comp_login_short_name','company_2.comp_full_name as comp_login_full_name','md_deposit_bank.bank_name as bank_name','md_deposit_bank.micr_code as micr_code','md_deposit_bank.ifs_code as ifs_code','md_deposit_bank.branch_name as branch_name','md_deposit_bank.branch_addr as branch_addr','md_employee.emp_name as emp_name')
                            ->where('td_insurance.delete_flag','N')
                                                    ->where('td_insurance.form_status','!=','P')
                            ->whereDate('td_insurance.entry_date','>=',date('Y-m-d',strtotime($start_date)))
                            ->whereDate('td_insurance.entry_date','<=',date('Y-m-d',strtotime($end_date)))
                            ->where('td_insurance.comp_login_dt',$login_status,NULL)
                            ->where('td_insurance.comp_login_cutt_off',$login_status,NULL)
                            ->orderBy('td_insurance.updated_at','desc')
                            ->paginate($paginate);
                    }
                }
            }else {
                if ($tin_no) {
                    $data=FixedDeposit::join('td_ins_form_received','td_ins_form_received.temp_tin_no','=','td_insurance.temp_tin_no')
                        ->leftJoin('md_ins_company','md_ins_company.id','=','td_insurance.company_id')
                        ->leftJoin('md_ins_company as company_2','company_2.id','=','td_insurance.comp_login_at')
                        ->leftJoin('md_ins_product_type','md_ins_product_type.id','=','td_insurance.product_type_id')
                        ->leftJoin('md_ins_products','md_ins_products.id','=','td_insurance.product_id')
                        ->leftJoin('md_client','md_client.id','=','td_insurance.proposer_id')
                        ->leftJoin('md_client as md_client_2','md_client_2.id','=','td_insurance.insured_person_id')
                        ->leftJoin('md_ins_type','md_ins_type.id','=','td_ins_form_received.ins_type_id')
                        ->leftJoin('md_deposit_bank','md_deposit_bank.id','=','td_insurance.chq_bank')
                        ->leftJoin('md_employee','md_employee.euin_no','=','td_ins_form_received.euin_no')
                        ->select('td_insurance.*','td_ins_form_received.bu_type as bu_type','td_ins_form_received.arn_no as arn_no','td_ins_form_received.euin_no as euin_no','td_ins_form_received.insure_bu_type as insure_bu_type','td_ins_form_received.ins_type_id as ins_type_id',
                        'md_ins_company.comp_short_name as comp_short_name','md_ins_company.comp_full_name as comp_full_name','md_ins_product_type.product_type as product_type','md_ins_products.product_name as product_name',
                        'md_client.client_code as proposer_code','md_client.client_name as proposer_name','md_client.pan as proposer_pan','md_client.client_type as proposer_type','md_client.dob as proposer_dob',
                        'md_client_2.client_code as insured_person_code','md_client_2.client_name as insured_person_name','md_client_2.pan as insured_person_pan','md_client_2.client_type as insured_person_type','md_client_2.dob as insured_person_dob',
                        'md_ins_type.type as ins_type','company_2.comp_short_name as comp_login_short_name','company_2.comp_full_name as comp_login_full_name','md_deposit_bank.bank_name as bank_name','md_deposit_bank.micr_code as micr_code','md_deposit_bank.ifs_code as ifs_code','md_deposit_bank.branch_name as branch_name','md_deposit_bank.branch_addr as branch_addr','md_employee.emp_name as emp_name')
                        ->where('td_insurance.delete_flag','N')
                                                ->where('td_insurance.form_status','!=','P')
                        ->where('td_insurance.tin_no',$tin_no)
                        ->orderBy('td_insurance.updated_at','desc')
                        ->paginate($paginate);
                }elseif (!empty($bu_type)) {
                    # code...
                } else {
                    // return 'Hii';
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
                        ->where('td_fixed_deposit.form_status','!=','P')
                        ->orderBy('td_fixed_deposit.updated_at','desc')
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

            if ($option==3) {
                if ($login_status=='L') {
                    $login_status="!=";
                }else{
                    $login_status="=";
                }

                if ($date_status=='T') {
                    if ($sort_by && $column_name) {
                        if ($column_name="insure_bu_type") {
                            $data=FixedDeposit::join('td_ins_form_received','td_ins_form_received.temp_tin_no','=','td_insurance.temp_tin_no')
                                ->leftJoin('md_ins_company','md_ins_company.id','=','td_insurance.company_id')
                                ->leftJoin('md_ins_company as company_2','company_2.id','=','td_insurance.comp_login_at')
                                ->leftJoin('md_ins_product_type','md_ins_product_type.id','=','td_insurance.product_type_id')
                                ->leftJoin('md_ins_products','md_ins_products.id','=','td_insurance.product_id')
                                ->leftJoin('md_client','md_client.id','=','td_insurance.proposer_id')
                                ->leftJoin('md_client as md_client_2','md_client_2.id','=','td_insurance.insured_person_id')
                                ->leftJoin('md_ins_type','md_ins_type.id','=','td_ins_form_received.ins_type_id')
                                ->leftJoin('md_deposit_bank','md_deposit_bank.id','=','td_insurance.chq_bank')
                        ->leftJoin('md_employee','md_employee.euin_no','=','td_ins_form_received.euin_no')
                                ->select('td_insurance.*','td_ins_form_received.bu_type as bu_type','td_ins_form_received.arn_no as arn_no','td_ins_form_received.euin_no as euin_no','td_ins_form_received.insure_bu_type as insure_bu_type','td_ins_form_received.ins_type_id as ins_type_id',
                                'md_ins_company.comp_short_name as comp_short_name','md_ins_company.comp_full_name as comp_full_name','md_ins_product_type.product_type as product_type','md_ins_products.product_name as product_name',
                                'md_client.client_code as proposer_code','md_client.client_name as proposer_name','md_client.pan as proposer_pan','md_client.client_type as proposer_type','md_client.dob as proposer_dob',
                                'md_client_2.client_code as insured_person_code','md_client_2.client_name as insured_person_name','md_client_2.pan as insured_person_pan','md_client_2.client_type as insured_person_type','md_client_2.dob as insured_person_dob',
                                'md_ins_type.type as ins_type','company_2.comp_short_name as comp_login_short_name','company_2.comp_full_name as comp_login_full_name','md_deposit_bank.bank_name as bank_name','md_deposit_bank.micr_code as micr_code','md_deposit_bank.ifs_code as ifs_code','md_deposit_bank.branch_name as branch_name','md_deposit_bank.branch_addr as branch_addr','md_employee.emp_name as emp_name')
                                ->where('td_insurance.delete_flag','N')
                                ->where('td_insurance.form_status','!=','P')
                                ->whereDate('td_insurance.entry_date',date('Y-m-d'))
                                ->where('td_insurance.comp_login_dt',$login_status,NULL)
                                ->where('td_insurance.comp_login_cutt_off',$login_status,NULL)
                                ->orderBy('td_ins_form_received.bu_type',$sort_by)
                                ->get();
                        }else {
                            $data=FixedDeposit::join('td_ins_form_received','td_ins_form_received.temp_tin_no','=','td_insurance.temp_tin_no')
                                ->leftJoin('md_ins_company','md_ins_company.id','=','td_insurance.company_id')
                                ->leftJoin('md_ins_company as company_2','company_2.id','=','td_insurance.comp_login_at')
                                ->leftJoin('md_ins_product_type','md_ins_product_type.id','=','td_insurance.product_type_id')
                                ->leftJoin('md_ins_products','md_ins_products.id','=','td_insurance.product_id')
                                ->leftJoin('md_client','md_client.id','=','td_insurance.proposer_id')
                                ->leftJoin('md_client as md_client_2','md_client_2.id','=','td_insurance.insured_person_id')
                                ->leftJoin('md_ins_type','md_ins_type.id','=','td_ins_form_received.ins_type_id')
                                ->leftJoin('md_deposit_bank','md_deposit_bank.id','=','td_insurance.chq_bank')
                        ->leftJoin('md_employee','md_employee.euin_no','=','td_ins_form_received.euin_no')
                                ->select('td_insurance.*','td_ins_form_received.bu_type as bu_type','td_ins_form_received.arn_no as arn_no','td_ins_form_received.euin_no as euin_no','td_ins_form_received.insure_bu_type as insure_bu_type','td_ins_form_received.ins_type_id as ins_type_id',
                                'md_ins_company.comp_short_name as comp_short_name','md_ins_company.comp_full_name as comp_full_name','md_ins_product_type.product_type as product_type','md_ins_products.product_name as product_name',
                                'md_client.client_code as proposer_code','md_client.client_name as proposer_name','md_client.pan as proposer_pan','md_client.client_type as proposer_type','md_client.dob as proposer_dob',
                                'md_client_2.client_code as insured_person_code','md_client_2.client_name as insured_person_name','md_client_2.pan as insured_person_pan','md_client_2.client_type as insured_person_type','md_client_2.dob as insured_person_dob',
                                'md_ins_type.type as ins_type','company_2.comp_short_name as comp_login_short_name','company_2.comp_full_name as comp_login_full_name','md_deposit_bank.bank_name as bank_name','md_deposit_bank.micr_code as micr_code','md_deposit_bank.ifs_code as ifs_code','md_deposit_bank.branch_name as branch_name','md_deposit_bank.branch_addr as branch_addr','md_employee.emp_name as emp_name')
                                ->where('td_insurance.delete_flag','N')
                        ->where('td_insurance.form_status','!=','P')
                                ->whereDate('td_insurance.entry_date',date('Y-m-d'))
                                ->where('td_insurance.comp_login_dt',$login_status,NULL)
                                ->where('td_insurance.comp_login_cutt_off',$login_status,NULL)
                                ->orderBy('td_insurance.'.$column_name,$sort_by)
                                ->get();
                        }
                    }else {
                        $data=FixedDeposit::join('td_ins_form_received','td_ins_form_received.temp_tin_no','=','td_insurance.temp_tin_no')
                            ->leftJoin('md_ins_company','md_ins_company.id','=','td_insurance.company_id')
                            ->leftJoin('md_ins_company as company_2','company_2.id','=','td_insurance.comp_login_at')
                            ->leftJoin('md_ins_product_type','md_ins_product_type.id','=','td_insurance.product_type_id')
                            ->leftJoin('md_ins_products','md_ins_products.id','=','td_insurance.product_id')
                            ->leftJoin('md_client','md_client.id','=','td_insurance.proposer_id')
                            ->leftJoin('md_client as md_client_2','md_client_2.id','=','td_insurance.insured_person_id')
                            ->leftJoin('md_ins_type','md_ins_type.id','=','td_ins_form_received.ins_type_id')
                            ->leftJoin('md_deposit_bank','md_deposit_bank.id','=','td_insurance.chq_bank')
                        ->leftJoin('md_employee','md_employee.euin_no','=','td_ins_form_received.euin_no')
                            ->select('td_insurance.*','td_ins_form_received.bu_type as bu_type','td_ins_form_received.arn_no as arn_no','td_ins_form_received.euin_no as euin_no','td_ins_form_received.insure_bu_type as insure_bu_type','td_ins_form_received.ins_type_id as ins_type_id',
                            'md_ins_company.comp_short_name as comp_short_name','md_ins_company.comp_full_name as comp_full_name','md_ins_product_type.product_type as product_type','md_ins_products.product_name as product_name',
                            'md_client.client_code as proposer_code','md_client.client_name as proposer_name','md_client.pan as proposer_pan','md_client.client_type as proposer_type','md_client.dob as proposer_dob',
                            'md_client_2.client_code as insured_person_code','md_client_2.client_name as insured_person_name','md_client_2.pan as insured_person_pan','md_client_2.client_type as insured_person_type','md_client_2.dob as insured_person_dob',
                            'md_ins_type.type as ins_type','company_2.comp_short_name as comp_login_short_name','company_2.comp_full_name as comp_login_full_name','md_deposit_bank.bank_name as bank_name','md_deposit_bank.micr_code as micr_code','md_deposit_bank.ifs_code as ifs_code','md_deposit_bank.branch_name as branch_name','md_deposit_bank.branch_addr as branch_addr','md_employee.emp_name as emp_name')
                            ->where('td_insurance.delete_flag','N')
                        ->where('td_insurance.form_status','!=','P')
                            ->whereDate('td_insurance.entry_date',date('Y-m-d'))
                            ->where('td_insurance.comp_login_dt',$login_status,NULL)
                            ->where('td_insurance.comp_login_cutt_off',$login_status,NULL)
                            ->orderBy('td_insurance.updated_at','desc')
                            ->get();
                    }
                }else {
                    if ($sort_by && $column_name) {
                        if ($column_name="insure_bu_type") {
                            $data=FixedDeposit::join('td_ins_form_received','td_ins_form_received.temp_tin_no','=','td_insurance.temp_tin_no')
                            ->leftJoin('md_ins_company','md_ins_company.id','=','td_insurance.company_id')
                            ->leftJoin('md_ins_company as company_2','company_2.id','=','td_insurance.comp_login_at')
                            ->leftJoin('md_ins_product_type','md_ins_product_type.id','=','td_insurance.product_type_id')
                            ->leftJoin('md_ins_products','md_ins_products.id','=','td_insurance.product_id')
                            ->leftJoin('md_client','md_client.id','=','td_insurance.proposer_id')
                            ->leftJoin('md_client as md_client_2','md_client_2.id','=','td_insurance.insured_person_id')
                            ->leftJoin('md_ins_type','md_ins_type.id','=','td_ins_form_received.ins_type_id')
                            ->leftJoin('md_deposit_bank','md_deposit_bank.id','=','td_insurance.chq_bank')
                        ->leftJoin('md_employee','md_employee.euin_no','=','td_ins_form_received.euin_no')
                            ->select('td_insurance.*','td_ins_form_received.bu_type as bu_type','td_ins_form_received.arn_no as arn_no','td_ins_form_received.euin_no as euin_no','td_ins_form_received.insure_bu_type as insure_bu_type','td_ins_form_received.ins_type_id as ins_type_id',
                            'md_ins_company.comp_short_name as comp_short_name','md_ins_company.comp_full_name as comp_full_name','md_ins_product_type.product_type as product_type','md_ins_products.product_name as product_name',
                            'md_client.client_code as proposer_code','md_client.client_name as proposer_name','md_client.pan as proposer_pan','md_client.client_type as proposer_type','md_client.dob as proposer_dob',
                            'md_client_2.client_code as insured_person_code','md_client_2.client_name as insured_person_name','md_client_2.pan as insured_person_pan','md_client_2.client_type as insured_person_type','md_client_2.dob as insured_person_dob',
                            'md_ins_type.type as ins_type','company_2.comp_short_name as comp_login_short_name','company_2.comp_full_name as comp_login_full_name','md_deposit_bank.bank_name as bank_name','md_deposit_bank.micr_code as micr_code','md_deposit_bank.ifs_code as ifs_code','md_deposit_bank.branch_name as branch_name','md_deposit_bank.branch_addr as branch_addr','md_employee.emp_name as emp_name')
                            ->where('td_insurance.delete_flag','N')
                        ->where('td_insurance.form_status','!=','P')
                            ->whereDate('td_insurance.entry_date','>=',date('Y-m-d',strtotime($start_date)))
                            ->whereDate('td_insurance.entry_date','<=',date('Y-m-d',strtotime($end_date)))
                            ->where('td_insurance.comp_login_dt',$login_status,NULL)
                            ->where('td_insurance.comp_login_cutt_off',$login_status,NULL)
                            ->orderBy('td_ins_form_received.bu_type',$sort_by)
                            ->get();
                        }else {
                            $data=FixedDeposit::join('td_ins_form_received','td_ins_form_received.temp_tin_no','=','td_insurance.temp_tin_no')
                            ->leftJoin('md_ins_company','md_ins_company.id','=','td_insurance.company_id')
                            ->leftJoin('md_ins_company as company_2','company_2.id','=','td_insurance.comp_login_at')
                            ->leftJoin('md_ins_product_type','md_ins_product_type.id','=','td_insurance.product_type_id')
                            ->leftJoin('md_ins_products','md_ins_products.id','=','td_insurance.product_id')
                            ->leftJoin('md_client','md_client.id','=','td_insurance.proposer_id')
                            ->leftJoin('md_client as md_client_2','md_client_2.id','=','td_insurance.insured_person_id')
                            ->leftJoin('md_ins_type','md_ins_type.id','=','td_ins_form_received.ins_type_id')
                            ->leftJoin('md_deposit_bank','md_deposit_bank.id','=','td_insurance.chq_bank')
                        ->leftJoin('md_employee','md_employee.euin_no','=','td_ins_form_received.euin_no')
                            ->select('td_insurance.*','td_ins_form_received.bu_type as bu_type','td_ins_form_received.arn_no as arn_no','td_ins_form_received.euin_no as euin_no','td_ins_form_received.insure_bu_type as insure_bu_type','td_ins_form_received.ins_type_id as ins_type_id',
                            'md_ins_company.comp_short_name as comp_short_name','md_ins_company.comp_full_name as comp_full_name','md_ins_product_type.product_type as product_type','md_ins_products.product_name as product_name',
                            'md_client.client_code as proposer_code','md_client.client_name as proposer_name','md_client.pan as proposer_pan','md_client.client_type as proposer_type','md_client.dob as proposer_dob',
                            'md_client_2.client_code as insured_person_code','md_client_2.client_name as insured_person_name','md_client_2.pan as insured_person_pan','md_client_2.client_type as insured_person_type','md_client_2.dob as insured_person_dob',
                            'md_ins_type.type as ins_type','company_2.comp_short_name as comp_login_short_name','company_2.comp_full_name as comp_login_full_name','md_deposit_bank.bank_name as bank_name','md_deposit_bank.micr_code as micr_code','md_deposit_bank.ifs_code as ifs_code','md_deposit_bank.branch_name as branch_name','md_deposit_bank.branch_addr as branch_addr','md_employee.emp_name as emp_name')
                            ->where('td_insurance.delete_flag','N')
                        ->where('td_insurance.form_status','!=','P')
                            ->whereDate('td_insurance.entry_date','>=',date('Y-m-d',strtotime($start_date)))
                            ->whereDate('td_insurance.entry_date','<=',date('Y-m-d',strtotime($end_date)))
                            ->where('td_insurance.comp_login_dt',$login_status,NULL)
                            ->where('td_insurance.comp_login_cutt_off',$login_status,NULL)
                            ->orderBy('td_insurance.'.$column_name,$sort_by)
                            ->get();
                        }
                    }else {
                        $data=FixedDeposit::join('td_ins_form_received','td_ins_form_received.temp_tin_no','=','td_insurance.temp_tin_no')
                            ->leftJoin('md_ins_company','md_ins_company.id','=','td_insurance.company_id')
                            ->leftJoin('md_ins_company as company_2','company_2.id','=','td_insurance.comp_login_at')
                            ->leftJoin('md_ins_product_type','md_ins_product_type.id','=','td_insurance.product_type_id')
                            ->leftJoin('md_ins_products','md_ins_products.id','=','td_insurance.product_id')
                            ->leftJoin('md_client','md_client.id','=','td_insurance.proposer_id')
                            ->leftJoin('md_client as md_client_2','md_client_2.id','=','td_insurance.insured_person_id')
                            ->leftJoin('md_ins_type','md_ins_type.id','=','td_ins_form_received.ins_type_id')
                            ->leftJoin('md_deposit_bank','md_deposit_bank.id','=','td_insurance.chq_bank')
                        ->leftJoin('md_employee','md_employee.euin_no','=','td_ins_form_received.euin_no')
                            ->select('td_insurance.*','td_ins_form_received.bu_type as bu_type','td_ins_form_received.arn_no as arn_no','td_ins_form_received.euin_no as euin_no','td_ins_form_received.insure_bu_type as insure_bu_type','td_ins_form_received.ins_type_id as ins_type_id',
                            'md_ins_company.comp_short_name as comp_short_name','md_ins_company.comp_full_name as comp_full_name','md_ins_product_type.product_type as product_type','md_ins_products.product_name as product_name',
                            'md_client.client_code as proposer_code','md_client.client_name as proposer_name','md_client.pan as proposer_pan','md_client.client_type as proposer_type','md_client.dob as proposer_dob',
                            'md_client_2.client_code as insured_person_code','md_client_2.client_name as insured_person_name','md_client_2.pan as insured_person_pan','md_client_2.client_type as insured_person_type','md_client_2.dob as insured_person_dob',
                            'md_ins_type.type as ins_type','company_2.comp_short_name as comp_login_short_name','company_2.comp_full_name as comp_login_full_name','md_deposit_bank.bank_name as bank_name','md_deposit_bank.micr_code as micr_code','md_deposit_bank.ifs_code as ifs_code','md_deposit_bank.branch_name as branch_name','md_deposit_bank.branch_addr as branch_addr','md_employee.emp_name as emp_name')
                            ->where('td_insurance.delete_flag','N')
                        ->where('td_insurance.form_status','!=','P')
                            ->whereDate('td_insurance.entry_date','>=',date('Y-m-d',strtotime($start_date)))
                            ->whereDate('td_insurance.entry_date','<=',date('Y-m-d',strtotime($end_date)))
                            ->where('td_insurance.comp_login_dt',$login_status,NULL)
                            ->where('td_insurance.comp_login_cutt_off',$login_status,NULL)
                            ->orderBy('td_insurance.updated_at','desc')
                            ->get();
                    }
                }
            }else {
                if ($tin_no) {
                    
                }elseif (!empty($bu_type)) {
                    
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
                        ->where('td_fixed_deposit.form_status','!=','P')
                        ->orderBy('td_fixed_deposit.updated_at','desc')
                        ->get();
                }
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
