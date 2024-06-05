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
                        $queryString='td_mutual_fund_trans.trans_date';
                        $condition=(strlen($rawQuery) > 0)? " AND ":" ";
                        $rawQuery.=$condition.$queryString." >= '". date('Y-m-d',strtotime($start_date))."'";
                        $rawQuery.=" AND ".$queryString." <= '". date('Y-m-d',strtotime($end_date))."'";
                        // $rawQuery.=Helper::FrmToDateRawQuery($start_date,$end_date,$rawQuery,$queryString);
                        // return $rawQuery;
                    }
                } else {
                    return $date_range;
                }
            } 
            session()->forget('start_date');
            session()->forget('end_date');
            session(['start_date' => $start_date]);
            session(['end_date' => $end_date]);
            // return $rawQuery;
            // DB::enableQueryLog();
            $all_data=MutualFundTransaction::with('capitalgainloss')->leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_mutual_fund_trans.product_code')
                ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
                ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                ->leftJoin('md_amc','md_amc.amc_code','=','td_mutual_fund_trans.amc_code')
                ->leftJoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
                ->leftJoin('md_option','md_option.id','=','md_scheme_isin.option_id')
                ->select('td_mutual_fund_trans.rnt_id','td_mutual_fund_trans.folio_no','td_mutual_fund_trans.product_code','td_mutual_fund_trans.pur_price','td_mutual_fund_trans.trans_date','td_mutual_fund_trans.trans_mode',
                'md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name','md_amc.amc_short_name as amc_name',
                'md_plan.plan_name as plan_name','md_option.opt_name as option_name')
                ->selectRaw('IF(td_mutual_fund_trans.rnt_id=1,md_scheme_isin.isin_no,td_mutual_fund_trans.isin_no) as isin_no')
                ->selectRaw('sum(td_mutual_fund_trans.units) as tot_units')
                ->selectRaw('sum(td_mutual_fund_trans.amount) as tot_amount')
                ->selectRaw('sum(td_mutual_fund_trans.stamp_duty) as tot_stamp_duty')
                ->selectRaw('sum(td_mutual_fund_trans.tds) as tot_tds')
                ->selectRaw('count(*) as tot_rows')
                ->selectRaw('(select close from td_benchmark_scheme where benchmark=1 AND date=td_mutual_fund_trans.trans_date) as nifty50')
                ->selectRaw('(select close from td_benchmark_scheme where benchmark=70 AND date=td_mutual_fund_trans.trans_date) as sensex')
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
                ->groupBy('td_mutual_fund_trans.folio_no')
                ->groupBy('td_mutual_fund_trans.product_code')
                ->groupBy('td_mutual_fund_trans.isin_no')
                ->orderBy('td_mutual_fund_trans.trans_date','ASC')
                ->get();
            // dd(DB::getQueryLog());
            // return $all_data;
            $data=[];
            foreach ($all_data as $key_001 => $value_001) {
                $capitalgainloss=$value_001->capitalgainloss;
            
                $all_process_data=[];
                $all_rejection_data=[];
                foreach ($capitalgainloss as $key1 => $value1) {
                    if ($value1->rnt_id==1 && $value1->transaction_type=="Transfer In" && $value1->transaction_subtype=="Transfer In") {
                        $broker_data=$this->getBrokerData($value1,$start_date,$end_date);
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
                $purchase_data=[];
                $redemption_data=[];
                $all_amt_arr=[];
                $all_date_arr=[];
                foreach ($all_process_data as $key => $value) {
                    if(strpos($value->transaction_subtype, 'Purchase' )!== false || strpos($value->transaction_subtype, 'Switch In' )!== false 
                        || strpos($value->transaction_subtype, 'Dividend Reinvestment')!== false || strpos($value->transaction_subtype, 'STP In')!== false) {
                        if ($key > 0) {
                            $value->cumml_units=number_format((float)($value->tot_units + $all_process_data[($key-1)]->cumml_units) , 4, '.', '') ;
                        }else {
                            $value->cumml_units=$value->tot_units;
                        }
                        array_push($purchase_data,$value);
                        array_push($all_amt_arr,-$value->tot_amount);
                        array_push($all_date_arr,$value->trans_date);
                    } else if (strpos($value->transaction_subtype, 'Redemption' )!== false || strpos($value->transaction_subtype, 'Switch Out' )!== false 
                        || strpos($value->transaction_subtype, 'Transfer Out')!== false || strpos($value->transaction_subtype, 'SWP')!== false
                        || strpos($value->transaction_subtype, 'STP Out')!== false) {
                        $value->cumml_units=0;
                        array_push($redemption_data,$value);
                        array_push($all_amt_arr,$value->tot_amount);
                        array_push($all_date_arr,$value->trans_date);
                    }
                }
                
                $value_001->for_checking="test_data";
                array_push($data,$value_001);
            }

            // $all_trans_product=[];
            // $data=[];
            // foreach ($all_data as $key => $value) {
            //     $value->inv_since=date('Y-m-d',strtotime($value->trans_date));
            //     $value->pur_nav=$value->pur_price;
            //     $f_trans_product="(nav_date=(SELECT MAX(nav_date) FROM td_nav_details WHERE product_code='".$value->product_code."' AND nav_date <='".$valuation_as_on."') AND product_code='".$value->product_code."')";
            //     array_push($all_trans_product,$f_trans_product);
            //     array_push($data,$value);
            // }
            // usort($data, function($a, $b) {
            //     return $a['scheme_name'] <=> $b['scheme_name'];
            // });
            // // return $data;
            // $string_version_product_code = implode(',', $all_trans_product);
            // // return $string_version_product_code;
            // $res_array=[];
            // if (count($data)>0) {
            //     $res_array =DB::connection('mysql_nav')
            //     ->select('SELECT product_code,isin_no,DATE_FORMAT(nav_date, "%Y-%m-%d") as nav_date,nav FROM td_nav_details where '.str_replace(",","  OR  ",$string_version_product_code));
            // }
            // // return $res_array;
            // $filter_data=[];
            // foreach ($data as $data_key => $value1) {
            //     $isin_no=$value1->isin_no;
            //     $product_code=$value1->product_code;
            //     $new='';
            //     if (count($res_array) > 0) {
            //         foreach($res_array as $val_nav){
            //             if($val_nav->product_code==$product_code){
            //                 $new=$val_nav;
            //             }
            //         }
            //     }
            //     // return $new;
            //     $value1->new=$new;
            //     $value1->curr_nav=isset($new->nav)?$new->nav:0;
            //     $value1->nav_date=isset($new->nav_date)?$new->nav_date:0;
            //     //calculation
            //     $mydata='';
            //     $foliotrans=$value1->foliotrans;
            //     if ($value1->tot_amount > 0) {
            //         $json  = json_encode($foliotrans);
            //         $array = json_decode($json, true);
            //         if (array_search('Consolidation In',array_column($array,'transaction_subtype'))) {
            //             $foliotrans=TransHelper::ConsolidationInQuery($value1->rnt_id,$value1->folio_no,$value1->isin_no,$value1->product_code,$valuation_as_on);
            //         }
            //         $mydata=TransHelper::calculate($foliotrans);
            //     }
            //     // $mydata=$this->calculate($value1->foliotrans);
            //     $value1->mydata=$mydata;
            //     $value1->nifty50=isset($mydata['nifty50'])?$mydata['nifty50']:$value1->nifty50;
            //     $value1->sensex=isset($mydata['sensex'])?$mydata['sensex']:$value1->sensex;
            //     $value1->idcwp=0;
            //     $value1->idcw_reinv=isset($mydata['idcw_reinv'])? number_format((float)$mydata['idcw_reinv'], 2, '.', ''):0;
            //     $value1->idcwr=number_format((float)($value1->idcwp + $value1->idcw_reinv), 2, '.', '');
            //     $value1->inv_since=isset($mydata['inv_since'])? $mydata['inv_since']:$value1->inv_since;
            //     $value1->pur_nav=isset($mydata['pur_nav'])?$mydata['pur_nav']:$value1->pur_nav;
            //     $value1->transaction_type=isset($mydata['transaction_type'])?$mydata['transaction_type']:$value1->transaction_type;
            //     $value1->transaction_subtype=isset($mydata['transaction_subtype'])?$mydata['transaction_subtype']:$value1->transaction_subtype;
            //     $value1->inv_cost=isset($mydata['inv_cost'])?number_format((float)$mydata['inv_cost'], 2, '.', ''):0;
            //     $value1->tot_units=isset($mydata['tot_units'])?number_format((float)$mydata['tot_units'], 2, '.', ''):0;
            //     $value1->curr_val= number_format((float)($value1->curr_nav * $value1->tot_units), 2, '.', '');
            //     $value1->gain_loss=number_format((float)(($value1->curr_val - $value1->inv_cost) + $value1->idcwr), 2, '.', '');
            //     if ($value1->gain_loss==0 || $value1->inv_cost==0) {
            //         $value1->ret_abs=0;
            //     }else {
            //         $value1->ret_abs=number_format((float)(($value1->gain_loss / $value1->inv_cost) * 100), 2, '.', '');
            //     }
            //     array_push($filter_data,$value1);
            // }
            
            $mydata=[];
            $mydata['client_details']=$client_details;
            $mydata['data']=$all_data;
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($mydata);
    }

    public static function getBrokerData($foliotrans_value,$start_date,$end_date)
    {
        $rawInnerQuery='';
        $queryString='tt_broker_change_trans_report.folio_no';
        $rawInnerQuery.=Helper::WhereRawQuery($foliotrans_value->folio_no,$rawInnerQuery,$queryString);
        $queryString='tt_broker_change_trans_report.product_code';
        $rawInnerQuery.=Helper::WhereRawQuery($foliotrans_value->product_code,$rawInnerQuery,$queryString);
        // return $rawInnerQuery;
        $queryString='td_mutual_fund_trans.trans_date';
        $condition=(strlen($rawInnerQuery) > 0)? " AND ":" ";
        $rawInnerQuery.=$condition.$queryString." >= '". date('Y-m-d',strtotime($start_date))."'";
        $rawInnerQuery.=" AND ".$queryString." <= '". date('Y-m-d',strtotime($end_date))."'";

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