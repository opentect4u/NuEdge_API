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
    NAVDetailsSec
};
use Validator;
use Illuminate\Support\Carbon;
use Excel;
use App\Helpers\TransHelper;
use DB;
use Session;

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
            $all_data=MutualFundTransaction::with('foliotrans')->leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_mutual_fund_trans.product_code')
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
                ->selectRaw('(select close from td_benchmark_scheme where benchmark=1 AND DATE(date)=DATE(td_mutual_fund_trans.trans_date)) as nifty50')
                ->selectRaw('(select close from td_benchmark_scheme where benchmark=70 AND DATE(date)=DATE(td_mutual_fund_trans.trans_date)) as sensex')
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
                $mydata='';
                $foliotrans=$value1->foliotrans;

                // if ($foliotrans[(count($foliotrans)-1)]->transaction_subtype=='Full Redemption' || $data_key==19) {
                // }else {
                //     $mydata=$this->calculate($value1->foliotrans);
                // // }
                // if ($value1->product_code=='D110' && $value1->rnt_id=1) {
                //     $mydata=$this->calculate($value1->foliotrans);
                // }
                // if ($data_key==10 || $data_key==3) {
                //     // return $foliotrans;
                //     $mydata=$this->calculate($foliotrans);
                // }
                if ($value1->tot_amount > 0) {
                    $mydata=$this->calculate($foliotrans);
                }
                // $mydata=$this->calculate($value1->foliotrans);
                $value1->mydata=$mydata;
                $value1->inv_cost=isset($mydata['inv_cost'])?number_format((float)$mydata['inv_cost'], 2, '.', ''):0;
                $value1->tot_units=isset($mydata['tot_units'])?$mydata['tot_units']:0;
                $value1->curr_val=$value1->curr_nav * $value1->tot_units;
                $value1->gain_loss=$value1->curr_val - $value1->inv_cost;
                if ($value1->gain_loss==0 || $value1->inv_cost==0) {
                    $value1->ret_abs=0;
                }else {
                    $value1->ret_abs=($value1->gain_loss / $value1->inv_cost) * 100;
                }
                $value1->idcw_reinv=0;
                $value1->idcwp=0;
                $value1->idcwr=0;
                $value1->xirr=0;
                $value1->trans_mode=0;
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

    public function search1(Request $request)
    {
        try {
            // return $request;
            $valuation_as_on= "2024-03-14";
            $client_name= "Tanmay Sarkar";
            $pan_no= "BPPPS4831C";
            $view_type= "C";
            // trans_type: A
            // view_funds_type: A
            // $valuation_as_on=$request->valuation_as_on;
            // $view_type=$request->view_type;
            // $pan_no=$request->pan_no;
            // $client_name=$request->client_name;

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
            $all_data=MutualFundTransaction::with('foliotrans')->leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_mutual_fund_trans.product_code')
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
                ->selectRaw('(select close from td_benchmark_scheme where benchmark=1 AND DATE(date)=DATE(td_mutual_fund_trans.trans_date)) as nifty50')
                ->selectRaw('(select close from td_benchmark_scheme where benchmark=70 AND DATE(date)=DATE(td_mutual_fund_trans.trans_date)) as sensex')
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
                $mydata='';
                $foliotrans=$value1->foliotrans;

                // if ($foliotrans[(count($foliotrans)-1)]->transaction_subtype=='Full Redemption' || $data_key==19) {
                // }else {
                //     $mydata=$this->calculate($value1->foliotrans);
                // // }
                // if ($value1->product_code=='D110' && $value1->rnt_id=1) {
                //     $mydata=$this->calculate($value1->foliotrans);
                // }
                if ($data_key==10) {
                    // return $value1;
                    // return $foliotrans;
                    // $mydata=$this->calculate($foliotrans);
                    // return $mydata;
                    $purchase_data=[];
                    $redemption_data=[];
                    $purchase_amt_arr=[];
                    $redemption_amt_arr=[];
                    foreach ($foliotrans as $key => $value) {
                        if(strpos($value->transaction_subtype, 'Purchase' )!== false ) {
                            if ($key > 0) {
                                $value->cumml_units=number_format((float)($value->tot_units + $foliotrans[($key-1)]->cumml_units) , 4, '.', '') ;
                            }else {
                                $value->cumml_units=$value->tot_units;
                            }
                            array_push($purchase_data,$value);
                            array_push($purchase_amt_arr,$value->tot_amount);
                        }elseif (strpos($value->transaction_subtype, 'Redemption' )!== false ) {
                            $value->cumml_units=0;
                            array_push($redemption_data,$value);
                            array_push($redemption_amt_arr,$value->tot_amount);
                        }
                    }
                    // return $redemption_data;
                    // return $purchase_data;

                    /*******************************************start purchase and redemption case******************************************/
                    $deduct_unit_array=[];

                    // foreach ($redemption_data as $key => $redemption_value) {
                    //     $rdm_tot_units=$redemption_value->tot_units;
                    //     // return $rdm_tot_units;
                    //     // df 360.542
                    //     // $rdm_tot_units=336.338;
                    //     // $rdm_tot_units=336.338;  // 0
                    //     // $rdm_tot_units=330.338;  // -4
                        
                    //     $after_remaining_array=[];
                    //     $flag='Y';
                    //     foreach ($purchase_data as $purchase_key => $purchase_value) {
                    //         // 336.338  tot_unit
                    //         // return $rdm_tot_units;
                    //         // return $purchase_value;
                    //         // if ( $key==7) {
                    //         //     return $purchase_data;
                    //         //     # code...
                    //         // }
                            
                    //         $purchase_cumml_units=number_format((float)$purchase_value['cumml_units'], 4, '.', '');
                            
                    //         $purchase_value['cumml_units']=(number_format((float)$purchase_cumml_units, 4, '.', '') - number_format((float)$rdm_tot_units, 4, '.', ''));
                    //         // if ($rdm_tot_units >= 0) {
                    //         //     // $rdm_tot_units=(number_format((float)$purchase_cumml_units, 4, '.', '') - number_format((float)$rdm_tot_units, 4, '.', ''));
                    //         //     $purchase_value['cumml_units']=(number_format((float)$purchase_cumml_units, 4, '.', '') - number_format((float)$rdm_tot_units, 4, '.', ''));
                    //         // }else {
                    //         //     $rdm_tot_units=(number_format((float)$purchase_cumml_units, 4, '.', '') + number_format((float)$rdm_tot_units, 4, '.', ''));
                    //         //     $rdm_tot_units=(number_format((float)$purchase_cumml_units, 4, '.', '') + number_format((float)$rdm_tot_units, 4, '.', ''));
                    //         //     =
                    //         // }
                    //         // return $rdm_tot_units;
                    //         // if ( $key==7) {
                    //         //     return $purchase_data;
                    //         //     # code...
                    //         // }

                    //         if ($purchase_value['cumml_units'] >=0) {
                    //             // return 'if';
                    //             // return $flag; 
                    //             if ($flag=='Y') {
                    //                 $set_units=$purchase_value['cumml_units'];
                    //                 $newarr['trans_date']=$purchase_value['trans_date'];
                    //                 $newarr['pur_price']=$purchase_value['pur_price'];
                    //                 $newarr['transaction_type']="Remaining";
                    //                 $newarr['transaction_subtype']="Remaining";
                    //                 $newarr['tot_units']=$set_units;
                    //                 $newarr['cumml_units']=$set_units;
                    //                 $newarr['tot_amount']= number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
                    //                 $newarr['gross_amount']=number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
                    //                 array_push($after_remaining_array,$newarr);
                    //                 $flag='N';
                    //             }else {
                    //                 array_push($after_remaining_array,$purchase_value);
                    //             }
                    //         }else {
                    //             // return 'else';
                    //             $deduct_unit_array=[...$deduct_unit_array,$purchase_value];
                    //             # code...
                    //         }
                            
                    //         // $purchase_cumml_units=number_format((float)$purchase_value['cumml_units'], 4, '.', '');
                    //         // $purchase_value['cumml_units']=(number_format((float)$purchase_cumml_units, 4, '.', '') - number_format((float)$rdm_tot_units, 4, '.', ''));
                    //         // // return $purchase_value['cumml_units'];
                    //         // if ($purchase_value['cumml_units']==0) {
                    //         //     // return 'if';
                    //         //     $set_units=$purchase_value['cumml_units'];
                    //         //     $purchase_value['cumml_units']=$set_units;
                    //         //     $deduct_unit_array=[...$deduct_unit_array,$purchase_value];
                    //         //     $rdm_tot_units=0;
                    //         //     $newarr=[];
                    //         //     // $newarr['id']=$purchase_value['id'];
                    //         //     $newarr['trans_date']=$purchase_value['trans_date'];
                    //         //     $newarr['pur_price']=$purchase_value['pur_price'];
                    //         //     $newarr['transaction_type']="Remaining";
                    //         //     $newarr['transaction_subtype']="Remaining";
                    //         //     $newarr['tot_units']=$set_units;
                    //         //     $newarr['cumml_units']=$set_units;
                    //         //     $newarr['tot_amount']= number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
                    //         //     $newarr['gross_amount']=number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
                    //         //     array_push($after_remaining_array,$newarr);
                    //         //     // return $deduct_unit_array;
                    //         //     // return $after_remaining_array;
                    //         // }else if ($purchase_value['cumml_units'] < 0) {
                    //         //     // return 'if1';
                    //         //     // $purchase_value['cumml_units']=$purchase_value['tot_units'] + $after_remaining_array[(count($after_remaining_array)-1)]['cumml_units'] ;
                    //         //     // array_push($after_remaining_array,$purchase_value);
                    //         //     $deduct_unit_array=[...$deduct_unit_array,$purchase_value];
                    //         //     // return $deduct_unit_array;
                    //         // }else if ($purchase_value['cumml_units'] > 0) {
                    //         //     // if ( $key==7) {
                    //         //     //     # code...
                    //         //     //     return $purchase_data;
                    //         //     // }
                    //         //     // return 'if2';
                    //         //     // return $purchase_cumml_units .'  ---  '.number_format((float)$rdm_tot_units, 4, '.', '');
                    //         //     // return $purchase_value['cumml_units'];
                    //         //     if ($flag=='Y') {
                    //         //         $flag='N';
                    //         //         $set_units=$purchase_value['cumml_units'];
                    //         //         $purchase_value['cumml_units']=$set_units;
                    //         //         $deduct_unit_array=[...$deduct_unit_array,$purchase_value];
                    //         //         $rdm_tot_units=0;
                    //         //         $newarr=[];
                    //         //         // $newarr['id']=$purchase_value['id'];
                    //         //         $newarr['trans_date']=$purchase_value['trans_date'];
                    //         //         $newarr['pur_price']=$purchase_value['pur_price'];
                    //         //         $newarr['transaction_type']="Remaining";
                    //         //         $newarr['transaction_subtype']="Remaining";
                    //         //         $newarr['tot_units']=$set_units;
                    //         //         $newarr['cumml_units']=$set_units;
                    //         //         $newarr['tot_amount']= number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
                    //         //         $newarr['gross_amount']=number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
                    //         //         array_push($after_remaining_array,$newarr);
                    //         //     }else {
                    //         //         $purchase_value['cumml_units']=$purchase_value['tot_units'] + $after_remaining_array[(count($after_remaining_array)-1)]['cumml_units'] ;
                    //         //         array_push($after_remaining_array,$purchase_value);
                    //         //     }
                    //         // }
                    //         // return $deduct_unit_array;
                    //     }
                    //     // return $after_remaining_array;
                    //     // if ($key==7) {
                    //     //     # code...
                    //     //     return $after_remaining_array;
                    //     // }
                    //     $purchase_data=$after_remaining_array;
                    // }
                    // // return $purchase_data;
                    // // $final_array=array_merge($deduct_unit_array,$purchase_data);
                    // // return $final_array;
                    // // return $purchase_data;
                    // $tot_amount=0;
                    // foreach ($purchase_data as $key => $value) {
                    //     $tot_amount +=$value['tot_amount'];
                    // }
                    // // foreach ($redemption_data as $key => $value) {
                    // //     $tot_amount +=$value['tot_amount'];
                    // // }
                    
                    // return $tot_amount;

                    



                    foreach ($redemption_data as $redemption_key => $redemption_value) {
                        $rdm_tot_units=number_format((float)$redemption_value->tot_units, 4, '.', '');
                        $deduct_unit_array=[];
                        $flag='Y';
                        foreach ($purchase_data as $purchase_key => $purchase_value) {
                            if ($purchase_value['cumml_units'] >= 0) {
                                // if ($purchase_key==0) {
                                //     return $purchase_value['cumml_units']."-----".$rdm_tot_units;
                                // }
                                $purchase_cumml_units=number_format((float)$purchase_value['cumml_units'], 2, '.', '');
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
                            // array_push($final_data_arr,$value);
                            $inv_cost +=number_format((float)$value['tot_amount'], 2, '.', '');
                        }
                    }
                    return $inv_cost;
                    foreach ($final_data_arr as $key => $value) {
                        if ($key==0) {
                            $value['tot_amount']=number_format((float)($value['pur_price'] * $value['cumml_units']), 2, '.', '');
                            // return $value;
                            $inv_cost +=number_format((float)$value['tot_amount'], 2, '.', '');
                        }else {
                            $inv_cost +=number_format((float)$value['tot_amount'], 2, '.', '');
                        }
                    }
                    return $inv_cost;
                }
                // $mydata=$this->calculate($value1->foliotrans);
                $value1->mydata=$mydata;
                $value1->inv_cost=isset($mydata['inv_cost'])?number_format((float)$mydata['inv_cost'], 2, '.', ''):0;
                $value1->tot_units=isset($mydata['tot_units'])?$mydata['tot_units']:0;
                $value1->curr_val=$value1->curr_nav * $value1->tot_units;
                $value1->gain_loss=$value1->curr_val - $value1->inv_cost;
                if ($value1->gain_loss==0 || $value1->inv_cost==0) {
                    $value1->ret_abs=0;
                }else {
                    $value1->ret_abs=($value1->gain_loss / $value1->inv_cost) * 100;
                }
                $value1->idcw_reinv=0;
                $value1->idcwp=0;
                $value1->idcwr=0;
                $value1->xirr=0;
                $value1->trans_mode=0;
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

    public function showDetails(Request $request)
    {
        try {
            // return $request;
            $rnt_id=$request->rnt_id;
            $folio_no=$request->folio_no;
            $isin_no=$request->isin_no;
            $product_code=$request->product_code;
            $nav_date=$request->nav_date;
            $valuation_as_on=$request->valuation_as_on;

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


            $current_nav=DB::connection('mysql_nav')
                ->select('SELECT nav,nav_date FROM td_nav_details WHERE product_code="'.$request->product_code.'" AND DATE(nav_date) = (SELECT MAX(DATE(nav_date)) FROM td_nav_details WHERE product_code="'.$request->product_code.'" AND DATE(nav_date) <= DATE("'.$valuation_as_on.'"))');
            // return $current_nav;
            // DB::enableQueryLog();
            $all_data=MutualFundTransaction::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_mutual_fund_trans.product_code')
                ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
                ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                ->leftJoin('md_amc','md_amc.amc_code','=','td_mutual_fund_trans.amc_code')
                ->select('td_mutual_fund_trans.*','md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name',
                'md_subcategory.subcategory_name as subcat_name','md_amc.amc_short_name as amc_name')
                ->selectRaw('sum(units) as tot_units')
                ->selectRaw('sum(amount) as tot_amount')
                ->selectRaw('sum(stamp_duty) as tot_stamp_duty')
                ->selectRaw('sum(tds) as tot_tds')
                ->selectRaw('count(*) as tot_rows')
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
            $purchase_data=[];
            $redemption_data=[];
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

                    // $now = time(); // or your date as well
                    $now = strtotime($valuation_as_on); // or your date as well
                    $your_date = strtotime(date('Y-m-d',strtotime($value->trans_date)));
                    $datediff = $now - $your_date;
                    $days=round($datediff / (60 * 60 * 24));
                    $value->days=$days;
                    $value->curr_nav=$current_nav[0]->nav;
                   
                    /* CAGR calculation $nper=271/365;
                    $cagr = pow((6193.43/4561),(1/$nper)) - 1; */

                    array_push($data,$value);
                    if( strpos($value->transaction_subtype, 'Purchase' )!== false ) {
                        if ($key > 0) {
                            $value->cumml_units=$value->tot_units + $all_data[($key-1)]->cumml_units ;
                        }else {
                            $value->cumml_units=$value->tot_units;
                        }
                        array_push($purchase_data,$value);
                    }
                    if( strpos($value->transaction_subtype, 'Redemption' )!== false ) {
                        $value->cumml_units=0;
                        array_push($redemption_data,$value);
                    }
                }
            // return $data;
            // return $all_dates;
            // return $redemption_data;
            // $deduct_unit_array=[];
            if (count($redemption_data)> 0) {
                // foreach ($redemption_data as $redemption_key => $redemption_value) {
                //     $rdm_tot_units=$redemption_value->tot_units;
                //     // $rdm_tot_units=336.3380;
                //     $deduct_unit_array=[];
                //     foreach ($purchase_data as $purchase_key => $purchase_value) {
                //         if ($purchase_value['cumml_units'] >= 0) {
                //             // if ($purchase_key==0) {
                //             //     return $purchase_value['cumml_units']."-----".$rdm_tot_units;
                //             // }
                //             $purchase_cumml_units=$purchase_value['cumml_units'];
                //             $purchase_value['cumml_units']=$purchase_cumml_units - $rdm_tot_units;
                //             // return $purchase_value['cumml_units'];
                //             // if ($purchase_cumml_units == $rdm_tot_units) {
                //             //     // return 'if';
                //             //     $set_units=$purchase_value['cumml_units'];
                //             //     $purchase_value['cumml_units']=0;
                //             //     array_push($deduct_unit_array,$purchase_value);
                //             //     $rdm_tot_units=0;
                //             //     $newarr=[];
                //             //     $newarr['id']=$purchase_value['id'];
                //             //     $newarr['trans_date']=$purchase_value['trans_date'];
                //             //     $newarr['pur_price']=$purchase_value['pur_price'];
                //             //     $newarr['sensex']=$purchase_value['sensex'];
                //             //     $newarr['nifty50']=$purchase_value['nifty50'];
                //             //     $newarr['curr_nav']=$purchase_value['curr_nav'];
                //             //     $newarr['transaction_type']="Remaining";
                //             //     $newarr['transaction_subtype']="Remaining";
                //             //     $newarr['tot_units']=$set_units;
                //             //     $newarr['cumml_units']=$set_units;
                //             //     $newarr['tot_amount']= ($set_units * $purchase_value['pur_price']);
                //             //     $newarr['tot_gross_amount']=($set_units * $purchase_value['pur_price']);
                //             //     $newarr['gross_amount']=($set_units * $purchase_value['pur_price']);
                //             //     array_push($deduct_unit_array,$newarr);
                //             //     // return $deduct_unit_array;
                //             // } else {
                //                 // return 'else';
                //                 if ($purchase_value['cumml_units'] > 0 ) {
                //                     if ($purchase_data[($purchase_key - 1)]['cumml_units'] < 0) {
                //                         $set_units=$purchase_value['cumml_units'];
                //                         $purchase_value['cumml_units']=0;
                //                         array_push($deduct_unit_array,$purchase_value);
                //                         $rdm_tot_units=0;
                //                         $newarr=[];
                //                         $newarr['id']=$purchase_value['id'];
                //                         $newarr['trans_date']=$purchase_value['trans_date'];
                //                         $newarr['pur_price']=$purchase_value['pur_price'];
                //                         $newarr['sensex']=$purchase_value['sensex'];
                //                         $newarr['nifty50']=$purchase_value['nifty50'];
                //                         $newarr['curr_nav']=$purchase_value['curr_nav'];
                //                         $newarr['days']=$purchase_value['days'];
                //                         $newarr['trans_mode']=$purchase_value['trans_mode'];
                //                         $newarr['transaction_type']="Remaining";
                //                         $newarr['transaction_subtype']="Remaining";
                //                         $newarr['tot_units']=$set_units;
                //                         $newarr['cumml_units']=$set_units;
                //                         $newarr['tot_amount']= number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
                //                         $newarr['tot_gross_amount']=number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
                //                         $newarr['gross_amount']=number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
                //                         $newarr['curr_val']=number_format((float)($set_units * $purchase_value['curr_nav']), 2, '.', '');
                //                         $newarr['gain_loss']=number_format((float)($newarr['curr_val'] - $newarr['tot_amount']), 2, '.', '');
                //                         $newarr['ret_abs']=number_format((float)(($newarr['gain_loss'] / $newarr['tot_amount'])*100), 2, '.', '');
                //                         $nper =($newarr['days'] / 365);
                //                         $newarr['ret_cagr']=number_format((float)((pow(($newarr['curr_val']/$newarr['tot_amount']),(1/$nper)) - 1) * 100), 2, '.', '');
                //                         array_push($deduct_unit_array,$newarr);
                //                     }else {
                //                         $purchase_value['cumml_units']=$purchase_value['tot_units'] + $deduct_unit_array[(count($deduct_unit_array)-1)]['cumml_units'] ;
                //                         $purchase_value['curr_val']=number_format((float)($purchase_value['tot_units'] * $purchase_value['curr_nav']), 2, '.', '');
                //                         $purchase_value['gain_loss']=number_format((float)($purchase_value['curr_val'] - $purchase_value['tot_amount']), 2, '.', '');
                //                         $purchase_value['ret_abs']=number_format((float)(($purchase_value['gain_loss'] / $purchase_value['tot_amount'])*100), 2, '.', '');
                //                         $nper =($purchase_value['days'] / 365);
                //                         $purchase_value['ret_cagr']=number_format((float)((pow(($purchase_value['curr_val']/$purchase_value['tot_amount']),(1/$nper)) - 1) * 100), 2, '.', '');
                //                         array_push($deduct_unit_array,$purchase_value);
                //                     }
                //                 }else {
                //                     // return 'else1';
                //                     $purchase_value['curr_val']=number_format((float)($purchase_value['tot_units'] * $purchase_value['curr_nav']), 2, '.', '');
                //                     $purchase_value['gain_loss']=number_format((float)($purchase_value['curr_val'] - $purchase_value['tot_amount']), 2, '.', '');
                //                     $purchase_value['ret_abs']=number_format((float)(($purchase_value['gain_loss'] / $purchase_value['tot_amount'])*100), 2, '.', '');
                //                     $nper =($purchase_value['days'] / 365);
                //                     $purchase_value['ret_cagr']=number_format((float)((pow(($purchase_value['curr_val']/$purchase_value['tot_amount']),(1/$nper)) - 1) * 100), 2, '.', '');
                //                     array_push($deduct_unit_array,$purchase_value);
                //                     // return $deduct_unit_array;
                //                 }
                //             // }
                //         }else {
                //             $purchase_value['curr_val']=number_format((float)($purchase_value['tot_units'] * $purchase_value['curr_nav']), 2, '.', '');
                //             $purchase_value['gain_loss']=number_format((float)($purchase_value['curr_val'] - $purchase_value['tot_amount']), 2, '.', '');
                //             $purchase_value['ret_abs']=number_format((float)(($purchase_value['gain_loss'] / $purchase_value['tot_amount'])*100), 2, '.', '');
                //             $nper =($purchase_value['days'] / 365);
                //             $purchase_value['ret_cagr']=number_format((float)((pow(($purchase_value['curr_val']/$purchase_value['tot_amount']),(1/$nper)) - 1) * 100), 2, '.', '');
                //             array_push($deduct_unit_array,$purchase_value);
                //         }
                //     }
                //     // return  $deduct_unit_array;
                //     $purchase_data=$deduct_unit_array;
                // }

                // $final_data_arr=[];
                // $final_data_arr1=[];
                // foreach ($purchase_data as $key => $value) {
                //     if ($value['cumml_units'] > 0) {
                //         array_push($final_data_arr,$value);
                //     }else {
                //         array_push($final_data_arr1,$value);
                //     }
                // }
                // $final_data_arr2=[];
                // foreach ($final_data_arr as $key => $value) {
                //     if ($key==0) {
                //         $value['tot_amount']=number_format((float)($value['pur_price'] * $value['cumml_units']), 2, '.', '');
                //         $value['gross_amount']=number_format((float)($value['pur_price'] * $value['cumml_units']), 2, '.', '');
                //         // return $value;
                //         array_push($final_data_arr2,$value);
                //     }else {
                //         array_push($final_data_arr2,$value);
                //     }
                // }
                // $final_arr_1=array_merge($final_data_arr1,$final_data_arr2);


                foreach ($redemption_data as $redemption_key => $redemption_value) {
                    $rdm_tot_units=number_format((float)$redemption_value->tot_units, 4, '.', '');
                    $deduct_unit_array=[];
                    $flag='Y';
                    foreach ($purchase_data as $purchase_key => $purchase_value) {
                        if ($purchase_value['cumml_units'] >= 0) {
                            
                            $purchase_cumml_units=number_format((float)$purchase_value['cumml_units'], 2, '.', '');
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
                                        $newarr['gain_loss']=number_format((float)($newarr['curr_val'] - $newarr['tot_amount']), 2, '.', '');
                                        $newarr['ret_abs']=number_format((float)(($newarr['gain_loss'] / $newarr['tot_amount'])*100), 2, '.', '');
                                        $nper =($newarr['days'] / 365);
                                        $newarr['ret_cagr']=number_format((float)((pow(($newarr['curr_val']/$newarr['tot_amount']),(1/$nper)) - 1) * 100), 2, '.', '');
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
                                            $newarr['gain_loss']=number_format((float)($newarr['curr_val'] - $newarr['tot_amount']), 2, '.', '');
                                            $newarr['ret_abs']=number_format((float)(($newarr['gain_loss'] / $newarr['tot_amount'])*100), 2, '.', '');
                                            $nper =($newarr['days'] / 365);
                                            $newarr['ret_cagr']=number_format((float)((pow(($newarr['curr_val']/$newarr['tot_amount']),(1/$nper)) - 1) * 100), 2, '.', '');
                                            array_push($deduct_unit_array,$newarr);
                                            $flag='N';
                                        }else{
                                            $purchase_value['cumml_units']=number_format((float)$purchase_value['tot_units'], 4, '.', '') + number_format((float)$deduct_unit_array[(count($deduct_unit_array)-1)]['cumml_units'], 4, '.', '') ;
                                            $purchase_value['curr_val']=number_format((float)($purchase_value['tot_units'] * $purchase_value['curr_nav']), 2, '.', '');
                                            $purchase_value['gain_loss']=number_format((float)($purchase_value['curr_val'] - $purchase_value['tot_amount']), 2, '.', '');
                                            $purchase_value['ret_abs']=number_format((float)(($purchase_value['gain_loss'] / $purchase_value['tot_amount'])*100), 2, '.', '');
                                            $nper =($purchase_value['days'] / 365);
                                            $purchase_value['ret_cagr']=number_format((float)((pow(($purchase_value['curr_val']/$purchase_value['tot_amount']),(1/$nper)) - 1) * 100), 2, '.', '');
                                            array_push($deduct_unit_array,$purchase_value);
                                        }
                                    }
                                }else {
                                    $purchase_value['curr_val']=number_format((float)($purchase_value['tot_units'] * $purchase_value['curr_nav']), 2, '.', '');
                                    $purchase_value['gain_loss']=number_format((float)($purchase_value['curr_val'] - $purchase_value['tot_amount']), 2, '.', '');
                                    $purchase_value['ret_abs']=number_format((float)(($purchase_value['gain_loss'] / $purchase_value['tot_amount'])*100), 2, '.', '');
                                    $nper =($purchase_value['days'] / 365);
                                    $purchase_value['ret_cagr']=number_format((float)((pow(($purchase_value['curr_val']/$purchase_value['tot_amount']),(1/$nper)) - 1) * 100), 2, '.', '');
                                    array_push($deduct_unit_array,$purchase_value);
                                    // return $deduct_unit_array;
                                }
                        }else {
                            $purchase_value['curr_val']=number_format((float)($purchase_value['tot_units'] * $purchase_value['curr_nav']), 2, '.', '');
                            $purchase_value['gain_loss']=number_format((float)($purchase_value['curr_val'] - $purchase_value['tot_amount']), 2, '.', '');
                            $purchase_value['ret_abs']=number_format((float)(($purchase_value['gain_loss'] / $purchase_value['tot_amount'])*100), 2, '.', '');
                            $nper =($purchase_value['days'] / 365);
                            $purchase_value['ret_cagr']=number_format((float)((pow(($purchase_value['curr_val']/$purchase_value['tot_amount']),(1/$nper)) - 1) * 100), 2, '.', '');
                            array_push($deduct_unit_array,$purchase_value);
                        }
                    }
                    // return  $deduct_unit_array;
                    $purchase_data=$deduct_unit_array;
                }

                $final_arr=array_merge($purchase_data,$redemption_data);
                usort($final_arr, function($a, $b) {
                    return $a['trans_date'] <=> $b['trans_date'];
                });
            }else {
                $final_arr=[];
                foreach ($purchase_data as $purchase_key => $purchase_value) {
                    $purchase_value['curr_val']=number_format((float)($purchase_value['tot_units'] * $purchase_value['curr_nav']), 2, '.', '');
                    $purchase_value['gain_loss']=number_format((float)($purchase_value['curr_val'] - $purchase_value['tot_amount']), 2, '.', '');
                    $purchase_value['ret_abs']=number_format((float)(($purchase_value['gain_loss'] / $purchase_value['tot_amount'])*100), 2, '.', '');
                    $nper =($purchase_value['days']==0)?0:($purchase_value['days'] / 365);
                    $purchase_value['ret_cagr']=($nper==0)?0:number_format((float)((pow(($purchase_value['curr_val']/$purchase_value['tot_amount']),(1/$nper)) - 1) * 100), 2, '.', '');
                    array_push($final_arr,$purchase_value);
                }
            }
            // return $purchase_data;
            // $purchase_data=[];
            // $redemption_data=[];
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($final_arr);
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


    public function calculate($foliotrans){
        $purchase_data=[];
        $redemption_data=[];
        
        $purchase_amt_arr=[];
        $redemption_amt_arr=[];

        $all_amt_arr=[];
        $all_date_arr=[];

        foreach ($foliotrans as $key => $value) {
            if(strpos($value->transaction_subtype, 'Purchase' )!== false ) {
                if ($key > 0) {
                    $value->cumml_units=number_format((float)($value->tot_units + $foliotrans[($key-1)]->cumml_units) , 4, '.', '') ;
                }else {
                    $value->cumml_units=$value->tot_units;
                }
                array_push($purchase_data,$value);
                // array_push($purchase_amt_arr,$value->tot_amount);
                array_push($all_amt_arr,-$value->tot_amount);
                array_push($all_date_arr,$value->trans_date);
            }elseif (strpos($value->transaction_subtype, 'Redemption' )!== false ) {
                $value->cumml_units=0;
                array_push($redemption_data,$value);
                // array_push($redemption_amt_arr,$value->tot_amount);
                array_push($all_amt_arr,$value->tot_amount);
                array_push($all_date_arr,$value->trans_date);
            }
        }

        /*******************************************start purchase and redemption case******************************************/
        $inv_cost=0;
        if (count($redemption_data) > 0) {
        
            foreach ($redemption_data as $redemption_key => $redemption_value) {
                $rdm_tot_units=number_format((float)$redemption_value->tot_units, 4, '.', '');
                $deduct_unit_array=[];
                $flag='Y';
                foreach ($purchase_data as $purchase_key => $purchase_value) {
                    if ($purchase_value['cumml_units'] >= 0) {
                        
                        $purchase_cumml_units=number_format((float)$purchase_value['cumml_units'], 2, '.', '');
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
                    // array_push($final_data_arr,$value);
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

    public function calculate_old_1($foliotrans){
        $purchase_data=[];
        $redemption_data=[];
        
        $purchase_amt_arr=[];
        $redemption_amt_arr=[];

        $all_amt_arr=[];
        $all_date_arr=[];

        foreach ($foliotrans as $key => $value) {
            if(strpos($value->transaction_subtype, 'Purchase' )!== false ) {
                if ($key > 0) {
                    $value->cumml_units=number_format((float)($value->tot_units + $foliotrans[($key-1)]->cumml_units) , 4, '.', '') ;
                }else {
                    $value->cumml_units=$value->tot_units;
                }
                array_push($purchase_data,$value);
                // array_push($purchase_amt_arr,$value->tot_amount);
                array_push($all_amt_arr,-$value->tot_amount);
                array_push($all_date_arr,$value->trans_date);
            }elseif (strpos($value->transaction_subtype, 'Redemption' )!== false ) {
                $value->cumml_units=0;
                array_push($redemption_data,$value);
                // array_push($redemption_amt_arr,$value->tot_amount);
                array_push($all_amt_arr,$value->tot_amount);
                array_push($all_date_arr,$value->trans_date);
            }
        }

        /*******************************************start purchase and redemption case******************************************/
        $inv_cost=0;
        if (count($redemption_data) > 0) {
        
            foreach ($redemption_data as $redemption_key => $redemption_value) {
                $rdm_tot_units=$redemption_value->tot_units;
                // $rdm_tot_units=336.3380;
                $deduct_unit_array=[];
                foreach ($purchase_data as $purchase_key => $purchase_value) {
                    if ($purchase_value['cumml_units'] >= 0) {
                        // if ($purchase_key==0) {
                        //     return $purchase_value['cumml_units']."-----".$rdm_tot_units;
                        // }
                        $purchase_cumml_units=$purchase_value['cumml_units'];
                        $purchase_value['cumml_units']=$purchase_cumml_units - $rdm_tot_units;
                            // return 'else';
                            if ($purchase_value['cumml_units'] > 0 ) {
                                if ($purchase_data[($purchase_key - 1)]['cumml_units'] < 0) {
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
                                }else {
                                    $purchase_value['cumml_units']=$purchase_value['tot_units'] + $deduct_unit_array[(count($deduct_unit_array)-1)]['cumml_units'] ;
                                    $purchase_value['curr_val']=number_format((float)($purchase_value['tot_units'] * $purchase_value['curr_nav']), 2, '.', '');
                                    array_push($deduct_unit_array,$purchase_value);
                                }
                            }else {
                                // return 'else1';
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

            /*******************************************end purchase and redemption case******************************************/
            // $final_array=array_merge($purchase_data,$redemption_data);
            // return $final_array;
            $final_data_arr=[];
            foreach ($purchase_data as $key => $value) {
                if ($value['cumml_units'] > 0) {
                    array_push($final_data_arr,$value);
                }
            }
            // foreach ($purchase_data as $key => $value) {
            //     if ($value['cumml_units'] > 0) {
            //         $inv_cost +=number_format((float)$value['tot_amount'], 2, '.', '');
            //     }
            // }
            foreach ($final_data_arr as $key => $value) {
                if ($key==0) {
                    $value['tot_amount']=number_format((float)($value['pur_price'] * $value['cumml_units']), 2, '.', '');
                    // return $value;
                    $inv_cost +=number_format((float)$value['tot_amount'], 2, '.', '');
                }else {
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



    public function calculate_old($mydata)
    {
        // return $mydata;
        $purchase_data=[];
        $redemption_data=[];
        $purchase_amt_arr=[];
        $redemption_amt_arr=[];
        foreach ($mydata as $key => $value) {
            if(strpos($value->transaction_subtype, 'Purchase' )!== false ) {
                if ($key > 0) {
                    $value->cumml_units=$value->tot_units + $mydata[($key-1)]->cumml_units ;
                }else {
                    $value->cumml_units=$value->tot_units;
                }
                array_push($purchase_data,$value);
                array_push($purchase_amt_arr,$value->tot_amount);
            }elseif (strpos($value->transaction_subtype, 'Redemption' )!== false ) {
                $value->cumml_units=0;
                array_push($redemption_data,$value);
                array_push($redemption_amt_arr,$value->tot_amount);
            }
        }

        /*******************************************start purchase and redemption case******************************************/
        $deduct_unit_array=[];
        foreach ($redemption_data as $key => $redemption_value) {
            $rdm_tot_units=$redemption_value->tot_units;
            $after_remaining_array=[];
            foreach ($purchase_data as $purchase_key => $purchase_value) {
                $purchase_cumml_units=$purchase_value['cumml_units'];
                $purchase_value['cumml_units']=$purchase_cumml_units - $rdm_tot_units;
                if ($purchase_value['cumml_units'] >=0) {
                    if ($deduct_unit_array[(count($deduct_unit_array)-1)]['cumml_units'] <=  0) {
                        $set_units=$purchase_value['cumml_units'];
                        $purchase_value['cumml_units']=$set_units;
                        $deduct_unit_array=[...$deduct_unit_array,$purchase_value];
                        $rdm_tot_units=0;
                        $newarr=[];
                        $newarr['id']=$purchase_value['id'];
                        $newarr['trans_date']=$purchase_value['trans_date'];
                        $newarr['pur_price']=$purchase_value['pur_price'];
                        $newarr['transaction_type']="Remaining";
                        $newarr['transaction_subtype']="Remaining";
                        $newarr['tot_units']=$set_units;
                        $newarr['cumml_units']=$set_units;
                        $newarr['tot_amount']= number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
                        $newarr['gross_amount']=number_format((float)($set_units * $purchase_value['pur_price']), 2, '.', '');
                        array_push($after_remaining_array,$newarr);
                    }else {
                        $purchase_value['cumml_units']=$purchase_value['tot_units'] + (count($after_remaining_array)>0)?$after_remaining_array[(count($after_remaining_array)-1)]['cumml_units']:0 ;
                        array_push($after_remaining_array,$purchase_value);
                    }
                }else {
                    $deduct_unit_array=[...$deduct_unit_array,$purchase_value];
                }
            }
            $purchase_data=$after_remaining_array;
        }
        // return $purchase_data;
        /*******************************************end purchase and redemption case******************************************/

        $inv_cost=0;
        foreach ($purchase_data as $key => $value) {
            $inv_cost +=number_format((float)$value['tot_amount'], 2, '.', '');
        }
        // return $inv_cost;
        // $final_array=array_merge($deduct_unit_array,$purchase_data);
        // return $final_array;
                    
        $ck=[];
        $ck['inv_cost']=$inv_cost;
        $ck['tot_units']=(count($purchase_data) > 0)?$purchase_data[(count($purchase_data) - 1)]['cumml_units']:0;
        $ck['purchase_amt_arr']=$purchase_amt_arr;
        $ck['redemption_amt_arr']=$redemption_amt_arr;
        return $ck;
    }

    public function recentTrans(Request $request)
    {
        try {
            // return $request;
            $valuation_as_on=$request->valuation_as_on;
            $view_type=$request->view_type;
            $pan_no=$request->pan_no;
            $client_name=$request->client_name;
            $date_range=$request->date_range;

            // session()->forget('valuation_as_on');
            // session(['valuation_as_on' => $valuation_as_on]);
            // return Session::get('valuation_as_on');
            $client_details='';
            if ($view_type || $valuation_as_on || $date_range) {
                $rawQuery='';
                if ($date_range) {
                    $from_date=Carbon::parse(str_replace('/','-',explode("-",$date_range)[0]))->format('Y-m-d') ;
                    $to_date=Carbon::parse(str_replace('/','-',explode("-",$date_range)[1]))->format('Y-m-d') ;
                    // return $to_date;
                    $queryString='td_mutual_fund_trans.trans_date';
                    $rawQuery.=Helper::FrmToDateRawQuery($from_date,$to_date,$rawQuery,$queryString);
                }
                // return $rawQuery;
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

            $all_datas=MutualFundTransaction::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_mutual_fund_trans.product_code')
                ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
                ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                ->leftJoin('md_amc','md_amc.amc_code','=','td_mutual_fund_trans.amc_code')
                ->leftJoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
                ->leftJoin('md_option','md_option.id','=','md_scheme_isin.option_id')
                ->select('td_mutual_fund_trans.rnt_id','td_mutual_fund_trans.folio_no','td_mutual_fund_trans.product_code','td_mutual_fund_trans.pur_price','td_mutual_fund_trans.trans_date',
                'trxn_type_code','trxn_type_flag','trxn_nature_code','trans_desc','kf_trans_type','trans_flag',
                'md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name','md_amc.amc_short_name as amc_name',
                'md_plan.plan_name as plan_name','md_option.opt_name as option_name')
                ->selectRaw('IF(td_mutual_fund_trans.rnt_id=1,md_scheme_isin.isin_no,td_mutual_fund_trans.isin_no) as isin_no')
                ->selectRaw('sum(td_mutual_fund_trans.units) as tot_units')
                ->selectRaw('sum(td_mutual_fund_trans.amount) as tot_amount')
                ->selectRaw('sum(td_mutual_fund_trans.stamp_duty) as tot_stamp_duty')
                ->selectRaw('sum(td_mutual_fund_trans.tds) as tot_tds')
                ->selectRaw('count(*) as tot_rows')
                // ->selectRaw('(select close from td_benchmark_scheme where benchmark=1 AND DATE(date)=DATE(td_mutual_fund_trans.trans_date)) as nifty50')
                // ->selectRaw('(select close from td_benchmark_scheme where benchmark=70 AND DATE(date)=DATE(td_mutual_fund_trans.trans_date)) as sensex')
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
                    WHEN td_mutual_fund_trans.trans_flag="DP" || td_mutual_fund_trans.trans_flag="DR" THEN (SELECT trans_sub_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=td_mutual_fund_trans.kf_trans_type AND k_divident_flag=td_mutual_fund_trans.trans_flag limit 1)
                    WHEN td_mutual_fund_trans.trans_flag="TI" THEN "Transfer In"
                    WHEN td_mutual_fund_trans.trans_flag="TO" THEN "Transfer Out"
                    ELSE (SELECT trans_sub_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=td_mutual_fund_trans.kf_trans_type limit 1)
                END)
                )as transaction_subtype')
                ->selectRaw('IF(td_mutual_fund_trans.rnt_id=1,
                (SELECT process_type FROM md_mf_trans_type_subtype WHERE c_trans_type_code=td_mutual_fund_trans.trxn_type_code AND c_k_trans_type=td_mutual_fund_trans.trxn_type_flag AND c_k_trans_sub_type=td_mutual_fund_trans.trxn_nature_code limit 1),
                (CASE 
                    WHEN td_mutual_fund_trans.trans_flag="DP" || td_mutual_fund_trans.trans_flag="DR" THEN (SELECT process_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=td_mutual_fund_trans.kf_trans_type AND k_divident_flag=td_mutual_fund_trans.trans_flag limit 1)
                    WHEN td_mutual_fund_trans.trans_flag="TI" THEN (SELECT process_type FROM md_mf_trans_type_subtype WHERE trans_type="Transfer In" AND trans_sub_type="Transfer In" AND rnt_id=2 limit 1)
                    WHEN td_mutual_fund_trans.trans_flag="TO" THEN (SELECT process_type FROM md_mf_trans_type_subtype WHERE trans_type="Transfer Out" AND trans_sub_type="Transfer Out" AND rnt_id=2 limit 1)
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
                ->groupBy('td_mutual_fund_trans.product_code')
                ->groupBy('td_mutual_fund_trans.isin_no')
                // ->orderBy('md_scheme.scheme_name','ASC')
                ->orderBy('td_mutual_fund_trans.trans_date','ASC')
                ->get();
            
            $mydata=[];
            $mydata['client_details']=$client_details;
            $mydata['data']=$all_datas;
            $mydata['valuation_as_on']=$valuation_as_on;
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($mydata);
    }
}