<?php

namespace App\Http\Controllers\V1\Reports;

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

class MonthlyMisController extends Controller
{
    public function search(Request $request)
    {
        try {
            // return $request;
            $mis_month=$request->mis_month;
            // $pan_no=json_decode($request->pan_no);
            $amc_id=json_decode($request->amc_id);
            $cat_id=json_decode($request->cat_id);
            $sub_cat_id=json_decode($request->sub_cat_id);
            $scheme_id=json_decode($request->scheme_id);
            $trans_type=json_decode($request->trans_type);
            $trans_sub_type=json_decode($request->trans_sub_type);

            if ($mis_month || !empty($amc_id) || !empty($cat_id) || !empty($sub_cat_id) || !empty($scheme_id)) {
                $rawQuery='';
                if ($mis_month) {
                    $month=explode("-",$mis_month)[0];
                    $year=explode("-",$mis_month)[1];
                    // return $month;
                    $queryString='td_mutual_fund_trans.trans_date';
                    $rawQuery.='MONTH('.$queryString.')="'.$month.'" ';
                    $rawQuery.=' AND YEAR('.$queryString.')="'.$year.'" ';
                }
                
                // $queryString='td_mutual_fund_trans.folio_no';
                // $rawQuery.=Helper::WhereRawQuery($folio_no,$rawQuery,$queryString);
                // $queryString='td_mutual_fund_trans.first_client_pan';
                // $rawQuery.=Helper::WhereRawQuery($pan_no,$rawQuery,$queryString);
                // $queryString='td_mutual_fund_trans.first_client_name';
                // $rawQuery.=Helper::RawQueryOR($pan_no,$rawQuery,$queryString);
                $queryString='md_scheme.amc_id';
                $rawQuery.=Helper::WhereRawQuery($amc_id,$rawQuery,$queryString);
                $queryString='md_scheme.category_id';
                $rawQuery.=Helper::WhereRawQuery($cat_id,$rawQuery,$queryString);
                $queryString='md_scheme.subcategory_id';
                $rawQuery.=Helper::WhereRawQuery($sub_cat_id,$rawQuery,$queryString);
                $queryString='md_scheme_isin.scheme_id';
                $rawQuery.=Helper::WhereRawQuery($scheme_id,$rawQuery,$queryString);
                // return $rawQuery;
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
                    ->get();
                // dd(DB::getQueryLog());
            } 
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
                                $process_type=$get_type_subtype->process_type;
                            }
                        }else{
                            if ($get_type_subtype) {
                                $transaction_type=$get_type_subtype->trans_type." Rejection";
                                $transaction_subtype=$get_type_subtype->trans_sub_type." Rejection";
                                $process_type=$get_type_subtype->process_type;
                                if ($transaction_subtype=='Refund Rejection') {
                                    $process_type='O';
                                }else {
                                    $process_type='';
                                }
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
                            $process_type='';
                        }elseif ($trans_flag=='TO') {
                            $get_type_subtype='';
                            $transaction_type='Transfer Out';
                            $transaction_subtype='Transfer Out';
                            $process_type='O';
                        } else {
                            $get_type_subtype=MFTransTypeSubType::where('c_k_trans_sub_type',$kf_trans_type)
                                ->first();
                        }
                        
                        if ($get_type_subtype) {
                            $transaction_type=$get_type_subtype->trans_type;
                            $transaction_subtype=$get_type_subtype->trans_sub_type;
                            $process_type=$get_type_subtype->process_type;
                        }
                    }
                    $value->gross_amount= number_format((float)((float)$amount + (float)$value->stamp_duty + (float)$value->tds), 2, '.', '');
                    // number_format((float)$foo, 2, '.', '')
                    $value->tot_gross_amount= number_format((float)((float)$value->tot_amount + (float)$value->tot_stamp_duty + (float)$value->tot_tds), 2, '.', '');
                    $value->transaction_type=$transaction_type;
                    $value->transaction_subtype=$transaction_subtype;
                    $value->process_type=$process_type;

