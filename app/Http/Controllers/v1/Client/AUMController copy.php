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
    MFTransTypeSubType,
    NAVDetailsSec,
    BrokerChangeTransReport
};
use Validator;
use Illuminate\Support\Carbon;
use Excel;
use App\Helpers\TransHelper;
use DB;
use Session;

class AUMController extends Controller
{
    public function search(Request $request)
    {
        try {
            // return $request;
            $date=$request->date;
            $arn_no=$request->arn_no;
            // $date='2024-05-31';
            if ($date || $arn_no) {
                $rawQuery='';
                if ($date) {
                    $condition_v=(strlen($rawQuery) > 0)? " AND ":" ";
                    $queryString='td_mutual_fund_trans.trans_date';
                    $rawQuery.=$condition_v.$queryString."<= '".$date."'";
                }
            } 
            session()->forget('date');
            session(['date' => $date]);
            // return $rawQuery;
            // DB::enableQueryLog();

            $all_data=MutualFundTransaction::
                with(['schemes' =>  function ($query) {
                    $query->select('rnt_id','amc_code','product_code','isin_no');
                },'schemes.transdetails'=>function ($query) {
                    $query->select('rnt_id','amc_code','folio_no','product_code','isin_no','trans_date','trxn_type','trxn_type_flag','trxn_nature','amount','stamp_duty','tds','units','pur_price')
                    // ->selectRaw('IF(rnt_id=1,
                    // (SELECT trans_type FROM md_mf_trans_type_subtype WHERE c_trans_type_code=trxn_type_code AND c_k_trans_type=trxn_type_flag AND c_k_trans_sub_type=trxn_nature_code limit 1),
                    // (CASE 
                    //     WHEN trans_flag="DP" || trans_flag="DR" THEN (SELECT trans_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=kf_trans_type AND k_divident_flag=trans_flag limit 1)
                    //     WHEN trans_flag="TO" THEN "Transfer Out"
                    //     ELSE (SELECT trans_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=kf_trans_type limit 1)
                    // END)
                    // )as transaction_type')
                    // ->selectRaw('IF(rnt_id=1,
                    // (SELECT trans_sub_type FROM md_mf_trans_type_subtype WHERE c_trans_type_code=trxn_type_code AND c_k_trans_type=trxn_type_flag AND c_k_trans_sub_type=trxn_nature_code limit 1),
                    // (CASE 
                    //     WHEN trans_flag="DP" || trans_flag="DR" THEN (SELECT trans_sub_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=kf_trans_type AND k_divident_flag=trans_flag limit 1)
                    //     WHEN trans_flag="TO" THEN "Transfer Out"
                    //     ELSE (SELECT trans_sub_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=kf_trans_type limit 1)
                    // END)
                    // )as transaction_subtype')
                    ->selectRaw('sum(units) as tot_units')
                    ->selectRaw('sum(amount) as tot_amount')
                    ->selectRaw('sum(stamp_duty) as tot_stamp_duty')
                    ->selectRaw('IF(tds!="",sum(tds),0.00)as tot_tds');
                }])
                // with(['transdetails' => function ($query) {
                //     $query->select('rnt_id','amc_code','folio_no','product_code','isin_no','trans_date','trxn_type','trxn_type_flag','trxn_nature','amount','stamp_duty','tds','units','pur_price')
                //         ->selectRaw('IF(rnt_id=1,
                //         (SELECT trans_type FROM md_mf_trans_type_subtype WHERE c_trans_type_code=trxn_type_code AND c_k_trans_type=trxn_type_flag AND c_k_trans_sub_type=trxn_nature_code limit 1),
                //         (CASE 
                //             WHEN trans_flag="DP" || trans_flag="DR" THEN (SELECT trans_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=kf_trans_type AND k_divident_flag=trans_flag limit 1)
                //             WHEN trans_flag="TO" THEN "Transfer Out"
                //             ELSE (SELECT trans_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=kf_trans_type limit 1)
                //         END)
                //         )as transaction_type')
                //         ->selectRaw('IF(rnt_id=1,
                //         (SELECT trans_sub_type FROM md_mf_trans_type_subtype WHERE c_trans_type_code=trxn_type_code AND c_k_trans_type=trxn_type_flag AND c_k_trans_sub_type=trxn_nature_code limit 1),
                //         (CASE 
                //             WHEN trans_flag="DP" || trans_flag="DR" THEN (SELECT trans_sub_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=kf_trans_type AND k_divident_flag=trans_flag limit 1)
                //             WHEN trans_flag="TO" THEN "Transfer Out"
                //             ELSE (SELECT trans_sub_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=kf_trans_type limit 1)
                //         END)
                //         )as transaction_subtype')
                //         ->selectRaw('sum(units) as tot_units')
                //         ->selectRaw('sum(amount) as tot_amount')
                //         ->selectRaw('sum(stamp_duty) as tot_stamp_duty')
                //         ->selectRaw('IF(tds!="",sum(tds),0.00)as tot_tds')
                //         ->selectRaw('count(*) as tot_rows')
                //         ->selectRaw('(select close from td_benchmark_scheme where benchmark=1 AND date=trans_date) as nifty50')
                //         ->selectRaw('(select close from td_benchmark_scheme where benchmark=70 AND date=trans_date) as sensex');
                // }])
                ->leftJoin('md_amc','md_amc.amc_code','=','td_mutual_fund_trans.amc_code')
                ->select('td_mutual_fund_trans.rnt_id','td_mutual_fund_trans.amc_code','md_amc.amc_short_name as amc_name')
                ->where('td_mutual_fund_trans.delete_flag','N')
                ->where('td_mutual_fund_trans.amc_flag','N')
                ->where('td_mutual_fund_trans.scheme_flag','N')
                ->where('td_mutual_fund_trans.plan_option_flag','N')
                ->where('td_mutual_fund_trans.bu_type_flag','N')
                ->where('td_mutual_fund_trans.divi_mismatch_flag','N')
                ->whereRaw($rawQuery)
                ->groupBy('td_mutual_fund_trans.amc_code')
                ->orderBy('td_mutual_fund_trans.trans_date','ASC')
                ->get();
            // dd(DB::getQueryLog());
            // return $all_data[0];
            // $data=[];
            // foreach ($all_data as $key_datas => $datas) {
            //     // return $datas;
            //     $transdetails=$datas->transdetails;
            //     // return $transdetails;
            //     $all_process_data=[];
            //     $all_rejection_data=[];
            //     foreach ($transdetails as $key1 => $value1) {
            //         if ($value1->rnt_id==1 && $value1->transaction_type=="Transfer In" && $value1->transaction_subtype=="Transfer In") {
            //             $broker_data=$this->getBrokerData($value1);
            //             if (count($broker_data)> 0) {
            //                 foreach ($broker_data as $key => $broker_data_value) {
            //                     if ($broker_data_value->amount < 0) {
            //                         $broker_data_value->transaction_type=$value1->transaction_type." Rejection";
            //                         $broker_data_value->transaction_subtype=$value1->transaction_subtype." Rejection";
            //                     }
            //                     if( strpos($broker_data_value->transaction_subtype, 'Rejection' ) == false) {
            //                         array_push($all_process_data,$broker_data_value);
            //                     } else {
            //                         array_push($all_rejection_data,$value1);
            //                     }
            //                 }
            //             }else {
            //                 array_push($all_process_data,$value1);
            //             }
            //         }else {
            //             if ($value1->rnt_id==1 && $value1->amount < 0) {
            //                 $value1->transaction_type=$value1->transaction_type." Rejection";
            //                 $value1->transaction_subtype=$value1->transaction_subtype." Rejection";
            //             }
            //             if( strpos($value1->transaction_subtype, 'Rejection' ) == false) {
            //                 array_push($all_process_data,$value1);
            //             } else {
            //                 array_push($all_rejection_data,$value1);
            //             }
            //         }
            //     }
            //     // return $all_process_data;
            //     // return $all_rejection_data;
            //     // **************************Start Rejection Amount Delete*************************************
            //     foreach ($all_rejection_data as $key_0001 => $value_0001) {
            //         $amount=str_replace("-","",$value_0001->amount) ;
            //         $trans_date=$value_0001->trans_date;
            //         $get_final_success_data=[];
            //         foreach ($all_process_data as $key_002 => $value_002) {
            //             if ($value_002->trans_date==$trans_date && $value_002->amount==$amount) {
            //                 $amount=0;
            //             }else {
            //                 array_push($get_final_success_data,$value_002);
            //             }
            //         }
            //         $all_process_data=$get_final_success_data;
            //     }
            //     // **************************End Rejection Amount Delete*************************************
            //     // return $all_process_data;
            //     $purchase_data=[];
            //     $redemption_data=[];
            //     foreach ($all_process_data as $key => $value) {
            //         if(strpos($value->transaction_subtype, 'Purchase' )!== false || strpos($value->transaction_subtype, 'Switch In' )!== false 
            //             || strpos($value->transaction_subtype, 'Dividend Reinvestment')!== false || strpos($value->transaction_subtype, 'STP In')!== false) {
            //             if ($key > 0) {
            //                 $value->cumml_units=number_format((float)($value->tot_units + $all_process_data[($key-1)]->cumml_units) , 4, '.', '') ;
            //             }else {
            //                 $value->cumml_units=$value->tot_units;
            //             }
            //             array_push($purchase_data,$value);
            //         } else if (strpos($value->transaction_subtype, 'Redemption' )!== false || strpos($value->transaction_subtype, 'Switch Out' )!== false 
            //             || strpos($value->transaction_subtype, 'Transfer Out')!== false || strpos($value->transaction_subtype, 'SWP')!== false
            //             || strpos($value->transaction_subtype, 'STP Out')!== false) {
            //             $value->cumml_units=0;
            //             array_push($redemption_data,$value);
            //         }
            //     }
            //     /*********************for pledging condition*************************************/
            //     $purchase_data_recheck=[];
            //     foreach ($purchase_data as $key_001 => $value_001) {
            //         if ($key_001 > 0) {
            //             $value_001->cumml_units=number_format((float)($value_001->tot_units + $purchase_data[($key_001-1)]->cumml_units) , 4, '.', '');
            //         }else {
            //             $value_001->cumml_units=number_format((float)$value_001->tot_units, 4, '.', '');
            //         }
            //         array_push($purchase_data_recheck,$value_001);
            //     }
            //     $purchase_data=$purchase_data_recheck;
            //     /*********************for pledging condition*****************************************/
            //     /***************************************Search period calculation****************************************************/
            //     $search_period=[];
            //     $after_search_period=[];
            //     foreach ($redemption_data as $key_rd => $value_rd) {
            //         if ($value_rd->trans_date >= $start_date) {
            //             if ($value_rd->trans_date >= $start_date && $value_rd->trans_date <= $end_date) {
            //                 array_push($search_period,$value_rd);
            //             }
            //         }else {
            //             array_push($after_search_period,$value_rd);
            //         }
            //     }
            //     $datas->search_period=$search_period;
            //     /***************************************Search period calculation****************************************************/
            //     // return $search_period;
            //     if (count($search_period) > 0) {
            //         // return $search_period;
            //         /********************************************************************** */
            //         if (count($after_search_period) > 0) {
            //             foreach ($after_search_period as $redemption_key => $redemption_value) {
            //                 $rdm_tot_units=number_format((float)$redemption_value->tot_units, 4, '.', '');
            //                 $deduct_unit_array=[];
            //                 $flag='Y';
            //                 foreach ($purchase_data as $purchase_key => $purchase_value) {
            //                     if ($purchase_value['cumml_units'] >= 0) {
            //                         $purchase_cumml_units=number_format((float)$purchase_value['cumml_units'], 4, '.', '');
            //                         $purchase_value['cumml_units']=number_format((float)($purchase_cumml_units - $rdm_tot_units), 4, '.', '');
            //                         if ($purchase_value['cumml_units'] >= 0 ) {
            //                             $calculation_cumml_unit=isset($purchase_data[($purchase_key - 1)]['cumml_units'])?$purchase_data[($purchase_key - 1)]['cumml_units']:0;
            //                             if ($calculation_cumml_unit < 0) {
            //                                 $set_units=$purchase_value['cumml_units'];
            //                                 $purchase_value['cumml_units']=0;
            //                                 array_push($deduct_unit_array,$purchase_value);
            //                                 $rdm_tot_units=0;
            //                                 $newarr=[];
            //                                 $newarr['id']=$purchase_value['id'];
            //                                 $newarr['trans_date']=$purchase_value['trans_date'];
            //                                 $newarr['pur_price']=$purchase_value['pur_price'];
            //                                 $newarr['sensex']=$purchase_value['sensex'];
            //                                 $newarr['nifty50']=$purchase_value['nifty50'];
            //                                 $newarr['curr_nav']=$purchase_value['curr_nav'];
            //                                 $newarr['days']=$purchase_value['days'];
            //                                 $newarr['trans_mode']=$purchase_value['trans_mode'];
            //                                 $newarr['transaction_type']=$purchase_value['transaction_type'];
            //                                 $newarr['transaction_subtype']=$purchase_value['transaction_subtype'];
            //                                 $newarr['tot_units']=$set_units;
            //                                 $newarr['cumml_units']=$set_units;
            //                                 $newarr['tot_amount']= number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
            //                                 $newarr['tot_gross_amount']=number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
            //                                 $newarr['gross_amount']=number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
            //                                 $newarr['curr_val']=number_format((float)($set_units * $purchase_value['curr_nav']), 2, '.', '');
            //                                 array_push($deduct_unit_array,$newarr);
            //                                 $flag='N';
            //                             }else {
            //                                 if ($flag=='Y') {
            //                                     $set_units=$purchase_value['cumml_units'];
            //                                     $purchase_value['cumml_units']=0;
            //                                     array_push($deduct_unit_array,$purchase_value);
            //                                     $rdm_tot_units=0;
            //                                     $newarr=[];
            //                                     $newarr['id']=$purchase_value['id'];
            //                                     $newarr['trans_date']=$purchase_value['trans_date'];
            //                                     $newarr['pur_price']=$purchase_value['pur_price'];
            //                                     $newarr['sensex']=$purchase_value['sensex'];
            //                                     $newarr['nifty50']=$purchase_value['nifty50'];
            //                                     $newarr['curr_nav']=$purchase_value['curr_nav'];
            //                                     $newarr['days']=$purchase_value['days'];
            //                                     $newarr['trans_mode']=$purchase_value['trans_mode'];
            //                                     $newarr['transaction_type']=$purchase_value['transaction_type'];
            //                                     $newarr['transaction_subtype']=$purchase_value['transaction_subtype'];
            //                                     $newarr['tot_units']=$set_units;
            //                                     $newarr['cumml_units']=$set_units;
            //                                     $newarr['tot_amount']= number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
            //                                     $newarr['tot_gross_amount']=number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
            //                                     $newarr['gross_amount']=number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
            //                                     $newarr['curr_val']=number_format((float)($set_units * $purchase_value['curr_nav']), 2, '.', '');
            //                                     array_push($deduct_unit_array,$newarr);
            //                                     $flag='N';
            //                                 }else{
            //                                     $purchase_value['cumml_units']=number_format((float)$purchase_value['tot_units'], 4, '.', '') + number_format((float)$deduct_unit_array[(count($deduct_unit_array)-1)]['cumml_units'], 4, '.', '') ;
            //                                     $purchase_value['curr_val']=number_format((float)($purchase_value['tot_units'] * $purchase_value['curr_nav']), 2, '.', '');
            //                                     array_push($deduct_unit_array,$purchase_value);
            //                                 }
            //                             }
            //                         }else {
            //                             $purchase_value['curr_val']=number_format((float)($purchase_value['tot_units'] * $purchase_value['curr_nav']), 2, '.', '');
            //                             array_push($deduct_unit_array,$purchase_value);
            //                             // return $deduct_unit_array;
            //                         }
            //                     }else {
            //                         $purchase_value['curr_val']=number_format((float)($purchase_value['tot_units'] * $purchase_value['curr_nav']), 2, '.', '');
            //                         array_push($deduct_unit_array,$purchase_value);
            //                     }
            //                 }
            //                 // return  $deduct_unit_array;
            //                 $purchase_data=$deduct_unit_array;
            //             }
            //             // return $purchase_data;
            //             $final_arr=[];
            //             foreach ($purchase_data as $key_0 => $value_0) {
            //                 if ($value_0['cumml_units'] > 0) {
            //                     array_push($final_arr,$value_0);
            //                 }
            //             }
            //             $purchase_data=$final_arr;
            //         }
            //         /********************************************************************** */
            //         // return $purchase_data;
            //         /********************************************************************** */
            //         // return $search_period;
            //         foreach ($search_period as $redemption_key1 => $redemption_value1) {
            //             // return $redemption_value1;
            //             $rdm_tot_units=number_format((float)$redemption_value1->tot_units, 4, '.', '');
            //             $sell_date=$redemption_value1->trans_date;
            //             $sell_transaction_type=$redemption_value1->transaction_type;
            //             $sell_transaction_subtype=$redemption_value1->transaction_subtype;
            //             $sell_nav=$redemption_value1->pur_price;
                        
            //             $deduct_unit_array=[];
            //             $flag='Y';
            //             foreach ($purchase_data as $purchase_key => $purchase_value) {
            //                 if ($purchase_value['cumml_units'] >= 0) {
            //                     $purchase_cumml_units=number_format((float)$purchase_value['cumml_units'], 4, '.', '');
            //                     $purchase_value['cumml_units']=number_format((float)($purchase_cumml_units - $rdm_tot_units), 4, '.', '');
            //                     if ($purchase_value['cumml_units'] >= 0 ) {
            //                         $calculation_cumml_unit=isset($purchase_data[($purchase_key - 1)]['cumml_units'])?$purchase_data[($purchase_key - 1)]['cumml_units']:0;
            //                         if ($calculation_cumml_unit < 0) {
            //                             $set_units=$purchase_value['cumml_units'];
            //                             $purchase_value['cumml_units']=0;
            //                             $purchase_value['remaining_units']=$set_units;
            //                             $purchase_value['sell_date']=$sell_date;
            //                             $purchase_value['sell_transaction_type']=$sell_transaction_type;
            //                             $purchase_value['sell_transaction_subtype']=$sell_transaction_subtype;
            //                             $purchase_value['sell_nav']=$sell_nav;
            //                             array_push($deduct_unit_array,$purchase_value);
            //                             $rdm_tot_units=0;
            //                             $newarr=[];
            //                             $newarr['id']=$purchase_value['id'];
            //                             $newarr['trans_date']=$purchase_value['trans_date'];
            //                             $newarr['pur_price']=$purchase_value['pur_price'];
            //                             $newarr['sensex']=$purchase_value['sensex'];
            //                             $newarr['nifty50']=$purchase_value['nifty50'];
            //                             $newarr['curr_nav']=$purchase_value['curr_nav'];
            //                             $newarr['days']=$purchase_value['days'];
            //                             $newarr['trans_mode']=$purchase_value['trans_mode'];
            //                             $newarr['transaction_type']=$purchase_value['transaction_type'];
            //                             $newarr['transaction_subtype']=$purchase_value['transaction_subtype'];
            //                             $newarr['tot_units']=$set_units;
            //                             $newarr['cumml_units']=$set_units;
            //                             $newarr['tot_amount']= number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
            //                             $newarr['tot_gross_amount']=number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
            //                             $newarr['gross_amount']=number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
            //                             $newarr['curr_val']=number_format((float)($set_units * $purchase_value['curr_nav']), 2, '.', '');
            //                             $newarr['sell_date']=$sell_date;
            //                             $newarr['sell_transaction_type']=$sell_transaction_type;
            //                             $newarr['sell_transaction_subtype']=$sell_transaction_subtype;
            //                             $newarr['sell_nav']=$sell_nav;
            //                             array_push($deduct_unit_array,$newarr);
            //                             $flag='N';
            //                         }else {
            //                             if ($flag=='Y') {
            //                                 $set_units=$purchase_value['cumml_units'];
            //                                 $purchase_value['cumml_units']=0;
            //                                 $purchase_value['remaining_units']=$set_units;
            //                                 $purchase_value['sell_date']=$sell_date;
            //                                 $purchase_value['sell_transaction_type']=$sell_transaction_type;
            //                                 $purchase_value['sell_transaction_subtype']=$sell_transaction_subtype;
            //                                 $purchase_value['sell_nav']=$sell_nav;
            //                                 array_push($deduct_unit_array,$purchase_value);
            //                                 $rdm_tot_units=0;
            //                                 $newarr=[];
            //                                 $newarr['id']=$purchase_value['id'];
            //                                 $newarr['trans_date']=$purchase_value['trans_date'];
            //                                 $newarr['pur_price']=$purchase_value['pur_price'];
            //                                 $newarr['sensex']=$purchase_value['sensex'];
            //                                 $newarr['nifty50']=$purchase_value['nifty50'];
            //                                 $newarr['curr_nav']=$purchase_value['curr_nav'];
            //                                 $newarr['days']=$purchase_value['days'];
            //                                 $newarr['trans_mode']=$purchase_value['trans_mode'];
            //                                 $newarr['transaction_type']=$purchase_value['transaction_type'];
            //                                 $newarr['transaction_subtype']=$purchase_value['transaction_subtype'];
            //                                 $newarr['tot_units']=$set_units;
            //                                 $newarr['cumml_units']=$set_units;
            //                                 $newarr['tot_amount']= number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
            //                                 $newarr['tot_gross_amount']=number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
            //                                 $newarr['gross_amount']=number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
            //                                 $newarr['curr_val']=number_format((float)($set_units * $purchase_value['curr_nav']), 2, '.', '');
            //                                 $newarr['sell_date']=$sell_date;
            //                                 $newarr['sell_transaction_type']=$sell_transaction_type;
            //                                 $newarr['sell_transaction_subtype']=$sell_transaction_subtype;
            //                                 $newarr['sell_nav']=$sell_nav;
            //                                 array_push($deduct_unit_array,$newarr);
            //                                 $flag='N';
            //                             }else{
            //                                 $purchase_value['cumml_units']=number_format((float)$purchase_value['tot_units'], 4, '.', '') + number_format((float)$deduct_unit_array[(count($deduct_unit_array)-1)]['cumml_units'], 4, '.', '') ;
            //                                 $purchase_value['curr_val']=number_format((float)($purchase_value['tot_units'] * $purchase_value['curr_nav']), 2, '.', '');
            //                                 $purchase_value['sell_date']=$sell_date;
            //                                 $purchase_value['sell_transaction_type']=$sell_transaction_type;
            //                                 $purchase_value['sell_transaction_subtype']=$sell_transaction_subtype;
            //                                 $purchase_value['sell_nav']=$sell_nav;
            //                                 array_push($deduct_unit_array,$purchase_value);
            //                             }
            //                         }
            //                     }else {
            //                         $purchase_value['curr_val']=number_format((float)($purchase_value['tot_units'] * $purchase_value['curr_nav']), 2, '.', '');
            //                         $purchase_value['sell_date']=$sell_date;
            //                         $purchase_value['sell_transaction_type']=$sell_transaction_type;
            //                         $purchase_value['sell_transaction_subtype']=$sell_transaction_subtype;
            //                         $purchase_value['sell_nav']=$sell_nav;
            //                         array_push($deduct_unit_array,$purchase_value);
            //                         // return $deduct_unit_array;
            //                     }
            //                 }else {
            //                     $purchase_value['curr_val']=number_format((float)($purchase_value['tot_units'] * $purchase_value['curr_nav']), 2, '.', '');
            //                     array_push($deduct_unit_array,$purchase_value);
            //                 }
            //             }
            //             // return  $deduct_unit_array;
            //             $purchase_data=$deduct_unit_array;
            //         }
            //         // return $purchase_data;
            //         $calculation_arr=[];
            //         foreach ($purchase_data as $key_007 => $value_007) {
            //             if ($value_007['cumml_units'] <= 0 && $value_007['tot_units'] > 0) {
            //                 array_push($calculation_arr,$value_007);
            //             }
            //         }
            //         /********************************************************************** */
            //         // return $calculation_arr;
            //         $datas->calculation_arr=$calculation_arr;
            //         array_push($data,$datas);
            //     }
            //     // $datas->for_checking="test_data";
            //     // array_push($data,$datas);
            // }
            // usort($data, function($a, $b) {
            //     return $a['scheme_name'] <=> $b['scheme_name'];
            // });
            // // return $data;
            // $all_trans_product=[];
            // foreach ($data as $key_009 => $value_009) {
            //     $f_trans_product="(nav_date=(SELECT MAX(nav_date) FROM td_nav_details WHERE product_code='".$value_009->product_code."' AND nav_date <='2018-01-31') AND product_code='".$value_009->product_code."')";
            //     array_push($all_trans_product,$f_trans_product);
            // }
            // $string_version_product_code = implode(',', $all_trans_product);
            // // return $string_version_product_code;
            // $res_array=[];
            // if (count($data)>0) {
            //     $res_array =DB::connection('mysql_nav')
            //     ->select('SELECT product_code,isin_no,DATE_FORMAT(nav_date, "%Y-%m-%d") as nav_date,nav FROM td_nav_details where '.str_replace(",","  OR  ",$string_version_product_code));
            // }
            // // return $res_array;
            // $data_001=[];
            // foreach ($data as $key_0010 => $value_0010) {
            //     // return $value_0010;
            //     $product_code=$value_0010->product_code;
            //     $new='';
            //     if (count($res_array) > 0) {
            //         foreach($res_array as $val_nav){
            //             if($val_nav->product_code==$product_code){
            //                 $new=$val_nav;
            //             }
            //         }
            //     }
            //     // return $new;
            //     $value_0010->new=$new;
            //     $calculation_arr=$value_0010->calculation_arr;
            //     $calculation_arr_1=[];
            //     if ($value_0010->tax_type=='Equity Fund') {
            //         foreach ($calculation_arr as $key_calculation_arr => $value_calculation_arr) {
            //             // return $value_calculation_arr;
            //             $equity_pur_before_31_01_2018=($value_calculation_arr['trans_date'] < '2018-01-31' )?'Yes':'No';
            //             $value_calculation_arr['pur_before']=$equity_pur_before_31_01_2018;
                        
            //             $value_calculation_arr['nav_as_on_31_01_2018']=$new->nav;
            //             if ($equity_pur_before_31_01_2018=="Yes") {
            //                 $value_calculation_arr['amount_as_on_31_01_2018']=number_format((float)($value_calculation_arr['nav_as_on_31_01_2018'] * $value_calculation_arr['tot_units']), 2, '.', '');
            //                 $value_calculation_arr['debt_31_03_2023']="";
            //             }else {
            //                 $value_calculation_arr['amount_as_on_31_01_2018']="";
            //                 $value_calculation_arr['debt_31_03_2023']="";
            //             }
            //             $value_calculation_arr['sell_type']=$value_calculation_arr['sell_transaction_type'];
            //             if ($value_calculation_arr['cumml_units'] <= 0) {
            //                 $value_calculation_arr['redemp_amount']=number_format((float)($value_calculation_arr['sell_nav'] * $value_calculation_arr['tot_units']), 2, '.', '');
            //             }else {
            //                 $value_calculation_arr['redemp_amount']=0;
            //             }
            //             $value_calculation_arr['tot_tds']=0;
            //             $value_calculation_arr['stt']=0;
            //             $value_calculation_arr['net_sell_proceed']=$value_calculation_arr['redemp_amount'] -$value_calculation_arr['tot_tds'] - $value_calculation_arr['stt'];
            //             $value_calculation_arr['div_amount']=0;
            //             $now = strtotime($value_calculation_arr['sell_date']); // or your date as well
            //             $your_date = strtotime(date('Y-m-d',strtotime($value_calculation_arr['trans_date'])));
            //             $datediff = $now - $your_date;
            //             $days=round($datediff / (60 * 60 * 24));
            //             $value_calculation_arr['days']=$days;
            //             $value_calculation_arr['ltcg']="";
            //             $value_calculation_arr['stcg']="";
            //             if ($days > (365 -1)) {
            //                 $value_calculation_arr['ltcg']=number_format((float)(($value_calculation_arr['sell_nav'] - $value_calculation_arr['pur_price']) * $value_calculation_arr['tot_units']), 2, '.', '');
            //             }else {
            //                 $value_calculation_arr['stcg']=number_format((float)(($value_calculation_arr['sell_nav'] - $value_calculation_arr['pur_price']) * $value_calculation_arr['tot_units']), 2, '.', '');
            //             }
            //             if ($value_calculation_arr['pur_price'] > $value_calculation_arr['nav_as_on_31_01_2018']) {
            //                 $value_calculation_arr['index_ltcg']=number_format((float)(($value_calculation_arr['sell_nav'] -$value_calculation_arr['pur_price']) * $value_calculation_arr['tot_units']), 2, '.', '');
            //             }else {
            //                 $value_calculation_arr['index_ltcg']=number_format((float)(($value_calculation_arr['sell_nav'] -$value_calculation_arr['nav_as_on_31_01_2018']) * $value_calculation_arr['tot_units']), 2, '.', '');
            //             }
            //             array_push($calculation_arr_1,$value_calculation_arr);
            //         }
            //     }else if ($value_0010->tax_type=="Debt Fund") {
            //         foreach ($calculation_arr as $key_calculation_arr => $value_calculation_arr) {
            //             // return $value_calculation_arr;
            //             $debt_31_03_2023=($value_calculation_arr['trans_date'] < '2023-03-31' )?'Yes':'No';
            //             $value_calculation_arr['pur_before']="";
            //             $value_calculation_arr['nav_as_on_31_01_2018']="";
            //             $value_calculation_arr['amount_as_on_31_01_2018']="";
            //             $value_calculation_arr['debt_31_03_2023']=$debt_31_03_2023;

                        
            //             $value_calculation_arr['sell_type']=$value_calculation_arr['sell_transaction_type'];
            //             if ($value_calculation_arr['cumml_units'] <= 0) {
            //                 $value_calculation_arr['redemp_amount']=number_format((float)($value_calculation_arr['sell_nav'] * $value_calculation_arr['tot_units']), 2, '.', '');
            //             }else {
            //                 $value_calculation_arr['redemp_amount']=0;
            //             }
            //             $value_calculation_arr['tot_tds']=0;
            //             $value_calculation_arr['stt']=0;
            //             $value_calculation_arr['net_sell_proceed']=$value_calculation_arr['redemp_amount'] -$value_calculation_arr['tot_tds'] - $value_calculation_arr['stt'];
            //             $value_calculation_arr['div_amount']=0;
            //             $now = strtotime($value_calculation_arr['sell_date']); // or your date as well
            //             $your_date = strtotime(date('Y-m-d',strtotime($value_calculation_arr['trans_date'])));
            //             $datediff = $now - $your_date;
            //             $days=round($datediff / (60 * 60 * 24));
            //             $value_calculation_arr['days']=$days;
                        
            //             if ($days > ((365 *3) -1)) {
            //                 $value_calculation_arr['ltcg']=number_format((float)(($value_calculation_arr['sell_nav'] - $value_calculation_arr['pur_price']) * $value_calculation_arr['tot_units']), 2, '.', '');
            //                 $value_calculation_arr['stcg']="";
            //                 if ($value_calculation_arr['pur_price'] > $value_calculation_arr['nav_as_on_31_01_2018']) {
            //                     $value_calculation_arr['index_ltcg']=number_format((float)(($value_calculation_arr['sell_nav'] -$value_calculation_arr['pur_price']) * $value_calculation_arr['tot_units']), 2, '.', '');
            //                 }else {
            //                     $value_calculation_arr['index_ltcg']=number_format((float)(($value_calculation_arr['sell_nav'] -$value_calculation_arr['nav_as_on_31_01_2018']) * $value_calculation_arr['tot_units']), 2, '.', '');
            //                 }
            //             }else {
            //                 $value_calculation_arr['ltcg']="";
            //                 $value_calculation_arr['stcg']=number_format((float)(($value_calculation_arr['sell_nav'] - $value_calculation_arr['pur_price']) * $value_calculation_arr['tot_units']), 2, '.', '');
            //                 $value_calculation_arr['index_ltcg']=0;
            //             }
            //             array_push($calculation_arr_1,$value_calculation_arr);
            //         }
            //     }
                
            //     $value_0010->calculation_arr=$calculation_arr_1;
            //     array_push($data_001,$value_0010);
            // }
            // return $all_data;
            // foreach ($all_data as $all_data_key => $all_data_value) {
            //     return $all_data_value;
            //     foreach ($all_data_value->schemes as $key => $single_scheme) {
            //         return $single_scheme->transdetails();
            //         # code...
            //     }
            //     # code...
            //     // return transdetails
            // }
            // $grouped_types=[];
            // foreach($filter_data as $type){
            //     $grouped_types[$type['first_client_name']][] = $type;
            // }
            // // return $grouped_types;
            // $filter_data=$grouped_types;
                
            
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($all_data);
    }
}