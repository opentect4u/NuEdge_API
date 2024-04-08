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
            $all_data=MutualFundTransaction::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_mutual_fund_trans.product_code')
                ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
                ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                ->leftJoin('md_amc','md_amc.amc_code','=','td_mutual_fund_trans.amc_code')
                ->leftJoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
                ->leftJoin('md_option','md_option.id','=','md_scheme_isin.option_id')
                ->select('td_mutual_fund_trans.*','md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name','md_amc.amc_short_name as amc_name',
                'md_plan.plan_name as plan_name','md_option.opt_name as option_name')
                ->selectRaw('IF(td_mutual_fund_trans.rnt_id=1,md_scheme_isin.isin_no,td_mutual_fund_trans.isin_no) as isin_no')
                ->selectRaw('sum(td_mutual_fund_trans.units) as tot_units')
                ->selectRaw('sum(td_mutual_fund_trans.amount) as inv_cost')
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
            //     ORDER BY scheme_name ASC");
            // dd(DB::getQueryLog());
            // return $all_data;
            
            $all_trans_product=[];
            $data=[];
            foreach ($all_data as $key => $value) {
                $value->inv_since=date('Y-m-d',strtotime($value->trans_date));
                $value->pur_nav=$value->pur_price;
                $f_trans_product="(nav_date=(SELECT MAX(nav_date) FROM td_nav_details WHERE product_code='".$value->product_code."' AND DATE(nav_date) <= DATE('".$valuation_as_on."')) AND product_code='".$value->product_code."')";
                array_push($all_trans_product,$f_trans_product);
                array_push($data,$value);
            }
            $string_version_product_code = implode(',', $all_trans_product);
            // return $string_version_product_code;
            $res_array =DB::connection('mysql_nav')
                ->select('SELECT product_code,isin_no,DATE_FORMAT(nav_date, "%Y-%m-%d") as nav_date,nav FROM td_nav_details where '.str_replace(",","  OR  ",$string_version_product_code));
            // return $res_array;
            $filter_data=[];
            foreach ($data as $key => $value1) {
                $isin_no=$value1->isin_no;
                $product_code=$value1->product_code;
                $new='';
                if (count($res_array) > 0) {
                    foreach($res_array as $val_nav){
                        if($val_nav->product_code==$product_code){
                            $new=$val_nav;
                        }
                    }
                    // $new = array_filter($res_array, function ($var) use ($product_code) {
                    //     return  $var->product_code == $product_code;
                    // });
                }
                // return $new;
                $value1->new=$new;
                // $value1->curr_nav=isset($new[0]->nav)?$new[0]->nav:0;
                // $value1->nav_date=isset($new[0]->nav_date)?$new[0]->nav_date:0;
                $value1->curr_nav=isset($new->nav)?$new->nav:0;
                $value1->nav_date=isset($new->nav_date)?$new->nav_date:0;
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

                    $now = time(); // or your date as well
                    $your_date = strtotime(date('Y-m-d',strtotime($value->trans_date)));
                    $datediff = $now - $your_date;
                    $days=round($datediff / (60 * 60 * 24));
                    $value->days=($days - 1);
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
            // return $purchase_data;
            // $deduct_unit_array=[];
            foreach ($redemption_data as $redemption_key => $redemption_value) {
                $rdm_tot_units=$redemption_value->tot_units;
                // $rdm_tot_units=57.247;
                $deduct_unit_array=[];
                foreach ($purchase_data as $purchase_key => $purchase_value) {
                    if ($purchase_value['cumml_units'] >= 0) {
                        // if ($purchase_key==0) {
                        //     return $purchase_value['cumml_units']."-----".$rdm_tot_units;
                        // }
                        $purchase_cumml_units=$purchase_value['cumml_units'];
                        $purchase_value['cumml_units']=$purchase_cumml_units - $rdm_tot_units;
                        // return $purchase_value['cumml_units'];
                        if ($purchase_cumml_units==$rdm_tot_units) {
                            // return 'if';
                            $set_units=$purchase_value['cumml_units'];
                            $purchase_value['cumml_units']=0;
                            array_push($deduct_unit_array,$purchase_value);
                            $rdm_tot_units=0;
                            $newarr=[];
                            $newarr['id']=$purchase_value['id'];
                            $newarr['transaction_type']="Remaining";
                            $newarr['transaction_subtype']="Remaining";
                            $newarr['tot_units']=$set_units;
                            $newarr['cumml_units']=$set_units;
                            array_push($deduct_unit_array,$newarr);
                            // return $deduct_unit_array;
                        } else {
                            // return 'else';
                            if ($purchase_value['cumml_units'] > 0 ) {
                                if ($purchase_data[($purchase_key - 1)]['cumml_units'] < 0) {
                                    $set_units=$purchase_value['cumml_units'];
                                    $purchase_value['cumml_units']=0;
                                    array_push($deduct_unit_array,$purchase_value);
                                    $rdm_tot_units=0;
                                    $newarr=[];
                                    $newarr['id']=$purchase_value['id'];
                                    $newarr['transaction_type']="Remaining";
                                    $newarr['transaction_subtype']="Remaining";
                                    $newarr['tot_units']=$set_units;
                                    $newarr['cumml_units']=$set_units;
                                    array_push($deduct_unit_array,$newarr);
                                }else {
                                    $purchase_value['cumml_units']=$purchase_value['tot_units'] + $deduct_unit_array[(count($deduct_unit_array)-1)]['cumml_units'] ;
                                    array_push($deduct_unit_array,$purchase_value);
                                }
                            }else {
                                // return 'else1';
                                array_push($deduct_unit_array,$purchase_value);
                                // return $deduct_unit_array;
                            }
                        }
                    }else {
                        array_push($deduct_unit_array,$purchase_value);
                    }
                }
                // return  $deduct_unit_array;
                $purchase_data=$deduct_unit_array;
            }
            return $purchase_data;
            // $purchase_data=[];
            // $redemption_data=[];
            $final_arr=array_merge($purchase_data,$redemption_data);
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
}