                    if (!empty($trans_type) && in_array($transaction_type ,$trans_type) && !empty($trans_sub_type) && in_array($transaction_subtype ,$trans_sub_type)) {
                        array_push($data,$value);
                    }else if (!empty($trans_type) && in_array($transaction_type ,$trans_type)) {
                        array_push($data,$value);
                    }else if (!empty($transaction_subtype) && in_array($transaction_subtype ,$trans_sub_type)) {
                        array_push($data,$value);
                    }else{
                        array_push($data,$value);
                    }
                }
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }


    public function searchTrands(Request $request)
    {
        try {
            // return $request;
            $view_by=$request->view_by;
            $month_year=$request->month_year;
            $upto=$request->upto;
            $fin_year=$request->fin_year;
            $period_type=$request->period_type;

            // $no_of_month=$request->no_of_month;
            // $pan_no=json_decode($request->pan_no);
            $amc_id=json_decode($request->amc_id);
            $cat_id=json_decode($request->cat_id);
            $sub_cat_id=json_decode($request->sub_cat_id);
            $scheme_id=json_decode($request->scheme_id);
            $trans_type=json_decode($request->trans_type);
            $trans_sub_type=json_decode($request->trans_sub_type);

            if ($view_by || !empty($amc_id) || !empty($cat_id) || !empty($sub_cat_id) || !empty($scheme_id)) {
                $rawQuery='';
                $queryString='md_scheme.amc_id';
                $rawQuery.=Helper::WhereRawQuery($amc_id,$rawQuery,$queryString);
                $queryString='md_scheme.category_id';
                $rawQuery.=Helper::WhereRawQuery($cat_id,$rawQuery,$queryString);
                $queryString='md_scheme.subcategory_id';
                $rawQuery.=Helper::WhereRawQuery($sub_cat_id,$rawQuery,$queryString);
                $queryString='md_scheme_isin.scheme_id';
                $rawQuery.=Helper::WhereRawQuery($scheme_id,$rawQuery,$queryString);
                
                $data=[];
                $categories=[];
                $chart_data=[];
                $table_data=[];
                $monthly_inflow_amount_set=[];
                $monthly_outflow_amount_set=[];
                $monthly_net_inflow_amount_set=[];

                switch ($view_by) {
                    case 'M':
                        // return $month_year;
                        // return explode('-',$month_year);
                        $start_date=date('Y-m-d',strtotime("01-".str_replace('/','-',str_replace(' ','',explode('-',$month_year)[0]))));
                        $end_date=date('Y-m-d',strtotime("01-".str_replace('/','-',str_replace(' ','',explode('-',$month_year)[1]))));
                        // return $end_date;
                        $my_start_date=strtotime($end_date);
                        while(strtotime($end_date) >= strtotime($start_date))
                        {
                            if ($my_start_date==strtotime($end_date)) {
                                $split_date=date("Y-m",strtotime($end_date));
                                array_push($categories,date('M-Y',strtotime($split_date)));
                                // return $split_date;
                                $rawQuery1='';
                                $queryString='td_mutual_fund_trans.trans_date';
                                $rawQuery1.=(strlen($rawQuery) > 0)?" AND ":" ";
                                $rawQuery1.=' MONTH('.$queryString.')="'.explode("-",$split_date)[1].'" ';
                                $rawQuery1.=' AND YEAR('.$queryString.')="'.explode("-",$split_date)[0].'" ';
                                $myrawQuery=$rawQuery.$rawQuery1;
                                $all_data=MutualFundTransaction::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_mutual_fund_trans.product_code')
                                    ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                                    ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
                                    ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                                    ->leftJoin('md_amc','md_amc.amc_code','=','td_mutual_fund_trans.amc_code')
                                    ->leftJoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
                                    ->leftJoin('md_option','md_option.id','=','md_scheme_isin.option_id')
                                    ->select('td_mutual_fund_trans.*','md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name','md_amc.amc_short_name as amc_name',
                                    'md_plan.plan_name as plan_name','md_option.opt_name as option_name')
                                    ->selectRaw('sum(amount) as tot_amount')
                                    ->selectRaw('sum(stamp_duty) as tot_stamp_duty')
                                    ->selectRaw('sum(tds) as tot_tds')
                                    ->selectRaw('count(*) as tot_rows')
                                    ->where('td_mutual_fund_trans.delete_flag','N')
                                    ->where('td_mutual_fund_trans.amc_flag','N')
                                    ->where('td_mutual_fund_trans.scheme_flag','N')
                                    ->where('td_mutual_fund_trans.plan_option_flag','N')
                                    ->where('td_mutual_fund_trans.bu_type_flag','N')
                                    ->where('td_mutual_fund_trans.divi_mismatch_flag','N')
                                    ->whereRaw($myrawQuery)
                                    ->groupBy('td_mutual_fund_trans.trans_no')
                                    ->groupBy('td_mutual_fund_trans.trxn_type_flag')
                                    ->groupByRaw('IF(substr(trxn_nature,1,19)="Systematic-Reversed","Systematic-Reversed",trxn_nature)')
                                    ->groupBy('td_mutual_fund_trans.trans_desc')
                                    ->groupBy('td_mutual_fund_trans.kf_trans_type')
                                    // ->take(10)
                                    ->get();
                                // dd(DB::getQueryLog());
        
                                $inflow_amount=0;
                                $outflow_amount=0;
                                $net_inflow_amount=0;
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
                                                $process_type=$get_type_subtype->process_type;
                                            }
                                        }else{
                                            if ($get_type_subtype) {
                                                $transaction_type=$get_type_subtype->trans_type." Rejection";
                                                $transaction_subtype=$get_type_subtype->trans_sub_type." Rejection";
                                                if ($transaction_subtype=='Refund Rejection') {
                                                    $process_type='O';
                                                }else {
                                                    $process_type='';
                                                }
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
                                            $process_type='I';
                                        }elseif ($trans_flag=='TO') {
                                            $get_type_subtype='';
                                            $transaction_type='Transfer Out';
                                            $transaction_subtype='Transfer Out';
                                            $process_type='O';
                                        } else {
                                            $get_type_subtype=MFTransTypeSubType::where('c_k_trans_sub_type',$kf_trans_type)
                                                ->first();
                                        }
                                        
                                        if ($get_type_subtype) {
                                            $transaction_type=$get_type_subtype->trans_type;
                                            $transaction_subtype=$get_type_subtype->trans_sub_type;
                                            $process_type=$get_type_subtype->process_type;
                                        }
                                    }
                                    $value->gross_amount= number_format((float)((float)$amount + (float)$value->stamp_duty + (float)$value->tds), 2, '.', '');
                                    // number_format((float)$foo, 2, '.', '')
                                    $value->tot_gross_amount= number_format((float)((float)$value->tot_amount + (float)$value->tot_stamp_duty + (float)$value->tot_tds), 2, '.', '');
                                    $value->transaction_type=$transaction_type;
                                    $value->transaction_subtype=$transaction_subtype;
                                    $value->process_type=$process_type;
        
                                    if (!empty($trans_type) && in_array($transaction_type ,$trans_type) && !empty($trans_sub_type) && in_array($transaction_subtype ,$trans_sub_type)) {
                                        array_push($data,$value);
                                    }else if (!empty($trans_type) && in_array($transaction_type ,$trans_type)) {
                                        array_push($data,$value);
                                    }else if (!empty($transaction_subtype) && in_array($transaction_subtype ,$trans_sub_type)) {
                                        array_push($data,$value);
                                    }else{
                                        array_push($data,$value);
                                    }
        
                                    if ($value->process_type=='I') {
                                        $inflow_amount=$inflow_amount + $value->tot_gross_amount;
                                    }elseif ($value->process_type=='O') {
                                        $outflow_amount=$outflow_amount + $value->tot_gross_amount;
                                    }
                                }
        
                                $net_inflow_amount=$inflow_amount - $outflow_amount;
                                array_push($monthly_inflow_amount_set,$inflow_amount);
                                array_push($monthly_outflow_amount_set,$outflow_amount);
                                array_push($monthly_net_inflow_amount_set,$net_inflow_amount);
                                $myset_data=[];
                                $myset_data['monthly']=date('M-Y',strtotime($split_date));
                                $myset_data['monthly_inflow']=$inflow_amount;
                                $myset_data['monthly_outflow']=$outflow_amount;
                                $myset_data['monthly_net_inflow']=$net_inflow_amount;
                                $myset_data['per_of_growth']=0;
                                $myset_data['trend']=0;
                                array_push($table_data,$myset_data);
                                // return $myset_data;
                            }
                            $end_date= date("Y-m",strtotime("-1 month",strtotime($end_date)));
                            array_push($categories,date('M-Y',strtotime($end_date)));

                            $rawQuery1='';
                            $queryString='td_mutual_fund_trans.trans_date';
                            $rawQuery1.=(strlen($rawQuery) > 0)?" AND ":" ";
                            $rawQuery1.=' MONTH('.$queryString.')="'.explode("-",$end_date)[1].'" ';
                            $rawQuery1.=' AND YEAR('.$queryString.')="'.explode("-",$end_date)[0].'" ';
                            $myrawQuery=$rawQuery.$rawQuery1;
                            $all_data=MutualFundTransaction::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_mutual_fund_trans.product_code')
                                ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                                ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
                                ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                                ->leftJoin('md_amc','md_amc.amc_code','=','td_mutual_fund_trans.amc_code')
                                ->leftJoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
                                ->leftJoin('md_option','md_option.id','=','md_scheme_isin.option_id')
                                ->select('td_mutual_fund_trans.*','md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name','md_amc.amc_short_name as amc_name',
                                'md_plan.plan_name as plan_name','md_option.opt_name as option_name')
                                ->selectRaw('sum(amount) as tot_amount')
                                ->selectRaw('sum(stamp_duty) as tot_stamp_duty')
                                ->selectRaw('sum(tds) as tot_tds')
                                ->selectRaw('count(*) as tot_rows')
                                ->where('td_mutual_fund_trans.delete_flag','N')
                                ->where('td_mutual_fund_trans.amc_flag','N')
                                ->where('td_mutual_fund_trans.scheme_flag','N')
                                ->where('td_mutual_fund_trans.plan_option_flag','N')
                                ->where('td_mutual_fund_trans.bu_type_flag','N')
                                ->where('td_mutual_fund_trans.divi_mismatch_flag','N')
                                ->whereRaw($myrawQuery)
                                ->groupBy('td_mutual_fund_trans.trans_no')
                                ->groupBy('td_mutual_fund_trans.trxn_type_flag')
                                ->groupByRaw('IF(substr(trxn_nature,1,19)="Systematic-Reversed","Systematic-Reversed",trxn_nature)')
                                ->groupBy('td_mutual_fund_trans.trans_desc')
                                ->groupBy('td_mutual_fund_trans.kf_trans_type')
                                // ->take(10)
                                ->get();
                            // dd(DB::getQueryLog());
    
                            $inflow_amount=0;
                            $outflow_amount=0;
                            $net_inflow_amount=0;
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
                                            $process_type=$get_type_subtype->process_type;
                                        }
                                    }else{
                                        if ($get_type_subtype) {
                                            $transaction_type=$get_type_subtype->trans_type." Rejection";
                                            $transaction_subtype=$get_type_subtype->trans_sub_type." Rejection";
                                            if ($transaction_subtype=='Refund Rejection') {
                                                $process_type='O';
                                            }else {
                                                $process_type='';
                                            }
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
                                        $process_type='I';
                                    }elseif ($trans_flag=='TO') {
                                        $get_type_subtype='';
                                        $transaction_type='Transfer Out';
                                        $transaction_subtype='Transfer Out';
                                        $process_type='O';
                                    } else {
                                        $get_type_subtype=MFTransTypeSubType::where('c_k_trans_sub_type',$kf_trans_type)
                                            ->first();
                                    }
                                    
                                    if ($get_type_subtype) {
                                        $transaction_type=$get_type_subtype->trans_type;
                                        $transaction_subtype=$get_type_subtype->trans_sub_type;
                                        $process_type=$get_type_subtype->process_type;
                                    }
                                }
                                $value->gross_amount= number_format((float)((float)$amount + (float)$value->stamp_duty + (float)$value->tds), 2, '.', '');
                                // number_format((float)$foo, 2, '.', '')
                                $value->tot_gross_amount= number_format((float)((float)$value->tot_amount + (float)$value->tot_stamp_duty + (float)$value->tot_tds), 2, '.', '');
                                $value->transaction_type=$transaction_type;
                                $value->transaction_subtype=$transaction_subtype;
                                $value->process_type=$process_type;
    
                                if (!empty($trans_type) && in_array($transaction_type ,$trans_type) && !empty($trans_sub_type) && in_array($transaction_subtype ,$trans_sub_type)) {
                                    array_push($data,$value);
                                }else if (!empty($trans_type) && in_array($transaction_type ,$trans_type)) {
                                    array_push($data,$value);
                                }else if (!empty($transaction_subtype) && in_array($transaction_subtype ,$trans_sub_type)) {
                                    array_push($data,$value);
                                }else{
                                    array_push($data,$value);
                                }
    
                                if ($value->process_type=='I') {
                                    $inflow_amount=$inflow_amount + $value->tot_gross_amount;
                                }elseif ($value->process_type=='O') {
                                    $outflow_amount=$outflow_amount + $value->tot_gross_amount;
                                }
                            }
    
                            $net_inflow_amount=$inflow_amount - $outflow_amount;
                            array_push($monthly_inflow_amount_set,$inflow_amount);
                            array_push($monthly_outflow_amount_set,$outflow_amount);
                            array_push($monthly_net_inflow_amount_set,$net_inflow_amount);
                            $myset_data=[];
                            $myset_data['monthly']=date('M-Y',strtotime($end_date));
                            $myset_data['monthly_inflow']=$inflow_amount;
                            $myset_data['monthly_outflow']=$outflow_amount;
                            $myset_data['monthly_net_inflow']=$net_inflow_amount;
                            $myset_data['per_of_growth']=0;
                            $myset_data['trend']=0;
                            array_push($table_data,$myset_data);
                        }
                        // return $categories;

                        break;
                    case 'D':
                        for ($i=0; $i <= $upto; $i++) { 
                            $split_date=date('Y-m', strtotime('-'.$i.' months'));
                            array_push($categories,date('M-Y',strtotime($split_date)));
                            // return $rawQuery;
                            $rawQuery1='';
                            $queryString='td_mutual_fund_trans.trans_date';
                            $rawQuery1.=(strlen($rawQuery) > 0)?" AND ":" ";
                            $rawQuery1.=' MONTH('.$queryString.')="'.explode("-",$split_date)[1].'" ';
                            $rawQuery1.=' AND YEAR('.$queryString.')="'.explode("-",$split_date)[0].'" ';
                            $myrawQuery=$rawQuery.$rawQuery1;
                            // return $myrawQuery;
                            // DB::enableQueryLog();
                            // $this->searchTrands_QueryFun($split_date,$myrawQuery,$monthly_inflow_amount_set,$monthly_outflow_amount_set,$monthly_net_inflow_amount_set,$table_data,$trans_type,$trans_sub_type);
                            $all_data=MutualFundTransaction::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_mutual_fund_trans.product_code')
                                ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                                ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
                                ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                                ->leftJoin('md_amc','md_amc.amc_code','=','td_mutual_fund_trans.amc_code')
                                ->leftJoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
                                ->leftJoin('md_option','md_option.id','=','md_scheme_isin.option_id')
                                ->select('td_mutual_fund_trans.*','md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name','md_amc.amc_short_name as amc_name',
                                'md_plan.plan_name as plan_name','md_option.opt_name as option_name')
                                ->selectRaw('sum(amount) as tot_amount')
                                ->selectRaw('sum(stamp_duty) as tot_stamp_duty')
                                ->selectRaw('sum(tds) as tot_tds')
                                ->selectRaw('count(*) as tot_rows')
                                ->where('td_mutual_fund_trans.delete_flag','N')
                                ->where('td_mutual_fund_trans.amc_flag','N')
                                ->where('td_mutual_fund_trans.scheme_flag','N')
                                ->where('td_mutual_fund_trans.plan_option_flag','N')
                                ->where('td_mutual_fund_trans.bu_type_flag','N')
                                ->where('td_mutual_fund_trans.divi_mismatch_flag','N')
                                ->whereRaw($myrawQuery)
                                ->groupBy('td_mutual_fund_trans.trans_no')
                                ->groupBy('td_mutual_fund_trans.trxn_type_flag')
                                ->groupByRaw('IF(substr(trxn_nature,1,19)="Systematic-Reversed","Systematic-Reversed",trxn_nature)')
                                ->groupBy('td_mutual_fund_trans.trans_desc')
                                ->groupBy('td_mutual_fund_trans.kf_trans_type')
                                // ->take(10)
                                ->get();
                            // dd(DB::getQueryLog());
    
                            $inflow_amount=0;
                            $outflow_amount=0;
                            $net_inflow_amount=0;
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
                                            $process_type=$get_type_subtype->process_type;
                                        }
                                    }else{
                                        if ($get_type_subtype) {
                                            $transaction_type=$get_type_subtype->trans_type." Rejection";
                                            $transaction_subtype=$get_type_subtype->trans_sub_type." Rejection";
                                            if ($transaction_subtype=='Refund Rejection') {
                                                $process_type='O';
                                            }else {
                                                $process_type='';
                                            }
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
                                        $process_type='I';
                                    }elseif ($trans_flag=='TO') {
                                        $get_type_subtype='';
                                        $transaction_type='Transfer Out';
                                        $transaction_subtype='Transfer Out';
                                        $process_type='O';
                                    } else {
                                        $get_type_subtype=MFTransTypeSubType::where('c_k_trans_sub_type',$kf_trans_type)
                                            ->first();
                                    }
                                    
                                    if ($get_type_subtype) {
                                        $transaction_type=$get_type_subtype->trans_type;
                                        $transaction_subtype=$get_type_subtype->trans_sub_type;
                                        $process_type=$get_type_subtype->process_type;
                                    }
                                }
                                $value->gross_amount= number_format((float)((float)$amount + (float)$value->stamp_duty + (float)$value->tds), 2, '.', '');
                                // number_format((float)$foo, 2, '.', '')
                                $value->tot_gross_amount= number_format((float)((float)$value->tot_amount + (float)$value->tot_stamp_duty + (float)$value->tot_tds), 2, '.', '');
                                $value->transaction_type=$transaction_type;
                                $value->transaction_subtype=$transaction_subtype;
                                $value->process_type=$process_type;
    
                                if (!empty($trans_type) && in_array($transaction_type ,$trans_type) && !empty($trans_sub_type) && in_array($transaction_subtype ,$trans_sub_type)) {
                                    array_push($data,$value);
                                }else if (!empty($trans_type) && in_array($transaction_type ,$trans_type)) {
                                    array_push($data,$value);
                                }else if (!empty($transaction_subtype) && in_array($transaction_subtype ,$trans_sub_type)) {
                                    array_push($data,$value);
                                }else{
                                    array_push($data,$value);
                                }
    
                                if ($value->process_type=='I') {
                                    $inflow_amount=$inflow_amount + $value->tot_gross_amount;
                                }elseif ($value->process_type=='O') {
                                    $outflow_amount=$outflow_amount + $value->tot_gross_amount;
                                }
                            }
    
                            $net_inflow_amount=$inflow_amount - $outflow_amount;
                            array_push($monthly_inflow_amount_set,$inflow_amount);
                            array_push($monthly_outflow_amount_set,$outflow_amount);
                            array_push($monthly_net_inflow_amount_set,$net_inflow_amount);
                            $myset_data=[];
                            $myset_data['monthly']=date('M-Y',strtotime($split_date));
                            $myset_data['monthly_inflow']=$inflow_amount;
                            $myset_data['monthly_outflow']=$outflow_amount;
                            $myset_data['monthly_net_inflow']=$net_inflow_amount;
                            $myset_data['per_of_growth']=0;
                            $myset_data['trend']=0;
                            array_push($table_data,$myset_data);
                        }
                        break;
                    case 'Y':
                        // return $fin_year;
                        $start_date=explode('-',$fin_year)[0]."-04-01";
                        $lastday = date('t',strtotime(explode('-',$fin_year)[1]."-03-01"));
                        $cal_end_date = explode('-',$fin_year)[1]."-03-".$lastday;
                        $today=date('Y-m-d');
                        $end_date = (strtotime($today) >= strtotime($cal_end_date)) ? explode('-',$fin_year)[1]."-03-".$lastday : date('Y-m-d');
                        // return $start_date."  -  ".$end_date;
                        if ($period_type=='M') {
                            if (date('Y')==explode('-',$fin_year)[0] || date('Y')==explode('-',$fin_year)[1]) {
                                $split_date=date("Y-m",strtotime($end_date));
                                array_push($categories,$split_date);

                                $rawQuery1='';
                                $queryString='td_mutual_fund_trans.trans_date';
                                $rawQuery1.=(strlen($rawQuery) > 0)?" AND ":" ";
                                $rawQuery1.=' MONTH('.$queryString.')="'.explode("-",$split_date)[1].'" ';
                                $rawQuery1.=' AND YEAR('.$queryString.')="'.explode("-",$split_date)[0].'" ';
                                $myrawQuery=$rawQuery.$rawQuery1;

                                $all_data=MutualFundTransaction::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_mutual_fund_trans.product_code')
                                        ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                                        ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
                                        ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                                        ->leftJoin('md_amc','md_amc.amc_code','=','td_mutual_fund_trans.amc_code')
                                        ->leftJoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
                                        ->leftJoin('md_option','md_option.id','=','md_scheme_isin.option_id')
                                        ->select('td_mutual_fund_trans.*','md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name','md_amc.amc_short_name as amc_name',
                                        'md_plan.plan_name as plan_name','md_option.opt_name as option_name')
                                        ->selectRaw('sum(amount) as tot_amount')
                                        ->selectRaw('sum(stamp_duty) as tot_stamp_duty')
                                        ->selectRaw('sum(tds) as tot_tds')
                                        ->selectRaw('count(*) as tot_rows')
                                        ->where('td_mutual_fund_trans.delete_flag','N')
                                        ->where('td_mutual_fund_trans.amc_flag','N')
                                        ->where('td_mutual_fund_trans.scheme_flag','N')
                                        ->where('td_mutual_fund_trans.plan_option_flag','N')
                                        ->where('td_mutual_fund_trans.bu_type_flag','N')
                                        ->where('td_mutual_fund_trans.divi_mismatch_flag','N')
                                        ->whereRaw($myrawQuery)
                                        ->groupBy('td_mutual_fund_trans.trans_no')
                                        ->groupBy('td_mutual_fund_trans.trxn_type_flag')
                                        ->groupByRaw('IF(substr(trxn_nature,1,19)="Systematic-Reversed","Systematic-Reversed",trxn_nature)')
                                        ->groupBy('td_mutual_fund_trans.trans_desc')
                                        ->groupBy('td_mutual_fund_trans.kf_trans_type')
                                        ->get();
            
                                $inflow_amount=0;
                                $outflow_amount=0;
                                $net_inflow_amount=0;
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
                                                $process_type=$get_type_subtype->process_type;
                                            }
                                        }else{
                                            if ($get_type_subtype) {
                                                $transaction_type=$get_type_subtype->trans_type." Rejection";
                                                $transaction_subtype=$get_type_subtype->trans_sub_type." Rejection";
                                                if ($transaction_subtype=='Refund Rejection') {
                                                    $process_type='O';
                                                }else {
                                                    $process_type='';
                                                }
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
                                            $process_type='I';
                                        }elseif ($trans_flag=='TO') {
                                            $get_type_subtype='';
                                            $transaction_type='Transfer Out';
                                            $transaction_subtype='Transfer Out';
                                            $process_type='O';
                                        } else {
                                            $get_type_subtype=MFTransTypeSubType::where('c_k_trans_sub_type',$kf_trans_type)
                                                ->first();
                                        }
                                        
                                        if ($get_type_subtype) {
                                            $transaction_type=$get_type_subtype->trans_type;
                                            $transaction_subtype=$get_type_subtype->trans_sub_type;
                                            $process_type=$get_type_subtype->process_type;
                                        }
                                    }
                                    $value->gross_amount= number_format((float)((float)$amount + (float)$value->stamp_duty + (float)$value->tds), 2, '.', '');
                                    // number_format((float)$foo, 2, '.', '')
                                    $value->tot_gross_amount= number_format((float)((float)$value->tot_amount + (float)$value->tot_stamp_duty + (float)$value->tot_tds), 2, '.', '');
                                    $value->transaction_type=$transaction_type;
                                    $value->transaction_subtype=$transaction_subtype;
                                    $value->process_type=$process_type;
        
                                    if (!empty($trans_type) && in_array($transaction_type ,$trans_type) && !empty($trans_sub_type) && in_array($transaction_subtype ,$trans_sub_type)) {
                                        array_push($data,$value);
                                    }else if (!empty($trans_type) && in_array($transaction_type ,$trans_type)) {
                                        array_push($data,$value);
                                    }else if (!empty($transaction_subtype) && in_array($transaction_subtype ,$trans_sub_type)) {
                                        array_push($data,$value);
                                    }else{
                                        array_push($data,$value);
                                    }
        
                                    if ($value->process_type=='I') {
                                        $inflow_amount=$inflow_amount + $value->tot_gross_amount;
                                    }elseif ($value->process_type=='O') {
                                        $outflow_amount=$outflow_amount + $value->tot_gross_amount;
                                    }
                                }
                                // return $inflow_amount;
                                $net_inflow_amount=$inflow_amount - $outflow_amount;
                                array_push($monthly_inflow_amount_set,$inflow_amount);
                                array_push($monthly_outflow_amount_set,$outflow_amount);
                                array_push($monthly_net_inflow_amount_set,$net_inflow_amount);
                                $myset_data=[];
                                $myset_data['monthly']=$split_date;
                                $myset_data['monthly_inflow']=$inflow_amount;
                                $myset_data['monthly_outflow']=$outflow_amount;
                                $myset_data['monthly_net_inflow']=$net_inflow_amount;
                                $myset_data['per_of_growth']=0;
                                $myset_data['trend']=0;
                                array_push($table_data,$myset_data);
                            }
                            while(strtotime($end_date) >= strtotime($start_date))
                            {
                                $end_date= date("Y-m",strtotime("-1 month",strtotime($end_date)));
                                array_push($categories,$end_date);
                                // return $end_date;

                                $rawQuery1='';
                                $queryString='td_mutual_fund_trans.trans_date';
                                $rawQuery1.=(strlen($rawQuery) > 0)?" AND ":" ";
                                $rawQuery1.=' MONTH('.$queryString.')="'.explode("-",$end_date)[1].'" ';
                                $rawQuery1.=' AND YEAR('.$queryString.')="'.explode("-",$end_date)[0].'" ';
                                $myrawQuery=$rawQuery.$rawQuery1;
                                $all_data=MutualFundTransaction::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_mutual_fund_trans.product_code')
                                    ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                                    ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
                                    ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                                    ->leftJoin('md_amc','md_amc.amc_code','=','td_mutual_fund_trans.amc_code')
                                    ->leftJoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
                                    ->leftJoin('md_option','md_option.id','=','md_scheme_isin.option_id')
                                    ->select('td_mutual_fund_trans.*','md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name','md_amc.amc_short_name as amc_name',
                                    'md_plan.plan_name as plan_name','md_option.opt_name as option_name')
                                    ->selectRaw('sum(amount) as tot_amount')
                                    ->selectRaw('sum(stamp_duty) as tot_stamp_duty')
                                    ->selectRaw('sum(tds) as tot_tds')
                                    ->selectRaw('count(*) as tot_rows')
                                    ->where('td_mutual_fund_trans.delete_flag','N')
                                    ->where('td_mutual_fund_trans.amc_flag','N')
                                    ->where('td_mutual_fund_trans.scheme_flag','N')
                                    ->where('td_mutual_fund_trans.plan_option_flag','N')
                                    ->where('td_mutual_fund_trans.bu_type_flag','N')
                                    ->where('td_mutual_fund_trans.divi_mismatch_flag','N')
                                    ->whereRaw($myrawQuery)
                                    ->groupBy('td_mutual_fund_trans.trans_no')
                                    ->groupBy('td_mutual_fund_trans.trxn_type_flag')
                                    ->groupByRaw('IF(substr(trxn_nature,1,19)="Systematic-Reversed","Systematic-Reversed",trxn_nature)')
                                    ->groupBy('td_mutual_fund_trans.trans_desc')
                                    ->groupBy('td_mutual_fund_trans.kf_trans_type')
                                    // ->take(10)
                                    ->get();
                                // dd(DB::getQueryLog());
        
                                $inflow_amount=0;
                                $outflow_amount=0;
                                $net_inflow_amount=0;
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
                                                $process_type=$get_type_subtype->process_type;
                                            }
                                        }else{
                                            if ($get_type_subtype) {
                                                $transaction_type=$get_type_subtype->trans_type." Rejection";
                                                $transaction_subtype=$get_type_subtype->trans_sub_type." Rejection";
                                                if ($transaction_subtype=='Refund Rejection') {
                                                    $process_type='O';
                                                }else {
                                                    $process_type='';
                                                }
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
                                            $process_type='I';
                                        }elseif ($trans_flag=='TO') {
                                            $get_type_subtype='';
                                            $transaction_type='Transfer Out';
                                            $transaction_subtype='Transfer Out';
                                            $process_type='O';
                                        } else {
                                            $get_type_subtype=MFTransTypeSubType::where('c_k_trans_sub_type',$kf_trans_type)
                                                ->first();
                                        }
                                        
                                        if ($get_type_subtype) {
                                            $transaction_type=$get_type_subtype->trans_type;
                                            $transaction_subtype=$get_type_subtype->trans_sub_type;
                                            $process_type=$get_type_subtype->process_type;
                                        }
                                    }
                                    $value->gross_amount= number_format((float)((float)$amount + (float)$value->stamp_duty + (float)$value->tds), 2, '.', '');
                                    // number_format((float)$foo, 2, '.', '')
                                    $value->tot_gross_amount= number_format((float)((float)$value->tot_amount + (float)$value->tot_stamp_duty + (float)$value->tot_tds), 2, '.', '');
                                    $value->transaction_type=$transaction_type;
                                    $value->transaction_subtype=$transaction_subtype;
                                    $value->process_type=$process_type;
        
                                    if (!empty($trans_type) && in_array($transaction_type ,$trans_type) && !empty($trans_sub_type) && in_array($transaction_subtype ,$trans_sub_type)) {
                                        array_push($data,$value);
                                    }else if (!empty($trans_type) && in_array($transaction_type ,$trans_type)) {
                                        array_push($data,$value);
                                    }else if (!empty($transaction_subtype) && in_array($transaction_subtype ,$trans_sub_type)) {
                                        array_push($data,$value);
                                    }else{
                                        array_push($data,$value);
                                    }
        
                                    if ($value->process_type=='I') {
                                        $inflow_amount=$inflow_amount + $value->tot_gross_amount;
                                    }elseif ($value->process_type=='O') {
                                        $outflow_amount=$outflow_amount + $value->tot_gross_amount;
                                    }
                                }
        
                                $net_inflow_amount=$inflow_amount - $outflow_amount;
                                array_push($monthly_inflow_amount_set,$inflow_amount);
                                array_push($monthly_outflow_amount_set,$outflow_amount);
                                array_push($monthly_net_inflow_amount_set,$net_inflow_amount);
                                $myset_data=[];
                                $myset_data['monthly']=$end_date;
                                $myset_data['monthly_inflow']=$inflow_amount;
                                $myset_data['monthly_outflow']=$outflow_amount;
                                $myset_data['monthly_net_inflow']=$net_inflow_amount;
                                $myset_data['per_of_growth']=0;
                                $myset_data['trend']=0;
                                array_push($table_data,$myset_data);
                            }
                        } elseif ($period_type=='Q') {
                            // return $request;
                            // return date('m',strtotime($end_date));
                            // $end_date='2023-05-01';
                            // return $start_date."  -  ".$end_date;
                            if (date('m',strtotime($end_date))>=4 && date('m',strtotime($end_date))<=6) {
                                // return 'one';
                                for ($i=0; $i <= 2; $i++) { 
                                    if($i==0) {
                                        $start_date=explode('-',$fin_year)[0]."-04-01";
                                        $end_date=$end_date;
                                        // return $end_date;
                                        $split_date="QTR-I";
                                    }elseif($i==1) {
                                        $start_date=explode('-',$fin_year)[0]."-01-01";
                                        $end_date=explode('-',$fin_year)[0]."-03-".date('t',strtotime(explode('-',$fin_year)[0]."-03-01"));
                                        // return $end_date;
                                        $split_date="QTR-IV";
                                    }elseif($i==2) {
                                        $start_date=(explode('-',$fin_year)[0] - 1)."-09-01";
                                        $end_date=(explode('-',$fin_year)[0] - 1)."-12-".date('t',strtotime((explode('-',$fin_year)[0] - 1)."-12-01"));
                                        // return $end_date;
                                        $split_date="QTR-III";
                                    }
                                    $split_date=$split_date;
                                    array_push($categories,$split_date);

                                    $rawQuery1='';
                                    $queryString='td_mutual_fund_trans.trans_date';
                                    $rawQuery1.=(strlen($rawQuery) > 0)?" AND ":" ";
                                    $rawQuery1.=$queryString.'>="'.$start_date.'" AND '.$queryString.'<="'.$end_date.'" ';
                                    $myrawQuery=$rawQuery.$rawQuery1;
                                    // return $myrawQuery;
                                    // DB::enableQueryLog();
                                    // $this->searchTrands_QueryFun($split_date,$myrawQuery,$monthly_inflow_amount_set,$monthly_outflow_amount_set,$monthly_net_inflow_amount_set,$table_data,$trans_type,$trans_sub_type);
                                    $all_data=MutualFundTransaction::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_mutual_fund_trans.product_code')
                                        ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                                        ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
                                        ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                                        ->leftJoin('md_amc','md_amc.amc_code','=','td_mutual_fund_trans.amc_code')
                                        ->leftJoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
                                        ->leftJoin('md_option','md_option.id','=','md_scheme_isin.option_id')
                                        ->select('td_mutual_fund_trans.*','md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name','md_amc.amc_short_name as amc_name',
                                        'md_plan.plan_name as plan_name','md_option.opt_name as option_name')
                                        ->selectRaw('sum(amount) as tot_amount')
                                        ->selectRaw('sum(stamp_duty) as tot_stamp_duty')
                                        ->selectRaw('sum(tds) as tot_tds')
                                        ->selectRaw('count(*) as tot_rows')
                                        ->where('td_mutual_fund_trans.delete_flag','N')
                                        ->where('td_mutual_fund_trans.amc_flag','N')
                                        ->where('td_mutual_fund_trans.scheme_flag','N')
                                        ->where('td_mutual_fund_trans.plan_option_flag','N')
                                        ->where('td_mutual_fund_trans.bu_type_flag','N')
                                        ->where('td_mutual_fund_trans.divi_mismatch_flag','N')
                                        ->whereRaw($myrawQuery)
                                        ->groupBy('td_mutual_fund_trans.trans_no')
                                        ->groupBy('td_mutual_fund_trans.trxn_type_flag')
                                        ->groupByRaw('IF(substr(trxn_nature,1,19)="Systematic-Reversed","Systematic-Reversed",trxn_nature)')
                                        ->groupBy('td_mutual_fund_trans.trans_desc')
                                        ->groupBy('td_mutual_fund_trans.kf_trans_type')
                                        // ->take(10)
                                        ->get();
                                    // dd(DB::getQueryLog());
                                    // return $all_data;
                                    $inflow_amount=0;
                                    $outflow_amount=0;
                                    $net_inflow_amount=0;
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
                                                    $process_type=$get_type_subtype->process_type;
                                                }
                                            }else{
                                                if ($get_type_subtype) {
                                                    $transaction_type=$get_type_subtype->trans_type." Rejection";
                                                    $transaction_subtype=$get_type_subtype->trans_sub_type." Rejection";
                                                    if ($transaction_subtype=='Refund Rejection') {
                                                        $process_type='O';
                                                    }else {
                                                        $process_type='';
                                                    }
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
                                                $process_type='I';
                                            }elseif ($trans_flag=='TO') {
                                                $get_type_subtype='';
                                                $transaction_type='Transfer Out';
                                                $transaction_subtype='Transfer Out';
                                                $process_type='O';
                                            } else {
                                                $get_type_subtype=MFTransTypeSubType::where('c_k_trans_sub_type',$kf_trans_type)
                                                    ->first();
                                            }
                                            
                                            if ($get_type_subtype) {
                                                $transaction_type=$get_type_subtype->trans_type;
                                                $transaction_subtype=$get_type_subtype->trans_sub_type;
                                                $process_type=$get_type_subtype->process_type;
                                            }
                                        }
                                        $value->gross_amount= number_format((float)((float)$amount + (float)$value->stamp_duty + (float)$value->tds), 2, '.', '');
                                        // number_format((float)$foo, 2, '.', '')
                                        $value->tot_gross_amount= number_format((float)((float)$value->tot_amount + (float)$value->tot_stamp_duty + (float)$value->tot_tds), 2, '.', '');
                                        $value->transaction_type=$transaction_type;
                                        $value->transaction_subtype=$transaction_subtype;
                                        $value->process_type=$process_type;
            
                                        if (!empty($trans_type) && in_array($transaction_type ,$trans_type) && !empty($trans_sub_type) && in_array($transaction_subtype ,$trans_sub_type)) {
                                            array_push($data,$value);
                                        }else if (!empty($trans_type) && in_array($transaction_type ,$trans_type)) {
                                            array_push($data,$value);
                                        }else if (!empty($transaction_subtype) && in_array($transaction_subtype ,$trans_sub_type)) {
                                            array_push($data,$value);
                                        }else{
                                            array_push($data,$value);
                                        }
            
                                        if ($value->process_type=='I') {
                                            $inflow_amount=$inflow_amount + $value->tot_gross_amount;
                                        }elseif ($value->process_type=='O') {
                                            $outflow_amount=$outflow_amount + $value->tot_gross_amount;
                                        }
                                    }
            
                                    $net_inflow_amount=$inflow_amount - $outflow_amount;
                                    array_push($monthly_inflow_amount_set,$inflow_amount);
                                    array_push($monthly_outflow_amount_set,$outflow_amount);
                                    array_push($monthly_net_inflow_amount_set,$net_inflow_amount);
                                    $myset_data=[];
                                    $myset_data['monthly']=$split_date;
                                    $myset_data['monthly_inflow']=$inflow_amount;
                                    $myset_data['monthly_outflow']=$outflow_amount;
                                    $myset_data['monthly_net_inflow']=$net_inflow_amount;
                                    $myset_data['per_of_growth']=0;
                                    $myset_data['trend']=0;
                                    array_push($table_data,$myset_data);
                                }
                            }elseif (date('m',strtotime($end_date))>=7 && date('m',strtotime($end_date))<=9) {
                                // return 'two';
                                for ($i=0; $i <= 2; $i++) { 
                                    if($i==0) {
                                        $end_date=$end_date;
                                        $end_date=explode('-',$fin_year)[0]."-07-01";
                                        // return $end_date;
                                        $split_date="QTR-II";
                                    }elseif($i==1) {
                                        $start_date=explode('-',$fin_year)[0]."-04-01";
                                        $end_date=explode('-',$fin_year)[0]."-06-".date('t',strtotime(explode('-',$fin_year)[0]."-06-01"));
                                        // return $end_date;
                                        $split_date="QTR-I";
                                    }elseif($i==2) {
                                        $start_date=explode('-',$fin_year)[0]."-01-01";
                                        $end_date=explode('-',$fin_year)[0]."-03-".date('t',strtotime(explode('-',$fin_year)[0]."-03-01"));
                                        // return $end_date;
                                        $split_date="QTR-I";
                                    }
                                    $split_date=$split_date;
                                    array_push($categories,$split_date);

                                    $rawQuery1='';
                                    $queryString='td_mutual_fund_trans.trans_date';
                                    $rawQuery1.=(strlen($rawQuery) > 0)?" AND ":" ";
                                    $rawQuery1.=$queryString.'>="'.$start_date.'" AND '.$queryString.'<="'.$end_date.'" ';
                                    $myrawQuery=$rawQuery.$rawQuery1;
                                    // return $myrawQuery;
                                    // DB::enableQueryLog();
                                    // $this->searchTrands_QueryFun($split_date,$myrawQuery,$monthly_inflow_amount_set,$monthly_outflow_amount_set,$monthly_net_inflow_amount_set,$table_data,$trans_type,$trans_sub_type);
                                    $all_data=MutualFundTransaction::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_mutual_fund_trans.product_code')
                                        ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                                        ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
                                        ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                                        ->leftJoin('md_amc','md_amc.amc_code','=','td_mutual_fund_trans.amc_code')
                                        ->leftJoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
                                        ->leftJoin('md_option','md_option.id','=','md_scheme_isin.option_id')
                                        ->select('td_mutual_fund_trans.*','md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name','md_amc.amc_short_name as amc_name',
                                        'md_plan.plan_name as plan_name','md_option.opt_name as option_name')
                                        ->selectRaw('sum(amount) as tot_amount')
                                        ->selectRaw('sum(stamp_duty) as tot_stamp_duty')
                                        ->selectRaw('sum(tds) as tot_tds')
                                        ->selectRaw('count(*) as tot_rows')
                                        ->where('td_mutual_fund_trans.delete_flag','N')
                                        ->where('td_mutual_fund_trans.amc_flag','N')
                                        ->where('td_mutual_fund_trans.scheme_flag','N')
                                        ->where('td_mutual_fund_trans.plan_option_flag','N')
                                        ->where('td_mutual_fund_trans.bu_type_flag','N')
                                        ->where('td_mutual_fund_trans.divi_mismatch_flag','N')
                                        ->whereRaw($myrawQuery)
                                        ->groupBy('td_mutual_fund_trans.trans_no')
                                        ->groupBy('td_mutual_fund_trans.trxn_type_flag')
                                        ->groupByRaw('IF(substr(trxn_nature,1,19)="Systematic-Reversed","Systematic-Reversed",trxn_nature)')
                                        ->groupBy('td_mutual_fund_trans.trans_desc')
                                        ->groupBy('td_mutual_fund_trans.kf_trans_type')
                                        // ->take(10)
                                        ->get();
                                    // dd(DB::getQueryLog());
                                    // return $all_data;
                                    $inflow_amount=0;
                                    $outflow_amount=0;
                                    $net_inflow_amount=0;
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
                                                    $process_type=$get_type_subtype->process_type;
                                                }
                                            }else{
                                                if ($get_type_subtype) {
                                                    $transaction_type=$get_type_subtype->trans_type." Rejection";
                                                    $transaction_subtype=$get_type_subtype->trans_sub_type." Rejection";
                                                    if ($transaction_subtype=='Refund Rejection') {
                                                        $process_type='O';
                                                    }else {
                                                        $process_type='';
                                                    }
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
                                                $process_type='I';
                                            }elseif ($trans_flag=='TO') {
                                                $get_type_subtype='';
                                                $transaction_type='Transfer Out';
                                                $transaction_subtype='Transfer Out';
                                                $process_type='O';
                                            } else {
                                                $get_type_subtype=MFTransTypeSubType::where('c_k_trans_sub_type',$kf_trans_type)
                                                    ->first();
                                            }
                                            
                                            if ($get_type_subtype) {
                                                $transaction_type=$get_type_subtype->trans_type;
                                                $transaction_subtype=$get_type_subtype->trans_sub_type;
                                                $process_type=$get_type_subtype->process_type;
                                            }
                                        }
                                        $value->gross_amount= number_format((float)((float)$amount + (float)$value->stamp_duty + (float)$value->tds), 2, '.', '');
                                        // number_format((float)$foo, 2, '.', '')
                                        $value->tot_gross_amount= number_format((float)((float)$value->tot_amount + (float)$value->tot_stamp_duty + (float)$value->tot_tds), 2, '.', '');
                                        $value->transaction_type=$transaction_type;
                                        $value->transaction_subtype=$transaction_subtype;
                                        $value->process_type=$process_type;
            
                                        if (!empty($trans_type) && in_array($transaction_type ,$trans_type) && !empty($trans_sub_type) && in_array($transaction_subtype ,$trans_sub_type)) {
                                            array_push($data,$value);
                                        }else if (!empty($trans_type) && in_array($transaction_type ,$trans_type)) {
                                            array_push($data,$value);
                                        }else if (!empty($transaction_subtype) && in_array($transaction_subtype ,$trans_sub_type)) {
                                            array_push($data,$value);
                                        }else{
                                            array_push($data,$value);
                                        }
            
                                        if ($value->process_type=='I') {
                                            $inflow_amount=$inflow_amount + $value->tot_gross_amount;
                                        }elseif ($value->process_type=='O') {
                                            $outflow_amount=$outflow_amount + $value->tot_gross_amount;
                                        }
                                    }
            
                                    $net_inflow_amount=$inflow_amount - $outflow_amount;
                                    array_push($monthly_inflow_amount_set,$inflow_amount);
                                    array_push($monthly_outflow_amount_set,$outflow_amount);
                                    array_push($monthly_net_inflow_amount_set,$net_inflow_amount);
                                    $myset_data=[];
                                    $myset_data['monthly']=$split_date;
                                    $myset_data['monthly_inflow']=$inflow_amount;
                                    $myset_data['monthly_outflow']=$outflow_amount;
                                    $myset_data['monthly_net_inflow']=$net_inflow_amount;
                                    $myset_data['per_of_growth']=0;
                                    $myset_data['trend']=0;
                                    array_push($table_data,$myset_data);
                                }
                            }elseif (date('m',strtotime($end_date))>=10 && date('m',strtotime($end_date))<=12) {
                                // return 'three';
                                for ($i=0; $i <= 3; $i++) { 
                                    if($i==0) {
                                        $end_date=$end_date;
                                        $start_date=explode('-',$fin_year)[0]."-10-01";
                                        $split_date="QTR-III";
                                        // return $end_date;
                                    }elseif($i==1) {
                                        $start_date=explode('-',$fin_year)[0]."-07-01";
                                        $end_date=explode('-',$fin_year)[0]."-09-".date('t',strtotime(explode('-',$fin_year)[0]."-09-01"));
                                        $split_date="QTR-II";
                                        // return $end_date;
                                    }elseif($i==2) {
                                        $start_date=explode('-',$fin_year)[0]."-04-01";
                                        $end_date=explode('-',$fin_year)[0]."-06-".date('t',strtotime(explode('-',$fin_year)[0]."-06-01"));
                                        // return $end_date;
                                        $split_date="QTR-I";
                                    }elseif($i==3) {
                                        $start_date=explode('-',$fin_year)[0]."-01-01";
                                        $end_date=explode('-',$fin_year)[0]."-03-".date('t',strtotime(explode('-',$fin_year)[0]."-03-01"));
                                        // return $end_date;
                                        $split_date="QTR-I";
                                    }
                                    $split_date=$split_date;
                                    array_push($categories,$split_date);

                                    $rawQuery1='';
                                    $queryString='td_mutual_fund_trans.trans_date';
                                    $rawQuery1.=(strlen($rawQuery) > 0)?" AND ":" ";
                                    $rawQuery1.=$queryString.'>="'.$start_date.'" AND '.$queryString.'<="'.$end_date.'" ';
                                    $myrawQuery=$rawQuery.$rawQuery1;
                                    // return $myrawQuery;
                                    // DB::enableQueryLog();
                                    // $this->searchTrands_QueryFun($split_date,$myrawQuery,$monthly_inflow_amount_set,$monthly_outflow_amount_set,$monthly_net_inflow_amount_set,$table_data,$trans_type,$trans_sub_type);
                                    $all_data=MutualFundTransaction::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_mutual_fund_trans.product_code')
                                        ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                                        ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
                                        ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                                        ->leftJoin('md_amc','md_amc.amc_code','=','td_mutual_fund_trans.amc_code')
                                        ->leftJoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
                                        ->leftJoin('md_option','md_option.id','=','md_scheme_isin.option_id')
                                        ->select('td_mutual_fund_trans.*','md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name','md_amc.amc_short_name as amc_name',
                                        'md_plan.plan_name as plan_name','md_option.opt_name as option_name')
                                        ->selectRaw('sum(amount) as tot_amount')
                                        ->selectRaw('sum(stamp_duty) as tot_stamp_duty')
                                        ->selectRaw('sum(tds) as tot_tds')
                                        ->selectRaw('count(*) as tot_rows')
                                        ->where('td_mutual_fund_trans.delete_flag','N')
                                        ->where('td_mutual_fund_trans.amc_flag','N')
                                        ->where('td_mutual_fund_trans.scheme_flag','N')
                                        ->where('td_mutual_fund_trans.plan_option_flag','N')
                                        ->where('td_mutual_fund_trans.bu_type_flag','N')
                                        ->where('td_mutual_fund_trans.divi_mismatch_flag','N')
                                        ->whereRaw($myrawQuery)
                                        ->groupBy('td_mutual_fund_trans.trans_no')
                                        ->groupBy('td_mutual_fund_trans.trxn_type_flag')
                                        ->groupByRaw('IF(substr(trxn_nature,1,19)="Systematic-Reversed","Systematic-Reversed",trxn_nature)')
                                        ->groupBy('td_mutual_fund_trans.trans_desc')
                                        ->groupBy('td_mutual_fund_trans.kf_trans_type')
                                        // ->take(10)
                                        ->get();
                                    // dd(DB::getQueryLog());
                                    // return $all_data;
                                    $inflow_amount=0;
                                    $outflow_amount=0;
                                    $net_inflow_amount=0;
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
                                                    $process_type=$get_type_subtype->process_type;
                                                }
                                            }else{
                                                if ($get_type_subtype) {
                                                    $transaction_type=$get_type_subtype->trans_type." Rejection";
                                                    $transaction_subtype=$get_type_subtype->trans_sub_type." Rejection";
                                                    if ($transaction_subtype=='Refund Rejection') {
                                                        $process_type='O';
                                                    }else {
                                                        $process_type='';
                                                    }
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
                                                $process_type='I';
                                            }elseif ($trans_flag=='TO') {
                                                $get_type_subtype='';
                                                $transaction_type='Transfer Out';
                                                $transaction_subtype='Transfer Out';
                                                $process_type='O';
                                            } else {
                                                $get_type_subtype=MFTransTypeSubType::where('c_k_trans_sub_type',$kf_trans_type)
                                                    ->first();
                                            }
                                            
                                            if ($get_type_subtype) {
                                                $transaction_type=$get_type_subtype->trans_type;
                                                $transaction_subtype=$get_type_subtype->trans_sub_type;
                                                $process_type=$get_type_subtype->process_type;
                                            }
                                        }
                                        $value->gross_amount= number_format((float)((float)$amount + (float)$value->stamp_duty + (float)$value->tds), 2, '.', '');
                                        // number_format((float)$foo, 2, '.', '')
                                        $value->tot_gross_amount= number_format((float)((float)$value->tot_amount + (float)$value->tot_stamp_duty + (float)$value->tot_tds), 2, '.', '');
                                        $value->transaction_type=$transaction_type;
                                        $value->transaction_subtype=$transaction_subtype;
                                        $value->process_type=$process_type;
            
                                        if (!empty($trans_type) && in_array($transaction_type ,$trans_type) && !empty($trans_sub_type) && in_array($transaction_subtype ,$trans_sub_type)) {
                                            array_push($data,$value);
                                        }else if (!empty($trans_type) && in_array($transaction_type ,$trans_type)) {
                                            array_push($data,$value);
                                        }else if (!empty($transaction_subtype) && in_array($transaction_subtype ,$trans_sub_type)) {
                                            array_push($data,$value);
                                        }else{
                                            array_push($data,$value);
                                        }
            
                                        if ($value->process_type=='I') {
                                            $inflow_amount=$inflow_amount + $value->tot_gross_amount;
                                        }elseif ($value->process_type=='O') {
                                            $outflow_amount=$outflow_amount + $value->tot_gross_amount;
                                        }
                                    }
            
                                    $net_inflow_amount=$inflow_amount - $outflow_amount;
                                    array_push($monthly_inflow_amount_set,$inflow_amount);
                                    array_push($monthly_outflow_amount_set,$outflow_amount);
                                    array_push($monthly_net_inflow_amount_set,$net_inflow_amount);
                                    $myset_data=[];
                                    $myset_data['monthly']=$split_date;
                                    $myset_data['monthly_inflow']=$inflow_amount;
                                    $myset_data['monthly_outflow']=$outflow_amount;
                                    $myset_data['monthly_net_inflow']=$net_inflow_amount;
                                    $myset_data['per_of_growth']=0;
                                    $myset_data['trend']=0;
                                    array_push($table_data,$myset_data);
                                }
                            }elseif (date('m',strtotime($end_date))>=1 && date('m',strtotime($end_date))<=3) {
                                // return 'four';
                                // $dateToTest = "2015-05-01";
                                // $lastday = date('t',strtotime($dateToTest));
                                // return $lastday;
                                for ($i=0; $i <= 4; $i++) { 
                                    if ($i==0) {
                                        $end_date=$end_date;
                                        $start_date=explode('-',$fin_year)[1]."-01-01";
                                        $split_date="QTR-IV";
                                    }elseif($i==1) {
                                        $start_date=explode('-',$fin_year)[0]."-10-01";
                                        $end_date=explode('-',$fin_year)[0]."-12-".date('t',strtotime(explode('-',$fin_year)[0]."-12-01"));
                                        // return $end_date;
                                        $split_date="QTR-III";
                                    }elseif($i==2) {
                                        $start_date=explode('-',$fin_year)[0]."-07-01";
                                        $end_date=explode('-',$fin_year)[0]."-09-".date('t',strtotime(explode('-',$fin_year)[0]."-09-01"));
                                        // return $end_date;
                                        $split_date="QTR-II";
                                    }elseif($i==3) {
                                        $start_date=explode('-',$fin_year)[0]."-04-01";
                                        $end_date=explode('-',$fin_year)[0]."-06-".date('t',strtotime(explode('-',$fin_year)[0]."-06-01"));
                                        // return $end_date;
                                        $split_date="QTR-I";
                                    }elseif($i==4) {
                                        $start_date=explode('-',$fin_year)[0]."-01-01";
                                        $end_date=explode('-',$fin_year)[0]."-03-".date('t',strtotime(explode('-',$fin_year)[0]."-03-01"));
                                        // return $end_date;
                                        $split_date="QTR-I";
                                    }
                                    $split_date=$split_date;
                                    array_push($categories,$split_date);
                                    // return $categories;
                                    $rawQuery1='';
                                    $queryString='td_mutual_fund_trans.trans_date';
                                    $rawQuery1.=(strlen($rawQuery) > 0)?" AND ":" ";
                                    $rawQuery1.=$queryString.'>="'.$start_date.'" AND '.$queryString.'<="'.$end_date.'" ';
                                    $myrawQuery=$rawQuery.$rawQuery1;
                                    // return $myrawQuery;
                                    // DB::enableQueryLog();
                                    // $this->searchTrands_QueryFun($split_date,$myrawQuery,$monthly_inflow_amount_set,$monthly_outflow_amount_set,$monthly_net_inflow_amount_set,$table_data,$trans_type,$trans_sub_type);
                                    $all_data=MutualFundTransaction::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_mutual_fund_trans.product_code')
                                        ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                                        ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
                                        ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                                        ->leftJoin('md_amc','md_amc.amc_code','=','td_mutual_fund_trans.amc_code')
                                        ->leftJoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
                                        ->leftJoin('md_option','md_option.id','=','md_scheme_isin.option_id')
                                        ->select('td_mutual_fund_trans.*','md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name','md_amc.amc_short_name as amc_name',
                                        'md_plan.plan_name as plan_name','md_option.opt_name as option_name')
                                        ->selectRaw('sum(amount) as tot_amount')
                                        ->selectRaw('sum(stamp_duty) as tot_stamp_duty')
                                        ->selectRaw('sum(tds) as tot_tds')
                                        ->selectRaw('count(*) as tot_rows')
                                        ->where('td_mutual_fund_trans.delete_flag','N')
                                        ->where('td_mutual_fund_trans.amc_flag','N')
                                        ->where('td_mutual_fund_trans.scheme_flag','N')
                                        ->where('td_mutual_fund_trans.plan_option_flag','N')
                                        ->where('td_mutual_fund_trans.bu_type_flag','N')
                                        ->where('td_mutual_fund_trans.divi_mismatch_flag','N')
                                        ->whereRaw($myrawQuery)
                                        ->groupBy('td_mutual_fund_trans.trans_no')
                                        ->groupBy('td_mutual_fund_trans.trxn_type_flag')
                                        ->groupByRaw('IF(substr(trxn_nature,1,19)="Systematic-Reversed","Systematic-Reversed",trxn_nature)')
                                        ->groupBy('td_mutual_fund_trans.trans_desc')
                                        ->groupBy('td_mutual_fund_trans.kf_trans_type')
                                        // ->take(10)
                                        ->get();
                                    // dd(DB::getQueryLog());
                                    // return $all_data;
                                    $inflow_amount=0;
                                    $outflow_amount=0;
                                    $net_inflow_amount=0;
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
                                                    $process_type=$get_type_subtype->process_type;
                                                }
                                            }else{
                                                if ($get_type_subtype) {
                                                    $transaction_type=$get_type_subtype->trans_type." Rejection";
                                                    $transaction_subtype=$get_type_subtype->trans_sub_type." Rejection";
                                                    if ($transaction_subtype=='Refund Rejection') {
                                                        $process_type='O';
                                                    }else {
                                                        $process_type='';
                                                    }
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
                                                $process_type='I';
                                            }elseif ($trans_flag=='TO') {
                                                $get_type_subtype='';
                                                $transaction_type='Transfer Out';
                                                $transaction_subtype='Transfer Out';
                                                $process_type='O';
                                            } else {
                                                $get_type_subtype=MFTransTypeSubType::where('c_k_trans_sub_type',$kf_trans_type)
                                                    ->first();
                                            }
                                            
                                            if ($get_type_subtype) {
                                                $transaction_type=$get_type_subtype->trans_type;
                                                $transaction_subtype=$get_type_subtype->trans_sub_type;
                                                $process_type=$get_type_subtype->process_type;
                                            }
                                        }
                                        $value->gross_amount= number_format((float)((float)$amount + (float)$value->stamp_duty + (float)$value->tds), 2, '.', '');
                                        // number_format((float)$foo, 2, '.', '')
                                        $value->tot_gross_amount= number_format((float)((float)$value->tot_amount + (float)$value->tot_stamp_duty + (float)$value->tot_tds), 2, '.', '');
                                        $value->transaction_type=$transaction_type;
                                        $value->transaction_subtype=$transaction_subtype;
                                        $value->process_type=$process_type;
            
                                        if (!empty($trans_type) && in_array($transaction_type ,$trans_type) && !empty($trans_sub_type) && in_array($transaction_subtype ,$trans_sub_type)) {
                                            array_push($data,$value);
                                        }else if (!empty($trans_type) && in_array($transaction_type ,$trans_type)) {
                                            array_push($data,$value);
                                        }else if (!empty($transaction_subtype) && in_array($transaction_subtype ,$trans_sub_type)) {
                                            array_push($data,$value);
                                        }else{
                                            array_push($data,$value);
                                        }
            
                                        if ($value->process_type=='I') {
                                            $inflow_amount=$inflow_amount + $value->tot_gross_amount;
                                        }elseif ($value->process_type=='O') {
                                            $outflow_amount=$outflow_amount + $value->tot_gross_amount;
                                        }
                                    }
            
                                    $net_inflow_amount=$inflow_amount - $outflow_amount;
                                    array_push($monthly_inflow_amount_set,$inflow_amount);
                                    array_push($monthly_outflow_amount_set,$outflow_amount);
                                    array_push($monthly_net_inflow_amount_set,$net_inflow_amount);
                                    $myset_data=[];
                                    $myset_data['monthly']=$split_date;
                                    $myset_data['monthly_inflow']=$inflow_amount;
                                    $myset_data['monthly_outflow']=$outflow_amount;
                                    $myset_data['monthly_net_inflow']=$net_inflow_amount;
                                    $myset_data['per_of_growth']=0;
                                    $myset_data['trend']=0;
                                    array_push($table_data,$myset_data);
                                }
                            }
                            // return $categories;
                        }elseif ($period_type=='H') {
                            // return $request;
                            // $start_date=explode('-',$fin_year)[0]."-10-01";
                            // $end_date=$end_date;
                            // return $start_date."  -  ".$end_date;
                            if (strtotime($end_date) >= strtotime($today)) {
                                for ($i=0; $i <= 2; $i++) { 
                                    if($i==0) {
                                        $start_date=explode('-',$fin_year)[0]."-10-01";
                                        $end_date=$end_date;
                                        $split_date="2nd-HALF";
                                    }elseif($i==1) {
                                        $start_date=explode('-',$fin_year)[0]."-04-01";
                                        $end_date=explode('-',$fin_year)[0]."-09-".date('t',strtotime(explode('-',$fin_year)[0]."-09-01"));
                                        $split_date="1st-HALF";
                                    }elseif($i==2) {
                                        $start_date=(explode('-',$fin_year)[0] - 1)."-10-01";
                                        $end_date=explode('-',$fin_year)[0]."-03-".date('t',strtotime(explode('-',$fin_year)[0]."-03-01"));
                                        $split_date="1st-HALF";
                                    }
                                    $split_date=$split_date;
                                    array_push($categories,$split_date);
                                    // return $split_date;
                                    $rawQuery1='';
                                    $queryString='td_mutual_fund_trans.trans_date';
                                    $rawQuery1.=(strlen($rawQuery) > 0)?" AND ":" ";
                                    $rawQuery1.=$queryString.'>="'.$start_date.'" AND '.$queryString.'<="'.$end_date.'" ';
                                    $myrawQuery=$rawQuery.$rawQuery1;
                                    // return $myrawQuery;
                                    // DB::enableQueryLog();
                                    // $this->searchTrands_QueryFun($split_date,$myrawQuery,$monthly_inflow_amount_set,$monthly_outflow_amount_set,$monthly_net_inflow_amount_set,$table_data,$trans_type,$trans_sub_type);
                                    $all_data=MutualFundTransaction::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_mutual_fund_trans.product_code')
                                        ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                                        ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
                                        ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                                        ->leftJoin('md_amc','md_amc.amc_code','=','td_mutual_fund_trans.amc_code')
                                        ->leftJoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
                                        ->leftJoin('md_option','md_option.id','=','md_scheme_isin.option_id')
                                        ->select('td_mutual_fund_trans.*','md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name','md_amc.amc_short_name as amc_name',
                                        'md_plan.plan_name as plan_name','md_option.opt_name as option_name')
                                        ->selectRaw('sum(amount) as tot_amount')
                                        ->selectRaw('sum(stamp_duty) as tot_stamp_duty')
                                        ->selectRaw('sum(tds) as tot_tds')
                                        ->selectRaw('count(*) as tot_rows')
                                        ->where('td_mutual_fund_trans.delete_flag','N')
                                        ->where('td_mutual_fund_trans.amc_flag','N')
                                        ->where('td_mutual_fund_trans.scheme_flag','N')
                                        ->where('td_mutual_fund_trans.plan_option_flag','N')
                                        ->where('td_mutual_fund_trans.bu_type_flag','N')
                                        ->where('td_mutual_fund_trans.divi_mismatch_flag','N')
                                        ->whereRaw($myrawQuery)
                                        ->groupBy('td_mutual_fund_trans.trans_no')
                                        ->groupBy('td_mutual_fund_trans.trxn_type_flag')
                                        ->groupByRaw('IF(substr(trxn_nature,1,19)="Systematic-Reversed","Systematic-Reversed",trxn_nature)')
                                        ->groupBy('td_mutual_fund_trans.trans_desc')
                                        ->groupBy('td_mutual_fund_trans.kf_trans_type')
                                        // ->take(10)
                                        ->get();
                                    // return $all_data;
                                    $inflow_amount=0;
                                    $outflow_amount=0;
                                    $net_inflow_amount=0;
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
                                                    $process_type=$get_type_subtype->process_type;
                                                }
                                            }else{
                                                if ($get_type_subtype) {
                                                    $transaction_type=$get_type_subtype->trans_type." Rejection";
                                                    $transaction_subtype=$get_type_subtype->trans_sub_type." Rejection";
                                                    if ($transaction_subtype=='Refund Rejection') {
                                                        $process_type='O';
                                                    }else {
                                                        $process_type='';
                                                    }
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
                                                $process_type='I';
                                            }elseif ($trans_flag=='TO') {
                                                $get_type_subtype='';
                                                $transaction_type='Transfer Out';
                                                $transaction_subtype='Transfer Out';
                                                $process_type='O';
                                            } else {
                                                $get_type_subtype=MFTransTypeSubType::where('c_k_trans_sub_type',$kf_trans_type)
                                                    ->first();
                                            }
                                            
                                            if ($get_type_subtype) {
                                                $transaction_type=$get_type_subtype->trans_type;
                                                $transaction_subtype=$get_type_subtype->trans_sub_type;
                                                $process_type=$get_type_subtype->process_type;
                                            }
                                        }
                                        $value->gross_amount= number_format((float)((float)$amount + (float)$value->stamp_duty + (float)$value->tds), 2, '.', '');
                                        // number_format((float)$foo, 2, '.', '')
                                        $value->tot_gross_amount= number_format((float)((float)$value->tot_amount + (float)$value->tot_stamp_duty + (float)$value->tot_tds), 2, '.', '');
                                        $value->transaction_type=$transaction_type;
                                        $value->transaction_subtype=$transaction_subtype;
                                        $value->process_type=$process_type;
            
                                        if (!empty($trans_type) && in_array($transaction_type ,$trans_type) && !empty($trans_sub_type) && in_array($transaction_subtype ,$trans_sub_type)) {
                                            array_push($data,$value);
                                        }else if (!empty($trans_type) && in_array($transaction_type ,$trans_type)) {
                                            array_push($data,$value);
                                        }else if (!empty($transaction_subtype) && in_array($transaction_subtype ,$trans_sub_type)) {
                                            array_push($data,$value);
                                        }else{
                                            array_push($data,$value);
                                        }
            
                                        if ($value->process_type=='I') {
                                            $inflow_amount=$inflow_amount + $value->tot_gross_amount;
                                        }elseif ($value->process_type=='O') {
                                            $outflow_amount=$outflow_amount + $value->tot_gross_amount;
                                        }
                                    }
            
                                    $net_inflow_amount=$inflow_amount - $outflow_amount;
                                    array_push($monthly_inflow_amount_set,$inflow_amount);
                                    array_push($monthly_outflow_amount_set,$outflow_amount);
                                    array_push($monthly_net_inflow_amount_set,$net_inflow_amount);
                                    $myset_data=[];
                                    $myset_data['monthly']=$split_date;
                                    $myset_data['monthly_inflow']=$inflow_amount;
                                    $myset_data['monthly_outflow']=$outflow_amount;
                                    $myset_data['monthly_net_inflow']=$net_inflow_amount;
                                    $myset_data['per_of_growth']=0;
                                    $myset_data['trend']=0;
                                    array_push($table_data,$myset_data);
                                }
                            }else {
                                for ($i=0; $i <= 2; $i++) { 
                                    if($i==0) {
                                        $start_date=explode('-',$fin_year)[0]."-10-01";
                                        $end_date=$end_date;
                                        $split_date="2nd-HALF";
                                    }elseif($i==1) {
                                        $start_date=explode('-',$fin_year)[0]."-04-01";
                                        $end_date=explode('-',$fin_year)[0]."-09-".date('t',strtotime(explode('-',$fin_year)[0]."-09-01"));
                                        $split_date="1st-HALF";
                                    }elseif($i==2) {
                                        $start_date=(explode('-',$fin_year)[0] - 1)."-10-01";
                                        $end_date=explode('-',$fin_year)[0]."-03-".date('t',strtotime((explode('-',$fin_year)[0] - 1)."-03-01"));
                                        $split_date="1st-HALF";
                                    }
                                    $split_date=$split_date;
                                    array_push($categories,$split_date);

                                    $rawQuery1='';
                                    $queryString='td_mutual_fund_trans.trans_date';
                                    $rawQuery1.=(strlen($rawQuery) > 0)?" AND ":" ";
                                    $rawQuery1.=$queryString.'>="'.$start_date.'" AND '.$queryString.'<="'.$end_date.'" ';
                                    $myrawQuery=$rawQuery.$rawQuery1;
                                    // return $myrawQuery;
                                    // DB::enableQueryLog();
                                    // $this->searchTrands_QueryFun($split_date,$myrawQuery,$monthly_inflow_amount_set,$monthly_outflow_amount_set,$monthly_net_inflow_amount_set,$table_data,$trans_type,$trans_sub_type);
                                    $all_data=MutualFundTransaction::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_mutual_fund_trans.product_code')
                                        ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                                        ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
                                        ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                                        ->leftJoin('md_amc','md_amc.amc_code','=','td_mutual_fund_trans.amc_code')
                                        ->leftJoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
                                        ->leftJoin('md_option','md_option.id','=','md_scheme_isin.option_id')
                                        ->select('td_mutual_fund_trans.*','md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name','md_amc.amc_short_name as amc_name',
                                        'md_plan.plan_name as plan_name','md_option.opt_name as option_name')
                                        ->selectRaw('sum(amount) as tot_amount')
                                        ->selectRaw('sum(stamp_duty) as tot_stamp_duty')
                                        ->selectRaw('sum(tds) as tot_tds')
                                        ->selectRaw('count(*) as tot_rows')
                                        ->where('td_mutual_fund_trans.delete_flag','N')
                                        ->where('td_mutual_fund_trans.amc_flag','N')
                                        ->where('td_mutual_fund_trans.scheme_flag','N')
                                        ->where('td_mutual_fund_trans.plan_option_flag','N')
                                        ->where('td_mutual_fund_trans.bu_type_flag','N')
                                        ->where('td_mutual_fund_trans.divi_mismatch_flag','N')
                                        ->whereRaw($myrawQuery)
                                        ->groupBy('td_mutual_fund_trans.trans_no')
                                        ->groupBy('td_mutual_fund_trans.trxn_type_flag')
                                        ->groupByRaw('IF(substr(trxn_nature,1,19)="Systematic-Reversed","Systematic-Reversed",trxn_nature)')
                                        ->groupBy('td_mutual_fund_trans.trans_desc')
                                        ->groupBy('td_mutual_fund_trans.kf_trans_type')
                                        // ->take(10)
                                        ->get();
                                    // return $all_data;
                                    $inflow_amount=0;
                                    $outflow_amount=0;
                                    $net_inflow_amount=0;
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
                                                    $process_type=$get_type_subtype->process_type;
                                                }
                                            }else{
                                                if ($get_type_subtype) {
                                                    $transaction_type=$get_type_subtype->trans_type." Rejection";
                                                    $transaction_subtype=$get_type_subtype->trans_sub_type." Rejection";
                                                    if ($transaction_subtype=='Refund Rejection') {
                                                        $process_type='O';
                                                    }else {
                                                        $process_type='';
                                                    }
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
                                                $process_type='I';
                                            }elseif ($trans_flag=='TO') {
                                                $get_type_subtype='';
                                                $transaction_type='Transfer Out';
                                                $transaction_subtype='Transfer Out';
                                                $process_type='O';
                                            } else {
                                                $get_type_subtype=MFTransTypeSubType::where('c_k_trans_sub_type',$kf_trans_type)
                                                    ->first();
                                            }
                                            
                                            if ($get_type_subtype) {
                                                $transaction_type=$get_type_subtype->trans_type;
                                                $transaction_subtype=$get_type_subtype->trans_sub_type;
                                                $process_type=$get_type_subtype->process_type;
                                            }
                                        }
                                        $value->gross_amount= number_format((float)((float)$amount + (float)$value->stamp_duty + (float)$value->tds), 2, '.', '');
                                        // number_format((float)$foo, 2, '.', '')
                                        $value->tot_gross_amount= number_format((float)((float)$value->tot_amount + (float)$value->tot_stamp_duty + (float)$value->tot_tds), 2, '.', '');
                                        $value->transaction_type=$transaction_type;
                                        $value->transaction_subtype=$transaction_subtype;
                                        $value->process_type=$process_type;
            
                                        if (!empty($trans_type) && in_array($transaction_type ,$trans_type) && !empty($trans_sub_type) && in_array($transaction_subtype ,$trans_sub_type)) {
                                            array_push($data,$value);
                                        }else if (!empty($trans_type) && in_array($transaction_type ,$trans_type)) {
                                            array_push($data,$value);
                                        }else if (!empty($transaction_subtype) && in_array($transaction_subtype ,$trans_sub_type)) {
                                            array_push($data,$value);
                                        }else{
                                            array_push($data,$value);
                                        }
            
                                        if ($value->process_type=='I') {
                                            $inflow_amount=$inflow_amount + $value->tot_gross_amount;
                                        }elseif ($value->process_type=='O') {
                                            $outflow_amount=$outflow_amount + $value->tot_gross_amount;
                                        }
                                    }
            
                                    $net_inflow_amount=$inflow_amount - $outflow_amount;
                                    array_push($monthly_inflow_amount_set,$inflow_amount);
                                    array_push($monthly_outflow_amount_set,$outflow_amount);
                                    array_push($monthly_net_inflow_amount_set,$net_inflow_amount);
                                    $myset_data=[];
                                    $myset_data['monthly']=$split_date;
                                    $myset_data['monthly_inflow']=$inflow_amount;
                                    $myset_data['monthly_outflow']=$outflow_amount;
                                    $myset_data['monthly_net_inflow']=$net_inflow_amount;
                                    $myset_data['per_of_growth']=0;
                                    $myset_data['trend']=0;
                                    array_push($table_data,$myset_data);
                                }
                            }
                        }
                        break;
                    case 'F':
                        // return $request;
                        for ($i=0; $i <= $upto; $i++) { 
                            $split_date=date('Y', strtotime('-'.$i.' Years'));
                            array_push($categories,$split_date);
                            // return $split_date;
                            // return $rawQuery;
                            $rawQuery1='';
                            $queryString='td_mutual_fund_trans.trans_date';
                            $rawQuery1.=(strlen($rawQuery) > 0)?" AND ":" ";
                            $rawQuery1.=' YEAR('.$queryString.')="'.$split_date.'" ';
                            $myrawQuery=$rawQuery.$rawQuery1;
                            // return $myrawQuery;
                            $all_data=MutualFundTransaction::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_mutual_fund_trans.product_code')
                                ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                                ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
                                ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                                ->leftJoin('md_amc','md_amc.amc_code','=','td_mutual_fund_trans.amc_code')
                                ->leftJoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
                                ->leftJoin('md_option','md_option.id','=','md_scheme_isin.option_id')
                                ->select('td_mutual_fund_trans.*','md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name','md_amc.amc_short_name as amc_name',
                                'md_plan.plan_name as plan_name','md_option.opt_name as option_name')
                                ->selectRaw('sum(amount) as tot_amount')
                                ->selectRaw('sum(stamp_duty) as tot_stamp_duty')
                                ->selectRaw('sum(tds) as tot_tds')
                                ->selectRaw('count(*) as tot_rows')
                                ->where('td_mutual_fund_trans.delete_flag','N')
                                ->where('td_mutual_fund_trans.amc_flag','N')
                                ->where('td_mutual_fund_trans.scheme_flag','N')
                                ->where('td_mutual_fund_trans.plan_option_flag','N')
                                ->where('td_mutual_fund_trans.bu_type_flag','N')
                                ->where('td_mutual_fund_trans.divi_mismatch_flag','N')
                                ->whereRaw($myrawQuery)
                                ->groupBy('td_mutual_fund_trans.trans_no')
                                ->groupBy('td_mutual_fund_trans.trxn_type_flag')
                                ->groupByRaw('IF(substr(trxn_nature,1,19)="Systematic-Reversed","Systematic-Reversed",trxn_nature)')
                                ->groupBy('td_mutual_fund_trans.trans_desc')
                                ->groupBy('td_mutual_fund_trans.kf_trans_type')
                                ->take(10)
                                ->get();
                            // dd(DB::getQueryLog());
    
                            $inflow_amount=0;
                            $outflow_amount=0;
                            $net_inflow_amount=0;
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
                                            $process_type=$get_type_subtype->process_type;
                                        }
                                    }else{
                                        if ($get_type_subtype) {
                                            $transaction_type=$get_type_subtype->trans_type." Rejection";
                                            $transaction_subtype=$get_type_subtype->trans_sub_type." Rejection";
                                            if ($transaction_subtype=='Refund Rejection') {
                                                $process_type='O';
                                            }else {
                                                $process_type='';
                                            }
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
                                        $process_type='I';
                                    }elseif ($trans_flag=='TO') {
                                        $get_type_subtype='';
                                        $transaction_type='Transfer Out';
                                        $transaction_subtype='Transfer Out';
                                        $process_type='O';
                                    } else {
                                        $get_type_subtype=MFTransTypeSubType::where('c_k_trans_sub_type',$kf_trans_type)
                                            ->first();
                                    }
                                    
                                    if ($get_type_subtype) {
                                        $transaction_type=$get_type_subtype->trans_type;
                                        $transaction_subtype=$get_type_subtype->trans_sub_type;
                                        $process_type=$get_type_subtype->process_type;
                                    }
                                }
                                $value->gross_amount= number_format((float)((float)$amount + (float)$value->stamp_duty + (float)$value->tds), 2, '.', '');
                                // number_format((float)$foo, 2, '.', '')
                                $value->tot_gross_amount= number_format((float)((float)$value->tot_amount + (float)$value->tot_stamp_duty + (float)$value->tot_tds), 2, '.', '');
                                $value->transaction_type=$transaction_type;
                                $value->transaction_subtype=$transaction_subtype;
                                $value->process_type=$process_type;
    
                                if (!empty($trans_type) && in_array($transaction_type ,$trans_type) && !empty($trans_sub_type) && in_array($transaction_subtype ,$trans_sub_type)) {
                                    array_push($data,$value);
                                }else if (!empty($trans_type) && in_array($transaction_type ,$trans_type)) {
                                    array_push($data,$value);
                                }else if (!empty($transaction_subtype) && in_array($transaction_subtype ,$trans_sub_type)) {
                                    array_push($data,$value);
                                }else{
                                    array_push($data,$value);
                                }
    
                                if ($value->process_type=='I') {
                                    $inflow_amount=$inflow_amount + $value->tot_gross_amount;
                                }elseif ($value->process_type=='O') {
                                    $outflow_amount=$outflow_amount + $value->tot_gross_amount;
                                }
                            }
    
                            $net_inflow_amount=$inflow_amount - $outflow_amount;
                            array_push($monthly_inflow_amount_set,$inflow_amount);
                            array_push($monthly_outflow_amount_set,$outflow_amount);
                            array_push($monthly_net_inflow_amount_set,$net_inflow_amount);
                            $myset_data=[];
                            $myset_data['monthly']=$split_date;
                            $myset_data['monthly_inflow']=$inflow_amount;
                            $myset_data['monthly_outflow']=$outflow_amount;
                            $myset_data['monthly_net_inflow']=$net_inflow_amount;
                            $myset_data['per_of_growth']=0;
                            $myset_data['trend']=0;
                            array_push($table_data,$myset_data);
                        }
                        break;
                    default:
                        break;
                }
            } 
            $chart_data=[
                ['name'=>'Monthly Inflow','data'=>$monthly_inflow_amount_set],
                ['name'=>'Monthly Outflow','data'=>$monthly_outflow_amount_set],
                ['name'=>'Net Inflow','data'=>$monthly_net_inflow_amount_set]
            ];
          
            $final_data=[];
            $final_data['categories']=$categories;
            $final_data['chart_data']=$chart_data;
            $final_data['table_data']=$table_data;
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($final_data);
    }

}