<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\{
    Scheme,
    MutualFundTransaction,
    SipStpTransaction,
    MFTransTypeSubType
};
use App\Helpers\TransHelper;
use App\Helpers\Helper;

class TestController extends Controller
{
    public function index()
    {

        $data=[];
        // $aArray = file('C:\Users\Chitta\Downloads\28072023105405_152496138R49.txt', FILE_IGNORE_NEW_LINES);
        // $aArray = file('C:\Users\Chitta\Downloads\28072023105405_152496138R49.txt');
        // $aArray = file('C:\Users\Chitta\Downloads\28072023151755_152516610R49_new.txt',FILE_IGNORE_NEW_LINES);
        $aArray = file('C:\Users\Chitta\Documents\Nuedge-Online\31_07_2023_transaction\6093033632027015AH8FDJOJ3JDQKKHM6JPO5LIIHF2P17098625196BMB152683051R2\31072023151059_152683051R2.txt',FILE_IGNORE_NEW_LINES);
        
        
        // return $aArray[0];
        // return count($aArray);
        $start=0;
        $end=100;
        for ($i=$start; $i < $end ; $i++) { 
            // return $aArray[$i];
            $exp_data=explode("\t",$aArray[$i]);
            // return count($exp_data);
            return $exp_data;
            
            // return $exp_data[0];
        }
        // foreach($aArray as $key =>$line) {
        //     return $line;
        //     // if ($key > 0) {
        //     //     return $line;
        //     // }
        //     // $exp_data=explode("\t",$line);
        //     // return $exp_data;
        //     // return $exp_data[0];
            
        //     array_push($data,$line);
        // }
        // return $data;

        // ===========================================================================
        // if (str_starts_with('http://www.google.com', 'httppp')) {
        //     $val='if';
        // }else {
        //     $val='else';
        // }
        // return $val;
    }

