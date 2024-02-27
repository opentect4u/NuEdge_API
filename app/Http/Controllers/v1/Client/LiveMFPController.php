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
            $valuation_as_on=$request->valuation_as_on;
            $view_type=$request->view_type;
            $pan_no=$request->pan_no;
            $client_name=$request->client_name;

            $client_details='';
            if ($view_type ) {
                $rawQuery='';
                $client_rawQuery='';
                if ($view_type=='C') {
                    if (!$pan_no) {
                        $queryString='td_mutual_fund_trans.first_client_name';
                        $rawQuery.=Helper::WhereRawQuery($client_name,$rawQuery,$queryString);
                        $client_queryString='md_client.client_name';
                        $client_rawQuery.=Helper::WhereRawQuery($client_name,$client_rawQuery,$client_queryString);
                    }else {
                        $queryString='td_mutual_fund_trans.first_client_pan';
                        $rawQuery.=Helper::WhereRawQuery($pan_no,$rawQuery,$queryString);
                        $client_queryString='md_client.pan';
                        $client_rawQuery.=Helper::WhereRawQuery($pan_no,$client_rawQuery,$client_queryString);
                    }
                    $client_details=Client::whereRaw($client_rawQuery)->first();
                }else {
                    # code...
                }
            } 
            // return $client_details;
            // DB::enableQueryLog();
            $all_data=MutualFundTransaction::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_mutual_fund_trans.product_code')
                ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
                ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                ->leftJoin('md_amc','md_amc.amc_code','=','td_mutual_fund_trans.amc_code')
                ->leftJoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
                ->leftJoin('md_option','md_option.id','=','md_scheme_isin.option_id')
                ->select('td_mutual_fund_trans.*','md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name','md_amc.amc_short_name as amc_name',
                'md_plan.plan_name as plan_name','md_option.opt_name as option_name'
                )
                ->selectRaw('IF(td_mutual_fund_trans.rnt_id=1,md_scheme_isin.isin_no,td_mutual_fund_trans.isin_no) as isin_no')
                // ->selectRaw('(select close from td_benchmark_scheme where benchmark=1 and date=CURDATE()) as nifty50')
                // ->selectRaw('(select close from td_benchmark_scheme where benchmark=70 and date=CURDATE()) as sensex')
                ->selectRaw('(select close from td_benchmark_scheme where benchmark=1 ORDER BY date DESC limit 1) as nifty50')
                ->selectRaw('(select close from td_benchmark_scheme where benchmark=70 ORDER BY date DESC limit 1) as sensex')
                // ->selectRaw('(select trans_date from td_mutual_fund_trans where folio_no=td_mutual_fund_trans.folio_no and product_code=td_mutual_fund_trans.product_code order by trans_date ASC limit 1)as inv_since')
                // ->selectRaw('(select nav from td_nav_details where product_code=td_mutual_fund_trans.product_code order by nav_date ASC limit 1)as curr_nav')
                ->selectRaw('sum(td_mutual_fund_trans.units) as tot_units')
                ->selectRaw('sum(td_mutual_fund_trans.amount) as inv_cost')
                ->selectRaw('sum(td_mutual_fund_trans.stamp_duty) as tot_stamp_duty')
                ->selectRaw('sum(td_mutual_fund_trans.tds) as tot_tds')
                ->selectRaw('count(*) as tot_rows')
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
                ->orderBy('md_scheme.scheme_name','ASC')
                ->take(5)
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

                    // if ($trxn_type && $trxn_type_flag && $trxn_nature) {  //for cams
                    //     $trxn_code=TransHelper::transTypeToCodeCAMS($trxn_type);
                    //     $trxn_nature_code=TransHelper::trxnNatureCodeCAMS($trxn_nature);

                    //     $value->trxn_code=$trxn_code;
                    //     $value->trxn_type_flag_code=$trxn_type_flag;
                    //     $value->trxn_nature_code=$trxn_nature_code;
                        
                    //     $get_type_subtype=MFTransTypeSubType::where('c_trans_type_code',$trxn_code)
                    //         ->where('c_k_trans_type',$trxn_type_flag)
                    //         ->where('c_k_trans_sub_type',$trxn_nature_code)
                    //         ->first();
                        
                    //     if ($amount > 0) {
                    //         if ($get_type_subtype) {
                    //             $transaction_type=$get_type_subtype->trans_type;
                    //             $transaction_subtype=$get_type_subtype->trans_sub_type;
                    //         }
                    //     }else{
                    //         if ($get_type_subtype) {
                    //             $transaction_type=$get_type_subtype->trans_type." Rejection";
                    //             $transaction_subtype=$get_type_subtype->trans_sub_type." Rejection";
                    //         }
                    //     }
                    // }else {
                    //     $kf_trans_type=$value->kf_trans_type;
                    //     $trans_flag=$value->trans_flag;
                    //     if ($trans_flag=='DP' || $trans_flag=='DR') {
                    //         $get_type_subtype=MFTransTypeSubType::where('c_k_trans_sub_type',$kf_trans_type)
                    //             ->where('k_divident_flag',$trans_flag)
                    //             ->first();
                    //     }elseif ($trans_flag=='TI') {
                    //         $get_type_subtype='';
                    //         $transaction_type='Transfer In';
                    //         $transaction_subtype='Transfer In';
                    //     }elseif ($trans_flag=='TO') {
                    //         $get_type_subtype='';
                    //         $transaction_type='Transfer Out';
                    //         $transaction_subtype='Transfer Out';
                    //     } else {
                    //         $get_type_subtype=MFTransTypeSubType::where('c_k_trans_sub_type',$kf_trans_type)
                    //             ->first();
                    //     }
                        
                    //     if ($get_type_subtype) {
                    //         $transaction_type=$get_type_subtype->trans_type;
                    //         $transaction_subtype=$get_type_subtype->trans_sub_type;
                    //     }
                    // }
                    $value->gross_amount= number_format((float)((float)$amount + (float)$value->stamp_duty + (float)$value->tds), 2, '.', '');
                    // number_format((float)$foo, 2, '.', '')
                    $value->tot_gross_amount= number_format((float)((float)$value->tot_amount + (float)$value->tot_stamp_duty + (float)$value->tot_tds), 2, '.', '');
                    $value->transaction_type=$transaction_type;
                    $value->transaction_subtype=$transaction_subtype;

                    $current_nav=DB::select('SELECT nav,nav_date FROM td_nav_details WHERE product_code="'.$value->product_code.'" AND DATE(nav_date) = (SELECT MAX(DATE(nav_date)) FROM td_nav_details WHERE product_code="'.$value->product_code.'" AND DATE(nav_date) <= DATE("'.$valuation_as_on.'"))');
                    // return $current_nav[0]->nav;
                    $value->curr_nav=$current_nav[0]->nav;
                    $value->nav_date=$current_nav[0]->nav_date;
                    $value->inv_since=MutualFundTransaction::where('folio_no',$value->folio_no)
                        ->where('product_code',$value->product_code)
                        ->select('trans_date')
                        ->orderBy('trans_date','ASC')
                        ->first();
                    $value->pur_nav=MutualFundTransaction::where('folio_no',$value->folio_no)
                        ->where('product_code',$value->product_code)
                        ->select('pur_price')
                        ->orderBy('trans_date','ASC')
                        ->first();


                    $value->curr_val=$value->curr_nav * $value->tot_units;
                    $value->gain_loss=$value->curr_val - $value->inv_cost;
                    if ($value->gain_loss==0 || $value->inv_cost==0) {
                        $value->ret_abs=0;
                    }else {
                        $value->ret_abs=($value->gain_loss / $value->inv_cost) * 100;
                    }

                    $value->idcw_reinv=0;
                    $value->idcwp=0;
                    $value->idcwr=0;
                    $value->xirr=0;
                    $value->trans_mode=0;

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

            $mydata=[];
            $mydata['client_details']=$client_details;
            $mydata['data']=$data;
            $mydata['valuation_as_on']=$valuation_as_on;
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($mydata);
    }

    public function showDetails(Request $request)
    {
        try {
            // return $request;
            $rnt_id=$request->rnt_id;
            $folio_no=$request->folio_no;
            $isin_no=$request->isin_no;
            $product_code=$request->product_code;
            $nav_date=$request->nav_date;
           
            $rawQuery='';
            $queryString='td_mutual_fund_trans.folio_no';
            $rawQuery.=Helper::WhereRawQuery($folio_no,$rawQuery,$queryString);
            $queryString='td_mutual_fund_trans.product_code';
            $rawQuery.=Helper::WhereRawQuery($product_code,$rawQuery,$queryString);
            if ($rnt_id==2) {
                $queryString='td_mutual_fund_trans.isin_no';
                $rawQuery.=Helper::WhereRawQuery($isin_no,$rawQuery,$queryString);
            } 
            $current_nav=DB::select('SELECT nav,nav_date FROM td_nav_details WHERE product_code="'.$request->product_code.'" AND DATE(nav_date) = (SELECT MAX(DATE(nav_date)) FROM td_nav_details WHERE product_code="'.$request->product_code.'" AND DATE(nav_date) <= DATE("'.$nav_date.'"))');
            // return $current_nav;
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
                ->selectRaw('sum(units) as tot_units')
                ->selectRaw('sum(amount) as tot_amount')
                ->selectRaw('sum(stamp_duty) as tot_stamp_duty')
                ->selectRaw('sum(tds) as tot_tds')
                ->selectRaw('count(*) as tot_rows')
                ->selectRaw('(select bu_type from md_business_type where bu_code=md_employee.bu_type_id and branch_id=md_employee.branch_id limit 1) as bu_type')

                ->selectRaw('(select close from td_benchmark_scheme where benchmark=1 AND date=td_mutual_fund_trans.trans_date) as nifty50')
                ->selectRaw('(select close from td_benchmark_scheme where benchmark=70 AND date=td_mutual_fund_trans.trans_date) as sensex')

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
                ->orderBy('td_mutual_fund_trans.trans_date','asc')
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
                    $value->xirr_process_type=$get_type_subtype->xirr_process_type;
                    $value->gross_amount= number_format((float)((float)$amount + (float)$value->stamp_duty + (float)$value->tds), 2, '.', '');
                    $value->tot_gross_amount= number_format((float)((float)$value->tot_amount + (float)$value->tot_stamp_duty + (float)$value->tot_tds), 2, '.', '');
                    $value->transaction_type=$transaction_type;
                    $value->transaction_subtype=$transaction_subtype;

                    $now = time(); // or your date as well
                    $your_date = strtotime(date('Y-m-d',strtotime($value->trans_date)));
                    $datediff = $now - $your_date;
                    $days=round($datediff / (60 * 60 * 24));
                    $value->days=($days - 1);
                    $value->curr_nav=$current_nav[0]->nav;
                   
                    /* CAGR calculation $nper=271/365;
                    $cagr = pow((6193.43/4561),(1/$nper)) - 1; */

                    array_push($data,$value);
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
                ->orderBy('td_mutual_fund_trans.trans_date','asc')
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
