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
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            
            $tin_no=$request->tin_no;
            $option=$request->option;
            $sub_brk_cd=$request->sub_brk_cd;
            $proposer_name=$request->proposer_name;
            $euin_no=$request->euin_no;
            $bu_type=json_decode($request->bu_type);
            $ins_type_id=json_decode($request->ins_type_id);
            $insured_bu_type=json_decode($request->insured_bu_type);
            $from_date=$request->from_date;
            $to_date=$request->to_date;

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
                            ->whereDate('td_insurance.entry_date','>=',date('Y-m-d',strtotime($start_date)))
                            ->whereDate('td_insurance.entry_date','<=',date('Y-m-d',strtotime($end_date)))
                            ->where('td_insurance.comp_login_dt',$login_status,NULL)
                            ->where('td_insurance.comp_login_cutt_off',$login_status,NULL)
                            ->orderBy('td_insurance.updated_at','desc')
                            ->paginate($paginate);
                    }
                }
            }else {
                if ($sort_by && $column_name) {
                    if ($column_name=="bu_type" || $column_name=="arn_no" || $column_name=="euin_no" || $column_name=="insure_bu_type") {
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
                            ->where('td_insurance.tin_no',$tin_no)
                            ->orderBy('td_ins_form_received.bu_type',$sort_by)
                            ->paginate($paginate);
                    }elseif ($column_name=="comp_full_name" || $column_name=="comp_short_name") {
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
                            ->where('td_insurance.tin_no',$tin_no)
                            ->orderBy('md_ins_company.'.$column_name,$sort_by)
                            ->paginate($paginate);
                    }elseif ($column_name=="product_type") {
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
                            ->where('td_insurance.tin_no',$tin_no)
                            ->orderBy('md_ins_product_type.'.$column_name,$sort_by)
                            ->paginate($paginate);
                    }elseif ($column_name=="product_name") {
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
                            ->where('td_insurance.tin_no',$tin_no)
                            ->orderBy('md_ins_products.'.$column_name,$sort_by)
                            ->paginate($paginate);
                    } else {
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
                            ->where('td_insurance.tin_no',$tin_no)
                            ->orderBy('td_insurance.'.$column_name,$sort_by)
                            ->paginate($paginate);
                    }
                }elseif ($from_date && $to_date) {
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
                        ->whereDate('td_insurance.entry_date','>=',$from_date)
                        ->whereDate('td_insurance.entry_date','<=',$to_date)
                        ->orderBy('td_insurance.updated_at','desc')
                        ->paginate($paginate);
                }elseif (!empty($bu_type)) {
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
                        ->whereIn('td_ins_form_received.bu_type',$bu_type)
                        ->orderBy('td_insurance.updated_at','desc')
                        ->paginate($paginate);
                }elseif (!empty($ins_type_id)) {
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
                        ->whereIn('td_ins_form_received.ins_type_id',$ins_type_id)
                        ->orderBy('td_insurance.updated_at','desc')
                        ->paginate($paginate);
                }elseif (!empty($insured_bu_type)) {
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
                        ->whereIn('td_ins_form_received.insure_bu_type',$insured_bu_type)
                        ->orderBy('td_insurance.updated_at','desc')
                        ->paginate($paginate);
                }elseif ($proposer_name) {
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
                        ->where('md_client.client_code','like', '%' . $proposer_name . '%')
                        ->orWhere('md_client.client_name','like', '%' . $proposer_name . '%')
                        ->orWhere('md_client.pan','like', '%' . $proposer_name . '%')
                        ->orWhere('company_2.proposer_name','like', '%' . $proposer_name . '%')
                        ->orWhere('company_2.client_name','like', '%' . $proposer_name . '%')
                        ->orWhere('company_2.pan','like', '%' . $proposer_name . '%')
                        ->orderBy('td_insurance.updated_at','desc')
                        ->paginate($paginate);
                } elseif ($tin_no) {
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
                        ->where('td_insurance.tin_no',$tin_no)
                        ->orderBy('td_insurance.updated_at','desc')
                        ->paginate($paginate);
                }elseif (!empty($bu_type)) {
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
                        ->whereIn('td_ins_form_received.bu_type',$bu_type)
                        ->orderBy('td_insurance.updated_at','desc')
                        ->paginate($paginate);
                } else {
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
                        ->orderBy('td_insurance.updated_at','desc')
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
            $proposer_name=$request->proposer_name;
            $euin_no=$request->euin_no;
            $bu_type=json_decode($request->bu_type);
            $ins_type_id=json_decode($request->ins_type_id);
            $insured_bu_type=json_decode($request->insured_bu_type);
            $from_date=$request->from_date;
            $to_date=$request->to_date;

            
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
                            ->whereDate('td_insurance.entry_date','>=',date('Y-m-d',strtotime($start_date)))
                            ->whereDate('td_insurance.entry_date','<=',date('Y-m-d',strtotime($end_date)))
                            ->where('td_insurance.comp_login_dt',$login_status,NULL)
                            ->where('td_insurance.comp_login_cutt_off',$login_status,NULL)
                            ->orderBy('td_insurance.updated_at','desc')
                            ->get();
                    }
                }
            }else {
                if ($sort_by && $column_name) {
                    if ($column_name=="bu_type" || $column_name=="arn_no" || $column_name=="euin_no" || $column_name=="insure_bu_type") {
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
                            ->where('td_insurance.tin_no',$tin_no)
                            ->orderBy('td_ins_form_received.bu_type',$sort_by)
                            ->get();
                    }elseif ($column_name=="comp_full_name" || $column_name=="comp_short_name") {
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
                            ->where('td_insurance.tin_no',$tin_no)
                            ->orderBy('md_ins_company.'.$column_name,$sort_by)
                            ->get();
                    }elseif ($column_name=="product_type") {
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
                            ->where('td_insurance.tin_no',$tin_no)
                            ->orderBy('md_ins_product_type.'.$column_name,$sort_by)
                            ->get();
                    }elseif ($column_name=="product_name") {
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
                            ->where('td_insurance.tin_no',$tin_no)
                            ->orderBy('md_ins_products.'.$column_name,$sort_by)
                            ->get();
                    } else {
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
                            ->where('td_insurance.tin_no',$tin_no)
                            ->orderBy('td_insurance.'.$column_name,$sort_by)
                            ->get();
                    }
                }elseif ($from_date && $to_date) {
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
                        ->whereDate('td_insurance.entry_date','>=',$from_date)
                        ->whereDate('td_insurance.entry_date','<=',$to_date)
                        ->orderBy('td_insurance.updated_at','desc')
                        ->get();
                }elseif (!empty($bu_type)) {
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
                        ->whereIn('td_ins_form_received.bu_type',$bu_type)
                        ->orderBy('td_insurance.updated_at','desc')
                        ->get();
                }elseif (!empty($ins_type_id)) {
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
                        ->whereIn('td_ins_form_received.ins_type_id',$ins_type_id)
                        ->orderBy('td_insurance.updated_at','desc')
                        ->get();
                }elseif (!empty($insured_bu_type)) {
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
                        ->whereIn('td_ins_form_received.insure_bu_type',$insured_bu_type)
                        ->orderBy('td_insurance.updated_at','desc')
                        ->get();
                }elseif ($proposer_name) {
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
                        ->where('md_client.client_code','like', '%' . $proposer_name . '%')
                        ->orWhere('md_client.client_name','like', '%' . $proposer_name . '%')
                        ->orWhere('md_client.pan','like', '%' . $proposer_name . '%')
                        ->orWhere('company_2.proposer_name','like', '%' . $proposer_name . '%')
                        ->orWhere('company_2.client_name','like', '%' . $proposer_name . '%')
                        ->orWhere('company_2.pan','like', '%' . $proposer_name . '%')
                        ->orderBy('td_insurance.updated_at','desc')
                        ->get();
                } elseif ($tin_no) {
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
                        ->where('td_insurance.tin_no',$tin_no)
                        ->orderBy('td_insurance.updated_at','desc')
                        ->get();
                }elseif (!empty($bu_type)) {
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
                        ->whereIn('td_ins_form_received.bu_type',$bu_type)
                        ->orderBy('td_insurance.updated_at','desc')
                        ->get();
                } else {
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
                        ->orderBy('td_insurance.updated_at','desc')
                        ->get();
                }
            }
        } catch (\Throwable $th) {
            //throw $th;
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
            return $request;
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
                        'proposer_id'=>$request->proposer_id,
                        'insure_bu_type'=>$request->insure_bu_type,
                        'ins_type_id'=>$request->ins_type_id,
                        'recv_from'=>$request->recv_from,
                        // 'branch_code'=>$branch_code,
                        // 'created_by'=>'',
                    ));       
                    
                    $insured_person_id=$request->insured_person_id;
                    $insured_person_name=$request->insured_person_name;
                    $insured_person_pan=$request->insured_person_pan;
                    $insured_person_dob=$request->insured_person_dob;
                    if ($insured_person_id=='' && $insured_person!='' && $insured_person_pan!='' && $insured_person_dob!='') {
                        $t_c_data=Client::create(array(
                            'client_name'=>$insured_person,
                            'pan'=>$insured_person_pan,
                            'dob'=>$insured_person_dob,
                            'client_type'=>'E',
                            // 'created_by'=>'',
                        ));  
                        $insured_person_id=$t_c_data->id;
                    }

                    $app_form_scan=$request->app_form_scan;
                    $doc_name='';
                    if ($app_form_scan) {
                        $cv_path_extension=$app_form_scan->getClientOriginalExtension();
                        $doc_name=microtime(true).".".$cv_path_extension;
                        $app_form_scan->move(public_path('ins-application-form/'),$doc_name);
                    }
                    $data=FixedDeposit::create(array(
                        'temp_tin_no' =>$request->temp_tin_no,
                        'tin_no'=> $tin_no,
                        'entry_tin_status'=>$request->tin_status,
                        'entry_date'=> date('Y-m-d'),
                        'proposer_id'=>$request->proposer_id,
                        'insured_person_id'=>$insured_person_id,
                        'company_id'=>$request->company_id,
                        'product_type_id'=>$request->product_type_id,
                        'product_id'=>$request->product_id,
                        'proposal_no'=>isset($request->proposal_no)?$request->proposal_no:NULL,
                        'policy_no'=>isset($request->policy_no)?$request->policy_no:NULL,
                        'sum_assured'=>isset($request->sum_assured)?$request->sum_assured:NULL,
                        'sum_insured'=>isset($request->sum_insured)?$request->sum_insured:NULL,
                        'mode_of_premium'=>$request->mode_of_premium,
                        'premium_paying_date'=>isset($request->premium_paying_date)?$request->premium_paying_date:NULL,
                        'gross_premium'=>$request->gross_premium,
                        'net_premium'=>$request->net_premium,
                        'third_party_premium'=>isset($request->third_party_premium)?$request->third_party_premium:NULL,
                        'od_premium'=>isset($request->od_premium)?$request->od_premium:NULL,
                        'mode_of_payment'=>$request->mode_of_payment,
                        'chq_bank'=>$request->chq_bank,
                        'acc_no'=>$request->acc_no,
                        'payment_ref_no'=>$request->payment_ref_no,
                        'chq_no'=>$request->chq_no,
                        'policy_term'=>$request->policy_term,
                        'policy_pre_pay_term'=>$request->policy_pre_pay_term,
                        'app_form_scan'=>$doc_name,
                        'comp_login_at'=>$request->comp_login_at,
                        'remarks'=>$request->remarks,
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
                    $app_form_scan->move(public_path('ins-application-form/'),$doc_name);
                }
                $data=FixedDeposit::create(array(
                    'temp_tin_no' =>$ttin_no,
                    'tin_no'=> $tin_no,
                    'entry_tin_status'=>$request->tin_status,
                    'entry_date'=> date('Y-m-d'),
                    'proposer_id'=>$request->proposer_id,
                    'insured_person_id'=>$insured_person_id,
                    'company_id'=>$request->company_id,
                    'product_type_id'=>$request->product_type_id,
                    'product_id'=>$request->product_id,
                    'proposal_no'=>isset($request->proposal_no)?$request->proposal_no:NULL,
                    'policy_no'=>isset($request->policy_no)?$request->policy_no:NULL,
                    'sum_assured'=>isset($request->sum_assured)?$request->sum_assured:NULL,
                    'sum_insured'=>isset($request->sum_insured)?$request->sum_insured:NULL,
                    'mode_of_premium'=>$request->mode_of_premium,
                    'premium_paying_date'=>isset($request->premium_paying_date)?$request->premium_paying_date:NULL,
                    'gross_premium'=>$request->gross_premium,
                    'net_premium'=>$request->net_premium,
                    'third_party_premium'=>isset($request->third_party_premium)?$request->third_party_premium:NULL,
                    'od_premium'=>isset($request->od_premium)?$request->od_premium:NULL,
                    'mode_of_payment'=>$request->mode_of_payment,
                    'chq_bank'=>$request->chq_bank,
                    'acc_no'=>$request->acc_no,
                    'payment_ref_no'=>$request->payment_ref_no,
                    'chq_no'=>$request->chq_no,
                    'policy_term'=>$request->policy_term,
                    'policy_pre_pay_term'=>$request->policy_pre_pay_term,
                    'app_form_scan'=>$doc_name,
                    'comp_login_at'=>$request->comp_login_at,
                    'remarks'=>$request->remarks,
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
