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
    BrokerChangeTransReport,
    SchemeISIN,
    MutualFundTransactionMerge
};
use Validator;
use Illuminate\Support\Carbon;
use Excel;
use App\Helpers\TransHelper;
use App\Helpers\AumHelper;
use DB;
use Session;
use App\Models\Disclaimer;
use App\Models\AumReport;
use App\Jobs\AumCalculationJob;
use App\Jobs\PrevDayUnitsJob;
use App\Jobs\OpManualUpJob;
use App\Jobs\OpNFTManualUpJob;
use App\Http\Controllers\V1\Client\AumCalculationController as AumCalculationController1;

class AumCalculationController extends Controller
{
    public function index(Request $request)
    {
        try {
            // return  'hii';

            $mydata=[];

            PrevDayUnitsJob::dispatch();
            // OpNFTManualUpJob::dispatch();
            return 'Job Run Successfully';
            $clients=MutualFundTransaction::select('id','first_client_name','first_client_pan')
            // ->where('delete_flag','N')
            // ->where('amc_flag','N')
            // ->where('scheme_flag','N')
            // ->where('plan_option_flag','N')
            // ->where('bu_type_flag','N')
            // ->where('divi_mismatch_flag','N')
            // ->where('portfolio_show_flag','Y')
            // ->where('rnt_id',1)
            // ->whereDate('created_at',date('Y-m-d'))
            ->groupBy('first_client_name')
            ->groupBy('first_client_pan')
            ->get();
            // return $clients;
            // $valuation_as_on= date("Y-m-d", strtotime("- 1 day"));
            // foreach ($clients as $key => $client) {
            //     // return $client;
            //     $port=AumCalculationController1::calucationTotUnitsAndInvCost($client->first_client_name,$client->first_client_pan,$valuation_as_on);
            //     // return $port;
            //     $insert_arr=[];
            //     foreach ($port as $key => $single) {
            //         // return $single;
            //         $insert_arr[]=[
            //             'rnt_id'=>$single['rnt_id'],
            //             'first_client_name'=>$single['first_client_name'],
            //             'first_client_pan'=>$single['first_client_pan'],
            //             'amc_code'=>$single['amc_code'],
            //             'folio_no'=>$single['folio_no'],
            //             'product_code'=>$single['product_code'],
            //             'trans_date'=>$valuation_as_on,
            //             'transaction_type'=>$single['transaction_type'],
            //             'transaction_subtype'=>$single['transaction_subtype'],
            //             'amc_name'=>$single['amc_name'],
            //             'scheme_name'=>$single['scheme_name'],
            //             'cat_name'=>$single['cat_name'],
            //             'subcat_name'=>$single['subcat_name'],
            //             'plan_name'=>$single['plan_name'],
            //             'option_name'=>$single['option_name'],
            //             'divident_paid'=>$single['idcwp'],
            //             'divident_reinvest'=>$single['idcw_reinv'],
            //             'total_unit'=>$single['tot_units'],
            //             'total_inv_cost'=>$single['inv_cost'],
            //             'all_amount_arr'=>json_encode($single['mydata']['all_amt_arr']),
            //             'all_date_arr'=>json_encode($single['mydata']['all_date_arr']),
            //             'portfolio_show_flag'=>$single['portfolio_show_flag'],
            //             'created_at'=>date('Y-m-d H:i:s'),
            //             'updated_at'=>date('Y-m-d H:i:s')
            //         ];
            //     }
            //     // AumReport::insert($insert_arr);
            //     return $insert_arr;
            // }
            // dispatch((new AumCalculationJob($clients))->onQueue('clients'));
            // RunDispatch::chain($clients)->dispatch();
            AumCalculationJob::dispatch($clients);
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($clients);
    }

    public function test()
    {
        
        // $arr=['B105G','B106D','B106DP'];
        $pan='AMEPG0111G';
        $clients=MutualFundTransaction::select('id','first_client_name','first_client_pan')
                        // ->whereIn('product_code',$arr)
            ->where('first_client_pan',$pan)
            ->groupBy('first_client_name')
            ->groupBy('first_client_pan')
            ->get();
        // return $clients;
        
        $valuation_as_on=date('Y-m-d');
        foreach ($clients as $key => $client) {
            $port=AumCalculationController1::calucationTotUnitsAndInvCost($client->first_client_name,$client->first_client_pan,$valuation_as_on);
            return $port;
        }
        // AumCalculationJob::dispatch($clients);




        
        // $port=AumCalculationController1::calucationTotUnitsAndInvCost__($client->first_client_name,$client->first_client_pan,$valuation_as_on);
        // return $port;
        // calucationTotUnitsAndInvCost($client_name,$pan_no,$valuation_as_on)
        return 'Job Run Successfully';

    }
   
    public static function calucationTotUnitsAndInvCost($client_name,$pan_no,$valuation_as_on)
    {
        try {
            session()->forget('valuation_as_on');
            // return Session::get('valuation_as_on');
            $client_details='';
            if ($valuation_as_on) {
                $rawQuery='';
                 
                if ($valuation_as_on) {
                    $condition_v=(strlen($rawQuery) > 0)? " AND ":" ";
                    $queryString='td_mutual_fund_trans.trans_date';
                    $rawQuery.=$condition_v.$queryString."<= '".$valuation_as_on."'";
                }
                $client_rawQuery='';
                if (!$pan_no) {
                    $queryString='td_mutual_fund_trans.first_client_name';
                    $rawQuery.=Helper::WhereRawQuery($client_name,$rawQuery,$queryString);
                }else {
                    $queryString='td_mutual_fund_trans.first_client_pan';
                    $rawQuery.=Helper::WhereRawQuery($pan_no,$rawQuery,$queryString);
                }
            } 

            session(['valuation_as_on' => $valuation_as_on]);
            // return $rawQuery;
            // return $client_details;
            // DB::enableQueryLog();
            $all_data=MutualFundTransaction::with('foliotrans')
                ->leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_mutual_fund_trans.product_code')
                ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
                ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                ->leftJoin('md_amc','md_amc.amc_code','=','td_mutual_fund_trans.amc_code')
                ->leftJoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
                ->leftJoin('md_option','md_option.id','=','md_scheme_isin.option_id')
                ->select('td_mutual_fund_trans.portfolio_show_flag','td_mutual_fund_trans.rnt_id','td_mutual_fund_trans.folio_no','td_mutual_fund_trans.product_code','td_mutual_fund_trans.amc_code','td_mutual_fund_trans.pur_price','td_mutual_fund_trans.trans_date','td_mutual_fund_trans.trans_mode',
                'md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name','md_category.id as cat_id','md_subcategory.id as subcat_id',
                'md_amc.amc_short_name as amc_name','md_plan.plan_name as plan_name','md_option.opt_name as option_name',
                'md_scheme.id as scheme_id','md_scheme.amc_id as amc_id')
                ->selectRaw('UCASE(td_mutual_fund_trans.first_client_name) as first_client_name,td_mutual_fund_trans.first_client_pan')
                ->selectRaw('IF(td_mutual_fund_trans.rnt_id=1,md_scheme_isin.isin_no,td_mutual_fund_trans.isin_no) as isin_no')
                ->selectRaw('sum(td_mutual_fund_trans.units) as tot_units')
                ->selectRaw('sum(td_mutual_fund_trans.amount) as tot_amount')
                ->selectRaw('sum(td_mutual_fund_trans.stamp_duty) as tot_stamp_duty')
                ->selectRaw('sum(td_mutual_fund_trans.tds) as tot_tds')
                ->selectRaw('count(*) as tot_rows')
                ->selectRaw('IF(td_mutual_fund_trans.rnt_id=1,
                (SELECT trans_type FROM md_mf_trans_type_subtype WHERE c_trans_type_code=td_mutual_fund_trans.trxn_type_code AND c_k_trans_type=td_mutual_fund_trans.trxn_type_flag AND c_k_trans_sub_type=td_mutual_fund_trans.trxn_nature_code limit 1),
                (CASE 
                    WHEN td_mutual_fund_trans.trans_flag="DP" || td_mutual_fund_trans.trans_flag="DR" THEN (SELECT trans_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=td_mutual_fund_trans.kf_trans_type AND k_divident_flag=td_mutual_fund_trans.trans_flag limit 1)
                    WHEN td_mutual_fund_trans.trans_flag="TO" THEN "Transfer Out"
                    ELSE (SELECT trans_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=td_mutual_fund_trans.kf_trans_type limit 1)
                END)
                )as transaction_type')
                ->selectRaw('IF(td_mutual_fund_trans.rnt_id=1,
                (SELECT trans_sub_type FROM md_mf_trans_type_subtype WHERE c_trans_type_code=td_mutual_fund_trans.trxn_type_code AND c_k_trans_type=td_mutual_fund_trans.trxn_type_flag AND c_k_trans_sub_type=td_mutual_fund_trans.trxn_nature_code limit 1),
                (CASE 
                    WHEN td_mutual_fund_trans.trans_flag="DP" || td_mutual_fund_trans.trans_flag="DR" THEN (SELECT trans_sub_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=td_mutual_fund_trans.kf_trans_type AND k_divident_flag=td_mutual_fund_trans.trans_flag limit 1)
                    WHEN td_mutual_fund_trans.trans_flag="TO" THEN "Transfer Out"
                    ELSE (SELECT trans_sub_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=td_mutual_fund_trans.kf_trans_type limit 1)
                END)
                )as transaction_subtype')
                ->where('td_mutual_fund_trans.delete_flag','N')
                ->where('td_mutual_fund_trans.amc_flag','N')
                ->where('td_mutual_fund_trans.scheme_flag','N')
                ->where('td_mutual_fund_trans.plan_option_flag','N')
                ->where('td_mutual_fund_trans.bu_type_flag','N')
                ->where('td_mutual_fund_trans.divi_mismatch_flag','N')
                ->where('td_mutual_fund_trans.portfolio_show_flag','Y')
                ->whereRaw($rawQuery)
                ->groupBy('td_mutual_fund_trans.folio_no')
                ->groupBy('td_mutual_fund_trans.product_code')
                ->groupBy('td_mutual_fund_trans.isin_no')
                ->orderBy('td_mutual_fund_trans.trans_date','ASC')
                ->get()
                ->toArray();
            // dd(DB::getQueryLog());
            // dd(DB::getQueryLog());
            // return $all_data;
            usort($all_data, function($a, $b) {
                return $a['scheme_name'] <=> $b['scheme_name'];
            });
            $filter_data=[];
            foreach ($all_data as $data_key => $value1) {
                // return $value1;
                $isin_no=$value1['isin_no'];
                $product_code=$value1['product_code'];
               
                $value1['curr_nav']=0;
                $mydata='';
                $foliotrans=$value1['foliotrans'];
                $json  = json_encode($foliotrans);
                $array = json_decode($json, true);
                if (array_search('Consolidation In',array_column($array,'transaction_subtype'))) {
                    // $foliotrans=TransHelper::ConsolidationInQuery($value1['rnt_id'],$value1['folio_no'],$value1['isin_no'],$value1['product_code'],$valuation_as_on);
                    $foliotrans=AumCalculationController1::ConsolidationInQuery($value1['rnt_id'],$value1['folio_no'],$value1['isin_no'],$value1['product_code'],$valuation_as_on);
                }
                // $mydata=TransHelper::calculate($foliotrans,$value1->curr_nav,$valuation_as_on);
                $mydata=AumCalculationController1::calculate($foliotrans,$value1['curr_nav'],$valuation_as_on);
                // return $mydata;
                $value1['mydata']=$mydata;
                $value1['idcwp']=0;
                $value1['idcw_reinv']=isset($mydata['idcw_reinv'])? number_format((float)$mydata['idcw_reinv'], 2, '.', ''):0;
                $value1['idcwr']=number_format((float)($value1['idcwp'] + $value1['idcw_reinv']), 2, '.', '');
                $value1['inv_cost']=isset($mydata['inv_cost'])?number_format((float)$mydata['inv_cost'], 2, '.', ''):0;
                $value1['tot_units']=isset($mydata['tot_units'])?number_format((float)$mydata['tot_units'], 2, '.', ''):0;
               
                array_push($filter_data,$value1);
            }
            return $filter_data;
        } catch (\Throwable $th) {
            // throw $th;
            return [];
        }
    }

    public static function calculate($foliotrans,$curr_nav,$valuation_as_on){
        $purchase_data=[];
        $redemption_data=[];
        $purchase_amt_arr=[];
        $redemption_amt_arr=[];
        $all_amt_arr=[];
        $all_date_arr=[];
        $return_data=[];
        /*******************************************Start CAMS Broker Change Data**********************************************************/
        $get_rejection_data=[];
        // if ($foliotrans[0]['rnt_id']==1 && $foliotrans[0]['transaction_type']=='Transfer In' && $foliotrans[0]['transaction_subtype']=='Transfer In') {
            $final_foliotrans=[];
            foreach ($foliotrans as $key => $foliotrans_value) {
                if ($foliotrans_value['transaction_type']=="Transfer In" && $foliotrans_value['transaction_subtype']=="Transfer In") {
                    // return $foliotrans_value;
                    $broker_data=AumCalculationController1::getBrokerData($foliotrans_value);
                    // return $broker_data;
                    if (count($broker_data)> 0) {
                        foreach ($broker_data as $key => $broker_data_value) {
                            if ($broker_data_value['amount'] < 0) {
                                $broker_data_value['transaction_type']=$broker_data_value['transaction_type']." Rejection";
                                $broker_data_value['transaction_subtype']=$broker_data_value['transaction_subtype']." Rejection";
                            }
                            if( strpos($broker_data_value['transaction_subtype'], 'Rejection' ) == false) {
                                array_push($final_foliotrans,$broker_data_value);
                            }else {
                                array_push($get_rejection_data,$broker_data_value);
                            }
                            // array_push($final_foliotrans,$broker_data_value);
                        }
                    }else {
                        array_push($final_foliotrans,$foliotrans_value);
                    }
                }else {
                    if ($foliotrans_value['rnt_id']==1 && $foliotrans_value['amount'] < 0) {
                        $foliotrans_value['transaction_type']=$foliotrans_value['transaction_type']." Rejection";
                        $foliotrans_value['transaction_subtype']=$foliotrans_value['transaction_subtype']." Rejection";
                    }
                    if( strpos($foliotrans_value['transaction_subtype'], 'Rejection' ) == false) {
                        array_push($final_foliotrans,$foliotrans_value);
                    }else {
                        array_push($get_rejection_data,$foliotrans_value);
                    }
                    // array_push($final_foliotrans,$foliotrans_value);
                }
            }
            $foliotrans=$final_foliotrans;
        // }
        // return $foliotrans;
        // return $get_rejection_data;
        /*******************************************End CAMS Broker Change Data**********************************************************/
        // **************************Start Rejection Amount Delete*************************************
        foreach ($get_rejection_data as $key_0001 => $value_0001) {
            $amount=str_replace("-","",$value_0001['amount']) ;
            $trans_date=$value_0001['trans_date'];
            $get_final_success_data=[];
            foreach ($foliotrans as $key_002 => $value_002) {
                if ($value_002['trans_date']==$trans_date && $value_002['amount']==$amount) {
                    $amount=0;
                }else {
                    array_push($get_final_success_data,$value_002);
                }
            }
            $foliotrans=$get_final_success_data;
        }
        // **************************End Rejection Amount Delete*************************************
        // return $foliotrans;
        /*****************start if only rejection data available*************** */
        // if (count($foliotrans)==0) {
        //     $foliotrans=$get_rejection_data;
        // }
        /*****************end if only rejection data available*************** */
        // $return_data['inv_since']=date('Y-m-d',strtotime($foliotrans[0]['trans_date']));
        // $return_data['pur_nav']=$foliotrans[0]['pur_price'];
        // $return_data['nifty50']=$foliotrans[0]['nifty50'];
        // $return_data['sensex']=$foliotrans[0]['sensex'];
        /*************************************start transaction_type_subtype modify**********************************************************/
        // if ($foliotrans[0]['transaction_type']=="Purchase" && $foliotrans[0]['transaction_subtype']=="Fresh Purchase") {
        //     if ((isset($foliotrans[1]['transaction_type']) && $foliotrans[1]['transaction_type']=="SIP Purchase") && (isset($foliotrans[1]['transaction_subtype']) && $foliotrans[1]['transaction_subtype']=="SIP Purchase Installment")) {
        //         $return_data['transaction_type']=$foliotrans[1]['transaction_type'];
        //         $return_data['transaction_subtype']=$foliotrans[1]['transaction_subtype'];
        //     }else {
        //         $return_data['transaction_type']=$foliotrans[0]['transaction_type'];
        //         $return_data['transaction_subtype']=$foliotrans[0]['transaction_subtype'];
        //     }
        // }else {
        //     $return_data['transaction_type']=$foliotrans[0]['transaction_type'];
        //     $return_data['transaction_subtype']=$foliotrans[0]['transaction_subtype'];
        // }
        /*************************************end transaction_type_subtype modify**********************************************************/

        foreach ($foliotrans as $key => $value) {
            if(strpos($value['transaction_subtype'], 'Purchase' )!== false || strpos($value['transaction_subtype'], 'Switch In' )!== false 
                || strpos($value['transaction_subtype'], 'Dividend Reinvestment')!== false || strpos($value['transaction_subtype'], 'STP In')!== false) {
                array_push($purchase_data,$value);
                /****************************************** */
                array_push($all_amt_arr,-$value['tot_amount']);
                array_push($all_date_arr,$value['trans_date']);
                /****************************************** */
            }elseif (strpos($value['transaction_subtype'], 'Redemption' )!== false || strpos($value['transaction_subtype'], 'Switch Out' )!== false 
                || strpos($value['transaction_subtype'], 'Transfer Out')!== false || strpos($value['transaction_subtype'], 'SWP')!== false
                || strpos($value['transaction_subtype'], 'STP Out')!== false) {
                array_push($redemption_data,$value);
                /****************************************** */
                array_push($all_amt_arr,-$value['tot_amount']);
                array_push($all_date_arr,$value['trans_date']);
                /****************************************** */
            }
        }
        // return $purchase_data;

        // *********************for pledging condition*****************
        $purchase_data_recheck=[];
        foreach ($purchase_data as $key_001 => $value_001) {
            if ($key_001 > 0) {
                $value_001['cumml_units']=number_format((float)($value_001['tot_units'] + $purchase_data_recheck[($key_001-1)]['cumml_units']) , 4, '.', '');
            }else {
                $value_001['cumml_units']=number_format((float)$value_001['tot_units'], 4, '.', '');
            }
            array_push($purchase_data_recheck,$value_001);
        }
        // return $purchase_data_recheck;
        $purchase_data=$purchase_data_recheck;
        // *********************for pledging condition*****************

        $idcw_reinv=0;
        $idcw_paid=0;
        $inv_cost=0;
        $cal_purchase_data=[];
        if (count($redemption_data) > 0) {
            /*******************************************start purchase and redemption case******************************************/
            foreach ($redemption_data as $redemption_key => $redemption_value) {
                $rdm_tot_units=number_format((float)$redemption_value['tot_units'], 4, '.', '');
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
                                $newarr['id']=isset($purchase_value['id'])?$purchase_value['id']:0;
                                $newarr['trans_date']=$purchase_value['trans_date'];
                                $newarr['pur_price']=$purchase_value['pur_price'];
                                // $newarr['sensex']=$purchase_value['sensex'];
                                // $newarr['nifty50']=$purchase_value['nifty50'];
                                $newarr['curr_nav']=$curr_nav;
                                // $newarr['days']=$purchase_value['days'];
                                // $newarr['trans_mode']=$purchase_value['trans_mode'];
                                $newarr['transaction_type']="Remaining";
                                $newarr['transaction_subtype']="Remaining";
                                $newarr['stamp_duty']=$purchase_value['stamp_duty'];
                                $newarr['tot_stamp_duty']=$purchase_value['tot_stamp_duty'];
                                $newarr['tot_tds']=$purchase_value['tot_tds'];
                                $newarr['prev_transaction_type']=$purchase_value['transaction_type'];
                                $newarr['prev_transaction_subtype']=$purchase_value['transaction_type'];
                                $newarr['tot_units']=number_format((float)$set_units, 4, '.', '');
                                $newarr['cumml_units']=number_format((float)$set_units, 4, '.', '');
                                $newarr['tot_amount']= number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
                                $newarr['tot_gross_amount']=number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
                                $newarr['gross_amount']=number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
                                $newarr['curr_val']=number_format((float)($set_units * $newarr['curr_nav']), 2, '.', '');
                                array_push($deduct_unit_array,$newarr);
                                $flag='N';
                            }else {
                                if ($flag=='Y') {
                                    $set_units=$purchase_value['cumml_units'];
                                    $purchase_value['cumml_units']=0;
                                    array_push($deduct_unit_array,$purchase_value);
                                    $rdm_tot_units=0;
                                    $newarr=[];
                                    $newarr['id']=isset($purchase_value['id'])?$purchase_value['id']:0;
                                    $newarr['trans_date']=$purchase_value['trans_date'];
                                    $newarr['pur_price']=$purchase_value['pur_price'];
                                    // $newarr['sensex']=$purchase_value['sensex'];
                                    // $newarr['nifty50']=$purchase_value['nifty50'];
                                    $newarr['curr_nav']=$curr_nav;
                                    // $newarr['days']=$purchase_value['days'];
                                    // $newarr['trans_mode']=$purchase_value['trans_mode'];
                                    $newarr['transaction_type']="Remaining";
                                    $newarr['transaction_subtype']="Remaining";
                                    $newarr['stamp_duty']=$purchase_value['stamp_duty'];
                                    $newarr['tot_stamp_duty']=$purchase_value['tot_stamp_duty'];
                                    $newarr['tot_tds']=$purchase_value['tot_tds'];
                                    $newarr['prev_transaction_type']=$purchase_value['transaction_type'];
                                    $newarr['prev_transaction_subtype']=$purchase_value['transaction_type'];
                                    $newarr['tot_units']=number_format((float)$set_units, 4, '.', '');
                                    $newarr['cumml_units']=number_format((float)$set_units, 4, '.', '');
                                    $newarr['tot_amount']= number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
                                    $newarr['tot_gross_amount']=number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
                                    $newarr['gross_amount']=number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
                                    $newarr['curr_val']=number_format((float)($set_units * $newarr['curr_nav']), 2, '.', '');
                                    array_push($deduct_unit_array,$newarr);
                                    $flag='N';
                                }else{
                                    $purchase_value['cumml_units']=number_format((float)($purchase_value['tot_units'] + $deduct_unit_array[(count($deduct_unit_array)-1)]['cumml_units'] ), 4, '.', '');
                                    $purchase_value['curr_val']=number_format((float)($purchase_value['tot_units'] * $curr_nav), 2, '.', '');
                                    array_push($deduct_unit_array,$purchase_value);
                                }
                            }
                        }else {
                            $purchase_value['curr_val']=number_format((float)($purchase_value['tot_units'] * $curr_nav), 2, '.', '');
                            array_push($deduct_unit_array,$purchase_value);
                            // return $deduct_unit_array;
                        }
                    }else {
                        $purchase_value['curr_val']=number_format((float)($purchase_value['tot_units'] * $curr_nav), 2, '.', '');
                        array_push($deduct_unit_array,$purchase_value);
                    }
                }
                // return  $deduct_unit_array;
                $purchase_data=$deduct_unit_array;
            }
            // return $purchase_data;
            /*******************************************end purchase and redemption case******************************************/
            // $final_array=array_merge($deduct_unit_array,$purchase_data);
            // return $final_array;
            // $final_data_arr=[];
            $inv_cost=0;
            foreach ($purchase_data as $key => $value) {
                if ($value['cumml_units'] > 0) {
                    if (strpos($value['transaction_subtype'], 'Dividend Reinvestment')!== false) {
                        $idcw_reinv +=number_format((float)$value['tot_amount'], 2, '.', '');
                    }
                    $inv_cost +=number_format((float)$value['tot_amount'], 2, '.', '');
                }
            }
            
        }else {
            foreach ($purchase_data as $key => $value) {
                if (strpos($value['transaction_subtype'], 'Dividend Reinvestment')!== false) {
                    $idcw_reinv +=number_format((float)$value['tot_amount'], 2, '.', '');
                }
                $inv_cost +=number_format((float)$value['tot_amount'], 2, '.', '');
            }
        }
        // return $all_amt_arr;
        // return $all_date_arr;
        $return_data['inv_cost']=$inv_cost;
        $return_data['tot_units']=(count($purchase_data) > 0)?$purchase_data[(count($purchase_data) - 1)]['cumml_units']:0;
        $return_data['all_amt_arr']=$all_amt_arr;
        $return_data['all_date_arr']=$all_date_arr;
        $return_data['idcw_reinv']=$idcw_reinv;
        $return_data['idcw_paid']=$idcw_paid;
        $return_data['cal_purchase_data']=$cal_purchase_data;
        return $return_data;
    }

    public static function ConsolidationInQuery($rnt_id,$folio_no,$isin_no,$product_code,$valuation_as_on)
    {
        $rawQuery='';
        $queryString='td_mutual_fund_trans.folio_no';
        $rawQuery.=Helper::WhereRawQuery($folio_no,$rawQuery,$queryString);
        $queryString='td_mutual_fund_trans.product_code';
        $rawQuery.=Helper::WhereRawQuery($product_code,$rawQuery,$queryString);
        if ($rnt_id==2) {
            $queryString='td_mutual_fund_trans.isin_no';
            $rawQuery.=Helper::WhereRawQuery($isin_no,$rawQuery,$queryString);
        } 
        $condition=(strlen($rawQuery) > 0)? " AND ":" ";
        $queryString='td_mutual_fund_trans.trans_date';
        $rawQuery.=$condition.$queryString." <= '".$valuation_as_on."'";
        // return $rawQuery;
        // DB::enableQueryLog();
        $all_data=MutualFundTransaction::select('rnt_id','folio_no','product_code','isin_no','trans_date','trxn_type','trxn_type_flag','trxn_nature','amount','stamp_duty','tds',
            'units','pur_price')
            ->selectRaw('sum(units) as tot_units')
            ->selectRaw('sum(amount) as tot_amount')
            ->selectRaw('sum(stamp_duty) as tot_stamp_duty')
            ->selectRaw('IF(tds!="",sum(tds),0.00)as tot_tds')
            ->selectRaw('count(*) as tot_rows')
            ->selectRaw('(select close from td_benchmark_scheme where benchmark=1 AND date=trans_date) as nifty50')
            ->selectRaw('(select close from td_benchmark_scheme where benchmark=70 AND date=trans_date) as sensex')
            ->selectRaw('IF(td_mutual_fund_trans.rnt_id=1,
                (SELECT trans_type FROM md_mf_trans_type_subtype WHERE c_trans_type_code=td_mutual_fund_trans.trxn_type_code AND c_k_trans_type=td_mutual_fund_trans.trxn_type_flag AND c_k_trans_sub_type=td_mutual_fund_trans.trxn_nature_code limit 1),
                (CASE 
                    WHEN td_mutual_fund_trans.trans_flag="DP" || td_mutual_fund_trans.trans_flag="DR" THEN (SELECT trans_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=td_mutual_fund_trans.kf_trans_type AND k_divident_flag=td_mutual_fund_trans.trans_flag limit 1)
                    WHEN td_mutual_fund_trans.trans_flag="TO" THEN "Transfer Out"
                    ELSE (SELECT trans_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=td_mutual_fund_trans.kf_trans_type limit 1)
                END)
                )as transaction_type')
            ->selectRaw('IF(td_mutual_fund_trans.rnt_id=1,
                (SELECT trans_sub_type FROM md_mf_trans_type_subtype WHERE c_trans_type_code=td_mutual_fund_trans.trxn_type_code AND c_k_trans_type=td_mutual_fund_trans.trxn_type_flag AND c_k_trans_sub_type=td_mutual_fund_trans.trxn_nature_code limit 1),
                (CASE 
                    WHEN td_mutual_fund_trans.trans_flag="DP" || td_mutual_fund_trans.trans_flag="DR" THEN (SELECT trans_sub_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=td_mutual_fund_trans.kf_trans_type AND k_divident_flag=td_mutual_fund_trans.trans_flag limit 1)
                    WHEN td_mutual_fund_trans.trans_flag="TO" THEN "Transfer Out"
                    ELSE (SELECT trans_sub_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=td_mutual_fund_trans.kf_trans_type limit 1)
                END)
                )as transaction_subtype')
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
            ->orderBy('td_mutual_fund_trans.trans_date','asc')
            ->get()
            ->toArray();
        return $all_data;
    }

    public static function getBrokerData($foliotrans_value)
    {
        $rawInnerQuery='';
        $queryString='tt_broker_change_trans_report.folio_no';
        $rawInnerQuery.=Helper::WhereRawQuery($foliotrans_value['folio_no'],$rawInnerQuery,$queryString);
        $queryString='tt_broker_change_trans_report.product_code';
        $rawInnerQuery.=Helper::WhereRawQuery($foliotrans_value['product_code'],$rawInnerQuery,$queryString);
        // return $rawInnerQuery;
        // DB::enableQueryLog();
        $broker_data=BrokerChangeTransReport::select('tt_broker_change_trans_report.rnt_id','tt_broker_change_trans_report.folio_no','tt_broker_change_trans_report.product_code',
            'tt_broker_change_trans_report.isin_no','tt_broker_change_trans_report.trans_date','tt_broker_change_trans_report.trxn_type',
            'tt_broker_change_trans_report.trxn_type_flag','tt_broker_change_trans_report.trxn_nature','tt_broker_change_trans_report.amount',
            'tt_broker_change_trans_report.stamp_duty','tt_broker_change_trans_report.tds','tt_broker_change_trans_report.units','tt_broker_change_trans_report.pur_price',
            'tt_broker_change_trans_report.trans_no')
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
            ->groupBy('tt_broker_change_trans_report.trans_flag')
            ->orderBy('tt_broker_change_trans_report.trans_date','ASC')
            ->get()->toArray();
        
        // dd(DB::getQueryLog());
        return $broker_data;
    }

    public function search___(Request $request)
    {
        try {
            // return $request;
            $valuation_as_on=$request->valuation_as_on;
            $view_type=$request->view_type;
            $pan_no=$request->pan_no;
            $client_name=$request->client_name;
            $view_funds_type=$request->view_funds_type;
            $family_members_pan=json_decode($request->family_members_pan);
            $family_members_name=json_decode($request->family_members_name);
            $trans_type=$request->trans_type;
            $trans_duration=$request->trans_duration;
            // return $trans_duration;
            session()->forget('valuation_as_on');
            
            // return Session::get('valuation_as_on');
            $client_details='';
            if ($view_type || $valuation_as_on || $view_funds_type || $trans_duration) {
                $rawQuery='';
                 
                if ($valuation_as_on) {
                    $condition_v=(strlen($rawQuery) > 0)? " AND ":" ";
                    $queryString='td_mutual_fund_trans.trans_date';
                    $rawQuery.=$condition_v.$queryString."<= '".$valuation_as_on."'";
                }
                
                if ($view_type=='C') {
                    $client_rawQuery='';
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
                }else {
                    $queryString='td_mutual_fund_trans.first_client_pan';
                    $condition=(strlen($rawQuery) > 0)? " AND (":" (";
                    $row_name_string=  "'" .implode("','", $family_members_pan). "'";
                    $rawQuery.=$condition.$queryString." IN (".$row_name_string.")";
                    $queryString='td_mutual_fund_trans.first_client_name';
                    $condition1=(strlen($rawQuery) > 0)? " OR ":" ";
                    $row_name_string1=  "'" .implode("','", $family_members_name). "'";
                    $rawQuery.=$condition1.$queryString." IN (".$row_name_string1."))";

                    /***********************Client************************ */
                    $client_rawQuery='';
                    $client_queryString='md_client.pan';
                    $client_condition=(strlen($client_rawQuery) > 0)? " AND (":" (";
                    $client_row_name_string="'" .implode("','", $family_members_pan)."'";
                    $client_rawQuery.=$client_condition.$client_queryString." IN (".$client_row_name_string.")";
                    $client_queryString='md_client.client_name';
                    $client_condition1=(strlen($client_rawQuery) > 0)? " OR ":" ";
                    $client_row_name_string1="'" .implode("','", $family_members_name)."'";
                    $client_rawQuery.=$client_condition1.$client_queryString." IN (".$client_row_name_string1."))";

                }
            } 

            session(['valuation_as_on' => $valuation_as_on]);
            // return $rawQuery;
            // return $client_details;
            // DB::enableQueryLog();
            $all_data=MutualFundTransaction::with('foliotrans')->leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_mutual_fund_trans.product_code')
                ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
                ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                ->leftJoin('md_amc','md_amc.amc_code','=','td_mutual_fund_trans.amc_code')
                ->leftJoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
                ->leftJoin('md_option','md_option.id','=','md_scheme_isin.option_id')
                ->select('td_mutual_fund_trans.portfolio_show_flag','td_mutual_fund_trans.rnt_id','td_mutual_fund_trans.folio_no','td_mutual_fund_trans.product_code','td_mutual_fund_trans.pur_price','td_mutual_fund_trans.trans_date','td_mutual_fund_trans.trans_mode',
                'md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name','md_category.id as cat_id','md_subcategory.id as subcat_id',
                'md_amc.amc_short_name as amc_name','md_plan.plan_name as plan_name','md_option.opt_name as option_name')
                ->selectRaw('UCASE(td_mutual_fund_trans.first_client_name) as first_client_name,td_mutual_fund_trans.first_client_pan')
                ->selectRaw('IF(td_mutual_fund_trans.rnt_id=1,md_scheme_isin.isin_no,td_mutual_fund_trans.isin_no) as isin_no')
                ->selectRaw('sum(td_mutual_fund_trans.units) as tot_units')
                ->selectRaw('sum(td_mutual_fund_trans.amount) as tot_amount')
                ->selectRaw('sum(td_mutual_fund_trans.stamp_duty) as tot_stamp_duty')
                ->selectRaw('sum(td_mutual_fund_trans.tds) as tot_tds')
                ->selectRaw('count(*) as tot_rows')
                ->selectRaw('IF(td_mutual_fund_trans.rnt_id=1,
                (SELECT trans_type FROM md_mf_trans_type_subtype WHERE c_trans_type_code=td_mutual_fund_trans.trxn_type_code AND c_k_trans_type=td_mutual_fund_trans.trxn_type_flag AND c_k_trans_sub_type=td_mutual_fund_trans.trxn_nature_code limit 1),
                (CASE 
                    WHEN td_mutual_fund_trans.trans_flag="DP" || td_mutual_fund_trans.trans_flag="DR" THEN (SELECT trans_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=td_mutual_fund_trans.kf_trans_type AND k_divident_flag=td_mutual_fund_trans.trans_flag limit 1)
                    WHEN td_mutual_fund_trans.trans_flag="TO" THEN "Transfer Out"
                    ELSE (SELECT trans_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=td_mutual_fund_trans.kf_trans_type limit 1)
                END)
                )as transaction_type')
                ->selectRaw('IF(td_mutual_fund_trans.rnt_id=1,
                (SELECT trans_sub_type FROM md_mf_trans_type_subtype WHERE c_trans_type_code=td_mutual_fund_trans.trxn_type_code AND c_k_trans_type=td_mutual_fund_trans.trxn_type_flag AND c_k_trans_sub_type=td_mutual_fund_trans.trxn_nature_code limit 1),
                (CASE 
                    WHEN td_mutual_fund_trans.trans_flag="DP" || td_mutual_fund_trans.trans_flag="DR" THEN (SELECT trans_sub_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=td_mutual_fund_trans.kf_trans_type AND k_divident_flag=td_mutual_fund_trans.trans_flag limit 1)
                    WHEN td_mutual_fund_trans.trans_flag="TO" THEN "Transfer Out"
                    ELSE (SELECT trans_sub_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=td_mutual_fund_trans.kf_trans_type limit 1)
                END)
                )as transaction_subtype')
                ->where('td_mutual_fund_trans.delete_flag','N')
                ->where('td_mutual_fund_trans.amc_flag','N')
                ->where('td_mutual_fund_trans.scheme_flag','N')
                ->where('td_mutual_fund_trans.plan_option_flag','N')
                ->where('td_mutual_fund_trans.bu_type_flag','N')
                ->where('td_mutual_fund_trans.divi_mismatch_flag','N')
                ->where('td_mutual_fund_trans.portfolio_show_flag','Y')
                ->whereRaw($rawQuery)
                ->groupBy('td_mutual_fund_trans.folio_no')
                ->groupBy('td_mutual_fund_trans.product_code')
                ->groupBy('td_mutual_fund_trans.isin_no')
                ->orderBy('td_mutual_fund_trans.trans_date','ASC')
                ->get();
            // dd(DB::getQueryLog());
            // $all_data=DB::select("SELECT rnt_id,folio_no,scheme_name,cat_name,product_code,
            //     subcat_name,amc_name,plan_name,option_name,isin_no,nifty50,sensex,
            //     SUM(units) AS tot_units, 
            //     SUM(amount) AS inv_cost, 
            //     SUM(stamp_duty) AS tot_stamp_duty, 
            //     SUM(tds) AS tot_tds, 
            //     COUNT(*) AS tot_rows FROM `portfolio_report` 
            //     WHERE first_client_pan='".$pan_no."'
            //     and trans_date <='".$valuation_as_on."'
            //     GROUP BY scheme_name,cat_name,product_code,
            //     subcat_name,amc_name,plan_name,option_name,isin_no
            //     ORDER BY trans_date ASC");
            // dd(DB::getQueryLog());
            // return $all_data;
            $all_trans_product=[];
            $data=[];
            foreach ($all_data as $key => $value) {
                $value->inv_since=date('Y-m-d',strtotime($value->trans_date));
                $value->pur_nav=$value->pur_price;
                $f_trans_product="(nav_date=(SELECT MAX(nav_date) FROM td_nav_details WHERE product_code='".$value->product_code."' AND nav_date <='".$valuation_as_on."') AND product_code='".$value->product_code."')";
                array_push($all_trans_product,$f_trans_product);
                array_push($data,$value);
            }
            usort($data, function($a, $b) {
                return $a['scheme_name'] <=> $b['scheme_name'];
            });
            // return $data;
            $string_version_product_code = implode(',', $all_trans_product);
            // return $string_version_product_code;
            $res_array=[];
            if (count($data)>0) {
                $res_array =DB::connection('mysql_nav')
                ->select('SELECT product_code,isin_no,DATE_FORMAT(nav_date, "%Y-%m-%d") as nav_date,nav FROM td_nav_details where '.str_replace(",","  OR  ",$string_version_product_code));
            }
            // return $res_array;
            $filter_data=[];
            foreach ($data as $data_key => $value1) {
                $isin_no=$value1->isin_no;
                $product_code=$value1->product_code;
                $new='';
                if (count($res_array) > 0) {
                    foreach($res_array as $val_nav){
                        if($val_nav->product_code==$product_code){
                            $new=$val_nav;
                        }
                    }
                }
                // return $new;
                $value1->new=$new;
                $value1->curr_nav=isset($new->nav)?$new->nav:0;
                $value1->nav_date=isset($new->nav_date)?$new->nav_date:0;
                //calculation
                $mydata='';
                $foliotrans=$value1->foliotrans;
                // if ($value1->tot_amount > 0) {
                    $json  = json_encode($foliotrans);
                    $array = json_decode($json, true);
                    if (array_search('Consolidation In',array_column($array,'transaction_subtype'))) {
                        $foliotrans=TransHelper::ConsolidationInQuery($value1->rnt_id,$value1->folio_no,$value1->isin_no,$value1->product_code,$valuation_as_on);
                    }
                    $mydata=TransHelper::calculate($foliotrans,$value1->curr_nav,$valuation_as_on);
                // }
                // $mydata=$this->calculate($value1->foliotrans);
                $value1->mydata=$mydata;
                $value1->nifty50=isset($mydata['nifty50'])?(int)$mydata['nifty50']:$value1->nifty50;
                $value1->sensex=isset($mydata['sensex'])?(int)$mydata['sensex']:$value1->sensex;
                $value1->idcwp=0;
                $value1->idcw_reinv=isset($mydata['idcw_reinv'])? number_format((float)$mydata['idcw_reinv'], 2, '.', ''):0;
                $value1->idcwr=number_format((float)($value1->idcwp + $value1->idcw_reinv), 2, '.', '');
                $value1->inv_since=isset($mydata['inv_since'])? $mydata['inv_since']:$value1->inv_since;
                $value1->pur_nav=isset($mydata['pur_nav'])?$mydata['pur_nav']:$value1->pur_nav;
                $value1->transaction_type=isset($mydata['transaction_type'])?$mydata['transaction_type']:$value1->transaction_type;
                $value1->transaction_subtype=isset($mydata['transaction_subtype'])?$mydata['transaction_subtype']:$value1->transaction_subtype;
                $value1->inv_cost=isset($mydata['inv_cost'])?number_format((float)$mydata['inv_cost'], 2, '.', ''):0;
                $value1->tot_units=isset($mydata['tot_units'])?number_format((float)$mydata['tot_units'], 2, '.', ''):0;
                $value1->curr_val= number_format((float)($value1->curr_nav * $value1->tot_units), 2, '.', '');
                $value1->gain_loss=number_format((float)(($value1->curr_val - $value1->inv_cost) + $value1->idcwr), 2, '.', '');
                if ($value1->gain_loss==0 || $value1->inv_cost==0) {
                    $value1->ret_abs=0;
                }else {
                    $value1->ret_abs=number_format((float)(($value1->gain_loss / $value1->inv_cost) * 100), 2, '.', '');
                }
                
                array_push($filter_data,$value1);
            }

            // return $filter_data;
            // for Trans. Duration filter purpose
            if ($trans_duration) {
                $filter_data1=[];
                foreach ($filter_data as $filter_data_key => $filter_data_value) {
                    // return $filter_data_value;
                    if ($filter_data_value->curr_val > 0) {
                        $cal_purchase_data=$filter_data_value['mydata']['cal_purchase_data'];
                        // return $cal_purchase_data;
                        $re_cal_purchase_data=[];
                        switch ($trans_duration) {
                            case '< 1':
                                $from_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime($valuation_as_on)) . " -1 year"));
                                $to_date =$valuation_as_on;
                                foreach ($cal_purchase_data as $key => $cal_purchase_data_value) {
                                    if (strtotime($cal_purchase_data_value['trans_date']) >= strtotime($from_date) && strtotime($cal_purchase_data_value['trans_date']) <= strtotime($to_date)) {
                                        array_push($re_cal_purchase_data,$cal_purchase_data_value);
                                    }
                                }
                                break;
                            case '> 1':
                                $trans_duration_cal_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime($valuation_as_on)) . " -1 year"));
                                foreach ($cal_purchase_data as $key => $cal_purchase_data_value) {
                                    if (strtotime($cal_purchase_data_value['trans_date']) <= strtotime($trans_duration_cal_date)) {
                                        array_push($re_cal_purchase_data,$cal_purchase_data_value);
                                    }
                                }
                                break;
                            case '> 2':
                                $trans_duration_cal_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime($valuation_as_on)) . " -2 year"));
                                foreach ($cal_purchase_data as $key => $cal_purchase_data_value) {
                                    if (strtotime($cal_purchase_data_value['trans_date']) <= strtotime($trans_duration_cal_date)) {
                                        array_push($re_cal_purchase_data,$cal_purchase_data_value);
                                    }
                                }
                                break;
                            case '> 3':
                                $trans_duration_cal_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime($valuation_as_on)) . " -3 year"));
                                foreach ($cal_purchase_data as $key => $cal_purchase_data_value) {
                                    if (strtotime($cal_purchase_data_value['trans_date']) <= strtotime($trans_duration_cal_date)) {
                                        array_push($re_cal_purchase_data,$cal_purchase_data_value);
                                    }
                                }
                                break;
                            case '> 4':
                                $trans_duration_cal_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime($valuation_as_on)) . " -4 year"));
                                foreach ($cal_purchase_data as $key => $cal_purchase_data_value) {
                                    if (strtotime($cal_purchase_data_value['trans_date']) <= strtotime($trans_duration_cal_date)) {
                                        array_push($re_cal_purchase_data,$cal_purchase_data_value);
                                    }
                                }
                                break;
                            case '> 5':
                                $trans_duration_cal_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime($valuation_as_on)) . " -5 year"));
                                foreach ($cal_purchase_data as $key => $cal_purchase_data_value) {
                                    if (strtotime($cal_purchase_data_value['trans_date']) <= strtotime($trans_duration_cal_date)) {
                                        array_push($re_cal_purchase_data,$cal_purchase_data_value);
                                    }
                                }
                                break;
                            case '> 7':
                                $trans_duration_cal_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime($valuation_as_on)) . " -7 year"));
                                foreach ($cal_purchase_data as $key => $cal_purchase_data_value) {
                                    if (strtotime($cal_purchase_data_value['trans_date']) <= strtotime($trans_duration_cal_date)) {
                                        array_push($re_cal_purchase_data,$cal_purchase_data_value);
                                    }
                                }
                                break;
                            case '> 10':
                                $trans_duration_cal_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime($valuation_as_on)) . " -10 year"));
                                foreach ($cal_purchase_data as $key => $cal_purchase_data_value) {
                                    if (strtotime($cal_purchase_data_value['trans_date']) <= strtotime($trans_duration_cal_date)) {
                                        array_push($re_cal_purchase_data,$cal_purchase_data_value);
                                    }
                                }
                                break;
                            case 'D':
                                $trans_date_range=$request->trans_date_range;
                                $from_date=Carbon::parse(str_replace('/','-',explode("-",$trans_date_range)[0]))->format('Y-m-d') ;
                                $to_date=Carbon::parse(str_replace('/','-',explode("-",$trans_date_range)[1]))->format('Y-m-d') ;
                                // return $to_date;
                                foreach ($cal_purchase_data as $key => $cal_purchase_data_value) {
                                    if (strtotime($cal_purchase_data_value['trans_date']) >= strtotime($from_date) && strtotime($cal_purchase_data_value['trans_date']) <= strtotime($to_date)) {
                                        array_push($re_cal_purchase_data,$cal_purchase_data_value);
                                    }
                                }
                                break;
                            default:
                                break;
                        }
                        // return $re_cal_purchase_data;
                        // $filter_data_value['mydata']=['cal_purchase_data' => $re_cal_purchase_data];
                        $re_cal_purchase_datas=[];
                        $re_cal_purchase_datas['re_cal_purchase_data']=$re_cal_purchase_data;
                        $inv_cost=0;                    
                        $idcwp=0;
                        $idcw_reinv=0;
                        $tot_units=0;
                        foreach ($re_cal_purchase_data as $key => $re_cal_purchase_data_value) {
                            if ($re_cal_purchase_data_value['cumml_units'] > 0) {
                                if (strpos($re_cal_purchase_data_value['transaction_subtype'], 'Dividend Reinvestment')!== false) {
                                    $idcw_reinv +=number_format((float)$re_cal_purchase_data_value['tot_amount'], 2, '.', '');
                                }
                                if (strpos($re_cal_purchase_data_value['transaction_subtype'], 'Dividend Payout')!== false) {
                                    $idcwp +=number_format((float)$re_cal_purchase_data_value['tot_amount'], 2, '.', '');
                                }
                                $inv_cost +=number_format((float)$re_cal_purchase_data_value['tot_amount'], 2, '.', '');
                            }
                            $tot_units +=number_format((float)$re_cal_purchase_data_value['tot_units'], 4, '.', '');
                        }
                        $re_cal_purchase_datas['idcwp']=$idcwp;
                        $re_cal_purchase_datas['idcw_reinv']=$idcw_reinv;
                        $re_cal_purchase_datas['idcwr']=number_format((float)($re_cal_purchase_datas['idcwp'] + $re_cal_purchase_datas['idcw_reinv']), 2, '.', '');
                        $re_cal_purchase_datas['inv_cost']=number_format((float)$inv_cost, 2, '.', '');
                        $re_cal_purchase_datas['tot_units']=number_format((float)$tot_units, 4, '.', '');
                        $re_cal_purchase_datas['curr_val']= number_format((float)($filter_data_value['curr_nav'] * $re_cal_purchase_datas['tot_units']), 2, '.', '');
                        $re_cal_purchase_datas['gain_loss']=number_format((float)(($re_cal_purchase_datas['curr_val'] - $re_cal_purchase_datas['inv_cost']) + $re_cal_purchase_datas['idcwr']), 2, '.', '');
                        if ($re_cal_purchase_datas['gain_loss']==0 || $re_cal_purchase_datas['inv_cost']==0) {
                            $re_cal_purchase_datas['ret_abs']=0;
                        }else {
                            $re_cal_purchase_datas['ret_abs']=number_format((float)(($re_cal_purchase_datas['gain_loss'] / $re_cal_purchase_datas['inv_cost']) * 100), 2, '.', '');
                        }

                        // return $re_cal_purchase_datas;
                        $filter_data_value['re_cal_purchase_datas']=$re_cal_purchase_datas;
                        array_push($filter_data1,$filter_data_value);
                    }else {
                        array_push($filter_data1,$filter_data_value);
                    }
                }
                // return $filter_data1;
                $filter_data=$filter_data1;
            }
            // for Trans. Duration filter purpose

            $disclaimer=Disclaimer::select('dis_des','font_size','color_code')->find(1);
            if ($view_type=='F') {
                $grouped_types=[];
                foreach($filter_data as $type){
                    $grouped_types[$type['first_client_name']][] = $type;
                }
                // return $grouped_types;
                $filter_data=$grouped_types;
            }
            $mydata=[];
            $mydata['client_details']=$client_details;
            $mydata['data']=$filter_data;
            $mydata['valuation_as_on']=$valuation_as_on;
            $mydata['disclaimer']=$disclaimer;
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($mydata);
    }
}