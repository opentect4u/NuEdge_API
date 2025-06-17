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
    MFTransTypeSubType,
    Disclaimer
};
use Validator;
use Illuminate\Support\Carbon;
use Excel;
use App\Helpers\TransHelper;
use DB;

class MonthlyMisController extends Controller
{
    /**
     * Display a listing of the mutual fund transactions.
     *
     * @return \Illuminate\Http\Response
     */
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

            $brn_cd=json_decode($request->brn_cd);
            $bu_type=json_decode($request->bu_type);
            $rm_id=json_decode($request->rm_id);
            $euin_no=json_decode($request->euin_no);
            $sub_brk_cd=json_decode($request->sub_brk_cd);

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
                
                // dd(DB::getQueryLog());
            } 

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

                    if (!empty($trans_type) || !empty($trans_sub_type)) {
                        if (in_array($transaction_type ,$trans_type) && in_array($transaction_subtype ,$trans_sub_type)) {
                            array_push($data,$value);
                        }else if (in_array($transaction_type ,$trans_type)) {
                            array_push($data,$value);
                        }else if (in_array($transaction_subtype ,$trans_sub_type)) {
                            array_push($data,$value);
                        }
                    } else {
                        array_push($data,$value);
                    }
                }

            $disclaimer=Disclaimer::select('dis_des','font_size','color_code')->find(3);
            $mydata=[];
            $mydata['data']=$data;
            $mydata['disclaimer']=$disclaimer;
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($mydata);
    }

    /**
     * Search mutual fund transactions based on raw query.
     *
     * @param string $rawQuery
     * @return \Illuminate\Support\Collection
     */
    public function searchTrandsQuery($rawQuery)
    {
        $all_data=MutualFundTransaction::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_mutual_fund_trans.product_code')
            ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
            ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
            ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
            ->leftJoin('md_amc','md_amc.amc_code','=','td_mutual_fund_trans.amc_code')
            ->leftJoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
            ->leftJoin('md_option','md_option.id','=','md_scheme_isin.option_id')
            ->select('td_mutual_fund_trans.amount','td_mutual_fund_trans.stamp_duty','td_mutual_fund_trans.tds','td_mutual_fund_trans.trans_flag','td_mutual_fund_trans.trxn_type_code',
            'td_mutual_fund_trans.trxn_type_flag','td_mutual_fund_trans.trxn_nature_code','td_mutual_fund_trans.trans_date',
            'md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name','md_amc.amc_short_name as amc_name',
            'md_plan.plan_name as plan_name','md_option.opt_name as option_name')
            ->selectRaw('sum(amount) as tot_amount')
            ->selectRaw('sum(stamp_duty) as tot_stamp_duty')
            ->selectRaw('sum(tds) as tot_tds')
            ->selectRaw('count(*) as tot_rows')
            ->selectRaw('IF(td_mutual_fund_trans.rnt_id=1,
            (SELECT trans_type FROM md_mf_trans_type_subtype WHERE c_trans_type_code=td_mutual_fund_trans.trxn_type_code AND c_k_trans_type=td_mutual_fund_trans.trxn_type_flag AND c_k_trans_sub_type=td_mutual_fund_trans.trxn_nature_code limit 1),
            (CASE 
                WHEN td_mutual_fund_trans.trans_flag="DP" || td_mutual_fund_trans.trans_flag="DR" THEN (SELECT trans_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=td_mutual_fund_trans.kf_trans_type AND k_divident_flag=td_mutual_fund_trans.trans_flag limit 1)
                WHEN td_mutual_fund_trans.trans_flag="TI" THEN "Transfer In"
                WHEN td_mutual_fund_trans.trans_flag="TO" THEN "Transfer Out"
                ELSE (SELECT trans_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=td_mutual_fund_trans.kf_trans_type limit 1)
            END)
            )as transaction_type')
            ->selectRaw('IF(td_mutual_fund_trans.rnt_id=1,
            (SELECT trans_sub_type FROM md_mf_trans_type_subtype WHERE c_trans_type_code=td_mutual_fund_trans.trxn_type_code AND c_k_trans_type=td_mutual_fund_trans.trxn_type_flag AND c_k_trans_sub_type=td_mutual_fund_trans.trxn_nature_code limit 1),
            (CASE 
                WHEN td_mutual_fund_trans.trans_flag="DP" || td_mutual_fund_trans.trans_flag="DR" THEN (SELECT process_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=td_mutual_fund_trans.kf_trans_type AND k_divident_flag=td_mutual_fund_trans.trans_flag limit 1)
                WHEN td_mutual_fund_trans.trans_flag="TI" THEN "Transfer In"
                WHEN td_mutual_fund_trans.trans_flag="TO" THEN "Transfer Out"
                ELSE (SELECT trans_sub_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=td_mutual_fund_trans.kf_trans_type limit 1)
            END)
            )as transaction_subtype')
            ->selectRaw('IF(td_mutual_fund_trans.rnt_id=1,
            (SELECT process_type FROM md_mf_trans_type_subtype WHERE c_trans_type_code=td_mutual_fund_trans.trxn_type_code AND c_k_trans_type=td_mutual_fund_trans.trxn_type_flag AND c_k_trans_sub_type=td_mutual_fund_trans.trxn_nature_code limit 1),
            (CASE 
                WHEN td_mutual_fund_trans.trans_flag="DP" || td_mutual_fund_trans.trans_flag="DR" THEN (SELECT process_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=td_mutual_fund_trans.kf_trans_type AND k_divident_flag=td_mutual_fund_trans.trans_flag limit 1)
                WHEN td_mutual_fund_trans.trans_flag="TI" THEN "I"
                WHEN td_mutual_fund_trans.trans_flag="TO" THEN "O"
                ELSE (SELECT process_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=td_mutual_fund_trans.kf_trans_type limit 1)
            END)
            )as process_type')
            ->where('td_mutual_fund_trans.delete_flag','N')
            ->where('td_mutual_fund_trans.amc_flag','N')
            ->where('td_mutual_fund_trans.scheme_flag','N')
            ->where('td_mutual_fund_trans.plan_option_flag','N')
            ->where('td_mutual_fund_trans.bu_type_flag','N')
            ->where('td_mutual_fund_trans.divi_mismatch_flag','N')
            ->whereRaw($rawQuery)
            ->groupBy('td_mutual_fund_trans.trans_no')
            ->groupBy('td_mutual_fund_trans.trxn_type_flag')
            ->groupBy('td_mutual_fund_trans.trxn_nature_code')
            ->groupBy('td_mutual_fund_trans.trans_desc')
            ->groupBy('td_mutual_fund_trans.kf_trans_type')
            ->groupBy('td_mutual_fund_trans.trans_flag')
            ->groupBy('td_mutual_fund_trans.pur_price')
            ->orderBy('td_mutual_fund_trans.trans_date','DESC')
            // ->inRandomOrder()
            // ->take(10)
            ->get();
        return $all_data;
    }

    /**
     * Search mutual fund transactions based on various filters.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
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
                        // return $request;
                        // return $month_year;
                        // return explode('-',$month_year);
                        $start_date=date('Y-m-d',strtotime("01-".str_replace('/','-',str_replace(' ','',explode('-',$month_year)[0]))));
                        $end_date=date('Y-m-d',strtotime("01-".str_replace('/','-',str_replace(' ','',explode('-',$month_year)[1]))));
                        $start_date= date("Y-m-d",strtotime("-1 month",strtotime($start_date)));
                        $end_date=date("Y-m-t", strtotime($end_date));
                        if(strtotime($end_date) > strtotime(date('Y-m-d'))){
                            $end_date=date('Y-m-d');
                        }
                        // return $start_date.' - '.$end_date;
                        $queryString='td_mutual_fund_trans.trans_date';
                        $rawQuery.=Helper::FrmToDateRawQuery($start_date,$end_date,$rawQuery,$queryString);
                        $all_data=$this->searchTrandsQuery($rawQuery);
                        // return $all_data[0];
                        // return count($all_data);
                        $dates = [];
                        foreach($all_data as $event){
                            $timestamp = strtotime($event['trans_date']);
                            $dateSort = date('M-Y', $timestamp);
                            $dates[$dateSort][] = $event;
                        }
                        // return $dates;
                        foreach ($dates as $key => $date) {
                            // return $key;
                            $monthly_date=date('Y-m-d',strtotime($key));
                            array_push($categories,$key);
                            $inflow_amount=0;
                            $outflow_amount=0;
                            $net_inflow_amount=0;
                            foreach ($date as $key1 => $value) {
                                if ($value->rnt_id==1) {
                                    if ($value->amount < 0) {
                                        $value->transaction_type=$value->trans_type." Rejection";
                                        $value->transaction_subtype=$value->trans_sub_type." Rejection";
                                        if ($value->transaction_subtype=='Refund Rejection') {
                                            $value->process_type='O';
                                        }
                                    }
                                }
                                // return $value;
                                $value->gross_amount= number_format((float)((float)$value->amount + (float)$value->stamp_duty + (float)$value->tds), 2, '.', '');
                                // number_format((float)$foo, 2, '.', '')
                                $value->tot_gross_amount= number_format((float)((float)$value->tot_amount + (float)$value->tot_stamp_duty + (float)$value->tot_tds), 2, '.', '');
                                
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
                            $myset_data['monthly']=$monthly_date;
                            $myset_data['monthly_inflow']=$inflow_amount;
                            $myset_data['monthly_outflow']=$outflow_amount;
                            $myset_data['monthly_net_inflow']=$net_inflow_amount;
                            $myset_data['per_of_growth']=0;
                            $myset_data['trend']=0;
                            array_push($table_data,$myset_data);
                        }
                        break;
                    case 'D':
                        // return $upto;
                        $end_date=date('Y-m-d');
                        $start_date=date('Y-m', strtotime('-'.$upto.' months')).'-01';
                        // return $start_date.' - '.$end_date;
                        $queryString='td_mutual_fund_trans.trans_date';
                        $rawQuery.=Helper::FrmToDateRawQuery($start_date,$end_date,$rawQuery,$queryString);
                        $all_data=$this->searchTrandsQuery($rawQuery);
                        // return $all_data[0];
                        // return count($all_data);
                        $dates = [];
                        foreach($all_data as $event){
                            $timestamp = strtotime($event['trans_date']);
                            $dateSort = date('M-Y', $timestamp);
                            $dates[$dateSort][] = $event;
                        }
                        // return $dates;
                        foreach ($dates as $key => $date) {
                            // return $key;
                            $monthly_date=date('Y-m-d',strtotime($key));
                            array_push($categories,$key);
                            $inflow_amount=0;
                            $outflow_amount=0;
                            $net_inflow_amount=0;
                            foreach ($date as $key1 => $value) {
                                if ($value->rnt_id==1) {
                                    if ($value->amount < 0) {
                                        $value->transaction_type=$value->trans_type." Rejection";
                                        $value->transaction_subtype=$value->trans_sub_type." Rejection";
                                        if ($value->transaction_subtype=='Refund Rejection') {
                                            $value->process_type='O';
                                        }
                                    }
                                }
                                // return $value;
                                $value->gross_amount= number_format((float)((float)$value->amount + (float)$value->stamp_duty + (float)$value->tds), 2, '.', '');
                                // number_format((float)$foo, 2, '.', '')
                                $value->tot_gross_amount= number_format((float)((float)$value->tot_amount + (float)$value->tot_stamp_duty + (float)$value->tot_tds), 2, '.', '');
                                
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
                            $myset_data['monthly']=$monthly_date;
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
                        // return Helper::getFinYear();
                        // $start_date=explode('-',$fin_year)[0]."-04-01";
                        // $lastday = date('t',strtotime(explode('-',$fin_year)[1]."-03-01"));
                        // $cal_end_date = explode('-',$fin_year)[1]."-03-".$lastday;
                        // $today=date('Y-m-d');
                        // $end_date = (strtotime($today) >= strtotime($cal_end_date)) ? explode('-',$fin_year)[1]."-03-".$lastday : date('Y-m-d');
                        // return $start_date."  -  ".$end_date;
                        if ($period_type=='M') {
                            $start_date=explode('-',$fin_year)[0]."-04-01";
                            $add_month=(date('m',strtotime($start_date)) == date('m',strtotime(date("Y-m-d"))))?2:1;
                            $start_date= date("Y-m-d",strtotime("-".$add_month." month",strtotime($start_date)));
                            $end_date = explode('-',$fin_year)[1]."-03-".date('t',strtotime(explode('-',$fin_year)[1]."-03-01"));
                            if(strtotime($end_date) > strtotime(date('Y-m-d'))){
                                $end_date=date('Y-m-d');
                            }
                            // return $start_date."  -  ".$end_date;
                            $queryString='td_mutual_fund_trans.trans_date';
                            $rawQuery.=Helper::FrmToDateRawQuery($start_date,$end_date,$rawQuery,$queryString);
                            $all_data=$this->searchTrandsQuery($rawQuery);
                            // return $all_data[0];
                            // return count($all_data);
                            $dates = [];
                            foreach($all_data as $event){
                                $timestamp = strtotime($event['trans_date']);
                                $dateSort = date('M-Y', $timestamp);
                                $dates[$dateSort][] = $event;
                            }
                            // return $dates;
                            foreach ($dates as $key => $date) {
                                // return $key;
                                $monthly_date=date('Y-m-d',strtotime($key));
                                array_push($categories,$key);
                                $inflow_amount=0;
                                $outflow_amount=0;
                                $net_inflow_amount=0;
                                foreach ($date as $key1 => $value) {
                                    if ($value->rnt_id==1) {
                                        if ($value->amount < 0) {
                                            $value->transaction_type=$value->trans_type." Rejection";
                                            $value->transaction_subtype=$value->trans_sub_type." Rejection";
                                            if ($value->transaction_subtype=='Refund Rejection') {
                                                $value->process_type='O';
                                            }
                                        }
                                    }
                                    // return $value;
                                    $value->gross_amount= number_format((float)((float)$value->amount + (float)$value->stamp_duty + (float)$value->tds), 2, '.', '');
                                    // number_format((float)$foo, 2, '.', '')
                                    $value->tot_gross_amount= number_format((float)((float)$value->tot_amount + (float)$value->tot_stamp_duty + (float)$value->tot_tds), 2, '.', '');
                                    
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
                                $myset_data['monthly']=$monthly_date;
                                $myset_data['monthly_inflow']=$inflow_amount;
                                $myset_data['monthly_outflow']=$outflow_amount;
                                $myset_data['monthly_net_inflow']=$net_inflow_amount;
                                $myset_data['per_of_growth']=0;
                                $myset_data['trend']=0;
                                array_push($table_data,$myset_data);
                            }
                        } elseif ($period_type=='Q') {
                            $start_date=explode('-',$fin_year)[0]."-04-01";
                            $start_date= date("Y-m-d",strtotime("-3 month",strtotime($start_date)));
                            $end_date = explode('-',$fin_year)[1]."-03-".date('t',strtotime(explode('-',$fin_year)[1]."-03-01"));
                            // return $start_date."  -  ".$end_date;
                            $queryString='td_mutual_fund_trans.trans_date';
                            $rawQuery.=Helper::FrmToDateRawQuery($start_date,$end_date,$rawQuery,$queryString);
                            // return $rawQuery;
                            $all_data=$this->searchTrandsQuery($rawQuery);
                            // return $all_data;
                            $qtr_iv_end_date= date('Y-m-t',strtotime(explode('-',$fin_year)[1]."-03-01"));
                            $qtr_iv_start_date=explode('-',$fin_year)[1]."-01-01";
                            $qtr_iii_start_date=explode('-',$fin_year)[0]."-10-01";
                            $qtr_iii_end_date=date('Y-m-t',strtotime(explode('-',$fin_year)[0]."-12-01"));
                            $qtr_ii_start_date=explode('-',$fin_year)[0]."-07-01";
                            $qtr_ii_end_date=date('Y-m-t',strtotime(explode('-',$fin_year)[0]."-09-01"));
                            $qtr_i_start_date=explode('-',$fin_year)[0]."-04-01";
                            $qtr_i_end_date=date('Y-m-t',strtotime(explode('-',$fin_year)[0]."-06-01"));
                            $pre_qtr_iv_start_date=explode('-',$fin_year)[0]."-01-01";
                            $pre_qtr_iv_end_date=date('Y-m-t',strtotime(explode('-',$fin_year)[0]."-03-01"));
                            $dates = [];
                            $my_qtr_iv_data=[];
                            $my_qtr_iii_data=[];
                            $my_qtr_ii_data=[];
                            $my_qtr_i_data=[];
                            $my_pre_qtr_iv_data=[];
                            foreach($all_data as $event){
                                if (strtotime($event['trans_date']) >= strtotime($qtr_iv_start_date) && strtotime($event['trans_date']) <= strtotime($qtr_iv_end_date)) {
                                    $my_qtr_iv_data[] = $event;
                                } else if (strtotime($event['trans_date']) >= strtotime($qtr_iii_start_date) && strtotime($event['trans_date']) <= strtotime($qtr_iii_end_date)) {
                                    $my_qtr_iii_data[] = $event;
                                } else if (strtotime($event['trans_date']) >= strtotime($qtr_ii_start_date) && strtotime($event['trans_date']) <= strtotime($qtr_ii_end_date)) {
                                    $my_qtr_ii_data[] = $event;
                                } else if (strtotime($event['trans_date']) >= strtotime($qtr_i_start_date) && strtotime($event['trans_date']) <= strtotime($qtr_i_end_date)) {
                                    $my_qtr_i_data[] = $event;
                                } else if (strtotime($event['trans_date']) >= strtotime($pre_qtr_iv_start_date) && strtotime($event['trans_date']) <= strtotime($pre_qtr_iv_end_date)) {
                                    $my_pre_qtr_iv_data[] = $event;
                                }
                            }
                            $dates['QTR-IV']=$my_qtr_iv_data;
                            $dates['QTR-III']=$my_qtr_iii_data;
                            $dates['QTR-II']=$my_qtr_ii_data;
                            $dates['QTR-I']=$my_qtr_i_data;
                            $dates['PRV-QTR-IV']=$my_pre_qtr_iv_data;
                            // return $dates;
                            foreach ($dates as $key => $date) {
                                // return $key;
                                $monthly_date=$key;
                                array_push($categories,$key);
                                $inflow_amount=0;
                                $outflow_amount=0;
                                $net_inflow_amount=0;
                                foreach ($date as $key1 => $value) {
                                    if ($value->rnt_id==1) {
                                        if ($value->amount < 0) {
                                            $value->transaction_type=$value->trans_type." Rejection";
                                            $value->transaction_subtype=$value->trans_sub_type." Rejection";
                                            if ($value->transaction_subtype=='Refund Rejection') {
                                                $value->process_type='O';
                                            }
                                        }
                                    }
                                    // return $value;
                                    $value->gross_amount= number_format((float)((float)$value->amount + (float)$value->stamp_duty + (float)$value->tds), 2, '.', '');
                                    // number_format((float)$foo, 2, '.', '')
                                    $value->tot_gross_amount= number_format((float)((float)$value->tot_amount + (float)$value->tot_stamp_duty + (float)$value->tot_tds), 2, '.', '');
                                    
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
                                $myset_data['monthly']=$monthly_date;
                                $myset_data['monthly_inflow']=$inflow_amount;
                                $myset_data['monthly_outflow']=$outflow_amount;
                                $myset_data['monthly_net_inflow']=$net_inflow_amount;
                                $myset_data['per_of_growth']=0;
                                $myset_data['trend']=0;
                                array_push($table_data,$myset_data);
                            }
                        }elseif ($period_type=='H') {
                            $start_date=explode('-',$fin_year)[0]."-04-01";
                            $start_date= date("Y-m-d",strtotime("-6 month",strtotime($start_date)));
                            $end_date = explode('-',$fin_year)[1]."-03-".date('t',strtotime(explode('-',$fin_year)[1]."-03-01"));
                            // return $start_date."  -  ".$end_date;
                            $queryString='td_mutual_fund_trans.trans_date';
                            $rawQuery.=Helper::FrmToDateRawQuery($start_date,$end_date,$rawQuery,$queryString);
                            // return $rawQuery;
                            $all_data=$this->searchTrandsQuery($rawQuery);

                            $_2nd_half_start_date=explode('-',$fin_year)[0]."-10-01";
                            $_2nd_half_end_date=date('Y-m-t',strtotime(explode('-',$fin_year)[1]."-12-01"));
                            $_1st_half_start_date=explode('-',$fin_year)[0]."-04-01";
                            $_1st_half_end_date=date('Y-m-t',strtotime(explode('-',$fin_year)[0]."-09-01"));
                            $prev_2nd_half_start_date=(explode('-',$fin_year)[0] - 1)."-10-01";
                            $prev_2nd_half_end_date=date('Y-m-t',strtotime(explode('-',$fin_year)[0]."-03-01"));
                            $dates = [];
                            $my_2nd_half_data=[];
                            $my_1st_half_data=[];
                            $my_pre_2nd_half_data=[];
                            foreach($all_data as $event){
                                if (strtotime($event['trans_date']) >= strtotime($_2nd_half_start_date) && strtotime($event['trans_date']) <= strtotime($_2nd_half_end_date)) {
                                    $my_2nd_half_data[] = $event;
                                } else if (strtotime($event['trans_date']) >= strtotime($_1st_half_start_date) && strtotime($event['trans_date']) <= strtotime($_1st_half_end_date)) {
                                    $my_1st_half_data[] = $event;
                                } else if (strtotime($event['trans_date']) >= strtotime($prev_2nd_half_start_date) && strtotime($event['trans_date']) <= strtotime($prev_2nd_half_end_date)) {
                                    $my_pre_2nd_half_data[] = $event;
                                }
                            }
                            $dates['2nd-HALF']=$my_2nd_half_data;
                            $dates['1st-HALF']=$my_1st_half_data;
                            $dates['PREV-2nd-HALF']=$my_pre_2nd_half_data;
                            // return $dates;
                            foreach ($dates as $key => $date) {
                                // return $key;
                                $monthly_date=$key;
                                array_push($categories,$key);
                                $inflow_amount=0;
                                $outflow_amount=0;
                                $net_inflow_amount=0;
                                foreach ($date as $key1 => $value) {
                                    if ($value->rnt_id==1) {
                                        if ($value->amount < 0) {
                                            $value->transaction_type=$value->trans_type." Rejection";
                                            $value->transaction_subtype=$value->trans_sub_type." Rejection";
                                            if ($value->transaction_subtype=='Refund Rejection') {
                                                $value->process_type='O';
                                            }
                                        }
                                    }
                                    // return $value;
                                    $value->gross_amount= number_format((float)((float)$value->amount + (float)$value->stamp_duty + (float)$value->tds), 2, '.', '');
                                    // number_format((float)$foo, 2, '.', '')
                                    $value->tot_gross_amount= number_format((float)((float)$value->tot_amount + (float)$value->tot_stamp_duty + (float)$value->tot_tds), 2, '.', '');
                                    
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
                                $myset_data['monthly']=$monthly_date;
                                $myset_data['monthly_inflow']=$inflow_amount;
                                $myset_data['monthly_outflow']=$outflow_amount;
                                $myset_data['monthly_net_inflow']=$net_inflow_amount;
                                $myset_data['per_of_growth']=0;
                                $myset_data['trend']=0;
                                array_push($table_data,$myset_data);
                            }
                        }
                        break;
                    case 'F':
                        $end_date=date('Y-m-d');
                        // $end_date='2024-03-10';
                        if ( date('m',strtotime($end_date)) > 3 ) {
                            $year = date('Y',strtotime($end_date));
                            $start_date=($year - $upto).'-04-01';
                        }else {
                            $year = date('Y',strtotime($end_date));
                            $start_date=($year - ($upto + 1)).'-04-01';
                        }
                        // return $start_date.' - '.$end_date;
                        $queryString='td_mutual_fund_trans.trans_date';
                        $rawQuery.=Helper::FrmToDateRawQuery($start_date,$end_date,$rawQuery,$queryString);
                        $all_data=$this->searchTrandsQuery($rawQuery);
                        // return $all_data[0];
                        // return count($all_data);
                        $dates = [];
                        for ($i=0; $i <= $upto; $i++) { 
                            $date=date('Y-m-d', strtotime('-'.$i.' Years'));
                            // $date='2024-04-10';
                            if ( date('m',strtotime($date)) > 3 ) {
                                $year = date('Y',strtotime($date));
                                $cal_start_date=$year.'-04-01';
                                $e_day=($year + 1).'-03-01';
                                $cal_end_date=date("Y-m-t", strtotime($e_day));
                                $split_date=$year.' - '.($year + 1);
                            }else {
                                $year = date('Y',strtotime($date));
                                $cal_start_date=($year - 1).'-04-01';
                                $e_day=$year.'-03-01';
                                $cal_end_date=date("Y-m-t", strtotime($e_day));
                                $split_date=($year - 1).' - '.$year;
                            }
                            // return $cal_start_date.' - '.$cal_end_date;
                            $year_wise_data=[];
                            foreach($all_data as $event){
                                if (strtotime($event['trans_date']) >= strtotime($cal_start_date) && strtotime($event['trans_date']) <= strtotime($cal_end_date)) {
                                    $year_wise_data[] = $event;
                                }
                            }
                            $dates[$split_date]=$year_wise_data;
                        }
                        
                        foreach ($dates as $key => $date) {
                            // return $key;
                            $monthly_date=$key;
                            array_push($categories,$key);
                            $inflow_amount=0;
                            $outflow_amount=0;
                            $net_inflow_amount=0;
                            foreach ($date as $key1 => $value) {
                                if ($value->rnt_id==1) {
                                    if ($value->amount < 0) {
                                        $value->transaction_type=$value->trans_type." Rejection";
                                        $value->transaction_subtype=$value->trans_sub_type." Rejection";
                                        if ($value->transaction_subtype=='Refund Rejection') {
                                            $value->process_type='O';
                                        }
                                    }
                                }
                                // return $value;
                                $value->gross_amount= number_format((float)((float)$value->amount + (float)$value->stamp_duty + (float)$value->tds), 2, '.', '');
                                // number_format((float)$foo, 2, '.', '')
                                $value->tot_gross_amount= number_format((float)((float)$value->tot_amount + (float)$value->tot_stamp_duty + (float)$value->tot_tds), 2, '.', '');
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
                            $myset_data['monthly']=$monthly_date;
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
          
            $disclaimer=Disclaimer::select('dis_des')->find(3);
            $final_data=[];
            $final_data['categories']=$categories;
            $final_data['chart_data']=$chart_data;
            $final_data['table_data']=$table_data;
            $final_data['disclaimer']=$disclaimer->dis_des;
            // return $final_data;
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($final_data);
    }

}