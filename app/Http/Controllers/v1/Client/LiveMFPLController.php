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
use App\Http\Controllers\V1\Client\LiveMFPController;

class LiveMFPLController extends Controller
{
    public function search(Request $request)
    {
        try {
            // return $request;
            $valuation_as_on=$request->valuation_as_on;
            $view_type=$request->view_type;
            $pan_no=$request->pan_no;
            $client_name=$request->client_name;

            session()->forget('valuation_as_on');
            session(['valuation_as_on' => $valuation_as_on]);
            // return Session::get('valuation_as_on');
            $client_details='';
            if ($view_type || $valuation_as_on) {
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
                    $client_details=Client::whereRaw($client_rawQuery)->first();
                }else {
                    $queryString='td_mutual_fund_trans.first_client_pan';
                    $condition=(strlen($rawQuery) > 0)? " AND (":" (";
                    $row_name_string=  "'" .implode("','", $family_members_pan). "'";
                    $rawQuery.=$condition.$queryString." IN (".$row_name_string.")";
                    $queryString='td_mutual_fund_trans.first_client_name';
                    $condition1=(strlen($rawQuery) > 0)? " OR ":" ";
                    $row_name_string1=  "'" .implode("','", $family_members_name). "'";
                    $rawQuery.=$condition1.$queryString." IN (".$row_name_string1."))";
                }
            } 
            // return $rawQuery;
            // return $client_details;
            // DB::enableQueryLog();
            $all_data=MutualFundTransaction::with('profitloss')->leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_mutual_fund_trans.product_code')
                ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
                ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                ->leftJoin('md_amc','md_amc.amc_code','=','td_mutual_fund_trans.amc_code')
                ->leftJoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
                ->leftJoin('md_option','md_option.id','=','md_scheme_isin.option_id')
                ->select('td_mutual_fund_trans.rnt_id','td_mutual_fund_trans.folio_no','td_mutual_fund_trans.product_code','td_mutual_fund_trans.pur_price','td_mutual_fund_trans.trans_date',
                'md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name','md_amc.amc_short_name as amc_name',
                'md_plan.plan_name as plan_name','md_option.opt_name as option_name')
                ->selectRaw('IF(td_mutual_fund_trans.rnt_id=1,md_scheme_isin.isin_no,td_mutual_fund_trans.isin_no) as isin_no')
                ->selectRaw('sum(td_mutual_fund_trans.units) as tot_units')
                ->selectRaw('sum(td_mutual_fund_trans.amount) as tot_amount')
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
                // ->orderBy('md_scheme.scheme_name','ASC')
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
            $res_array =DB::connection('mysql_nav')
                ->select('SELECT product_code,isin_no,DATE_FORMAT(nav_date, "%Y-%m-%d") as nav_date,nav FROM td_nav_details where '.str_replace(",","  OR  ",$string_version_product_code));
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
                $profitloss=$value1->profitloss;
                $value1->profitloss=$profitloss;
                $purchase=0;
                $switch_in=0;
                $tot_inflow=0;
                $redemption=0;
                $switch_out=0;
                $tot_outflow=0;
                if ($value1->tot_amount > 0) {
                    foreach ($profitloss as $key => $profitloss_value) {
                        if ($profitloss_value->lmf_pl=='PL_P') {
                            $purchase +=$profitloss_value->tot_amount;
                        }elseif ($profitloss_value->lmf_pl=='PL_R') {
                            $redemption +=$profitloss_value->tot_amount;
                        }elseif ($profitloss_value->lmf_pl=='PL_SI') {
                            $switch_in +=$profitloss_value->tot_amount;
                        }elseif ($profitloss_value->lmf_pl=='PL_SO') {
                            $switch_out +=$profitloss_value->tot_amount;
                        }
                    }
                }
                
                $value1->purchase=$purchase;
                $value1->redemption=$redemption;
                $value1->switch_in=$switch_in;
                $value1->switch_out=$switch_out;
                $value1->tot_inflow=($purchase + $switch_in);
                $value1->tot_outflow=($redemption + $switch_out);

                $mydata='';
                if ($value1->tot_amount > 0) {
                    // return $profitloss;
                    $mydata=$this->calculate($profitloss);
                    // $mydata=TransHelper::calculate($profitloss);
                    // return $mydata;
                }
                $value1->mydata=$mydata;
                $value1->tot_units=isset($mydata['tot_units'])?number_format((float)$mydata['tot_units'], 4, '.', ''):0;
                $value1->curr_val=$value1->curr_nav * $value1->tot_units;
                array_push($filter_data,$value1);
            }
            
