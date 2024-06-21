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

class CapitalGLController extends Controller
{
    public function search(Request $request)
    {
        try {
            // return $request;
            $asset_type=$request->asset_type;
            $client_name=$request->client_name;
            $date_range=$request->date_range;
            $date_type=$request->date_type;
            $family_members=$request->family_members;
            $fin_year=$request->fin_year;
            $opt1=$request->opt1;
            $opt2=$request->opt2;
            $pan_no=$request->pan_no;
            $report_type=$request->report_type;
            $trans_periods=$request->trans_periods;
            $view_type=$request->view_type;

            $client_details='';
            if ($view_type || $date_type) {
                $rawQuery='';
                // if ($valuation_as_on) {
                //     $condition_v=(strlen($rawQuery) > 0)? " AND ":" ";
                //     $queryString='td_mutual_fund_trans.trans_date';
                //     $rawQuery.=$condition_v.$queryString."<= '".$valuation_as_on."'";
                // }
                if ($view_type=='C') {
                    $client_rawQuery='';
                    if (!$pan_no) {
                        $queryString='td_mutual_fund_trans.first_client_name';
                        $rawQuery.=Helper::WhereRawQuery($client_name,$rawQuery,$queryString);
                        $client_queryString='md_client.client_name';
                        $client_rawQuery.=Helper::WhereRawQuery($client_name,$client_rawQuery,$client_queryString);
                    } else {
                        $queryString='td_mutual_fund_trans.first_client_pan';
                        $rawQuery.=Helper::WhereRawQuery($pan_no,$rawQuery,$queryString);
                        $client_queryString='md_client.pan';
                        $client_rawQuery.=Helper::WhereRawQuery($pan_no,$client_rawQuery,$client_queryString);
                    }
                    $client_details=TransHelper::getClientDetails($client_rawQuery);
                } else {
                    $queryString='td_mutual_fund_trans.first_client_pan';
                    $condition=(strlen($rawQuery) > 0)? " AND (":" (";
                    $row_name_string=  "'" .implode("','", $family_members_pan). "'";
                    $rawQuery.=$condition.$queryString." IN (".$row_name_string.")";
                    $queryString='td_mutual_fund_trans.first_client_name';
                    $condition1=(strlen($rawQuery) > 0)? " OR ":" ";
                    $row_name_string1=  "'" .implode("','", $family_members_name). "'";
                    $rawQuery.=$condition1.$queryString." IN (".$row_name_string1."))";
                }
                if ($date_type=='F') {
                    if ($fin_year) {
                        $start_date=explode('-',$fin_year)[0]."-04-01";
                        $end_date=date('Y-m-t',strtotime(explode('-',$fin_year)[1]."-03-01"));
                        // return $end_date;
                        // $queryString='td_mutual_fund_trans.trans_date';
                        // $condition=(strlen($rawQuery) > 0)? " AND ":" ";
                        // $rawQuery.=$condition.$queryString." >= '". date('Y-m-d',strtotime($start_date))."'";
                        // $rawQuery.=" AND ".$queryString." <= '". date('Y-m-d',strtotime($end_date))."'";
                        // $rawQuery.=Helper::FrmToDateRawQuery($start_date,$end_date,$rawQuery,$queryString);
                        // return $rawQuery;
                    }
                } else {
                    $start_date=Carbon::parse(str_replace('/','-',explode("-",$date_range)[0]))->format('Y-m-d') ;
                    $end_date=Carbon::parse(str_replace('/','-',explode("-",$date_range)[1]))->format('Y-m-d') ;
                    // return $end_date;
                }
            } 
            // session()->forget('start_date');
            // session()->forget('end_date');
            // session(['start_date' => $start_date]);
            // session(['end_date' => $end_date]);
            // return $rawQuery;
            // DB::enableQueryLog();
            $all_data=MutualFundTransaction::with('capitalgainloss')->leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_mutual_fund_trans.product_code')
                ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
                ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                ->leftJoin('md_amc','md_amc.amc_code','=','td_mutual_fund_trans.amc_code')
                ->leftJoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
                ->leftJoin('md_option','md_option.id','=','md_scheme_isin.option_id')
                ->leftJoin('md_tax_implication','md_tax_implication.id','=','md_scheme.tax_implication_id')
                ->select('td_mutual_fund_trans.rnt_id','td_mutual_fund_trans.folio_no','td_mutual_fund_trans.product_code','td_mutual_fund_trans.pur_price','td_mutual_fund_trans.trans_date','td_mutual_fund_trans.trans_mode',
                'md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name','md_amc.amc_short_name as amc_name',
                'md_plan.plan_name as plan_name','md_option.opt_name as option_name','md_tax_implication.tax_type')
                ->selectRaw('IF(td_mutual_fund_trans.rnt_id=1,md_scheme_isin.isin_no,td_mutual_fund_trans.isin_no) as isin_no')
                ->selectRaw('sum(td_mutual_fund_trans.units) as tot_units')
                ->selectRaw('sum(td_mutual_fund_trans.amount) as tot_amount')
                ->selectRaw('sum(td_mutual_fund_trans.stamp_duty) as tot_stamp_duty')
                ->selectRaw('sum(td_mutual_fund_trans.tds) as tot_tds')
                ->selectRaw('count(*) as tot_rows')
                // ->selectRaw('IF(td_mutual_fund_trans.rnt_id=1,
                // (SELECT trans_type FROM md_mf_trans_type_subtype WHERE c_trans_type_code=td_mutual_fund_trans.trxn_type_code AND c_k_trans_type=td_mutual_fund_trans.trxn_type_flag AND c_k_trans_sub_type=td_mutual_fund_trans.trxn_nature_code limit 1),
                // (CASE 
                //     WHEN td_mutual_fund_trans.trans_flag="DP" || td_mutual_fund_trans.trans_flag="DR" THEN (SELECT trans_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=td_mutual_fund_trans.kf_trans_type AND k_divident_flag=td_mutual_fund_trans.trans_flag limit 1)
                //     WHEN td_mutual_fund_trans.trans_flag="TO" THEN "Transfer Out"
                //     ELSE (SELECT trans_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=td_mutual_fund_trans.kf_trans_type limit 1)
                // END)
                // )as transaction_type')
                // ->selectRaw('IF(td_mutual_fund_trans.rnt_id=1,
                // (SELECT trans_sub_type FROM md_mf_trans_type_subtype WHERE c_trans_type_code=td_mutual_fund_trans.trxn_type_code AND c_k_trans_type=td_mutual_fund_trans.trxn_type_flag AND c_k_trans_sub_type=td_mutual_fund_trans.trxn_nature_code limit 1),
                // (CASE 
                //     WHEN td_mutual_fund_trans.trans_flag="DP" || td_mutual_fund_trans.trans_flag="DR" THEN (SELECT trans_sub_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=td_mutual_fund_trans.kf_trans_type AND k_divident_flag=td_mutual_fund_trans.trans_flag limit 1)
                //     WHEN td_mutual_fund_trans.trans_flag="TO" THEN "Transfer Out"
                //     ELSE (SELECT trans_sub_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=td_mutual_fund_trans.kf_trans_type limit 1)
                // END)
                // )as transaction_subtype')
                ->where('td_mutual_fund_trans.delete_flag','N')
                ->where('td_mutual_fund_trans.amc_flag','N')
                ->where('td_mutual_fund_trans.scheme_flag','N')
                ->where('td_mutual_fund_trans.plan_option_flag','N')
                ->where('td_mutual_fund_trans.bu_type_flag','N')
                ->where('td_mutual_fund_trans.divi_mismatch_flag','N')
                ->whereRaw($rawQuery)
                ->groupBy('td_mutual_fund_trans.folio_no')
                ->groupBy('td_mutual_fund_trans.product_code')
                ->groupBy('td_mutual_fund_trans.isin_no')
                ->orderBy('td_mutual_fund_trans.trans_date','ASC')
                ->get();
            // dd(DB::getQueryLog());
            // return $all_data;
            $data=[];
            foreach ($all_data as $key_datas => $datas) {
                // return $datas;
                $capitalgainloss=$datas->capitalgainloss;
                // return $capitalgainloss;
                $all_process_data=[];
                $all_rejection_data=[];
                foreach ($capitalgainloss as $key1 => $value1) {
                    if ($value1->rnt_id==1 && $value1->transaction_type=="Transfer In" && $value1->transaction_subtype=="Transfer In") {
                        $broker_data=$this->getBrokerData($value1);
                        if (count($broker_data)> 0) {
                            foreach ($broker_data as $key => $broker_data_value) {
                                if ($broker_data_value->amount < 0) {
                                    $broker_data_value->transaction_type=$value1->transaction_type." Rejection";
                                    $broker_data_value->transaction_subtype=$value1->transaction_subtype." Rejection";
                                }
                                if( strpos($broker_data_value->transaction_subtype, 'Rejection' ) == false) {
                                    array_push($all_process_data,$broker_data_value);
                                } else {
                                    array_push($all_rejection_data,$value1);
                                }
                            }
                        }else {
                            array_push($all_process_data,$value1);
                        }
                    }else {
                        if ($value1->rnt_id==1 && $value1->amount < 0) {
                            $value1->transaction_type=$value1->transaction_type." Rejection";
                            $value1->transaction_subtype=$value1->transaction_subtype." Rejection";
                        }
                        if( strpos($value1->transaction_subtype, 'Rejection' ) == false) {
                            array_push($all_process_data,$value1);
                        } else {
                            array_push($all_rejection_data,$value1);
                        }
                    }
                }
                // return $all_process_data;
                // return $all_rejection_data;
                // **************************Start Rejection Amount Delete*************************************
                foreach ($all_rejection_data as $key_0001 => $value_0001) {
                    $amount=str_replace("-","",$value_0001->amount) ;
                    $trans_date=$value_0001->trans_date;
                    $get_final_success_data=[];
                    foreach ($all_process_data as $key_002 => $value_002) {
                        if ($value_002->trans_date==$trans_date && $value_002->amount==$amount) {
                            $amount=0;
                        }else {
                            array_push($get_final_success_data,$value_002);
                        }
                    }
                    $all_process_data=$get_final_success_data;
                }
                // **************************End Rejection Amount Delete*************************************
                // return $all_process_data;
                $purchase_data=[];
                $redemption_data=[];
                foreach ($all_process_data as $key => $value) {
                    if(strpos($value->transaction_subtype, 'Purchase' )!== false || strpos($value->transaction_subtype, 'Switch In' )!== false 
                        || strpos($value->transaction_subtype, 'Dividend Reinvestment')!== false || strpos($value->transaction_subtype, 'STP In')!== false) {
                        if ($key > 0) {
                            $value->cumml_units=number_format((float)($value->tot_units + $all_process_data[($key-1)]->cumml_units) , 4, '.', '') ;
                        }else {
                            $value->cumml_units=$value->tot_units;
                        }
                        array_push($purchase_data,$value);
                    } else if (strpos($value->transaction_subtype, 'Redemption' )!== false || strpos($value->transaction_subtype, 'Switch Out' )!== false 
                        || strpos($value->transaction_subtype, 'Transfer Out')!== false || strpos($value->transaction_subtype, 'SWP')!== false
                        || strpos($value->transaction_subtype, 'STP Out')!== false) {
                        $value->cumml_units=0;
                        array_push($redemption_data,$value);
                    }
                }
                /*********************for pledging condition*************************************/
                $purchase_data_recheck=[];
                foreach ($purchase_data as $key_001 => $value_001) {
                    if ($key_001 > 0) {
                        $value_001->cumml_units=number_format((float)($value_001->tot_units + $purchase_data[($key_001-1)]->cumml_units) , 4, '.', '');
                    }else {
                        $value_001->cumml_units=number_format((float)$value_001->tot_units, 4, '.', '');
                    }
                    array_push($purchase_data_recheck,$value_001);
                }
                $purchase_data=$purchase_data_recheck;
                /*********************for pledging condition*****************************************/
                /***************************************Search period calculation****************************************************/
                $search_period=[];
                $after_search_period=[];
                foreach ($redemption_data as $key_rd => $value_rd) {
                    if ($value_rd->trans_date >= $start_date) {
                        if ($value_rd->trans_date >= $start_date && $value_rd->trans_date <= $end_date) {
                            array_push($search_period,$value_rd);
                        }
                    }else {
                        array_push($after_search_period,$value_rd);
                    }
                }
                $datas->search_period=$search_period;
                /***************************************Search period calculation****************************************************/
                // return $search_period;
                if (count($search_period) > 0) {
                    // return $search_period;
                    /********************************************************************** */
                    if (count($after_search_period) > 0) {
                        foreach ($after_search_period as $redemption_key => $redemption_value) {
                            $rdm_tot_units=number_format((float)$redemption_value->tot_units, 4, '.', '');
                            $deduct_unit_array=[];
                            $flag='Y';
                            foreach ($purchase_data as $purchase_key => $purchase_value) {
                                if ($purchase_value['cumml_units'] >= 0) {
                                    $purchase_cumml_units=number_format((float)$purchase_value['cumml_units'], 4, '.', '');
                                    $purchase_value['cumml_units']=number_format((float)($purchase_cumml_units - $rdm_tot_units), 4, '.', '');
                                    if ($purchase_value['cumml_units'] >= 0 ) {
                                        $calculation_cumml_unit=isset($purchase_data[($purchase_key - 1)]['cumml_units'])?$purchase_data[($purchase_key - 1)]['cumml_units']:0;
                                        if ($calculation_cumml_unit < 0) {
                                            $set_units=$purchase_value['cumml_units'];
                                            $purchase_value['cumml_units']=0;
                                            array_push($deduct_unit_array,$purchase_value);
                                            $rdm_tot_units=0;
                                            $newarr=[];
                                            $newarr['id']=$purchase_value['id'];
                                            $newarr['trans_date']=$purchase_value['trans_date'];
                                            $newarr['pur_price']=$purchase_value['pur_price'];
                                            $newarr['sensex']=$purchase_value['sensex'];
                                            $newarr['nifty50']=$purchase_value['nifty50'];
                                            $newarr['curr_nav']=$purchase_value['curr_nav'];
                                            $newarr['days']=$purchase_value['days'];
                                            $newarr['trans_mode']=$purchase_value['trans_mode'];
                                            $newarr['transaction_type']=$purchase_value['transaction_type'];
                                            $newarr['transaction_subtype']=$purchase_value['transaction_subtype'];
                                            $newarr['tot_units']=$set_units;
                                            $newarr['cumml_units']=$set_units;
                                            $newarr['tot_amount']= number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
                                            $newarr['tot_gross_amount']=number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
                                            $newarr['gross_amount']=number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
                                            $newarr['curr_val']=number_format((float)($set_units * $purchase_value['curr_nav']), 2, '.', '');
                                            array_push($deduct_unit_array,$newarr);
                                            $flag='N';
                                        }else {
                                            if ($flag=='Y') {
                                                $set_units=$purchase_value['cumml_units'];
                                                $purchase_value['cumml_units']=0;
                                                array_push($deduct_unit_array,$purchase_value);
                                                $rdm_tot_units=0;
                                                $newarr=[];
                                                $newarr['id']=$purchase_value['id'];
                                                $newarr['trans_date']=$purchase_value['trans_date'];
                                                $newarr['pur_price']=$purchase_value['pur_price'];
                                                $newarr['sensex']=$purchase_value['sensex'];
                                                $newarr['nifty50']=$purchase_value['nifty50'];
                                                $newarr['curr_nav']=$purchase_value['curr_nav'];
                                                $newarr['days']=$purchase_value['days'];
                                                $newarr['trans_mode']=$purchase_value['trans_mode'];
                                                $newarr['transaction_type']=$purchase_value['transaction_type'];
                                                $newarr['transaction_subtype']=$purchase_value['transaction_subtype'];
                                                $newarr['tot_units']=$set_units;
                                                $newarr['cumml_units']=$set_units;
                                                $newarr['tot_amount']= number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
                                                $newarr['tot_gross_amount']=number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
                                                $newarr['gross_amount']=number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
                                                $newarr['curr_val']=number_format((float)($set_units * $purchase_value['curr_nav']), 2, '.', '');
                                                array_push($deduct_unit_array,$newarr);
                                                $flag='N';
                                            }else{
                                                $purchase_value['cumml_units']=number_format((float)$purchase_value['tot_units'], 4, '.', '') + number_format((float)$deduct_unit_array[(count($deduct_unit_array)-1)]['cumml_units'], 4, '.', '') ;
                                                $purchase_value['curr_val']=number_format((float)($purchase_value['tot_units'] * $purchase_value['curr_nav']), 2, '.', '');
                                                array_push($deduct_unit_array,$purchase_value);
                                            }
                                        }
                                    }else {
                                        $purchase_value['curr_val']=number_format((float)($purchase_value['tot_units'] * $purchase_value['curr_nav']), 2, '.', '');
                                        array_push($deduct_unit_array,$purchase_value);
                                        // return $deduct_unit_array;
                                    }
                                }else {
                                    $purchase_value['curr_val']=number_format((float)($purchase_value['tot_units'] * $purchase_value['curr_nav']), 2, '.', '');
                                    array_push($deduct_unit_array,$purchase_value);
                                }
                            }
                            // return  $deduct_unit_array;
                            $purchase_data=$deduct_unit_array;
                        }
                        // return $purchase_data;
                        $final_arr=[];
                        foreach ($purchase_data as $key_0 => $value_0) {
                            if ($value_0['cumml_units'] > 0) {
                                array_push($final_arr,$value_0);
                            }
                        }
                        $purchase_data=$final_arr;
                    }
                    /********************************************************************** */
                    // return $purchase_data;
                    /********************************************************************** */
                    // return $search_period;
                    foreach ($search_period as $redemption_key1 => $redemption_value1) {
                        // return $redemption_value1;
                        $rdm_tot_units=number_format((float)$redemption_value1->tot_units, 4, '.', '');
                        $sell_date=$redemption_value1->trans_date;
                        $sell_transaction_type=$redemption_value1->transaction_type;
                        $sell_transaction_subtype=$redemption_value1->transaction_subtype;
                        $sell_nav=$redemption_value1->pur_price;
                        
                        $deduct_unit_array=[];
                        $flag='Y';
                        foreach ($purchase_data as $purchase_key => $purchase_value) {
                            if ($purchase_value['cumml_units'] >= 0) {
                                $purchase_cumml_units=number_format((float)$purchase_value['cumml_units'], 4, '.', '');
                                $purchase_value['cumml_units']=number_format((float)($purchase_cumml_units - $rdm_tot_units), 4, '.', '');
                                if ($purchase_value['cumml_units'] >= 0 ) {
                                    $calculation_cumml_unit=isset($purchase_data[($purchase_key - 1)]['cumml_units'])?$purchase_data[($purchase_key - 1)]['cumml_units']:0;
                                    if ($calculation_cumml_unit < 0) {
                                        $set_units=$purchase_value['cumml_units'];
                                        $purchase_value['cumml_units']=0;
                                        $purchase_value['remaining_units']=$set_units;
                                        $purchase_value['sell_date']=$sell_date;
                                        $purchase_value['sell_transaction_type']=$sell_transaction_type;
                                        $purchase_value['sell_transaction_subtype']=$sell_transaction_subtype;
                                        $purchase_value['sell_nav']=$sell_nav;
                                        array_push($deduct_unit_array,$purchase_value);
                                        $rdm_tot_units=0;
                                        $newarr=[];
                                        $newarr['id']=$purchase_value['id'];
                                        $newarr['trans_date']=$purchase_value['trans_date'];
                                        $newarr['pur_price']=$purchase_value['pur_price'];
                                        $newarr['sensex']=$purchase_value['sensex'];
                                        $newarr['nifty50']=$purchase_value['nifty50'];
                                        $newarr['curr_nav']=$purchase_value['curr_nav'];
                                        $newarr['days']=$purchase_value['days'];
                                        $newarr['trans_mode']=$purchase_value['trans_mode'];
                                        $newarr['transaction_type']=$purchase_value['transaction_type'];
                                        $newarr['transaction_subtype']=$purchase_value['transaction_subtype'];
                                        $newarr['tot_units']=$set_units;
                                        $newarr['cumml_units']=$set_units;
                                        $newarr['tot_amount']= number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
                                        $newarr['tot_gross_amount']=number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
                                        $newarr['gross_amount']=number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
                                        $newarr['curr_val']=number_format((float)($set_units * $purchase_value['curr_nav']), 2, '.', '');
                                        $newarr['sell_date']=$sell_date;
                                        $newarr['sell_transaction_type']=$sell_transaction_type;
                                        $newarr['sell_transaction_subtype']=$sell_transaction_subtype;
                                        $newarr['sell_nav']=$sell_nav;
                                        array_push($deduct_unit_array,$newarr);
                                        $flag='N';
                                    }else {
                                        if ($flag=='Y') {
                                            $set_units=$purchase_value['cumml_units'];
                                            $purchase_value['cumml_units']=0;
                                            $purchase_value['remaining_units']=$set_units;
                                            $purchase_value['sell_date']=$sell_date;
                                            $purchase_value['sell_transaction_type']=$sell_transaction_type;
                                            $purchase_value['sell_transaction_subtype']=$sell_transaction_subtype;
                                            $purchase_value['sell_nav']=$sell_nav;
                                            array_push($deduct_unit_array,$purchase_value);
                                            $rdm_tot_units=0;
                                            $newarr=[];
                                            $newarr['id']=$purchase_value['id'];
                                            $newarr['trans_date']=$purchase_value['trans_date'];
                                            $newarr['pur_price']=$purchase_value['pur_price'];
                                            $newarr['sensex']=$purchase_value['sensex'];
                                            $newarr['nifty50']=$purchase_value['nifty50'];
                                            $newarr['curr_nav']=$purchase_value['curr_nav'];
                                            $newarr['days']=$purchase_value['days'];
                                            $newarr['trans_mode']=$purchase_value['trans_mode'];
                                            $newarr['transaction_type']=$purchase_value['transaction_type'];
                                            $newarr['transaction_subtype']=$purchase_value['transaction_subtype'];
                                            $newarr['tot_units']=$set_units;
                                            $newarr['cumml_units']=$set_units;
                                            $newarr['tot_amount']= number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
                                            $newarr['tot_gross_amount']=number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
                                            $newarr['gross_amount']=number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
                                            $newarr['curr_val']=number_format((float)($set_units * $purchase_value['curr_nav']), 2, '.', '');
                                            $newarr['sell_date']=$sell_date;
                                            $newarr['sell_transaction_type']=$sell_transaction_type;
                                            $newarr['sell_transaction_subtype']=$sell_transaction_subtype;
                                            $newarr['sell_nav']=$sell_nav;
                                            array_push($deduct_unit_array,$newarr);
                                            $flag='N';
                                        }else{
                                            $purchase_value['cumml_units']=number_format((float)$purchase_value['tot_units'], 4, '.', '') + number_format((float)$deduct_unit_array[(count($deduct_unit_array)-1)]['cumml_units'], 4, '.', '') ;
                                            $purchase_value['curr_val']=number_format((float)($purchase_value['tot_units'] * $purchase_value['curr_nav']), 2, '.', '');
                                            $purchase_value['sell_date']=$sell_date;
                                            $purchase_value['sell_transaction_type']=$sell_transaction_type;
                                            $purchase_value['sell_transaction_subtype']=$sell_transaction_subtype;
                                            $purchase_value['sell_nav']=$sell_nav;
                                            array_push($deduct_unit_array,$purchase_value);
                                        }
                                    }
                                }else {
                                    $purchase_value['curr_val']=number_format((float)($purchase_value['tot_units'] * $purchase_value['curr_nav']), 2, '.', '');
                                    $purchase_value['sell_date']=$sell_date;
                                    $purchase_value['sell_transaction_type']=$sell_transaction_type;
                                    $purchase_value['sell_transaction_subtype']=$sell_transaction_subtype;
                                    $purchase_value['sell_nav']=$sell_nav;
                                    array_push($deduct_unit_array,$purchase_value);
                                    // return $deduct_unit_array;
                                }
                            }else {
                                $purchase_value['curr_val']=number_format((float)($purchase_value['tot_units'] * $purchase_value['curr_nav']), 2, '.', '');
                                array_push($deduct_unit_array,$purchase_value);
                            }
                        }
                        // return  $deduct_unit_array;
                        $purchase_data=$deduct_unit_array;
                    }
                    // return $purchase_data;
                    $calculation_arr=[];
                    foreach ($purchase_data as $key_007 => $value_007) {
                        if ($value_007['cumml_units'] <= 0 && $value_007['tot_units'] > 0) {
                            array_push($calculation_arr,$value_007);
                        }
                    }
                    /********************************************************************** */
                    // return $calculation_arr;
                    $datas->calculation_arr=$calculation_arr;
                    array_push($data,$datas);
                }
                // $datas->for_checking="test_data";
                // array_push($data,$datas);
            }
            usort($data, function($a, $b) {
                return $a['scheme_name'] <=> $b['scheme_name'];
            });
            // return $data;
            $all_trans_product=[];
            foreach ($data as $key_009 => $value_009) {
                $f_trans_product="(nav_date=(SELECT MAX(nav_date) FROM td_nav_details WHERE product_code='".$value_009->product_code."' AND nav_date <='2018-01-31') AND product_code='".$value_009->product_code."')";
                array_push($all_trans_product,$f_trans_product);
            }
            $string_version_product_code = implode(',', $all_trans_product);
            // return $string_version_product_code;
            $res_array=[];
            if (count($data)>0) {
                $res_array =DB::connection('mysql_nav')
                ->select('SELECT product_code,isin_no,DATE_FORMAT(nav_date, "%Y-%m-%d") as nav_date,nav FROM td_nav_details where '.str_replace(",","  OR  ",$string_version_product_code));
            }
            // return $res_array;
            $data_001=[];
            foreach ($data as $key_0010 => $value_0010) {
                // return $value_0010;
                $product_code=$value_0010->product_code;
                $new='';
                if (count($res_array) > 0) {
                    foreach($res_array as $val_nav){
                        if($val_nav->product_code==$product_code){
                            $new=$val_nav;
                        }
                    }
                }
                // return $new;
                $value_0010->new=$new;
                $calculation_arr=$value_0010->calculation_arr;
                $calculation_arr_1=[];
                if ($value_0010->tax_type=='Equity Fund') {
                    foreach ($calculation_arr as $key_calculation_arr => $value_calculation_arr) {
                        // return $value_calculation_arr;
                        $equity_pur_before_31_01_2018=($value_calculation_arr['trans_date'] < '2018-01-31' )?'Yes':'No';
                        $value_calculation_arr['pur_before']=$equity_pur_before_31_01_2018;
                        
                        $value_calculation_arr['nav_as_on_31_01_2018']=$new->nav;
                        if ($equity_pur_before_31_01_2018=="Yes") {
                            $value_calculation_arr['amount_as_on_31_01_2018']=number_format((float)($value_calculation_arr['nav_as_on_31_01_2018'] * $value_calculation_arr['tot_units']), 2, '.', '');
                            $value_calculation_arr['debt_31_03_2023']="";
                        }else {
                            $value_calculation_arr['amount_as_on_31_01_2018']="";
                            $value_calculation_arr['debt_31_03_2023']="";
                        }
                        $value_calculation_arr['sell_type']=$value_calculation_arr['sell_transaction_type'];
                        if ($value_calculation_arr['cumml_units'] <= 0) {
                            $value_calculation_arr['redemp_amount']=number_format((float)($value_calculation_arr['sell_nav'] * $value_calculation_arr['tot_units']), 2, '.', '');
                        }else {
                            $value_calculation_arr['redemp_amount']=0;
                        }
                        $value_calculation_arr['tot_tds']=0;
                        $value_calculation_arr['stt']=0;
                        $value_calculation_arr['net_sell_proceed']=$value_calculation_arr['redemp_amount'] -$value_calculation_arr['tot_tds'] - $value_calculation_arr['stt'];
                        $value_calculation_arr['div_amount']=0;
                        $now = strtotime($value_calculation_arr['sell_date']); // or your date as well
                        $your_date = strtotime(date('Y-m-d',strtotime($value_calculation_arr['trans_date'])));
                        $datediff = $now - $your_date;
                        $days=round($datediff / (60 * 60 * 24));
                        $value_calculation_arr['days']=$days;
                        $value_calculation_arr['ltcg']="";
                        $value_calculation_arr['stcg']="";
                        if ($days > (365 -1)) {
                            $value_calculation_arr['ltcg']=number_format((float)(($value_calculation_arr['sell_nav'] - $value_calculation_arr['pur_price']) * $value_calculation_arr['tot_units']), 2, '.', '');
                        }else {
                            $value_calculation_arr['stcg']=number_format((float)(($value_calculation_arr['sell_nav'] - $value_calculation_arr['pur_price']) * $value_calculation_arr['tot_units']), 2, '.', '');
                        }
                        if ($value_calculation_arr['pur_price'] > $value_calculation_arr['nav_as_on_31_01_2018']) {
                            $value_calculation_arr['index_ltcg']=number_format((float)(($value_calculation_arr['sell_nav'] -$value_calculation_arr['pur_price']) * $value_calculation_arr['tot_units']), 2, '.', '');
                        }else {
                            $value_calculation_arr['index_ltcg']=number_format((float)(($value_calculation_arr['sell_nav'] -$value_calculation_arr['nav_as_on_31_01_2018']) * $value_calculation_arr['tot_units']), 2, '.', '');
                        }
                        array_push($calculation_arr_1,$value_calculation_arr);
                    }
                }else if ($value_0010->tax_type=="Debt Fund") {
                    foreach ($calculation_arr as $key_calculation_arr => $value_calculation_arr) {
                        // return $value_calculation_arr;
                        $debt_31_03_2023=($value_calculation_arr['trans_date'] < '2023-03-31' )?'Yes':'No';
                        $value_calculation_arr['pur_before']="";
                        $value_calculation_arr['nav_as_on_31_01_2018']="";
                        $value_calculation_arr['amount_as_on_31_01_2018']="";
                        $value_calculation_arr['debt_31_03_2023']=$debt_31_03_2023;

                        
                        $value_calculation_arr['sell_type']=$value_calculation_arr['sell_transaction_type'];
                        if ($value_calculation_arr['cumml_units'] <= 0) {
                            $value_calculation_arr['redemp_amount']=number_format((float)($value_calculation_arr['sell_nav'] * $value_calculation_arr['tot_units']), 2, '.', '');
                        }else {
                            $value_calculation_arr['redemp_amount']=0;
                        }
                        $value_calculation_arr['tot_tds']=0;
                        $value_calculation_arr['stt']=0;
                        $value_calculation_arr['net_sell_proceed']=$value_calculation_arr['redemp_amount'] -$value_calculation_arr['tot_tds'] - $value_calculation_arr['stt'];
                        $value_calculation_arr['div_amount']=0;
                        $now = strtotime($value_calculation_arr['sell_date']); // or your date as well
                        $your_date = strtotime(date('Y-m-d',strtotime($value_calculation_arr['trans_date'])));
                        $datediff = $now - $your_date;
                        $days=round($datediff / (60 * 60 * 24));
                        $value_calculation_arr['days']=$days;
                        
                        if ($days > ((365 *3) -1)) {
                            $value_calculation_arr['ltcg']=number_format((float)(($value_calculation_arr['sell_nav'] - $value_calculation_arr['pur_price']) * $value_calculation_arr['tot_units']), 2, '.', '');
                            $value_calculation_arr['stcg']="";
                            if ($value_calculation_arr['pur_price'] > $value_calculation_arr['nav_as_on_31_01_2018']) {
                                $value_calculation_arr['index_ltcg']=number_format((float)(($value_calculation_arr['sell_nav'] -$value_calculation_arr['pur_price']) * $value_calculation_arr['tot_units']), 2, '.', '');
                            }else {
                                $value_calculation_arr['index_ltcg']=number_format((float)(($value_calculation_arr['sell_nav'] -$value_calculation_arr['nav_as_on_31_01_2018']) * $value_calculation_arr['tot_units']), 2, '.', '');
                            }
                        }else {
                            $value_calculation_arr['ltcg']="";
                            $value_calculation_arr['stcg']=number_format((float)(($value_calculation_arr['sell_nav'] - $value_calculation_arr['pur_price']) * $value_calculation_arr['tot_units']), 2, '.', '');
                            $value_calculation_arr['index_ltcg']=0;
                        }
                        array_push($calculation_arr_1,$value_calculation_arr);
                    }
                }
                // else if ($value_0010->tax_type=="Debt Oriented Hybrid Fund") {
                //     foreach ($calculation_arr as $key_calculation_arr => $value_calculation_arr) {
                //         // return $value_calculation_arr;
                //         $equity_pur_before_31_01_2018=($value_calculation_arr['trans_date'] < '2018-01-31' )?'Yes':'No';
                //         $value_calculation_arr['pur_before']=$equity_pur_before_31_01_2018;
                        
                //         $value_calculation_arr['nav_as_on_31_01_2018']=$new->nav;
                //         if ($equity_pur_before_31_01_2018=="Yes") {
                //             $value_calculation_arr['amount_as_on_31_01_2018']=number_format((float)($value_calculation_arr['nav_as_on_31_01_2018'] * $value_calculation_arr['tot_units']), 2, '.', '');
                //             $value_calculation_arr['debt_31_03_2023']="";
                //         }else {
                //             $value_calculation_arr['amount_as_on_31_01_2018']="";
                //             $value_calculation_arr['debt_31_03_2023']="";
                //         }
                //         $value_calculation_arr['sell_type']=$value_calculation_arr['sell_transaction_type'];
                //         if ($value_calculation_arr['cumml_units'] <= 0) {
                //             $value_calculation_arr['redemp_amount']=number_format((float)($value_calculation_arr['sell_nav'] * $value_calculation_arr['tot_units']), 2, '.', '');
                //         }else {
                //             $value_calculation_arr['redemp_amount']=0;
                //         }
                //         $value_calculation_arr['tot_tds']=0;
                //         $value_calculation_arr['stt']=0;
                //         $value_calculation_arr['net_sell_proceed']=$value_calculation_arr['redemp_amount'] -$value_calculation_arr['tot_tds'] - $value_calculation_arr['stt'];
                //         $value_calculation_arr['div_amount']=0;
                //         $now = strtotime($value_calculation_arr['sell_date']); // or your date as well
                //         $your_date = strtotime(date('Y-m-d',strtotime($value_calculation_arr['trans_date'])));
                //         $datediff = $now - $your_date;
                //         $days=round($datediff / (60 * 60 * 24));
                //         $value_calculation_arr['days']=$days;
                //         if ($days > ((365 *3) -1)) {
                //             $value_calculation_arr['ltcg']=number_format((float)(($value_calculation_arr['sell_nav'] - $value_calculation_arr['pur_price']) * $value_calculation_arr['tot_units']), 2, '.', '');
                //         }else {
                //             $value_calculation_arr['stcg']=0;
                //         }
                //         if ($value_calculation_arr['pur_price'] > $value_calculation_arr['nav_as_on_31_01_2018']) {
                //             $value_calculation_arr['index_ltcg']=number_format((float)(($value_calculation_arr['sell_nav'] -$value_calculation_arr['pur_price']) * $value_calculation_arr['tot_units']), 2, '.', '');
                //         }else {
                //             $value_calculation_arr['index_ltcg']=number_format((float)(($value_calculation_arr['sell_nav'] -$value_calculation_arr['nav_as_on_31_01_2018']) * $value_calculation_arr['tot_units']), 2, '.', '');
                //         }
                //         array_push($calculation_arr_1,$value_calculation_arr);
                //     }
                // }
                $value_0010->calculation_arr=$calculation_arr_1;
                array_push($data_001,$value_0010);
            }
            // return 
            $mydata=[];
            $mydata['client_details']=$client_details;
            $mydata['data']=$data_001;
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($mydata);
    }

    public static function getBrokerData($foliotrans_value)
    {
        $rawInnerQuery='';
        $queryString='tt_broker_change_trans_report.folio_no';
        $rawInnerQuery.=Helper::WhereRawQuery($foliotrans_value->folio_no,$rawInnerQuery,$queryString);
        $queryString='tt_broker_change_trans_report.product_code';
        $rawInnerQuery.=Helper::WhereRawQuery($foliotrans_value->product_code,$rawInnerQuery,$queryString);

        $broker_data=BrokerChangeTransReport::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','tt_broker_change_trans_report.product_code')
            ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
            ->select('tt_broker_change_trans_report.rnt_id','tt_broker_change_trans_report.folio_no','tt_broker_change_trans_report.product_code',
            'tt_broker_change_trans_report.isin_no','tt_broker_change_trans_report.trans_date','tt_broker_change_trans_report.trxn_type',
            'tt_broker_change_trans_report.trxn_type_flag','tt_broker_change_trans_report.trxn_nature','tt_broker_change_trans_report.amount',
            'tt_broker_change_trans_report.stamp_duty','tt_broker_change_trans_report.tds','tt_broker_change_trans_report.units','tt_broker_change_trans_report.pur_price',
            'tt_broker_change_trans_report.trans_no',
            'md_scheme.scheme_name as scheme_name')
            ->selectRaw('sum(units) as tot_units')
            ->selectRaw('sum(amount) as tot_amount')
            ->selectRaw('sum(stamp_duty) as tot_stamp_duty')
            ->selectRaw('IF(tt_broker_change_trans_report.tds!="",sum(tds),0.00)as tot_tds')
            ->selectRaw('count(*) as tot_rows')
            ->selectRaw('(SELECT trans_type FROM md_mf_trans_type_subtype WHERE c_trans_type_code=tt_broker_change_trans_report.trxn_type_code AND c_k_trans_type=tt_broker_change_trans_report.trxn_type_flag AND c_k_trans_sub_type=tt_broker_change_trans_report.trxn_nature_code limit 1)as transaction_type')
            ->selectRaw('(SELECT trans_sub_type FROM md_mf_trans_type_subtype WHERE c_trans_type_code=tt_broker_change_trans_report.trxn_type_code AND c_k_trans_type=tt_broker_change_trans_report.trxn_type_flag AND c_k_trans_sub_type=tt_broker_change_trans_report.trxn_nature_code limit 1)as transaction_subtype')
            ->selectRaw('(SELECT lmf_pl FROM md_mf_trans_type_subtype WHERE c_trans_type_code=tt_broker_change_trans_report.trxn_type_code AND c_k_trans_type=tt_broker_change_trans_report.trxn_type_flag AND c_k_trans_sub_type=tt_broker_change_trans_report.trxn_nature_code limit 1)as lmf_pl')
            ->where('tt_broker_change_trans_report.delete_flag','N')
            ->where('tt_broker_change_trans_report.amc_flag','N')
            ->where('tt_broker_change_trans_report.scheme_flag','N')
            ->where('tt_broker_change_trans_report.plan_option_flag','N')
            ->where('tt_broker_change_trans_report.bu_type_flag','N')
            ->where('tt_broker_change_trans_report.divi_mismatch_flag','N')
            ->whereRaw($rawInnerQuery)
            ->groupBy('tt_broker_change_trans_report.trans_no')
            ->groupBy('tt_broker_change_trans_report.trxn_type_flag')
            ->groupBy('tt_broker_change_trans_report.trxn_nature_code')
            ->groupBy('tt_broker_change_trans_report.trans_desc')
            ->groupBy('tt_broker_change_trans_report.kf_trans_type')
            ->orderBy('tt_broker_change_trans_report.trans_date','ASC')
            ->get();
        return $broker_data;
    }

}