    public function test111()
    {
        $scheme_type='O';
        DB::enableQueryLog();

        $data=Scheme::join('md_amc','md_amc.id','=','md_scheme.amc_id')
                ->join('md_category','md_category.id','=','md_scheme.category_id')
                ->join('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                ->join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                ->select('md_scheme.*','md_amc.amc_name as amc_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcate_name','md_rnt.rnt_name as rnt_name')
                
                ->selectRaw('SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",16) as sip_amount_filter_1')
                ->selectRaw('SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",20) as sip_amount_filter_2')

                ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",16), "\"", -1) as D_sip_min_F_amount') // D sip min
                ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",20), "\"", -1) as D_sip_min_A_amount') // D sip min
                ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",36), "\"", -1) as W_sip_min_F_amount') // W sip min
                ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",40), "\"", -1) as W_sip_min_A_amount') // W sip min
                ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",56), "\"", -1) as F_sip_min_F_amount') // F sip min
                ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",60), "\"", -1) as F_sip_min_A_amount') // F sip min
                ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",76), "\"", -1) as M_sip_min_F_amount') // M sip min
                ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",80), "\"", -1) as M_sip_min_A_amount') // M sip min
                ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",96), "\"", -1) as Q_sip_min_F_amount') // Q sip min
                ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",100), "\"", -1) as Q_sip_min_A_amount') // Q sip min
                ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",116), "\"", -1) as S_sip_min_F_amount') // S sip min
                ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",120), "\"", -1) as S_sip_min_A_amount') // S sip min
                ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",136), "\"", -1) as A_sip_min_F_amount') // A sip min
                ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",140), "\"", -1) as A_sip_min_A_amount') // A sip min
                
                ->selectRaw('SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",16) as swp_amount_filter_1')

                ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",16), "\"", -1) as D_swp_min_amount') // D swp min
                ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",32), "\"", -1) as W_swp_min_amount') // W swp min
                ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",48), "\"", -1) as F_swp_min_amount') // F swp min
                ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",64), "\"", -1) as M_swp_min_amount') // M swp min
                ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",80), "\"", -1) as Q_swp_min_amount') // Q swp min
                ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",96), "\"", -1) as S_swp_min_amount') // S swp min
                ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",112), "\"", -1) as A_swp_min_amount') // A swp min
                
                ->selectRaw('SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",16) as stp_amount_filter_2')

                ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",16), "\"", -1) as D_stp_min_amount') // D stp min
                ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",32), "\"", -1) as W_stp_min_amount') // W stp min
                ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",48), "\"", -1) as F_stp_min_amount') // F stp min
                ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",64), "\"", -1) as M_stp_min_amount') // M stp min
                ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",80), "\"", -1) as Q_stp_min_amount') // Q stp min
                ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",96), "\"", -1) as S_stp_min_amount') // S stp min
                ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",112), "\"", -1) as A_stp_min_amount') // A stp min
                
                ->selectRaw('cast(SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",98), "\"", -1)AS UNSIGNED) as A_stp_min_amount_11') // A stp min
                // order by cast(SUBSTRING_INDEX(replace(book_page_name,'.pdf',''),'_',-1)AS UNSIGNED)");

                ->where('md_scheme.delete_flag','N')
                ->where('md_scheme.scheme_type',$scheme_type)
                ->orderBy('md_scheme.updated_at','desc')
                // ->whereRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",56), "\"", -1) = "500"')
                ->take(10)
                ->get(); 
        // DB::getQueryLog();
        // dd(DB::getQueryLog());
        // dd($data);

        return $data;
    }

    public function test(){

        // $master_arr = array('CAT','DOG','RABBIT');
        // $log_arr = array('CAT','CAR','RABBIT','DOG','PHONE');
        // $unique=array_unique( array_merge($master_arr, $log_arr) );
        // $master_arr=array_diff($unique, $master_arr);
        // // print_r($master_arr);
        // return $master_arr;

        // $A = array(1,2,3,4,5,6,7,8);
        // $B = array(1,2,3,4);

        // $C = array_intersect($A,$B);  //equals (1,2,3,4)
        // $A = array_diff($A,$B);   
        // return $A;
        // return 'hii';
        DB::enableQueryLog();
        $data=SipStpTransaction::where('rnt_id',1)
        ->where(function ($query) {
            $query->where('from_date', '>=', 'cease_terminate_date')
                //   ->where('from_date', '>', date('Y-m-d'));
                  ->orWhere('from_date', '>', date('Y-m-d'));
        })->get();
        // return $data;

        // $euin_no=MutualFundTransaction::select('euin_no')->where('folio_no','3101929013')->where('product_code','178ARRG')
        // ->orderBy('trans_date','ASC')
        //     // ->get();
        //     ->first();
                        // $euin_no=MutualFundTransaction::where('folio_no',6017105704)
                        // ->where('euin_no','!=','')->first(['euin_no'])->value('euin_no');

        dd(DB::getQueryLog());
        // return $euin_no;
    }



    public function xirr__()
    {
        $cf=[];
        $cf[0]= -4999.75;
        $cf[1]= -4999.75;
        $cf[2]= 10246.82;
        $cf[3]= -4999.75;
        $cf[4]= -4999.75;
        $cf[5]= -4999.75;
        $cf[6]= -4999.75;
        $cf[7]= -4999.75;
        $cf[8]= -4999.75;
        $cf[9]= -4999.75;
        $cf[10]= -4999.75;
        $cf[11]= -4999.75;
        $cf[12]= -4999.75;
        $cf[13]= -4999.75;
        $cf[14]= -4999.75;
        $cf[15]= -4999.75;
        $cf[16]= 65905.17;
        $cf[17]= -4999.75;
        $cf[18]= -4999.75;
        $cf[19]= -4999.75;
        $cf[20]= 14672.61;
        $cf[21]= -4999.75;
        $cf[22]= -4999.75;
        $cf[23]= -4999.75;
        $cf[24]= -4999.75;
        $cf[25]= -4999.75;
        $cf[26]= -4999.75;
        $cf[27]= -4999.75;
        $cf[28]= -4999.75;
        $cf[29]= -4999.75;
        $cf[30]= -4999.75;
        $cf[31]= 20934.67;
        $cf[32]= -4999.75;
        $cf[33]= 4846.07;
        $cf[34]= -4999.75;
        $cf[35]= 4884;
        $cf[36]= -4999.75;
        $cf[37]= -4999.75;
        $cf[38]= 52307;

        // return $cf;
        $numOfFlows = 39;
        $xirr=$this->computeIRR($cf, $numOfFlows);
        return $xirr;
        return 'hii';
    }
    
    public function xirr()
    {
        // $cf=[];
        // $cf[]= -5000;
        // $cf[]= -5000;
        // $cf[]= -5000;
        // $cf[]= -5000;
        // $cf[]= -5000;
        // $cf[]= -5000;
        // $cf[]= 31000;
        // // return $cf;
        // $numOfFlows = 7;
        // $xirr=$this->computeIRR($cf, $numOfFlows);
        // return $xirr ;
        // return 'hii';
        return view('index');
    }
    public function computeIRR($cf, $numOfFlows)
    {
        $LOW_RATE=0.01;
        $HIGH_RATE=0.01;
        $MAX_ITERATION=1000;
        $PRECISION_REQ=0.00000001;
        // $LOW_RATE=0;
        // $HIGH_RATE=0;
        // $MAX_ITERATION=1000;
        // $PRECISION_REQ=0;
        $i = 0;
        $j = 0;
        $m = 0.0;
        $old = 0.00;
        $new = 0.00;
        $oldguessRate = $LOW_RATE;
        $newguessRate = $LOW_RATE;
        $guessRate = $LOW_RATE;
        $lowGuessRate = $LOW_RATE;
        $highGuessRate = $HIGH_RATE;
        $npv = 0.0;
        $denom = 0.0;
        for($i=0; $i<$MAX_ITERATION; $i++){
            $npv = 0.00;
            for($j=0; $j<$numOfFlows; $j++){
                $denom = pow((1 + $guessRate),$j);
                $npv = $npv + ($cf[$j]/$denom);
            }
            /* Stop checking once the required precision is achieved */
            if(($npv > 0) && ($npv < $PRECISION_REQ))
            break;
            if($old == 0){
                $old = $npv;
            }else{
                $old = $new;
                $new = $npv;
            }
            if($i > 0){
                if($old < $new){
                    if($old < 0 && $new < 0){
                        $highGuessRate = $newguessRate;
                    }else{
                        $lowGuessRate = $newguessRate;
                    }
                }else{
                    if($old > 0 && $new > 0){
                        $lowGuessRate = $newguessRate;
                    }else{
                        $highGuessRate = $newguessRate;
                    }
                }
            }
            $oldguessRate = $guessRate;
            $guessRate = ($lowGuessRate + $highGuessRate) / 2;
            $newguessRate = $guessRate;
        }
        return $guessRate;
    }





    public function testing(Request $request)
    {
        // return $request;
        $fin_year='2023-2024';
        // $fin_year='2022-2023';
        // return explode('-',$fin_year);
        $start_date=explode('-',$fin_year)[0]."-04-01";
        $lastday = date('t',strtotime(explode('-',$fin_year)[1]."-03-01"));
        $end_date = explode('-',$fin_year)[1]."-03-".$lastday;

        $today=date('Y-m-d');
        $end_date = (strtotime($today) >= strtotime($end_date)) ? explode('-',$fin_year)[1]."-03-".$lastday : date('Y-m-d');

        // $today='2024-03-31';
        // return $start_date."  -  ".$end_date ;
        $rawQuery='';
        $categories=[];
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
                // return $myset_data;
            
        }
        return $table_data;

        // while(strtotime($end_date) >= strtotime($start_date))
        // {
        //     echo $end_date= date("Y-m",strtotime("-1 month",strtotime($end_date)));
        //     echo "\n\n---";
        // }

        // $loop_dates='2024-01-01';
        // $last_day_this_month='2024-02-26';
        // $data=[];
        // for($i = 0; $i <= date('m',strtotime($end_date)); $i++)
        // {
        //     // echo $i;
        //     // echo '<br>';
        //     array_push($data,$i);
        // }
        // return $data;
        
    }


    function dateRange( $first, $last, $step = '+1 day', $format = 'Y-m-d' ) {
        $dates = [];
        $current = strtotime( $first );
        $last = strtotime( $last );
    
        while( $current <= $last ) {
    
            $dates[] = date( $format, $current );
            $current = strtotime( $step, $current );
        }
    
        return $dates;
    }



    public function ttttt()
    {
        // ALTER TABLE td_nav_details_part_yearly PARTITION BY RANGE (YEAR(`nav_date`)) (
        //     PARTITION p_1999 VALUES LESS THAN (1999),
        //     PARTITION p_2000 VALUES LESS THAN (2000),
        //     PARTITION p_2001 VALUES LESS THAN (2001),
        //     PARTITION p_2002 VALUES LESS THAN (2002),
        //     PARTITION p_2003 VALUES LESS THAN (2003),
        //     PARTITION p_2004 VALUES LESS THAN (2004),
        //     PARTITION p_2005 VALUES LESS THAN (2005),
        //     PARTITION p_2006 VALUES LESS THAN (2006),
        //     PARTITION p_2007 VALUES LESS THAN (2007),
        //     PARTITION p_2008 VALUES LESS THAN (2008),
        //     PARTITION p_2009 VALUES LESS THAN (2009),
        //     PARTITION p_2010 VALUES LESS THAN (2010),
        //     PARTITION p_2011 VALUES LESS THAN (2011),
        //     PARTITION p_2012 VALUES LESS THAN (2012),
        //     PARTITION p_2013 VALUES LESS THAN (2013),
        //     PARTITION p_2014 VALUES LESS THAN (2014),
        //     PARTITION p_2015 VALUES LESS THAN (2015),
        //     PARTITION p_2016 VALUES LESS THAN (2016),
        //     PARTITION p_2017 VALUES LESS THAN (2017),
        //     PARTITION p_2018 VALUES LESS THAN (2018),
        //     PARTITION p_2019 VALUES LESS THAN (2019),
        //     PARTITION p_2020 VALUES LESS THAN (2020),
        //     PARTITION p_2021 VALUES LESS THAN (2021),
        //     PARTITION p_2022 VALUES LESS THAN (2022),
        //     PARTITION p_2023 VALUES LESS THAN (2023),
        //     PARTITION p_2024 VALUES LESS THAN (2024),
        //     PARTITION p_2025 VALUES LESS THAN (2025),
        //     PARTITION p_2026 VALUES LESS THAN (2026)
        // );
    }
}