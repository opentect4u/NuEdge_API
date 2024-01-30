<?php

namespace App\Http\Controllers\V1\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{
    MutualFund,
    Client,
    FormReceived,
    MutualFundTransaction,
    MFTransTypeSubType
};
use Validator;
use Illuminate\Support\Carbon;
use Excel;
use App\Helpers\TransHelper;
use DB;

class LiveMFPController extends Controller
{
    public function search(Request $request)
    {
        try {
            // return $request;
            $view_type=$request->view_type;
            $pan_no=$request->pan_no;
            $client_name=$request->client_name;

            if ($view_type ) {
                $rawQuery='';

                if ($view_type=='C') {
                    if (!$pan_no) {
                        $queryString='td_mutual_fund_trans.first_client_name';
                        $rawQuery.=Helper::WhereRawQuery($client_name,$rawQuery,$queryString);
                    }else {
                        $queryString='td_mutual_fund_trans.first_client_pan';
                        $rawQuery.=Helper::WhereRawQuery($pan_no,$rawQuery,$queryString);
                    }
                }else {
                    # code...
                }
            } 
            // DB::enableQueryLog();
            $all_data=MutualFundTransaction::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_mutual_fund_trans.product_code')
                ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
                ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                ->leftJoin('md_amc','md_amc.amc_code','=','td_mutual_fund_trans.amc_code')
                ->leftJoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
                ->leftJoin('md_option','md_option.id','=','md_scheme_isin.option_id')
                // ->leftJoin('md_employee','md_employee.euin_no','=','td_mutual_fund_trans.euin_no')
                ->leftJoin('md_employee','md_employee.euin_no','=',DB::raw('IF(td_mutual_fund_trans.euin_no!="",td_mutual_fund_trans.euin_no,(select euin_no from td_mutual_fund_trans where folio_no=td_mutual_fund_trans.folio_no and product_code=td_mutual_fund_trans.product_code AND euin_no!="" limit 1))'))
                ->leftJoin('md_branch','md_branch.id','=','md_employee.branch_id')
                ->select('td_mutual_fund_trans.*','md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name','md_amc.amc_short_name as amc_name',
                'md_plan.plan_name as plan_name','md_option.opt_name as option_name',
                'md_employee.emp_name as rm_name','md_branch.brn_name as branch','md_employee.bu_type_id as bu_type_id','md_employee.branch_id as branch_id','md_employee.euin_no as euin_no')
                ->selectRaw('sum(amount) as tot_amount')
                ->selectRaw('sum(stamp_duty) as tot_stamp_duty')
                ->selectRaw('sum(tds) as tot_tds')
                ->selectRaw('count(*) as tot_rows')

                ->selectRaw('(select bu_type from md_business_type where bu_code=md_employee.bu_type_id and branch_id=md_employee.branch_id limit 1) as bu_type')
                // ->selectRaw('IF(td_mutual_fund_trans.euin_no="",(select euin_no from td_mutual_fund_trans where folio_no=td_mutual_fund_trans.folio_no and euin_no!="" limit 1),td_mutual_fund_trans.euin_no) as my_euin_no')
                ->where('td_mutual_fund_trans.delete_flag','N')
                ->where('td_mutual_fund_trans.amc_flag','N')
                ->where('td_mutual_fund_trans.scheme_flag','N')
                ->where('td_mutual_fund_trans.plan_option_flag','N')
                ->where('td_mutual_fund_trans.bu_type_flag','N')
                ->where('td_mutual_fund_trans.divi_mismatch_flag','N')
                ->whereRaw($rawQuery)
                ->groupBy('td_mutual_fund_trans.product_code')
                ->groupBy('td_mutual_fund_trans.isin_no')
                // ->groupBy('td_mutual_fund_trans.trans_no')
                // ->groupBy('td_mutual_fund_trans.trxn_type_flag')
                // ->groupByRaw('IF(substr(trxn_nature,1,19)="Systematic-Reversed","Systematic-Reversed",trxn_nature)')
                // ->groupBy('td_mutual_fund_trans.trans_desc')
                // ->groupBy('td_mutual_fund_trans.kf_trans_type')
                ->get();
            // dd(DB::getQueryLog());
            // return $all_data;
                $data=[];
                foreach ($all_data as $key => $value) {
                    $euin=$value->euin_no;
                    $trans_no=$value->trans_no;
                    $trans_date=$value->trans_date;
                    // ====================start trans type & sub type=========================
                    $trxn_type=$value->trxn_type;
                    $trxn_type_flag=$value->trxn_type_flag;
                    $trxn_nature=$value->trxn_nature;
                    $amount=$value->amount;
                    $transaction_type='';
                    $transaction_subtype='';

                    if ($trxn_type && $trxn_type_flag && $trxn_nature) {  //for cams
                        $trxn_code=TransHelper::transTypeToCodeCAMS($trxn_type);
                        $trxn_nature_code=TransHelper::trxnNatureCodeCAMS($trxn_nature);

                        $value->trxn_code=$trxn_code;
                        $value->trxn_type_flag_code=$trxn_type_flag;
                        $value->trxn_nature_code=$trxn_nature_code;
                        
                        $get_type_subtype=MFTransTypeSubType::where('c_trans_type_code',$trxn_code)
                            ->where('c_k_trans_type',$trxn_type_flag)
                            ->where('c_k_trans_sub_type',$trxn_nature_code)
                            ->first();
                        
                        if ($amount > 0) {
                            if ($get_type_subtype) {
                                $transaction_type=$get_type_subtype->trans_type;
                                $transaction_subtype=$get_type_subtype->trans_sub_type;
                            }
                        }else{
                            if ($get_type_subtype) {
                                $transaction_type=$get_type_subtype->trans_type." Rejection";
                                $transaction_subtype=$get_type_subtype->trans_sub_type." Rejection";
                            }
                        }
                    }else {
                        $kf_trans_type=$value->kf_trans_type;
                        $trans_flag=$value->trans_flag;
                        if ($trans_flag=='DP' || $trans_flag=='DR') {
                            $get_type_subtype=MFTransTypeSubType::where('c_k_trans_sub_type',$kf_trans_type)
                                ->where('k_divident_flag',$trans_flag)
                                ->first();
                        }elseif ($trans_flag=='TI') {
                            $get_type_subtype='';
                            $transaction_type='Transfer In';
                            $transaction_subtype='Transfer In';
                        }elseif ($trans_flag=='TO') {
                            $get_type_subtype='';
                            $transaction_type='Transfer Out';
                            $transaction_subtype='Transfer Out';
                        } else {
                            $get_type_subtype=MFTransTypeSubType::where('c_k_trans_sub_type',$kf_trans_type)
                                ->first();
                        }
                        
                        if ($get_type_subtype) {
                            $transaction_type=$get_type_subtype->trans_type;
                            $transaction_subtype=$get_type_subtype->trans_sub_type;
                        }
                    }
                    $value->gross_amount= number_format((float)((float)$amount + (float)$value->stamp_duty + (float)$value->tds), 2, '.', '');
                    // number_format((float)$foo, 2, '.', '')
                    $value->tot_gross_amount= number_format((float)((float)$value->tot_amount + (float)$value->tot_stamp_duty + (float)$value->tot_tds), 2, '.', '');
                    $value->transaction_type=$transaction_type;
                    $value->transaction_subtype=$transaction_subtype;

                    // if (!empty($trans_type) && in_array($transaction_type ,$trans_type) && !empty($trans_sub_type) && in_array($transaction_subtype ,$trans_sub_type)) {
                    //     array_push($data,$value);
                    // }else if (!empty($trans_type) && in_array($transaction_type ,$trans_type)) {
                    //     array_push($data,$value);
                    // }else if (!empty($transaction_subtype) && in_array($transaction_subtype ,$trans_sub_type)) {
                    //     array_push($data,$value);
                    // }else{
                        array_push($data,$value);
                    // }
                }
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
            $rnt_id=$request->rnt_id;
            $folio_no=$request->folio_no;
            $isin_no=$request->isin_no;
            $product_code=$request->product_code;
           
            $rawQuery='';
            $queryString='td_mutual_fund_trans.folio_no';
            $rawQuery.=Helper::WhereRawQuery($folio_no,$rawQuery,$queryString);
            $queryString='td_mutual_fund_trans.product_code';
            $rawQuery.=Helper::WhereRawQuery($product_code,$rawQuery,$queryString);
            if ($rnt_id==2) {
                $queryString='td_mutual_fund_trans.isin_no';
                $rawQuery.=Helper::WhereRawQuery($isin_no,$rawQuery,$queryString);
            } 
            // DB::enableQueryLog();
            $all_data=MutualFundTransaction::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_mutual_fund_trans.product_code')
                ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
                ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                ->leftJoin('md_amc','md_amc.amc_code','=','td_mutual_fund_trans.amc_code')
                ->leftJoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
                ->leftJoin('md_option','md_option.id','=','md_scheme_isin.option_id')
                ->leftJoin('md_employee','md_employee.euin_no','=',DB::raw('IF(td_mutual_fund_trans.euin_no!="",td_mutual_fund_trans.euin_no,(select euin_no from td_mutual_fund_trans where folio_no=td_mutual_fund_trans.folio_no and product_code=td_mutual_fund_trans.product_code AND euin_no!="" limit 1))'))
                ->leftJoin('md_branch','md_branch.id','=','md_employee.branch_id')
                ->select('td_mutual_fund_trans.*','md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name','md_amc.amc_short_name as amc_name',
                'md_plan.plan_name as plan_name','md_option.opt_name as option_name',
                'md_employee.emp_name as rm_name','md_branch.brn_name as branch','md_employee.bu_type_id as bu_type_id','md_employee.branch_id as branch_id','md_employee.euin_no as euin_no')
                ->selectRaw('sum(amount) as tot_amount')
                ->selectRaw('sum(stamp_duty) as tot_stamp_duty')
                ->selectRaw('sum(tds) as tot_tds')
                ->selectRaw('count(*) as tot_rows')
                ->selectRaw('(select bu_type from md_business_type where bu_code=md_employee.bu_type_id and branch_id=md_employee.branch_id limit 1) as bu_type')
                ->where('td_mutual_fund_trans.delete_flag','N')
                ->where('td_mutual_fund_trans.amc_flag','N')
                ->where('td_mutual_fund_trans.scheme_flag','N')
                ->where('td_mutual_fund_trans.plan_option_flag','N')
                ->where('td_mutual_fund_trans.bu_type_flag','N')
                ->where('td_mutual_fund_trans.divi_mismatch_flag','N')
                ->whereRaw($rawQuery)
                ->groupBy('td_mutual_fund_trans.trans_no')
                ->groupBy('td_mutual_fund_trans.trxn_type_flag')
                ->groupByRaw('IF(substr(trxn_nature,1,19)="Systematic-Reversed","Systematic-Reversed",trxn_nature)')
                ->groupBy('td_mutual_fund_trans.trans_desc')
                ->groupBy('td_mutual_fund_trans.kf_trans_type')
                ->orderBy('td_mutual_fund_trans.trans_date','desc')
                ->get();
            // dd(DB::getQueryLog());
            // return $all_data;
                $data=[];
                foreach ($all_data as $key => $value) {
                    $euin=$value->euin_no;
                    $trans_no=$value->trans_no;
                    $trans_date=$value->trans_date;
                    // ====================start trans type & sub type=========================
                    $trxn_type=$value->trxn_type;
                    $trxn_type_flag=$value->trxn_type_flag;
                    $trxn_nature=$value->trxn_nature;
                    $amount=$value->amount;
                    $transaction_type='';
                    $transaction_subtype='';

                    if ($trxn_type && $trxn_type_flag && $trxn_nature) {  //for cams
                        $trxn_code=TransHelper::transTypeToCodeCAMS($trxn_type);
                        $trxn_nature_code=TransHelper::trxnNatureCodeCAMS($trxn_nature);

                        $value->trxn_code=$trxn_code;
                        $value->trxn_type_flag_code=$trxn_type_flag;
                        $value->trxn_nature_code=$trxn_nature_code;
                        
                        $get_type_subtype=MFTransTypeSubType::where('c_trans_type_code',$trxn_code)
                            ->where('c_k_trans_type',$trxn_type_flag)
                            ->where('c_k_trans_sub_type',$trxn_nature_code)
                            ->first();
                        
                        if ($amount > 0) {
                            if ($get_type_subtype) {
                                $transaction_type=$get_type_subtype->trans_type;
                                $transaction_subtype=$get_type_subtype->trans_sub_type;
                            }
                        }else{
                            if ($get_type_subtype) {
                                $transaction_type=$get_type_subtype->trans_type." Rejection";
                                $transaction_subtype=$get_type_subtype->trans_sub_type." Rejection";
                            }
                        }
                    }else {
                        $kf_trans_type=$value->kf_trans_type;
                        $trans_flag=$value->trans_flag;
                        if ($trans_flag=='DP' || $trans_flag=='DR') {
                            $get_type_subtype=MFTransTypeSubType::where('c_k_trans_sub_type',$kf_trans_type)
                                ->where('k_divident_flag',$trans_flag)
                                ->first();
                        }elseif ($trans_flag=='TI') {
                            $get_type_subtype='';
                            $transaction_type='Transfer In';
                            $transaction_subtype='Transfer In';
                        }elseif ($trans_flag=='TO') {
                            $get_type_subtype='';
                            $transaction_type='Transfer Out';
                            $transaction_subtype='Transfer Out';
                        } else {
                            $get_type_subtype=MFTransTypeSubType::where('c_k_trans_sub_type',$kf_trans_type)
                                ->first();
                        }
                        
                        if ($get_type_subtype) {
                            $transaction_type=$get_type_subtype->trans_type;
                            $transaction_subtype=$get_type_subtype->trans_sub_type;
                        }
                    }
                    $value->gross_amount= number_format((float)((float)$amount + (float)$value->stamp_duty + (float)$value->tds), 2, '.', '');
                    // number_format((float)$foo, 2, '.', '')
                    $value->tot_gross_amount= number_format((float)((float)$value->tot_amount + (float)$value->tot_stamp_duty + (float)$value->tot_tds), 2, '.', '');
                    $value->transaction_type=$transaction_type;
                    $value->transaction_subtype=$transaction_subtype;

                    // if (!empty($trans_type) && in_array($transaction_type ,$trans_type) && !empty($trans_sub_type) && in_array($transaction_subtype ,$trans_sub_type)) {
                    //     array_push($data,$value);
                    // }else if (!empty($trans_type) && in_array($transaction_type ,$trans_type)) {
                    //     array_push($data,$value);
                    // }else if (!empty($transaction_subtype) && in_array($transaction_subtype ,$trans_sub_type)) {
                    //     array_push($data,$value);
                    // }else{
                        array_push($data,$value);
                    // }
                }
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}