            $mydata=[];
            $mydata['client_details']=$client_details;
            $mydata['data']=$filter_data;
            $mydata['valuation_as_on']=$valuation_as_on;
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($mydata);
    }

    public function calculate($foliotrans){
        $purchase_data=[];
        $redemption_data=[];
        $purchase_amt_arr=[];
        $redemption_amt_arr=[];
        $all_amt_arr=[];
        $all_date_arr=[];
        // *******************************************Start CAMS Broker Change Data**********************************************************
        if ($foliotrans[0]['rnt_id']==1 && $foliotrans[0]['transaction_type']=='Transfer In' && $foliotrans[0]['transaction_subtype']=='Transfer In') {
            $final_foliotrans=[];
            foreach ($foliotrans as $key => $foliotrans_value) {
                if ($foliotrans_value->transaction_type=="Transfer In" && $foliotrans_value->transaction_subtype=="Transfer In") {
                    // return $foliotrans_value;
                    $broker_data=$this->getBrokerData($foliotrans_value);
                    // return $broker_data;
                    foreach ($broker_data as $key => $broker_data_value) {
                        array_push($final_foliotrans,$broker_data_value);
                    }
                }else {
                    array_push($final_foliotrans,$foliotrans_value);
                }
            }
            $foliotrans=$final_foliotrans;
        }
        // *******************************************End CAMS Broker Change Data**********************************************************

        foreach ($foliotrans as $key => $value) {
            if(strpos($value->transaction_subtype, 'Purchase' )!== false || strpos($value->transaction_subtype, 'Switch In' )!== false ) {
                if ($key > 0) {
                    $value->cumml_units=number_format((float)($value->tot_units + $foliotrans[($key-1)]->cumml_units) , 4, '.', '') ;
                }else {
                    $value->cumml_units=$value->tot_units;
                }
                array_push($purchase_data,$value);
                // array_push($purchase_amt_arr,$value->tot_amount);
                array_push($all_amt_arr,-$value->tot_amount);
                array_push($all_date_arr,$value->trans_date);
            }elseif (strpos($value->transaction_subtype, 'Redemption' )!== false || strpos($value->transaction_subtype, 'Switch Out' )!== false) {
                $value->cumml_units=0;
                array_push($redemption_data,$value);
                // array_push($redemption_amt_arr,$value->tot_amount);
                array_push($all_amt_arr,$value->tot_amount);
                array_push($all_date_arr,$value->trans_date);
            }
        }

        $inv_cost=0;
        if (count($redemption_data) > 0) {
            /*******************************************start purchase and redemption case******************************************/
            foreach ($redemption_data as $redemption_key => $redemption_value) {
                $rdm_tot_units=number_format((float)$redemption_value->tot_units, 4, '.', '');
                $deduct_unit_array=[];
                $flag='Y';
                foreach ($purchase_data as $purchase_key => $purchase_value) {
                    if ($purchase_value['cumml_units'] >= 0) {
                        $purchase_cumml_units=number_format((float)$purchase_value['cumml_units'], 4, '.', '');
                        $purchase_value['cumml_units']=$purchase_cumml_units - $rdm_tot_units;
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
                                $newarr['transaction_type']="Remaining";
                                $newarr['transaction_subtype']="Remaining";
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
                                    $newarr['transaction_type']="Remaining";
                                    $newarr['transaction_subtype']="Remaining";
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
            /*******************************************end purchase and redemption case******************************************/
            // $final_array=array_merge($deduct_unit_array,$purchase_data);
            // return $final_array;
            // $final_data_arr=[];
            $inv_cost=0;
            foreach ($purchase_data as $key => $value) {
                if ($value['cumml_units'] > 0) {
                    $inv_cost +=number_format((float)$value['tot_amount'], 2, '.', '');
                }
            }
        }else {
            // $inv_cost=0;
            foreach ($purchase_data as $key => $value) {
                $inv_cost +=number_format((float)$value['tot_amount'], 2, '.', '');
            }
        }
        // return $all_amt_arr;
        // return $all_date_arr;
        $ck=[];
        $ck['inv_cost']=$inv_cost;
        $ck['tot_units']=(count($purchase_data) > 0)?$purchase_data[(count($purchase_data) - 1)]['cumml_units']:0;
        $ck['all_amt_arr']=$all_amt_arr;
        $ck['all_date_arr']=$all_date_arr;
        return $ck;
    }

    public function getBrokerData($foliotrans_value)
    {
        $rawInnerQuery='';
        $queryString='tt_broker_change_trans_report.folio_no';
        $rawInnerQuery.=Helper::WhereRawQuery($foliotrans_value->folio_no,$rawInnerQuery,$queryString);
        $queryString='tt_broker_change_trans_report.product_code';
        $rawInnerQuery.=Helper::WhereRawQuery($foliotrans_value->product_code,$rawInnerQuery,$queryString);
        // return $rawInnerQuery;

        $broker_data=BrokerChangeTransReport::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','tt_broker_change_trans_report.product_code')
            ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
            ->select('tt_broker_change_trans_report.rnt_id','tt_broker_change_trans_report.folio_no','tt_broker_change_trans_report.product_code',
            'tt_broker_change_trans_report.isin_no','tt_broker_change_trans_report.trans_date','tt_broker_change_trans_report.trxn_type',
            'tt_broker_change_trans_report.trxn_type_flag','tt_broker_change_trans_report.trxn_nature','tt_broker_change_trans_report.amount',
            'tt_broker_change_trans_report.stamp_duty','tt_broker_change_trans_report.tds','tt_broker_change_trans_report.units','tt_broker_change_trans_report.pur_price',
            'tt_broker_change_trans_report.trans_no',
            'md_scheme.scheme_name as scheme_name')
            ->selectRaw('sum(amount) as tot_amount')
            ->selectRaw('sum(stamp_duty) as tot_stamp_duty')
            ->selectRaw('sum(tds) as tot_tds')
            ->selectRaw('count(*) as tot_rows')
            ->selectRaw('(SELECT trans_type FROM md_mf_trans_type_subtype WHERE c_trans_type_code=tt_broker_change_trans_report.trxn_type_code AND c_k_trans_type=tt_broker_change_trans_report.trxn_type_flag AND c_k_trans_sub_type=tt_broker_change_trans_report.trxn_nature_code limit 1)as transaction_type')
            ->selectRaw('(SELECT trans_sub_type FROM md_mf_trans_type_subtype WHERE c_trans_type_code=tt_broker_change_trans_report.trxn_type_code AND c_k_trans_type=tt_broker_change_trans_report.trxn_type_flag AND c_k_trans_sub_type=tt_broker_change_trans_report.trxn_nature_code limit 1)as transaction_subtype')
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