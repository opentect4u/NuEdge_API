<?php

namespace App\Http\Controllers\v1\INSOperation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{InsProduct,Insurance,InsFormReceived,InsBuOpportunity};
use Validator;

class BuOpportunityController extends Controller
{
    public function searchDetails(Request $request)
    {
        try {
            $paginate=$request->paginate;
            $field=$request->field;
            $order=$request->order;

            $from_date=$request->from_date;
            $to_date=$request->to_date;
            $temp_tin_no=$request->temp_tin_no;
            $proposer_code=$request->proposer_code;
            $ins_type_id=json_decode($request->ins_type_id);
            $renewal_month=$request->renewal_month;
            $renewal_year=$request->renewal_year;
            $company_id=json_decode($request->company_id);
            $product_type_id=json_decode($request->product_type_id);
            $product_id=json_decode($request->product_id);

            if ($order && $field) {
                $rawOrderBy='';
                if ($order > 0) {
                    $rawOrderBy=$field." ASC";
                } else {
                    $rawOrderBy=$field." DESC";
                }
                if (($from_date && $to_date) || $temp_tin_no || $proposer_code || $ins_type_id || $renewal_month || $renewal_year || $company_id || $product_type_id || $product_id) {
                    $rawQuery='';
                    $rawQuery=$this->filterCriteria($rawQuery,$from_date,$to_date,$temp_tin_no,$proposer_code,$ins_type_id,$renewal_month,$renewal_year,$company_id,$product_type_id,$product_id);
                    // return $rawQuery;
                    $data=InsBuOpportunity::leftJoin('md_branch','md_branch.id','=','td_ins_bu_opportunity.branch_code')
                        ->leftJoin('md_employee','md_employee.euin_no','=','td_ins_bu_opportunity.euin_no')
                        ->leftJoin('md_client','md_client.id','=','td_ins_bu_opportunity.proposer_id')
                        ->leftJoin('md_ins_type','md_ins_type.id','=','td_ins_bu_opportunity.ins_type_id')
                        ->leftJoin('md_client as md_client_2','md_client_2.id','=','td_ins_bu_opportunity.insured_person_id')
                        ->leftJoin('md_ins_company','md_ins_company.id','=','td_ins_bu_opportunity.comp_id')
                        ->leftJoin('md_ins_product_type','md_ins_product_type.id','=','td_ins_bu_opportunity.product_type_id')
                        ->leftJoin('md_ins_products','md_ins_products.id','=','td_ins_bu_opportunity.product_id')
                        ->select('td_ins_bu_opportunity.*','md_branch.brn_name as branch_name','md_employee.emp_name as rm_name','md_employee.emp_name as emp_name',
                        'md_client.client_name as proposer_name','md_client.client_code as proposer_code','md_client.pan as proposer_pan','md_client.dob as proposer_dob','md_ins_type.type as ins_type',
                        'md_client_2.client_name as insured_person_name','md_client_2.client_code as insured_person_code','md_client_2.pan as insured_person_pan','md_client_2.dob as insured_person_dob',
                        'md_ins_company.comp_short_name as comp_short_name','md_ins_company.comp_full_name as comp_full_name','md_ins_product_type.product_type as product_type','md_ins_products.product_name as product_name')
                        ->whereRaw($rawQuery)
                        ->orderByRaw($rawOrderBy)
                        ->paginate($paginate);
                } else {
                    $data=InsBuOpportunity::leftJoin('md_branch','md_branch.id','=','td_ins_bu_opportunity.branch_code')
                        ->leftJoin('md_employee','md_employee.euin_no','=','td_ins_bu_opportunity.euin_no')
                        ->leftJoin('md_client','md_client.id','=','td_ins_bu_opportunity.proposer_id')
                        ->leftJoin('md_ins_type','md_ins_type.id','=','td_ins_bu_opportunity.ins_type_id')
                        ->leftJoin('md_client as md_client_2','md_client_2.id','=','td_ins_bu_opportunity.insured_person_id')
                        ->leftJoin('md_ins_company','md_ins_company.id','=','td_ins_bu_opportunity.comp_id')
                        ->leftJoin('md_ins_product_type','md_ins_product_type.id','=','td_ins_bu_opportunity.product_type_id')
                        ->leftJoin('md_ins_products','md_ins_products.id','=','td_ins_bu_opportunity.product_id')
                        ->select('td_ins_bu_opportunity.*','md_branch.brn_name as branch_name','md_employee.emp_name as rm_name','md_employee.emp_name as emp_name',
                        'md_client.client_name as proposer_name','md_client.client_code as proposer_code','md_client.pan as proposer_pan','md_client.dob as proposer_dob','md_ins_type.type as ins_type',
                        'md_client_2.client_name as insured_person_name','md_client_2.client_code as insured_person_code','md_client_2.pan as insured_person_pan','md_client_2.dob as insured_person_dob',
                        'md_ins_company.comp_short_name as comp_short_name','md_ins_company.comp_full_name as comp_full_name','md_ins_product_type.product_type as product_type','md_ins_products.product_name as product_name')
                        ->orderByRaw($rawOrderBy)
                        ->paginate($paginate);
                }
            } elseif (($from_date && $to_date) || $temp_tin_no || $proposer_code || $ins_type_id || $renewal_month || $renewal_year || $company_id || $product_type_id || $product_id) {
                $rawQuery='';
                $rawQuery=$this->filterCriteria($rawQuery,$from_date,$to_date,$temp_tin_no,$proposer_code,$ins_type_id,$renewal_month,$renewal_year,$company_id,$product_type_id,$product_id);
                // return $rawQuery;
                $data=InsBuOpportunity::leftJoin('md_branch','md_branch.id','=','td_ins_bu_opportunity.branch_code')
                    ->leftJoin('md_employee','md_employee.euin_no','=','td_ins_bu_opportunity.euin_no')
                    ->leftJoin('md_client','md_client.id','=','td_ins_bu_opportunity.proposer_id')
                    ->leftJoin('md_ins_type','md_ins_type.id','=','td_ins_bu_opportunity.ins_type_id')
                    ->leftJoin('md_client as md_client_2','md_client_2.id','=','td_ins_bu_opportunity.insured_person_id')
                    ->leftJoin('md_ins_company','md_ins_company.id','=','td_ins_bu_opportunity.comp_id')
                    ->leftJoin('md_ins_product_type','md_ins_product_type.id','=','td_ins_bu_opportunity.product_type_id')
                    ->leftJoin('md_ins_products','md_ins_products.id','=','td_ins_bu_opportunity.product_id')
                    ->select('td_ins_bu_opportunity.*','md_branch.brn_name as branch_name','md_employee.emp_name as rm_name','md_employee.emp_name as emp_name',
                    'md_client.client_name as proposer_name','md_client.client_code as proposer_code','md_client.pan as proposer_pan','md_client.dob as proposer_dob','md_ins_type.type as ins_type',
                    'md_client_2.client_name as insured_person_name','md_client_2.client_code as insured_person_code','md_client_2.pan as insured_person_pan','md_client_2.dob as insured_person_dob',
                    'md_ins_company.comp_short_name as comp_short_name','md_ins_company.comp_full_name as comp_full_name','md_ins_product_type.product_type as product_type','md_ins_products.product_name as product_name')
                    ->whereRaw($rawQuery)
                    ->orderBy('rec_datetime','DESC')
                    ->paginate($paginate);
            } else {
                $data=InsBuOpportunity::leftJoin('md_branch','md_branch.id','=','td_ins_bu_opportunity.branch_code')
                    ->leftJoin('md_employee','md_employee.euin_no','=','td_ins_bu_opportunity.euin_no')
                    ->leftJoin('md_client','md_client.id','=','td_ins_bu_opportunity.proposer_id')
                    ->leftJoin('md_ins_type','md_ins_type.id','=','td_ins_bu_opportunity.ins_type_id')
                    ->leftJoin('md_client as md_client_2','md_client_2.id','=','td_ins_bu_opportunity.insured_person_id')
                    ->leftJoin('md_ins_company','md_ins_company.id','=','td_ins_bu_opportunity.comp_id')
                    ->leftJoin('md_ins_product_type','md_ins_product_type.id','=','td_ins_bu_opportunity.product_type_id')
                    ->leftJoin('md_ins_products','md_ins_products.id','=','td_ins_bu_opportunity.product_id')
                    ->select('td_ins_bu_opportunity.*','md_branch.brn_name as branch_name','md_employee.emp_name as rm_name','md_employee.emp_name as emp_name',
                    'md_client.client_name as proposer_name','md_client.client_code as proposer_code','md_client.pan as proposer_pan','md_client.dob as proposer_dob','md_ins_type.type as ins_type',
                    'md_client_2.client_name as insured_person_name','md_client_2.client_code as insured_person_code','md_client_2.pan as insured_person_pan','md_client_2.dob as insured_person_dob',
                    'md_ins_company.comp_short_name as comp_short_name','md_ins_company.comp_full_name as comp_full_name','md_ins_product_type.product_type as product_type','md_ins_products.product_name as product_name')
                    ->orderBy('rec_datetime','DESC')
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
            $data=[];
        } catch (\Throwable $th) {
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
                $data=InsBuOpportunity::where('temp_tin_no','like', '%' . $tin_no . '%')
                    ->orderBy('rec_datetime','DESC')
                    ->get();
            } else {
                $data=InsBuOpportunity::orderBy('rec_datetime','DESC')
                    ->get();
            }
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function createUpdate(Request $request)
    {
        try {
            // return $request;
            $temp_tin_no=$request->temp_tin_no;
            $arn_no=Helper::CommonParamValue(1);
            if ($temp_tin_no) {
                $fetch_dt=InsBuOpportunity::where('temp_tin_no',$temp_tin_no)->first();
                // return $fetch_dt;
                $upload_file=$request->upload_file;
                if ($upload_file) {
                    $path_extension=$upload_file->getClientOriginalExtension();
                    $upload_file_name=(microtime(true) * 100).".".$path_extension;
                    $upload_file->move(public_path('ins-business-opp/'),$upload_file_name);
                }else {
                    $upload_file_name=$fetch_dt->upload_file;
                }
                $up_data=InsBuOpportunity::where('temp_tin_no',$temp_tin_no)->update(array(
                    'bu_type'=>$request->bu_type,
                    'arn_no'=>$arn_no,
                    'sub_arn_no'=>isset($request->sub_arn_no)?$request->sub_arn_no:NULL,
                    'euin_no'=>$request->euin_no,
                    'sub_brk_cd'=>isset($request->sub_brk_cd)?$request->sub_brk_cd:NULL,
                    'ins_type_id'=>$request->ins_type_id,
                    'proposer_id'=>$request->proposer_id,
                    'same_as_above'=>$request->same_as_above,
                    'insured_person_id'=>$request->insured_person_id,
                    'comp_id'=>$request->comp_id,
                    'product_type_id'=>$request->product_type_id,
                    'product_id'=>$request->product_id,
                    'sum_insured'=>$request->sum_insured,
                    'renewal_dt'=>$request->renewal_dt,
                    'upload_file'=>$upload_file_name,
                    'remarks'=>$request->remarks,
                    'branch_code'=>Helper::getBranchCode(),
                    'delete_flag'=>'N',
                ));

                $data=InsBuOpportunity::leftJoin('md_branch','md_branch.id','=','td_ins_bu_opportunity.branch_code')
                    ->leftJoin('md_employee','md_employee.euin_no','=','td_ins_bu_opportunity.euin_no')
                    ->leftJoin('md_client','md_client.id','=','td_ins_bu_opportunity.proposer_id')
                    ->leftJoin('md_ins_type','md_ins_type.id','=','td_ins_bu_opportunity.ins_type_id')
                    ->leftJoin('md_client as md_client_2','md_client_2.id','=','td_ins_bu_opportunity.insured_person_id')
                    ->leftJoin('md_ins_company','md_ins_company.id','=','td_ins_bu_opportunity.comp_id')
                    ->leftJoin('md_ins_product_type','md_ins_product_type.id','=','td_ins_bu_opportunity.product_type_id')
                    ->leftJoin('md_ins_products','md_ins_products.id','=','td_ins_bu_opportunity.product_id')
                    ->select('td_ins_bu_opportunity.*','md_branch.brn_name as branch_name','md_employee.emp_name as rm_name','md_employee.emp_name as emp_name',
                    'md_client.client_name as proposer_name','md_client.client_code as proposer_code','md_client.pan as proposer_pan','md_client.dob as proposer_dob','md_ins_type.type as ins_type',
                    'md_client_2.client_name as insured_person_name','md_client_2.client_code as insured_person_code','md_client_2.pan as insured_person_pan','md_client_2.dob as insured_person_dob',
                    'md_ins_company.comp_short_name as comp_short_name','md_ins_company.comp_full_name as comp_full_name','md_ins_product_type.product_type as product_type','md_ins_products.product_name as product_name')
                    ->orderBy('rec_datetime','DESC')
                    ->where('temp_tin_no',$temp_tin_no)
                    ->first();
            }else {
                $is_has=InsBuOpportunity::get();
                // return $is_has; 
                if (count($is_has)>0) {
                    $temp_tin_no='INSR00'.(count($is_has)+1);
                } else {
                    $temp_tin_no='INSR001';
                }
                // return $temp_tin_no;

                $upload_file=$request->upload_file;
                $upload_file_name='';
                if ($upload_file) {
                    $path_extension=$upload_file->getClientOriginalExtension();
                    $upload_file_name=(microtime(true) * 100).".".$path_extension;
                    $upload_file->move(public_path('ins-business-opp/'),$upload_file_name);
                }
                $data=InsBuOpportunity::create(array(
                    'rec_datetime'=>date('Y-m-d H:i:s'),
                    'temp_tin_no'=>$temp_tin_no,
                    'bu_type'=>$request->bu_type,
                    'arn_no'=>$arn_no,
                    'sub_arn_no'=>isset($request->sub_arn_no)?$request->sub_arn_no:NULL,
                    'euin_no'=>$request->euin_no,
                    'sub_brk_cd'=>isset($request->sub_brk_cd)?$request->sub_brk_cd:NULL,
                    'ins_type_id'=>$request->ins_type_id,
                    'proposer_id'=>$request->proposer_id,
                    'same_as_above'=>$request->same_as_above,
                    'insured_person_id'=>$request->insured_person_id,
                    'comp_id'=>$request->comp_id,
                    'product_type_id'=>$request->product_type_id,
                    'product_id'=>$request->product_id,
                    'sum_insured'=>$request->sum_insured,
                    'renewal_dt'=>$request->renewal_dt,
                    'upload_file'=>$upload_file_name,
                    'remarks'=>$request->remarks,
                    'branch_code'=>Helper::getBranchCode(),
                    'delete_flag'=>'N',
                ));
            }
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function filterCriteria($rawQuery,$from_date,$to_date,$temp_tin_no,$proposer_code,$ins_type_id,$renewal_month,$renewal_year,$company_id,$product_type_id,$product_id)
    {
        $queryString='td_ins_bu_opportunity.rec_datetime';
        $rawQuery.=Helper::FrmToDateRawQuery($from_date,$to_date,$rawQuery,$queryString);
        $queryString1='td_ins_bu_opportunity.temp_tin_no';
        $rawQuery.=Helper::WhereRawQuery($temp_tin_no,$rawQuery,$queryString1);
        $queryString2='td_ins_bu_opportunity.proposer_id';
        $rawQuery.=Helper::WhereRawQuery($proposer_code,$rawQuery,$queryString2);
        $queryString3='td_ins_bu_opportunity.ins_type_id';
        $rawQuery.=Helper::WhereRawQuery($ins_type_id,$rawQuery,$queryString3);
        $queryString4='td_ins_bu_opportunity.comp_id';
        $rawQuery.=Helper::WhereRawQuery($company_id,$rawQuery,$queryString4);
        $queryString5='td_ins_bu_opportunity.product_type_id';
        $rawQuery.=Helper::WhereRawQuery($product_type_id,$rawQuery,$queryString5);
        $queryString6='td_ins_bu_opportunity.product_id';
        $rawQuery.=Helper::WhereRawQuery($product_id,$rawQuery,$queryString6);

        $queryString7='td_ins_bu_opportunity.renewal_dt';
        $rawQuery.=Helper::RawQueryOnlyMonth($renewal_month,$rawQuery,$queryString7);
        $rawQuery.=Helper::RawQueryOnlyYear($renewal_year,$rawQuery,$queryString7);
        return $rawQuery;
    }
}
