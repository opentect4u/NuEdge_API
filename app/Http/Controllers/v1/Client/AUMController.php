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
    MutualFundTransactionMerge,
    AumReport,
    ClientFamily
};
use Validator;
use Illuminate\Support\Carbon;
use Excel;
use App\Helpers\TransHelper;
use App\Helpers\AumHelper;
use DB;
use Session;

class AUMController extends Controller
{
    
    public function search______________(Request $request)
    {
        try {
            $date=$request->date;
            $arn_no=$request->arn_no;
            $date=date('Y-m-d');
            // $date='2025-01-12';
            if ($date || $arn_no) {
                $rawQuery='';
                $rawQueryBroker='';
                if ($date) {
                    $condition_v=(strlen($rawQuery) > 0)? " AND ":" ";
                    $queryString='td_mutual_fund_trans_merge.trans_date';
                    $rawQuery.=$condition_v.$queryString."<= '".$date."'";
                }
            } 
            // DB::enableQueryLog();
            // DB::statement("call validate_reservation(4,'2016-04-26 20:30',1,10,1,$status,$message)");

            // $data=MutualFundTransaction::where('folio_no','401116563703')->get()->toArray();
            // dd(DB::getQueryLog());
            // return $data;
            // $all_data=[];
            // $all_data=DB::select('SELECT td_mutual_fund_trans_merge.product_code,
            //     td_mutual_fund_trans_merge.folio_no,
            //     td_mutual_fund_trans_merge.trans_date,
            //     td_mutual_fund_trans_merge.pur_price,
            //     td_mutual_fund_trans_merge.units,
            //     td_mutual_fund_trans_merge.amount,
            //     td_mutual_fund_trans_merge.stamp_duty,
            //     td_mutual_fund_trans_merge.transaction_type,
            //     td_mutual_fund_trans_merge.transaction_subtype,
            //     SUM(td_mutual_fund_trans_merge.units) AS tot_units,
            //     SUM(td_mutual_fund_trans_merge.amount) AS tot_amount,
            //     SUM(td_mutual_fund_trans_merge.stamp_duty) AS tot_stamp_duty,
            //     IF(td_mutual_fund_trans_merge.tds!="",SUM(td_mutual_fund_trans_merge.tds),0.00) AS tot_tds,
            //     md_amc.amc_short_name AS amc_name,
            //     md_scheme.scheme_name AS scheme_name,
            //     md_category.cat_name AS cat_name,
            //     md_subcategory.subcategory_name AS subcat_name,
            //     md_plan.plan_name AS plan_name,
            //     md_option.opt_name AS option_name
            //     FROM td_mutual_fund_trans_merge 
            //     LEFT JOIN md_amc ON md_amc.amc_code = td_mutual_fund_trans_merge.amc_code
            //     LEFT JOIN md_scheme_isin ON md_scheme_isin.product_code = td_mutual_fund_trans_merge.product_code
            //     LEFT JOIN md_plan ON md_plan.id = md_scheme_isin.plan_id
            //     LEFT JOIN md_scheme ON md_scheme.id = md_scheme_isin.scheme_id
            //     LEFT JOIN md_option ON md_option.id = md_scheme_isin.option_id
            //     LEFT JOIN md_category ON md_category.id = md_scheme.category_id
            //     LEFT JOIN md_subcategory ON md_subcategory.id = md_scheme.subcategory_id
            //     WHERE td_mutual_fund_trans_merge.delete_flag="N"
            //     AND td_mutual_fund_trans_merge.amc_flag="N"
            //     AND td_mutual_fund_trans_merge.scheme_flag="N"
            //     AND td_mutual_fund_trans_merge.plan_option_flag="N"
            //     AND td_mutual_fund_trans_merge.bu_type_flag="N"
            //     AND td_mutual_fund_trans_merge.divi_mismatch_flag="N"
            //     GROUP BY td_mutual_fund_trans_merge.trans_no,
            //     td_mutual_fund_trans_merge.trxn_type_flag,
            //     td_mutual_fund_trans_merge.trxn_nature_code,
            //     td_mutual_fund_trans_merge.trans_desc,
            //     td_mutual_fund_trans_merge.kf_trans_type,
            //     td_mutual_fund_trans_merge.trans_flag,
            //     td_mutual_fund_trans_merge.pur_price
            //     ORDER BY trans_date ASC');

            // $all_data=DB::select("CALL aum_report_calculation('".$date."')");
            // $all_data = collect($all_data)->map(function($x){ return (array) $x; })->toArray(); 
            // return 'hii';
            $all_data=MutualFundTransactionMerge::leftJoin('md_amc','md_amc.amc_code','=','td_mutual_fund_trans_merge.amc_code')
                ->leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_mutual_fund_trans_merge.product_code')
                ->leftJoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
                ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                ->leftJoin('md_option','md_option.id','=','md_scheme_isin.option_id')
                ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
                ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                ->select('td_mutual_fund_trans_merge.amc_code','td_mutual_fund_trans_merge.product_code','td_mutual_fund_trans_merge.folio_no','td_mutual_fund_trans_merge.trans_date',
                'td_mutual_fund_trans_merge.pur_price','td_mutual_fund_trans_merge.units','td_mutual_fund_trans_merge.amount',
                'td_mutual_fund_trans_merge.stamp_duty','td_mutual_fund_trans_merge.transaction_type','td_mutual_fund_trans_merge.transaction_subtype',
                'md_amc.amc_short_name AS amc_name','md_scheme.scheme_name AS scheme_name','md_category.cat_name AS cat_name','md_subcategory.subcategory_name AS subcat_name','md_plan.plan_name AS plan_name','md_option.opt_name AS option_name')
                ->selectRaw('SUM(td_mutual_fund_trans_merge.units) AS tot_units')
                ->selectRaw('SUM(td_mutual_fund_trans_merge.amount) AS tot_amount')
                ->selectRaw('SUM(td_mutual_fund_trans_merge.stamp_duty) AS tot_stamp_duty')
                ->selectRaw('IF(td_mutual_fund_trans_merge.tds!="",SUM(td_mutual_fund_trans_merge.tds),0.00) AS tot_tds')
                ->where('td_mutual_fund_trans_merge.delete_flag','N')
                ->where('td_mutual_fund_trans_merge.amc_flag','N')
                ->where('td_mutual_fund_trans_merge.scheme_flag','N')
                ->where('td_mutual_fund_trans_merge.plan_option_flag','N')
                ->where('td_mutual_fund_trans_merge.bu_type_flag','N')
                ->where('td_mutual_fund_trans_merge.divi_mismatch_flag','N')
                ->whereRaw($rawQuery)
                ->groupBy('td_mutual_fund_trans_merge.trans_no')
                ->groupBy('td_mutual_fund_trans_merge.trxn_type_flag')
                ->groupBy('td_mutual_fund_trans_merge.trxn_nature_code')
                ->groupBy('td_mutual_fund_trans_merge.trans_desc')
                ->groupBy('td_mutual_fund_trans_merge.kf_trans_type')
                ->groupBy('td_mutual_fund_trans_merge.trans_flag')
                ->groupBy('td_mutual_fund_trans_merge.pur_price')
                ->orderBy('td_mutual_fund_trans_merge.trans_date','ASC')
                ->get()
                ->toArray();
            // return 'hii';
            $all_trans_product=[];
            $group_amc_data=[];
            foreach ($all_data as $value_product_code) {
                $f_trans_product="(nav_date=(SELECT MAX(nav_date) FROM td_nav_details WHERE product_code='".$value_product_code['product_code']."' AND nav_date <='".$date."') AND product_code='".$value_product_code['product_code']."')";
                array_push($all_trans_product,$f_trans_product);
                $group_amc_data[$value_product_code['amc_name']][$value_product_code['scheme_name']][]=$value_product_code;
                // $group_amc_data[$value_product_code->amc_code][$value_product_code->product_code][]=$value_product_code;
            }
            // return $all_trans_product;
            $res_array=[];
            if (count($all_data)>0) {
                $all_trans_product_unique = array_unique($all_trans_product);
                $string_version_product_code = implode(',', $all_trans_product_unique);
                $res_array =DB::connection('mysql_nav')
                    ->select('SELECT product_code,isin_no,DATE_FORMAT(nav_date, "%Y-%m-%d") as nav_date,nav FROM td_nav_details where '.str_replace(",","  OR  ",$string_version_product_code));
            }
            // return $res_array;
            $final_data=[];
            foreach ($group_amc_data as $key_group_amc_data => $value_group_amc_data) {  // amc loop
                // return $value_group_amc_data;
                foreach ($value_group_amc_data as $key_amc_wise_product_loop => $value_amc_wise_product_loop) { //amc wise product loop
                    // return $value_amc_wise_product_loop; //RMFSCGP
                    $my_data=[];
                    $product_code=$value_amc_wise_product_loop[0]['product_code'];
                    $new='';
                    if (count($res_array) > 0) {
                        foreach($res_array as $val_nav){
                            if($val_nav->product_code==$product_code){
                                $new=$val_nav;
                            }
                        }
                    }
                    $my_data['new']=$new;
                    $my_data['curr_nav']=isset($new->nav)?$new->nav:0;
                    $my_data['nav_date']=isset($new->nav_date)?$new->nav_date:0;
                    $my_data['amc_name']=$key_group_amc_data;
                    $my_data['amc_code']=$value_amc_wise_product_loop[0]['amc_code'];
                    $my_data['product_code']=$product_code;
                    $my_data['scheme_name']=$key_amc_wise_product_loop;
                    $my_data['cat_name']=$value_amc_wise_product_loop[0]['cat_name'];
                    $my_data['subcat_name']=$value_amc_wise_product_loop[0]['subcat_name'];
                    $my_data['plan_name']=$value_amc_wise_product_loop[0]['plan_name'];
                    $my_data['option_name']=$value_amc_wise_product_loop[0]['option_name'];
                    
                    $aum_calculation=AumHelper::calculate($value_amc_wise_product_loop,$my_data['curr_nav'],$my_data['nav_date']);
                    // return $aum_calculation;
                    $my_data['aum_calculation']=$aum_calculation;
                    $my_data['tot_units']=isset($aum_calculation['tot_units'])?$aum_calculation['tot_units']:0;
                    $my_data['inv_cost']=isset($aum_calculation['inv_cost'])?$aum_calculation['inv_cost']:0;
                    $my_data['idcw_reinv']=isset($aum_calculation['idcw_reinv'])?$aum_calculation['idcw_reinv']:0;
                    $my_data['idcw_paid']=isset($aum_calculation['idcw_paid'])?$aum_calculation['idcw_paid']:0;
                    $my_data['idcwr']=number_format((float)($my_data['idcw_paid'] + $my_data['idcw_reinv']), 2, '.', '');
                    $my_data['curr_aum']=number_format((float)($my_data['tot_units'] * $my_data['curr_nav']), 2, '.', '');
                    $my_data['gain_loss']=number_format((float)(($my_data['curr_aum'] - $my_data['inv_cost']) + $my_data['idcwr']), 2, '.', '');
                    $my_data['abs_rtn']= ($my_data['inv_cost']!=0)?number_format((float)(($my_data['gain_loss'] / $my_data['inv_cost']) * 100), 2, '.', ''):0;
                    array_push($final_data,$my_data);
                }
            }
            usort($final_data, function($a, $b) {
                return $a['amc_name'] <=> $b['amc_name'];
            });

        } catch (\Throwable $th) {
            throw $th;
        }
        return Helper::SuccessResponse($final_data);
    }

    public function search_____(Request $request)
    {
        try {
            $date=$request->date;
            $arn_no=$request->arn_no;
            // $date=date('Y-m-d');
            // $date='2025-01-12';
            if ($date || $arn_no) {
                $rawQuery='';
                $rawQueryBroker='';
                if ($date) {
                    $condition_v=(strlen($rawQuery) > 0)? " AND ":" ";
                    $queryString='trans_date';
                    $rawQuery.=$condition_v.$queryString."<= '".$date."'";
                }
            } 
            // DB::enableQueryLog();
            // DB::statement("call validate_reservation(4,'2016-04-26 20:30',1,10,1,$status,$message)");

            // $data=MutualFundTransaction::where('folio_no','401116563703')->get()->toArray();
            // dd(DB::getQueryLog());
            // return $data;
            // $all_data=[];
            // $all_data=DB::select('SELECT td_mutual_fund_trans_merge.product_code,
            //     td_mutual_fund_trans_merge.folio_no,
            //     td_mutual_fund_trans_merge.trans_date,
            //     td_mutual_fund_trans_merge.pur_price,
            //     td_mutual_fund_trans_merge.units,
            //     td_mutual_fund_trans_merge.amount,
            //     td_mutual_fund_trans_merge.stamp_duty,
            //     td_mutual_fund_trans_merge.transaction_type,
            //     td_mutual_fund_trans_merge.transaction_subtype,
            //     SUM(td_mutual_fund_trans_merge.units) AS tot_units,
            //     SUM(td_mutual_fund_trans_merge.amount) AS tot_amount,
            //     SUM(td_mutual_fund_trans_merge.stamp_duty) AS tot_stamp_duty,
            //     IF(td_mutual_fund_trans_merge.tds!="",SUM(td_mutual_fund_trans_merge.tds),0.00) AS tot_tds,
            //     md_amc.amc_short_name AS amc_name,
            //     md_scheme.scheme_name AS scheme_name,
            //     md_category.cat_name AS cat_name,
            //     md_subcategory.subcategory_name AS subcat_name,
            //     md_plan.plan_name AS plan_name,
            //     md_option.opt_name AS option_name
            //     FROM td_mutual_fund_trans_merge 
            //     LEFT JOIN md_amc ON md_amc.amc_code = td_mutual_fund_trans_merge.amc_code
            //     LEFT JOIN md_scheme_isin ON md_scheme_isin.product_code = td_mutual_fund_trans_merge.product_code
            //     LEFT JOIN md_plan ON md_plan.id = md_scheme_isin.plan_id
            //     LEFT JOIN md_scheme ON md_scheme.id = md_scheme_isin.scheme_id
            //     LEFT JOIN md_option ON md_option.id = md_scheme_isin.option_id
            //     LEFT JOIN md_category ON md_category.id = md_scheme.category_id
            //     LEFT JOIN md_subcategory ON md_subcategory.id = md_scheme.subcategory_id
            //     WHERE td_mutual_fund_trans_merge.delete_flag="N"
            //     AND td_mutual_fund_trans_merge.amc_flag="N"
            //     AND td_mutual_fund_trans_merge.scheme_flag="N"
            //     AND td_mutual_fund_trans_merge.plan_option_flag="N"
            //     AND td_mutual_fund_trans_merge.bu_type_flag="N"
            //     AND td_mutual_fund_trans_merge.divi_mismatch_flag="N"
            //     GROUP BY td_mutual_fund_trans_merge.trans_no,
            //     td_mutual_fund_trans_merge.trxn_type_flag,
            //     td_mutual_fund_trans_merge.trxn_nature_code,
            //     td_mutual_fund_trans_merge.trans_desc,
            //     td_mutual_fund_trans_merge.kf_trans_type,
            //     td_mutual_fund_trans_merge.trans_flag,
            //     td_mutual_fund_trans_merge.pur_price
            //     ORDER BY trans_date ASC');

            // $all_data=DB::select("CALL aum_report_calculation('".$date."')");
            // $all_data = collect($all_data)->map(function($x){ return (array) $x; })->toArray(); 
            // return 'hii';
            $all_data=MutualFundTransactionMerge::select('amc_code','product_code','folio_no','trans_date','pur_price','units',
                'amount','stamp_duty','transaction_type','transaction_subtype','amc_name','scheme_name','cat_name','subcat_name','plan_name','option_name')
                ->selectRaw('SUM(units) AS tot_units')
                ->selectRaw('SUM(amount) AS tot_amount')
                ->selectRaw('SUM(stamp_duty) AS tot_stamp_duty')
                ->selectRaw('IF(tds!="",SUM(tds),0.00) AS tot_tds')
                ->where('delete_flag','N')
                ->where('amc_flag','N')
                ->where('scheme_flag','N')
                ->where('plan_option_flag','N')
                ->where('bu_type_flag','N')
                ->where('divi_mismatch_flag','N')
                // ->whereRaw($rawQuery)
                ->groupBy('trans_no')
                ->groupBy('trxn_type_flag')
                ->groupBy('trxn_nature_code')
                ->groupBy('trans_desc')
                ->groupBy('kf_trans_type')
                ->groupBy('trans_flag')
                ->groupBy('pur_price')
                ->orderBy('trans_date','ASC')
                ->get()
                ->toArray();
            // return 'hii';
            $all_trans_product=[];
            $group_amc_data=[];
            foreach ($all_data as $value_product_code) {
                $f_trans_product="(nav_date=(SELECT MAX(nav_date) FROM td_nav_details WHERE product_code='".$value_product_code['product_code']."' AND nav_date <='".$date."') AND product_code='".$value_product_code['product_code']."')";
                array_push($all_trans_product,$f_trans_product);
                $group_amc_data[$value_product_code['amc_name']][$value_product_code['scheme_name']][]=$value_product_code;
                // $group_amc_data[$value_product_code->amc_code][$value_product_code->product_code][]=$value_product_code;
            }
            // return $all_trans_product;
            $res_array=[];
            if (count($all_data)>0) {
                $all_trans_product_unique = array_unique($all_trans_product);
                $string_version_product_code = implode(',', $all_trans_product_unique);
                $res_array =DB::connection('mysql_nav')
                    ->select('SELECT product_code,isin_no,DATE_FORMAT(nav_date, "%Y-%m-%d") as nav_date,nav FROM td_nav_details where '.str_replace(",","  OR  ",$string_version_product_code));
            }
            // return $res_array;
            $final_data=[];
            foreach ($group_amc_data as $key_group_amc_data => $value_group_amc_data) {  // amc loop
                // return $value_group_amc_data;
                foreach ($value_group_amc_data as $key_amc_wise_product_loop => $value_amc_wise_product_loop) { //amc wise product loop
                    // return $value_amc_wise_product_loop; //RMFSCGP
                    $my_data=[];
                    $product_code=$value_amc_wise_product_loop[0]['product_code'];
                    $new='';
                    if (count($res_array) > 0) {
                        foreach($res_array as $val_nav){
                            if($val_nav->product_code==$product_code){
                                $new=$val_nav;
                            }
                        }
                    }
                    $my_data['new']=$new;
                    $my_data['curr_nav']=isset($new->nav)?$new->nav:0;
                    $my_data['nav_date']=isset($new->nav_date)?$new->nav_date:0;
                    $my_data['amc_name']=$key_group_amc_data;
                    $my_data['amc_code']=$value_amc_wise_product_loop[0]['amc_code'];
                    $my_data['product_code']=$product_code;
                    $my_data['scheme_name']=$key_amc_wise_product_loop;
                    $my_data['cat_name']=$value_amc_wise_product_loop[0]['cat_name'];
                    $my_data['subcat_name']=$value_amc_wise_product_loop[0]['subcat_name'];
                    $my_data['plan_name']=$value_amc_wise_product_loop[0]['plan_name'];
                    $my_data['option_name']=$value_amc_wise_product_loop[0]['option_name'];
                    
                    $aum_calculation=AumHelper::calculate($value_amc_wise_product_loop,$my_data['curr_nav'],$my_data['nav_date']);
                    // return $aum_calculation;
                    $my_data['aum_calculation']=$aum_calculation;
                    $my_data['tot_units']=isset($aum_calculation['tot_units'])?$aum_calculation['tot_units']:0;
                    $my_data['inv_cost']=isset($aum_calculation['inv_cost'])?$aum_calculation['inv_cost']:0;
                    $my_data['idcw_reinv']=isset($aum_calculation['idcw_reinv'])?$aum_calculation['idcw_reinv']:0;
                    $my_data['idcw_paid']=isset($aum_calculation['idcw_paid'])?$aum_calculation['idcw_paid']:0;
                    $my_data['idcwr']=number_format((float)($my_data['idcw_paid'] + $my_data['idcw_reinv']), 2, '.', '');
                    $my_data['curr_aum']=number_format((float)($my_data['tot_units'] * $my_data['curr_nav']), 2, '.', '');
                    $my_data['gain_loss']=number_format((float)(($my_data['curr_aum'] - $my_data['inv_cost']) + $my_data['idcwr']), 2, '.', '');
                    $my_data['abs_rtn']= ($my_data['inv_cost']!=0)?number_format((float)(($my_data['gain_loss'] / $my_data['inv_cost']) * 100), 2, '.', ''):0;
                    array_push($final_data,$my_data);
                }
            }
            usort($final_data, function($a, $b) {
                return $a['amc_name'] <=> $b['amc_name'];
            });

        } catch (\Throwable $th) {
            throw $th;
        }
        return Helper::SuccessResponse($final_data);
    }
    
    public function search____3(Request $request)
    {
        try {
            // return $request;
            $date=$request->date;
            $arn_no=$request->arn_no;
            $date=date('Y-m-d');
            // $date='2025-01-12';
            if ($date || $arn_no) {
                $rawQuery='';
                $rawQueryBroker='';
                if ($date) {
                    $condition_v=(strlen($rawQuery) > 0)? " AND ":" ";
                    $queryString='trans_date';
                    $rawQuery.=$condition_v.$queryString." <= '".$date."'";
                    
                    // $condition_v1=(strlen($rawQueryBroker) > 0)? " AND ":" ";
                    // $queryString1='trans_date';
                    // $rawQueryBroker.=$condition_v1.$queryString1."<= '".$date."'";
                }
            } 

            // WHERE trans_date <= '2025-01-31' 
            // session()->forget('date');
            // session(['date' => $date]);
            // return $rawQuery;
            /******************************************************* */
            $all_data=DB::select("WITH 
            cte1 AS (
                SELECT product_code, SUM(units * (in_out = 'out')) went_out,
                SUM(amount * (in_out = 'out')) went_out_amount
                FROM td_mutual_fund_trans_2
                WHERE ".$rawQuery." 
                GROUP BY product_code
            ),
            cte2 AS (
                SELECT *, SUM(units * (in_out = 'in')) OVER (PARTITION BY product_code ORDER BY trans_date) cal_units,
                SUM(amount * (in_out = 'in')) OVER (PARTITION BY product_code ORDER BY trans_date) cal_amount
                FROM td_mutual_fund_trans_2 WHERE ".$rawQuery."
            )
            SELECT rnt_id,amc_code,folio_no,product_code,trans_date,pur_price,units,amount,stamp_duty,
            in_out, amc_name,scheme_name,cat_name,subcat_name,plan_name,option_name,
            CASE WHEN cal_amount - amount < in_out THEN cal_amount - in_out ELSE amount END result_amount,
            CASE WHEN cal_units - units < in_out THEN cal_units - in_out ELSE units END result
            FROM cte1
            JOIN cte2 USING (product_code)
            WHERE in_out = 'in'
            AND in_out < cal_units
            ORDER BY trans_date ASC");

            // $all_data=DB::select("WITH 
            // cte1 AS (
            //     SELECT product_code, SUM(units * (in_out = 'out')) went_out,
            //     SUM(amount * (in_out = 'out')) went_out_amount
            //     FROM td_mutual_fund_trans_merge
            //     GROUP BY product_code
            // ),
            // cte2 AS (
            //     SELECT *, SUM(units * (in_out = 'in')) OVER (PARTITION BY product_code ORDER BY trans_date) cal_units,
            //     SUM(amount * (in_out = 'in')) OVER (PARTITION BY product_code ORDER BY trans_date) cal_amount
            //     FROM td_mutual_fund_trans_merge
            // )
            // SELECT rnt_id,amc_code,folio_no,product_code,trans_date,pur_price,units,amount,stamp_duty,
            // in_out,
            // (SELECT amc_short_name FROM md_amc WHERE amc_code=amc_code LIMIT 1)AS amc_name,
            // (SELECT scheme_name FROM md_scheme WHERE id=(SELECT scheme_id FROM md_scheme_isin WHERE product_code=product_code LIMIT 1) LIMIT 1)AS scheme_name,
            // (SELECT plan_name FROM md_plan WHERE id=(SELECT plan_id FROM md_scheme_isin WHERE product_code=product_code LIMIT 1) LIMIT 1)AS plan_name,
            // (SELECT opt_name FROM md_option WHERE id=(SELECT option_id FROM md_scheme_isin WHERE product_code=product_code LIMIT 1) LIMIT 1)AS option_name,
            // (SELECT cat_name FROM md_category WHERE id=(SELECT category_id FROM md_scheme WHERE id=(SELECT scheme_id FROM md_scheme_isin WHERE product_code=product_code LIMIT 1) LIMIT 1) LIMIT 1)AS cat_name,
            // (SELECT subcategory_name FROM md_subcategory WHERE id=(SELECT subcategory_id FROM md_scheme WHERE id=(SELECT scheme_id FROM md_scheme_isin WHERE product_code=product_code LIMIT 1) LIMIT 1) LIMIT 1)AS subcat_name,
            // CASE WHEN cal_amount - amount < in_out THEN cal_amount - in_out ELSE amount END result_amount,
            // CASE WHEN cal_units - units < in_out THEN cal_units - in_out ELSE units END result
            // FROM cte1
            // JOIN cte2 USING (product_code)
            // WHERE in_out = 'in'
            // AND in_out < cal_units
            // ORDER BY trans_date ASC");

            
            // return $sp_data;
            // $all_data = collect($all_data)->map(function($x){ return (array) $x; })->toArray(); 
            /******************************************************* */
            // return DB::select('SELECT count(*) FROM v_aum_report');
            // DB::enableQueryLog();
            // MFTransTypeSubType::get()->toArray();
            // dd(DB::getQueryLog());
            // IF amc code
            // $all_data=DB::select('SELECT * FROM v_my_aum_report');
            // $all_data=DB::select('SELECT * FROM v_aum_report where amc_code IN ("IF","189") and '.$rawQuery.' order by trans_date asc');
            // $broker_data=DB::select('SELECT * FROM v_broker_change_report where amc_code IN ("IF","189") order by trans_date asc');
            // dd(DB::getQueryLog());
            // return $all_data[0];
            // return count($all_data);
            /******************************************************* */
            /** start for get nav data */
            $all_trans_product=[];
            $group_amc_data=[];
            foreach ($all_data as $value_product_code) {
                $f_trans_product="(nav_date=(SELECT MAX(nav_date) FROM td_nav_details WHERE product_code='".$value_product_code->product_code."' AND nav_date <='".$date."') AND product_code='".$value_product_code->product_code."')";
                array_push($all_trans_product,$f_trans_product);
                $group_amc_data[$value_product_code->amc_name][$value_product_code->scheme_name][]=$value_product_code;
                // $group_amc_data[$value_product_code->amc_code][$value_product_code->product_code][]=$value_product_code;
            }
            // return $all_trans_product;
            $res_array=[];
            if (count($all_data)>0) {
                $all_trans_product_unique = array_unique($all_trans_product);
                $string_version_product_code = implode(',', $all_trans_product_unique);
                $res_array =DB::connection('mysql_nav')
                    ->select('SELECT product_code,isin_no,DATE_FORMAT(nav_date, "%Y-%m-%d") as nav_date,nav FROM td_nav_details where '.str_replace(",","  OR  ",$string_version_product_code));
            }
            // return $res_array[0];
            // return 'ddd';
            /** end for get nav data */
            // return $all_data[0];
            // $group_amc_data=[];
            // foreach ($all_data as $value_amc_data) {
            //     $group_amc_data[$value_amc_data->amc_code][$value_amc_data->product_code][]=$value_amc_data;
            // }
            // return $group_amc_data;
            $final_data=[];
            foreach ($group_amc_data as $key_group_amc_data => $value_group_amc_data) {  // amc loop
                // return $value_group_amc_data;
                foreach ($value_group_amc_data as $key_amc_wise_product_loop => $value_amc_wise_product_loop) { //amc wise product loop
                    // return $value_amc_wise_product_loop; //RMFSCGP
                    $tot_units = 0;
                    $result_amount=0;
                    foreach($value_amc_wise_product_loop as $value)
                    {
                    $tot_units+= $value->result;
                    $result_amount+= $value->result_amount;
                    }
                    $my_data=[];
                    $product_code=$value_amc_wise_product_loop[0]->product_code;
                    $my_data['amc_name']=$key_group_amc_data;
                    $my_data['amc_code']=$value_amc_wise_product_loop[0]->amc_code;
                    $my_data['product_code']=$product_code;
                    $my_data['scheme_name']=$key_amc_wise_product_loop;
                    $my_data['cat_name']=$value_amc_wise_product_loop[0]->cat_name;
                    $my_data['subcat_name']=$value_amc_wise_product_loop[0]->subcat_name;
                    $my_data['plan_name']=$value_amc_wise_product_loop[0]->plan_name;
                    $my_data['option_name']=$value_amc_wise_product_loop[0]->option_name;
                    $my_data['tot_units']=$tot_units;
                    $my_data['inv_cost']=$result_amount;
                    $new='';
                    if (count($res_array) > 0) {
                        foreach($res_array as $val_nav){
                            if($val_nav->product_code==$product_code){
                                $new=$val_nav;
                            }
                        }
                    }
                    $my_data['new']=$new;
                    $my_data['curr_nav']=isset($new->nav)?$new->nav:0;
                    $my_data['nav_date']=isset($new->nav_date)?$new->nav_date:0;

                    $my_data['curr_aum']=number_format((float)($my_data['tot_units'] * $my_data['curr_nav']), 2, '.', '');
                    // start calculation
                    // return $my_data;
                    // $calculate_mydata=AumHelper::calculate($value_amc_wise_product_loop,$my_data['curr_nav'],$my_data['nav_date']);
                    // $my_data['calculate_mydata']=$calculate_mydata;
                    // $my_data['inv_cost']=$calculate_mydata['inv_cost'];
                    // $my_data['tot_units']=$calculate_mydata['tot_units'];
                    // $my_data['idcw_reinv']=$calculate_mydata['idcw_reinv'];
                    // $my_data['idcw_paid']=$calculate_mydata['idcw_paid'];
                    $my_data['idcw_reinv']=0;
                    $my_data['idcw_paid']=0;
                    $my_data['idcwr']=0;
                    $my_data['curr_aum']= number_format((float)($my_data['curr_nav'] * $my_data['tot_units']), 2, '.', '');
                    $my_data['gain_loss']=number_format((float)(($my_data['curr_aum'] - $my_data['inv_cost']) + $my_data['idcwr']), 2, '.', '');
                    $my_data['abs_rtn']= ($my_data['gain_loss']!=0 || $my_data['inv_cost']!=0)?number_format((float)(($my_data['gain_loss'] / $my_data['inv_cost']) * 100), 2, '.', ''):0;
                    array_push($final_data,$my_data);
                }
            }
            usort($final_data, function($a, $b) {
                return $a['amc_name'] <=> $b['amc_name'];
            });
            // return $final_data;
            // $filter_data=[];
            // foreach ($all_data as $data_key => $value1) {
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
            //     // if ($value1->tot_amount > 0) {
            //         $json  = json_encode($foliotrans);
            //         $array = json_decode($json, true);
            //         if (array_search('Consolidation In',array_column($array,'transaction_subtype'))) {
            //             $foliotrans=TransHelper::ConsolidationInQuery($value1->rnt_id,$value1->folio_no,$value1->isin_no,$value1->product_code,$valuation_as_on);
            //         }
            //         $mydata=TransHelper::calculate($foliotrans,$value1->curr_nav,$valuation_as_on);
            //     // }
            //     // $mydata=$this->calculate($value1->foliotrans);
            //     $value1->mydata=$mydata;
            //     $value1->nifty50=isset($mydata['nifty50'])?(int)$mydata['nifty50']:$value1->nifty50;
            //     $value1->sensex=isset($mydata['sensex'])?(int)$mydata['sensex']:$value1->sensex;
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
            // return $all_values_data[0];
            // return 'ddd';
            
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($final_data);
    }

    public function search1(Request $request)
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
            $md_mf_trans_type_subtype=MFTransTypeSubType::get()->toArray();

            $all_data=MutualFundTransaction::
                with(['schemes' =>  function ($query) {
                    $query->select('rnt_id','amc_code','product_code','isin_no');
                },'schemes.transdetails'=>function ($query) {
                    $query->select('rnt_id','amc_code','folio_no','product_code','isin_no','trans_date','trxn_type_code','trxn_type_flag','trxn_nature_code','kf_trans_type','trans_flag','amount','stamp_duty','tds','units','pur_price')
                    ->selectRaw('IF(rnt_id=1,
                    (SELECT trans_type FROM md_mf_trans_type_subtype WHERE c_trans_type_code=trxn_type_code AND c_k_trans_type=trxn_type_flag AND c_k_trans_sub_type=trxn_nature_code limit 1),
                    (CASE 
                        WHEN trans_flag="DP" || trans_flag="DR" THEN (SELECT trans_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=kf_trans_type AND k_divident_flag=trans_flag limit 1)
                        WHEN trans_flag="TO" THEN "Transfer Out"
                        ELSE (SELECT trans_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=kf_trans_type limit 1)
                    END)
                    )as transaction_type')
                    ->selectRaw('IF(rnt_id=1,
                    (SELECT trans_sub_type FROM md_mf_trans_type_subtype WHERE c_trans_type_code=trxn_type_code AND c_k_trans_type=trxn_type_flag AND c_k_trans_sub_type=trxn_nature_code limit 1),
                    (CASE 
                        WHEN trans_flag="DP" || trans_flag="DR" THEN (SELECT trans_sub_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=kf_trans_type AND k_divident_flag=trans_flag limit 1)
                        WHEN trans_flag="TO" THEN "Transfer Out"
                        ELSE (SELECT trans_sub_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=kf_trans_type limit 1)
                    END)
                    )as transaction_subtype')
                    ->selectRaw('sum(units) as tot_units')
                    ->selectRaw('sum(amount) as tot_amount')
                    ->selectRaw('sum(stamp_duty) as tot_stamp_duty')
                    ->selectRaw('IF(tds!="",sum(tds),0.00)as tot_tds');
                }])
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
            // usort($data, function($a, $b) {
            //     return $a['scheme_name'] <=> $b['scheme_name'];
            // });
            // // return $data;
            // return $all_data;
            $all_trans_product=[];
            $all_product_code=[];
            foreach ($all_data as $all_data_key => $all_data_value) {
                // return $all_data_value;
                foreach ($all_data_value->schemes as $key => $single_scheme) {
                    // return $single_scheme;
                    $f_trans_product="(nav_date=(SELECT MAX(nav_date) FROM td_nav_details WHERE product_code='".$single_scheme->product_code."' AND nav_date <='".$date."') AND product_code='".$single_scheme->product_code."')";
                    array_push($all_trans_product,$f_trans_product);
                    array_push($all_product_code,"'".$single_scheme->product_code."'");
                }
            }
            // return $all_trans_product;
            $res_array=[];
            if (count($all_data)>0) {
                $string_product_code = implode(',', $all_product_code);
                // return $string_product_code;
                $all_scheme_name=SchemeISIN::leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                    ->leftJoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
                    ->leftJoin('md_option','md_option.id','=','md_scheme_isin.option_id')
                    ->select('md_scheme_isin.product_code','md_scheme_isin.isin_no','md_scheme.scheme_name','md_plan.plan_name as plan_name','md_option.opt_name as option_name')
                    ->whereRaw('md_scheme_isin.product_code IN ('.$string_product_code.')')
                    ->get()
                    ->toArray();
                $string_version_product_code = implode(',', $all_trans_product);
                $res_array =DB::connection('mysql_nav')
                    ->select('SELECT product_code,isin_no,DATE_FORMAT(nav_date, "%Y-%m-%d") as nav_date,nav FROM td_nav_details where '.str_replace(",","  OR  ",$string_version_product_code));
            }

            $all_data_arr=[];
            foreach ($all_data as $all_data_key1 => $all_data_value1) {
                $all_schemes_arr=[];
                foreach ($all_data_value1->schemes as $schemes_key1 => $single_scheme1) {
                    $scheme_name='';
                    if (count($all_scheme_name) > 0) {
                        foreach($all_scheme_name as $val_scheme_name){
                            if($val_scheme_name['product_code']==$single_scheme1->product_code){
                                $scheme_name=$val_scheme_name;
                            }
                        }
                    }
                    $single_scheme1->scheme_name=$scheme_name['scheme_name'];
                    $single_scheme1->plan_name=$scheme_name['plan_name'];
                    $single_scheme1->option_name=$scheme_name['option_name'];
                    $new='';
                    if (count($res_array) > 0) {
                        foreach($res_array as $val_nav){
                            if($val_nav->product_code==$single_scheme1->product_code){
                                $new=$val_nav;
                            }
                        }
                    }
                    $single_scheme1->curr_nav=isset($new->nav)?$new->nav:0;
                    $single_scheme1->nav_date=isset($new->nav_date)?$new->nav_date:0;
                    $new='';
                    if (count($res_array) > 0) {
                        foreach($res_array as $val_nav){
                            if($val_nav->product_code==$single_scheme1->product_code){
                                $new=$val_nav;
                            }
                        }
                    }
                    $single_scheme1->curr_nav=isset($new->nav)?$new->nav:0;
                    $single_scheme1->nav_date=isset($new->nav_date)?$new->nav_date:0;
                    $transdetails=$single_scheme1->transdetails;
                    // $mydata=TransHelper::aum_calculate($transdetails);
                    // $single_scheme1->mydata=$mydata;
                    // $single_scheme1->transdetails='';
                    // return $single_scheme1;
                    // $all_transdetails_arr=[];
                    // foreach ($single_scheme1->transdetails as $transdetails_key => $trans_value) {
                    //     $trans_type_subtype='';
                    //     if (count($md_mf_trans_type_subtype) > 0) {
                    //         if ($trans_value['rnt_id']=="1") {
                    //             // return $trans_value;
                    //             foreach($md_mf_trans_type_subtype as $single_type_subtype_key => $single_type_subtype){
                    //                 // return $single_type_subtype;
                    //                 if ($single_type_subtype['rnt_id']==$trans_value['rnt_id'] && $single_type_subtype['c_trans_type_code']==$trans_value['trxn_type_code'] && $single_type_subtype['c_k_trans_type']==$trans_value['trxn_type_flag'] && $single_type_subtype['c_k_trans_sub_type']==$trans_value['trxn_nature_code']) {
                    //                     $trans_type_subtype=$single_type_subtype;
                    //                 }
                    //             }
                    //         } elseif ($trans_value['rnt_id']=="2") {
                    //             switch ($trans_value['trans_flag']) {
                    //                 case "DP" || "DR":
                    //                     foreach($md_mf_trans_type_subtype as $single_type_subtype_key => $single_type_subtype){
                    //                         if ($single_type_subtype['rnt_id']==$trans_value['rnt_id'] && $single_type_subtype['c_k_trans_sub_type']==$trans_value['kf_trans_type'] && $single_type_subtype['k_divident_flag']==$trans_value['trans_flag']) {
                    //                             $trans_type_subtype=$single_type_subtype;
                    //                         }
                    //                     }
                    //                     break;
                    //                 case "TO":
                    //                     $trans_type_subtype=[];
                    //                     $trans_type_subtype['trans_type']="Transfer Out";
                    //                     $trans_type_subtype['trans_sub_type']="Transfer Out";
                    //                     break;
                    //                 default:
                    //                     foreach($md_mf_trans_type_subtype as $single_type_subtype_key => $single_type_subtype){
                    //                         if ($single_type_subtype['rnt_id']==$trans_value['rnt_id'] && $single_type_subtype['c_k_trans_sub_type']==$trans_value['kf_trans_type']) {
                    //                             $trans_type_subtype=$single_type_subtype;
                    //                         }
                    //                     }
                    //                     break;
                    //             }
                    //         }
                    //     }
                    //     $trans_value->trans_type=isset($trans_type_subtype['trans_type'])?$trans_type_subtype['trans_type']:'';
                    //     $trans_value->trans_sub_type=isset($trans_type_subtype['trans_sub_type'])?$trans_type_subtype['trans_sub_type']:'';
                    //     // return $trans_type_subtype;
                    //     array_push($all_transdetails_arr,$trans_value);
                    // }
                    array_push($all_schemes_arr,$single_scheme1);
                }
                array_push($all_data_arr,$all_data_value1);
            }
            
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($all_data_arr);
    }

    public function search1_old(Request $request)
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
            $md_mf_trans_type_subtype=MFTransTypeSubType::get()->toArray();

            $all_data=MutualFundTransaction::
                with(['schemes' =>  function ($query) {
                    $query->select('rnt_id','amc_code','product_code','isin_no');
                },'schemes.transdetails'=>function ($query) {
                    $query->select('rnt_id','amc_code','folio_no','product_code','isin_no','trans_date','trxn_type_code','trxn_type_flag','trxn_nature_code','kf_trans_type','trans_flag','amount','stamp_duty','tds','units','pur_price')
                    ->selectRaw('IF(rnt_id=1,
                    (SELECT trans_type FROM md_mf_trans_type_subtype WHERE c_trans_type_code=trxn_type_code AND c_k_trans_type=trxn_type_flag AND c_k_trans_sub_type=trxn_nature_code limit 1),
                    (CASE 
                        WHEN trans_flag="DP" || trans_flag="DR" THEN (SELECT trans_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=kf_trans_type AND k_divident_flag=trans_flag limit 1)
                        WHEN trans_flag="TO" THEN "Transfer Out"
                        ELSE (SELECT trans_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=kf_trans_type limit 1)
                    END)
                    )as transaction_type')
                    ->selectRaw('IF(rnt_id=1,
                    (SELECT trans_sub_type FROM md_mf_trans_type_subtype WHERE c_trans_type_code=trxn_type_code AND c_k_trans_type=trxn_type_flag AND c_k_trans_sub_type=trxn_nature_code limit 1),
                    (CASE 
                        WHEN trans_flag="DP" || trans_flag="DR" THEN (SELECT trans_sub_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=kf_trans_type AND k_divident_flag=trans_flag limit 1)
                        WHEN trans_flag="TO" THEN "Transfer Out"
                        ELSE (SELECT trans_sub_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=kf_trans_type limit 1)
                    END)
                    )as transaction_subtype')
                    ->selectRaw('sum(units) as tot_units')
                    ->selectRaw('sum(amount) as tot_amount')
                    ->selectRaw('sum(stamp_duty) as tot_stamp_duty')
                    ->selectRaw('IF(tds!="",sum(tds),0.00)as tot_tds');
                }])
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
            // usort($data, function($a, $b) {
            //     return $a['scheme_name'] <=> $b['scheme_name'];
            // });
            // // return $data;
            return $all_data;
            $all_trans_product=[];
            $all_product_code=[];
            foreach ($all_data as $all_data_key => $all_data_value) {
                // return $all_data_value;
                foreach ($all_data_value->schemes as $key => $single_scheme) {
                    // return $single_scheme;
                    $f_trans_product="(nav_date=(SELECT MAX(nav_date) FROM td_nav_details WHERE product_code='".$single_scheme->product_code."' AND nav_date <='".$date."') AND product_code='".$single_scheme->product_code."')";
                    array_push($all_trans_product,$f_trans_product);
                    array_push($all_product_code,"'".$single_scheme->product_code."'");
                }
            }
            // return $all_trans_product;
            $res_array=[];
            if (count($all_data)>0) {
                $string_product_code = implode(',', $all_product_code);
                // return $string_product_code;
                $all_scheme_name=SchemeISIN::leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                    ->leftJoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
                    ->leftJoin('md_option','md_option.id','=','md_scheme_isin.option_id')
                    ->select('md_scheme_isin.product_code','md_scheme_isin.isin_no','md_scheme.scheme_name','md_plan.plan_name as plan_name','md_option.opt_name as option_name')
                    ->whereRaw('md_scheme_isin.product_code IN ('.$string_product_code.')')
                    ->get()
                    ->toArray();
                $string_version_product_code = implode(',', $all_trans_product);
                $res_array =DB::connection('mysql_nav')
                    ->select('SELECT product_code,isin_no,DATE_FORMAT(nav_date, "%Y-%m-%d") as nav_date,nav FROM td_nav_details where '.str_replace(",","  OR  ",$string_version_product_code));
            }

            $all_data_arr=[];
            foreach ($all_data as $all_data_key1 => $all_data_value1) {
                $all_schemes_arr=[];
                foreach ($all_data_value1->schemes as $schemes_key1 => $single_scheme1) {
                    $scheme_name='';
                    if (count($all_scheme_name) > 0) {
                        foreach($all_scheme_name as $val_scheme_name){
                            if($val_scheme_name['product_code']==$single_scheme1->product_code){
                                $scheme_name=$val_scheme_name;
                            }
                        }
                    }
                    $single_scheme1->scheme_name=$scheme_name['scheme_name'];
                    $single_scheme1->plan_name=$scheme_name['plan_name'];
                    $single_scheme1->option_name=$scheme_name['option_name'];
                    $all_transdetails_arr=[];
                    foreach ($single_scheme1->transdetails as $transdetails_key => $trans_value) {
                        $trans_type_subtype;
                        if (count($md_mf_trans_type_subtype) > 0) {
                            if ($trans_value['rnt_id']=="1") {
                                // return $trans_value;
                                foreach($md_mf_trans_type_subtype as $single_type_subtype_key => $single_type_subtype){
                                    // return $single_type_subtype;
                                    if ($single_type_subtype['rnt_id']==$trans_value['rnt_id'] && $single_type_subtype['c_trans_type_code']==$trans_value['trxn_type_code'] && $single_type_subtype['c_k_trans_type']==$trans_value['trxn_type_flag'] && $single_type_subtype['c_k_trans_sub_type']==$trans_value['trxn_nature_code']) {
                                        $trans_type_subtype=$single_type_subtype;
                                    }
                                }
                            } elseif ($trans_value['rnt_id']=="2") {
                                switch ($trans_value['trans_flag']) {
                                    case "DP" || "DR":
                                        foreach($md_mf_trans_type_subtype as $single_type_subtype_key => $single_type_subtype){
                                            if ($single_type_subtype['rnt_id']==$trans_value['rnt_id'] && $single_type_subtype['c_k_trans_sub_type']==$trans_value['kf_trans_type'] && $single_type_subtype['k_divident_flag']==$trans_value['trans_flag']) {
                                                $trans_type_subtype=$single_type_subtype;
                                            }
                                        }
                                        break;
                                    case "TO":
                                        $trans_type_subtype=[];
                                        $trans_type_subtype['trans_type']="Transfer Out";
                                        $trans_type_subtype['trans_sub_type']="Transfer Out";
                                        break;
                                    default:
                                        foreach($md_mf_trans_type_subtype as $single_type_subtype_key => $single_type_subtype){
                                            if ($single_type_subtype['rnt_id']==$trans_value['rnt_id'] && $single_type_subtype['c_k_trans_sub_type']==$trans_value['kf_trans_type']) {
                                                $trans_type_subtype=$single_type_subtype;
                                            }
                                        }
                                        break;
                                }
                            }
                        }
                        $trans_value->trans_type=isset($trans_type_subtype['trans_type'])?$trans_type_subtype['trans_type']:'';
                        $trans_value->trans_sub_type=isset($trans_type_subtype['trans_sub_type'])?$trans_type_subtype['trans_sub_type']:'';
                        // return $trans_type_subtype;
                        array_push($all_transdetails_arr,$trans_value);
                    }
                    // $single_scheme1->transdetails='';
                    array_push($all_schemes_arr,$single_scheme1);
                }
                array_push($all_data_arr,$all_data_value1);
            }
            
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($all_data_arr);
    }


    public function aumByScheme_old(Request $request)
    {
        try {
            // return $request;
            $valuation_as_on=$request->valuation_as_on;
            $valuation_as_on=date('Y-m-d');
            $product_code=$request->product_code;
            // $product_code='IFIQRG';
            $view_type=$request->view_type;
            $pan_no=$request->pan_no;
            $client_name=$request->client_name;
            $view_funds_type=$request->view_funds_type;

            session()->forget('valuation_as_on');
            session(['valuation_as_on' => $valuation_as_on]);
            // return Session::get('valuation_as_on');
            $client_details='';
            if ($product_code || $valuation_as_on || $view_funds_type) {
                $rawQuery='';
                if ($valuation_as_on) {
                    $condition_v=(strlen($rawQuery) > 0)? " AND ":" ";
                    $queryString='td_mutual_fund_trans.trans_date';
                    $rawQuery.=$condition_v.$queryString."<= '".$valuation_as_on."'";
                }
                if ($product_code) {
                    $condition_v=(strlen($rawQuery) > 0)? " AND ":" ";
                    $queryString='td_mutual_fund_trans.product_code';
                    $rawQuery.=$condition_v.$queryString."= '".$product_code."'";
                }
                // if ($view_type=='C') {
                //     $client_rawQuery='';
                //     if (!$pan_no) {
                //         $queryString='td_mutual_fund_trans.first_client_name';
                //         $rawQuery.=Helper::WhereRawQuery($client_name,$rawQuery,$queryString);
                //         $client_queryString='md_client.client_name';
                //         $client_rawQuery.=Helper::WhereRawQuery($client_name,$client_rawQuery,$client_queryString);
                //     }else {
                //         $queryString='td_mutual_fund_trans.first_client_pan';
                //         $rawQuery.=Helper::WhereRawQuery($pan_no,$rawQuery,$queryString);
                //         $client_queryString='md_client.pan';
                //         $client_rawQuery.=Helper::WhereRawQuery($pan_no,$client_rawQuery,$client_queryString);
                //     }
                //     $client_details=TransHelper::getClientDetails($client_rawQuery,$view_type);
                // }else {
                //     $queryString='td_mutual_fund_trans.first_client_pan';
                //     $condition=(strlen($rawQuery) > 0)? " AND (":" (";
                //     $row_name_string=  "'" .implode("','", $family_members_pan). "'";
                //     $rawQuery.=$condition.$queryString." IN (".$row_name_string.")";
                //     $queryString='td_mutual_fund_trans.first_client_name';
                //     $condition1=(strlen($rawQuery) > 0)? " OR ":" ";
                //     $row_name_string1=  "'" .implode("','", $family_members_name). "'";
                //     $rawQuery.=$condition1.$queryString." IN (".$row_name_string1."))";
                // }
                // if ($view_funds_type=='S') {
                //     $selected_funds=json_decode($request->selected_funds);
                //     $condition_selected_funds=(strlen($rawQuery) > 0)? " AND ":" ";
                //     foreach ($selected_funds as $single_fund_key => $single_fund) {
                //         // return $single_fund;
                //         if ($single_fund_key==0) {
                //            $rawQuery.=$condition_selected_funds."td_mutual_fund_trans.product_code IN (";
                //         } 
                //         $rawQuery.="'".$single_fund->product_code."'";
                //         if ($single_fund_key==(count($selected_funds)-1)) {
                //             $rawQuery.=")";
                //         }else {
                //             $rawQuery.=",";
                //         }
                //         // $condition_selected_funds=(strlen($rawQuery) > 0)? " AND ":" ";
                //         // $rawQuery.=$condition_selected_funds." (td_mutual_fund_trans.folio_no='".$single_fund->folio_no."' AND td_mutual_fund_trans.product_code='".$single_fund->product_code."' AND td_mutual_fund_trans.isin_no='".$single_fund->isin_no."')";
                //         // $rawQuery.=$condition_selected_funds." (td_mutual_fund_trans.folio_no='".$single_fund->folio_no."' AND td_mutual_fund_trans.product_code='".$single_fund->product_code."')";
                //     }
                // }elseif ($view_funds_type=='T') {
                //     $selected_type=json_decode($request->selected_type);
                //     $condition_selected_type=(strlen($rawQuery) > 0)? " AND ":" ";
                //     foreach ($selected_type as $single_fund_key => $single_fund) {
                //         // return $single_fund;
                //         if ($single_fund_key==0) {
                //            $rawQuery.=$condition_selected_type."td_mutual_fund_trans.product_code IN (";
                //         } 
                //         $rawQuery.="'".$single_fund->product_code."'";
                //         if ($single_fund_key==(count($selected_type)-1)) {
                //             $rawQuery.=")";
                //         }else {
                //             $rawQuery.=",";
                //         }
                //     }
                // }
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
                'md_plan.plan_name as plan_name','md_option.opt_name as option_name','td_mutual_fund_trans.first_client_name as client_name','td_mutual_fund_trans.first_client_pan as client_pan')
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
                $final_profitloss=[];
                foreach ($profitloss as $key => $profitloss_value) {
                    if ($profitloss_value->transaction_type=="Transfer In" && $profitloss_value->transaction_subtype=="Transfer In") {
                        $broker_data=TransHelper::getBrokerData($profitloss_value);
                        foreach ($broker_data as $broker_key => $broker_value) {
                            array_push($final_profitloss,$broker_value);
                        }
                    }else {
                        array_push($final_profitloss,$profitloss_value);
                    }
                }
                $profitloss=$final_profitloss;
                $value1->final_profitloss=$profitloss;
                $purchase=0;
                $switch_in=0;
                $tot_inflow=0;
                $redemption=0;
                $switch_out=0;
                $idcw_reinv=0;
                $idcwp=0;
                $tot_outflow=0;
                if ($value1->tot_amount > 0) {
                    foreach ($profitloss as $key_1 => $profitloss_value_1) {
                        if ($profitloss_value_1->lmf_pl=='PL_P') {
                            $purchase +=$profitloss_value_1->tot_amount;
                        }elseif ($profitloss_value_1->lmf_pl=='PL_R') {
                            $redemption +=$profitloss_value_1->tot_amount;
                        }elseif ($profitloss_value_1->lmf_pl=='PL_SI') {
                            $switch_in +=$profitloss_value_1->tot_amount;
                        }elseif ($profitloss_value_1->lmf_pl=='PL_SO') {
                            $switch_out +=$profitloss_value_1->tot_amount;
                        }elseif ($profitloss_value_1->lmf_pl=='PL_IR') {
                            $idcw_reinv +=$profitloss_value_1->tot_amount;
                        }elseif ($profitloss_value_1->lmf_pl=='PL_IP') {
                            $idcwp +=$profitloss_value_1->tot_amount;
                        }
                    }
                }
                
                $value1->purchase=$purchase;
                $value1->redemption=$redemption;
                $value1->switch_in=$switch_in;
                $value1->switch_out=$switch_out;
                $value1->idcw_reinv=$idcw_reinv;
                $value1->idcwp=$idcwp;
                $value1->tot_inflow=($purchase + $switch_in + $idcw_reinv);
                $value1->tot_outflow=($redemption + $switch_out + $idcwp);

                $my_profitloss=$value1->profitloss;
                $mydata='';
                $json  = json_encode($my_profitloss);
                $array = json_decode($json, true);
                if (array_search('Consolidation In',array_column($array,'transaction_subtype'))) {
                    $my_profitloss=TransHelper::ConsolidationInQuery($value1->rnt_id,$value1->folio_no,$value1->isin_no,$value1->product_code,$valuation_as_on);
                }
                
                $mydata=TransHelper::calculate($my_profitloss,$value1->curr_nav,$valuation_as_on);
                
                $value1->mydata=$mydata;
                $value1->idcw_reinv=isset($mydata['idcw_reinv'])? number_format((float)$mydata['idcw_reinv'], 2, '.', ''):0;
                $value1->idcwr=number_format((float)($value1->idcwp + $value1->idcw_reinv), 2, '.', '');
                $value1->inv_since=isset($mydata['inv_since'])? $mydata['inv_since']:$value1->inv_since;
                $value1->pur_nav=isset($mydata['pur_nav'])?$mydata['pur_nav']:$value1->pur_nav;
                $value1->transaction_type=isset($mydata['transaction_type'])?$mydata['transaction_type']:$value1->transaction_type;
                $value1->transaction_subtype=isset($mydata['transaction_subtype'])?$mydata['transaction_subtype']:$value1->transaction_subtype;
                $value1->inv_cost=isset($mydata['inv_cost'])?number_format((float)$mydata['inv_cost'], 2, '.', ''):0;
                $value1->tot_units=isset($mydata['tot_units'])?number_format((float)$mydata['tot_units'], 2, '.', ''):0;
                $value1->aum= number_format((float)($value1->curr_nav * $value1->tot_units), 2, '.', '');
                
                $value1->gain_loss=number_format((float)(($value1->tot_outflow + $value1->aum) - $value1->tot_inflow), 2, '.', '');
                if ($value1->gain_loss==0 || $value1->tot_inflow==0) {
                    $value1->ret_abs=0;
                }else {
                    $value1->ret_abs=number_format((float)(($value1->gain_loss / $value1->tot_inflow) * 100), 2, '.', '');
                }
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
        return Helper::SuccessResponse($filter_data);
    }
    

    public function aumByClient_old(Request $request)
    {
        try {
            // return $request;
            $date=$request->date;
            $arn_no=$request->arn_no;
            $date=date('Y-m-d');
            // $date='2025-01-12';
            if ($date || $arn_no) {
                $rawQuery='';
                $rawQueryBroker='';
                if ($date) {
                    $condition_v=(strlen($rawQuery) > 0)? " AND ":" ";
                    $queryString='trans_date';
                    $rawQuery.=$condition_v.$queryString." <= '".$date."'";
                    
                    // $condition_v1=(strlen($rawQueryBroker) > 0)? " AND ":" ";
                    // $queryString1='trans_date';
                    // $rawQueryBroker.=$condition_v1.$queryString1."<= '".$date."'";
                }
            } 
            DB::statement("call aum_by_client_report()");
            $all_data=DB::select("SELECT * FROM v_my_aum_report");
            /******************************************************* */
            /** start for get nav data */
            $all_trans_product=[];
            $group_amc_data=[];
            foreach ($all_data as $value_product_code) {
                $f_trans_product="(nav_date=(SELECT MAX(nav_date) FROM td_nav_details WHERE product_code='".$value_product_code->product_code."' AND nav_date <='".$date."') AND product_code='".$value_product_code->product_code."')";
                array_push($all_trans_product,$f_trans_product);
                $group_amc_data[$value_product_code->amc_name][$value_product_code->scheme_name][]=$value_product_code;
                // $group_amc_data[$value_product_code->amc_code][$value_product_code->product_code][]=$value_product_code;
            }
            // return $all_trans_product;
            $res_array=[];
            if (count($all_data)>0) {
                $string_version_product_code = implode(',', $all_trans_product_unique);
                $res_array =DB::connection('mysql_nav')
                    ->select('SELECT product_code,isin_no,DATE_FORMAT(nav_date, "%Y-%m-%d") as nav_date,nav FROM td_nav_details where '.str_replace(",","  OR  ",$string_version_product_code));
            }
            // return $res_array[0];
            // return 'ddd';
            /** end for get nav data */
            $final_data=[];
            foreach ($group_amc_data as $key_group_amc_data => $value_group_amc_data) {  // amc loop
                // return $value_group_amc_data;
                foreach ($value_group_amc_data as $key_amc_wise_product_loop => $value_amc_wise_product_loop) { //amc wise product loop
                    // return $value_amc_wise_product_loop; //RMFSCGP
                    $tot_units = 0;
                    $result_amount=0;
                    foreach($value_amc_wise_product_loop as $value)
                    {
                    $tot_units+= $value->result;
                    $result_amount+= $value->result_amount;
                    }
                    $my_data=[];
                    $product_code=$value_amc_wise_product_loop[0]->product_code;
                    $my_data['first_client_name']=$value_amc_wise_product_loop[0]->first_client_name;
                    $my_data['first_client_pan']=$value_amc_wise_product_loop[0]->first_client_pan;
                    $my_data['amc_name']=$key_group_amc_data;
                    $my_data['amc_code']=$value_amc_wise_product_loop[0]->amc_code;
                    $my_data['product_code']=$product_code;
                    $my_data['scheme_name']=$key_amc_wise_product_loop;
                    $my_data['cat_name']=$value_amc_wise_product_loop[0]->cat_name;
                    $my_data['subcat_name']=$value_amc_wise_product_loop[0]->subcat_name;
                    $my_data['plan_name']=$value_amc_wise_product_loop[0]->plan_name;
                    $my_data['option_name']=$value_amc_wise_product_loop[0]->option_name;
                    $my_data['tot_units']=$tot_units;
                    $my_data['inv_cost']=$result_amount;
                    $new='';
                    if (count($res_array) > 0) {
                        foreach($res_array as $val_nav){
                            if($val_nav->product_code==$product_code){
                                $new=$val_nav;
                            }
                        }
                    }
                    $my_data['new']=$new;
                    $my_data['curr_nav']=isset($new->nav)?$new->nav:0;
                    $my_data['nav_date']=isset($new->nav_date)?$new->nav_date:0;

                    $my_data['curr_aum']=number_format((float)($my_data['tot_units'] * $my_data['curr_nav']), 2, '.', '');
                    // start calculation
                    // return $my_data;
                    // $calculate_mydata=AumHelper::calculate($value_amc_wise_product_loop,$my_data['curr_nav'],$my_data['nav_date']);
                    // $my_data['calculate_mydata']=$calculate_mydata;
                    // $my_data['inv_cost']=$calculate_mydata['inv_cost'];
                    // $my_data['tot_units']=$calculate_mydata['tot_units'];
                    // $my_data['idcw_reinv']=$calculate_mydata['idcw_reinv'];
                    // $my_data['idcw_paid']=$calculate_mydata['idcw_paid'];
                    $my_data['idcw_reinv']=0;
                    $my_data['idcw_paid']=0;
                    $my_data['idcwr']=0;
                    $my_data['curr_aum']= number_format((float)($my_data['curr_nav'] * $my_data['tot_units']), 2, '.', '');
                    $my_data['gain_loss']=number_format((float)(($my_data['curr_aum'] - $my_data['inv_cost']) + $my_data['idcwr']), 2, '.', '');
                    $my_data['abs_rtn']= ($my_data['gain_loss']!=0 || $my_data['inv_cost']!=0)?number_format((float)(($my_data['gain_loss'] / $my_data['inv_cost']) * 100), 2, '.', ''):0;
                    array_push($final_data,$my_data);
                }
            }
            usort($final_data, function($a, $b) {
                return $a['amc_name'] <=> $b['amc_name'];
            });
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($final_data);
    }
    /*********************aum report as ************************** */
    public function search(Request $request)
    {
        try {
            // return $request;
            $date=$request->date;
            // $arn_no=$request->arn_no;
            $amc_id=json_decode($request->amc_id);
            $cat_id=json_decode($request->cat_id);
            $sub_cat_id=json_decode($request->sub_cat_id);
            $scheme_id=json_decode($request->scheme_id);
            // return $amc_id;
            // $date=date('Y-m-d');
            // $date='2025-01-12';
            $rnt_id=$request->rnt_id;

            if ($date || $rnt_id || !empty($amc_id) || !empty($cat_id) || !empty($sub_cat_id) || !empty($scheme_id)) {
                $rawQuery='';
                $rawQueryBroker='';
                if ($date) {
                    $condition_v=(strlen($rawQuery) > 0)? " AND ":" ";
                    $queryString='td_mutual_fund_trans_aum.trans_date';
                    $rawQuery.=$condition_v.$queryString."=(SELECT MAX(trans_date) FROM td_mutual_fund_trans_aum WHERE trans_date <='".$date."')";
                }
                if ($rnt_id) {
                    $queryString='td_mutual_fund_trans_aum.rnt_id';
                    $rawQuery.=Helper::WhereRawQuery($rnt_id,$rawQuery,$queryString);
                }

                $queryString='md_scheme.amc_id';
                $rawQuery.=Helper::WhereRawQuery($amc_id,$rawQuery,$queryString);
                $queryString='md_scheme.category_id';
                $rawQuery.=Helper::WhereRawQuery($cat_id,$rawQuery,$queryString);
                $queryString='md_scheme.subcategory_id';
                $rawQuery.=Helper::WhereRawQuery($sub_cat_id,$rawQuery,$queryString);
                $queryString='md_scheme_isin.scheme_id';
                $rawQuery.=Helper::WhereRawQuery($scheme_id,$rawQuery,$queryString);

            }
                
            // WHERE trans_date <= '2025-01-31' 
            // session()->forget('date');
            // session(['date' => $date]);
            // return $rawQuery;
            /******************************************************* */
            // $all_data=DB::select('SELECT *,SUM(total_unit) AS tot_units,SUM(total_inv_cost) as inv_cost FROM td_mutual_fund_trans_aum where trans_date=(SELECT MAX(trans_date) FROM td_mutual_fund_trans_aum WHERE trans_date <="'.$date.'") GROUP BY amc_code,product_code');
            $all_data=AumReport::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_mutual_fund_trans_aum.product_code')
                ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                ->leftJoin('md_rnt','md_rnt.id','=','td_mutual_fund_trans_aum.rnt_id')
                ->selectRaw('td_mutual_fund_trans_aum.*,SUM(td_mutual_fund_trans_aum.total_unit) AS tot_units, SUM(td_mutual_fund_trans_aum.total_inv_cost) as inv_cost,md_rnt.rnt_name')
                ->whereRaw($rawQuery)
                ->groupBy('td_mutual_fund_trans_aum.amc_code','td_mutual_fund_trans_aum.product_code')
                ->get();
            // return $all_data;
            /******************************************************* */
            /** start for get nav data */
            $all_trans_product=[];
            foreach ($all_data as $value_product_code) {
                $f_trans_product="(nav_date=(SELECT MAX(nav_date) FROM td_nav_details WHERE product_code='".$value_product_code->product_code."' AND nav_date <='".$date."') AND product_code='".$value_product_code->product_code."')";
                array_push($all_trans_product,$f_trans_product);
            }
            // return $all_trans_product;
            $res_array=[];
            if (count($all_data)>0) {
                $string_version_product_code = implode(',', $all_trans_product);
                $res_array =DB::connection('mysql_nav')
                    ->select('SELECT product_code,isin_no,DATE_FORMAT(nav_date, "%Y-%m-%d") as nav_date,nav FROM td_nav_details where '.str_replace(",","  OR  ",$string_version_product_code));
            }
            // return $res_array[0];
            // return 'ddd';
            /** end for get nav data */
            // return $all_data[0];
            // $group_amc_data=[];
            // foreach ($all_data as $value_amc_data) {
            //     $group_amc_data[$value_amc_data->amc_code][$value_amc_data->product_code][]=$value_amc_data;
            // }
            // return $all_data;
            $final_data=[];
            foreach ($all_data as $key_group_amc_data => $value_group_amc_data) {  // amc loop
                // return $value_group_amc_data;
                $product_code=$value_group_amc_data->product_code;
                $new='';
                if (count($res_array) > 0) {
                    foreach($res_array as $val_nav){
                        if($val_nav->product_code==$product_code){
                            $new=$val_nav;
                        }
                    }
                }
                $value_group_amc_data->new=$new;
                $value_group_amc_data->curr_nav=isset($new->nav)?$new->nav:0;
                $value_group_amc_data->nav_date=isset($new->nav_date)?$new->nav_date:0;
                $value_group_amc_data->idcw_reinv=0;
                $value_group_amc_data->idcw_paid=0;
                $value_group_amc_data->idcwr=0;
                $value_group_amc_data->curr_aum= number_format((float)($value_group_amc_data->curr_nav * $value_group_amc_data->tot_units), 2, '.', '');
                $value_group_amc_data->gain_loss=number_format((float)(($value_group_amc_data->curr_aum - $value_group_amc_data->inv_cost) + $value_group_amc_data->idcwr), 2, '.', '');
                $value_group_amc_data->abs_rtn= ($value_group_amc_data->gain_loss!=0 && $value_group_amc_data->inv_cost!=0)?number_format((float)(($value_group_amc_data->gain_loss / $value_group_amc_data->inv_cost) * 100), 2, '.', ''):0;
                array_push($final_data,$value_group_amc_data);
            }
            // $final_data = collect($final_data)->map(function($x){ return (array) $x; })->toArray(); 
            usort($final_data, function($a, $b) {
                return $a['amc_name'] <=> $b['amc_name'];
                // return $a->amc_name <=> $b->amc_name;
            });
            // return $final_data;
            // $filter_data=[];
            // foreach ($all_data as $data_key => $value1) {
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
            //     // if ($value1->tot_amount > 0) {
            //         $json  = json_encode($foliotrans);
            //         $array = json_decode($json, true);
            //         if (array_search('Consolidation In',array_column($array,'transaction_subtype'))) {
            //             $foliotrans=TransHelper::ConsolidationInQuery($value1->rnt_id,$value1->folio_no,$value1->isin_no,$value1->product_code,$valuation_as_on);
            //         }
            //         $mydata=TransHelper::calculate($foliotrans,$value1->curr_nav,$valuation_as_on);
            //     // }
            //     // $mydata=$this->calculate($value1->foliotrans);
            //     $value1->mydata=$mydata;
            //     $value1->nifty50=isset($mydata['nifty50'])?(int)$mydata['nifty50']:$value1->nifty50;
            //     $value1->sensex=isset($mydata['sensex'])?(int)$mydata['sensex']:$value1->sensex;
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
            // return $all_values_data[0];
            // return 'ddd';
            
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($final_data);
    }

    public function aumByClient(Request $request)
    {
        try {
            // return $request;
            $date=$request->date;
            $product_code=$request->product_code;
            // $arn_no=$request->arn_no;
            $amc_id=json_decode($request->amc_id);
            $cat_id=json_decode($request->cat_id);
            $sub_cat_id=json_decode($request->sub_cat_id);
            $scheme_id=json_decode($request->scheme_id);
            // return $amc_id;
            // $date=date('Y-m-d');
            // $date='2025-01-12';
            if ($date || $product_code || !empty($amc_id) || !empty($cat_id) || !empty($sub_cat_id) || !empty($scheme_id)) {
                $rawQuery='';
                $rawQueryBroker='';
                if ($date) {
                    $condition_v=(strlen($rawQuery) > 0)? " AND ":" ";
                    $queryString='td_mutual_fund_trans_aum.trans_date';
                    $rawQuery.=$condition_v.$queryString."=(SELECT MAX(trans_date) FROM td_mutual_fund_trans_aum WHERE trans_date <='".$date."')";
                }
                if ($product_code) {
                    $queryString='td_mutual_fund_trans_aum.product_code';
                    $rawQuery.=Helper::WhereRawQuery($product_code,$rawQuery,$queryString);
                }

                $queryString='md_scheme.amc_id';
                $rawQuery.=Helper::WhereRawQuery($amc_id,$rawQuery,$queryString);
                $queryString='md_scheme.category_id';
                $rawQuery.=Helper::WhereRawQuery($cat_id,$rawQuery,$queryString);
                $queryString='md_scheme.subcategory_id';
                $rawQuery.=Helper::WhereRawQuery($sub_cat_id,$rawQuery,$queryString);
                $queryString='md_scheme_isin.scheme_id';
                $rawQuery.=Helper::WhereRawQuery($scheme_id,$rawQuery,$queryString);
            }
                
            // WHERE trans_date <= '2025-01-31' 
            // session()->forget('date');
            // session(['date' => $date]);
            // return $rawQuery;
            /******************************************************* */
            // $all_data=DB::select('SELECT *,SUM(total_unit) AS tot_units,SUM(total_inv_cost) as inv_cost FROM td_mutual_fund_trans_aum where trans_date=(SELECT MAX(trans_date) FROM td_mutual_fund_trans_aum WHERE trans_date <="'.$date.'") GROUP BY amc_code,product_code');
            $all_data=AumReport::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_mutual_fund_trans_aum.product_code')
                ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                // ->selectRaw('td_mutual_fund_trans_aum.*,SUM(td_mutual_fund_trans_aum.total_unit) AS tot_units, SUM(td_mutual_fund_trans_aum.total_inv_cost) as inv_cost')
                ->selectRaw('td_mutual_fund_trans_aum.*,SUM(total_unit) AS tot_units,SUM(total_inv_cost) as inv_cost')
                ->whereRaw($rawQuery)
                ->groupBy('td_mutual_fund_trans_aum.folio_no','td_mutual_fund_trans_aum.product_code')
                ->get();
            // return $all_data;
            /******************************************************* */
            /** start for get nav data */
            $all_trans_product=[];
            foreach ($all_data as $value_product_code) {
                $f_trans_product="(nav_date=(SELECT MAX(nav_date) FROM td_nav_details WHERE product_code='".$value_product_code->product_code."' AND nav_date <='".$date."') AND product_code='".$value_product_code->product_code."')";
                array_push($all_trans_product,$f_trans_product);
            }
            // return $all_trans_product;
            $res_array=[];
            if (count($all_data)>0) {
                $all_trans_product_unique = array_unique($all_trans_product);
                // return count($all_trans_product).' - '.count($all_trans_product_unique);
                $string_version_product_code = implode(',', $all_trans_product_unique);
                // return $string_version_product_code;
                // return 'SELECT product_code,isin_no,DATE_FORMAT(nav_date, "%Y-%m-%d") as nav_date,nav FROM td_nav_details where '.str_replace(",","  OR  ",$string_version_product_code);
                $res_array =DB::connection('mysql_nav')
                    ->select('SELECT product_code,isin_no,DATE_FORMAT(nav_date, "%Y-%m-%d") as nav_date,nav FROM td_nav_details where '.str_replace(",","  OR  ",$string_version_product_code));
            }
            // return $all_data;
            // return $res_array[0];
            // return 'ddd';
            /** end for get nav data */
            // return $all_data[0];
            // $group_amc_data=[];
            // foreach ($all_data as $value_amc_data) {
            //     $group_amc_data[$value_amc_data->amc_code][$value_amc_data->product_code][]=$value_amc_data;
            // }
            // return $all_data;
            $final_data=[];
            foreach ($all_data as $key_group_amc_data => $value_group_amc_data) {  // amc loop
                // return $value_group_amc_data;
                $product_code=$value_group_amc_data->product_code;
                $new='';
                if (count($res_array) > 0) {
                    foreach($res_array as $val_nav){
                        if($val_nav->product_code==$product_code){
                            $new=$val_nav;
                        }
                    }
                }
                $value_group_amc_data->new=$new;
                $value_group_amc_data->curr_nav=isset($new->nav)?$new->nav:0;
                $value_group_amc_data->nav_date=isset($new->nav_date)?$new->nav_date:0;
                $value_group_amc_data->idcw_reinv=0;
                $value_group_amc_data->idcw_paid=0;
                $value_group_amc_data->idcwr=0;
                $value_group_amc_data->curr_aum= number_format((float)($value_group_amc_data->curr_nav * $value_group_amc_data->tot_units), 2, '.', '');
                $value_group_amc_data->gain_loss=number_format((float)(($value_group_amc_data->curr_aum - $value_group_amc_data->inv_cost) + $value_group_amc_data->idcwr), 2, '.', '');
                $value_group_amc_data->abs_rtn= ($value_group_amc_data->gain_loss!=0 && $value_group_amc_data->inv_cost!=0)?number_format((float)(($value_group_amc_data->gain_loss / $value_group_amc_data->inv_cost) * 100), 2, '.', ''):0;
                array_push($final_data,$value_group_amc_data);
            }
            // $final_data = collect($final_data)->map(function($x){ return (array) $x; })->toArray(); 
            // usort($final_data, function($a, $b) {
            //     return $a['first_client_name'] <=> $b['first_client_name'];
            //     // return $a->amc_name <=> $b->amc_name;
            // });
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($final_data);
    }

    public function aumByFamily(Request $request)
    {
        try {
            // return $request;
            $date=$request->date;
            $product_code=$request->product_code;
            // $arn_no=$request->arn_no;
            $amc_id=json_decode($request->amc_id);
            $cat_id=json_decode($request->cat_id);
            $sub_cat_id=json_decode($request->sub_cat_id);
            $scheme_id=json_decode($request->scheme_id);
            // return $amc_id;
            $date=date('Y-m-d');
            // $date='2025-01-12';
            if ($date || $product_code || !empty($amc_id) || !empty($cat_id) || !empty($sub_cat_id) || !empty($scheme_id)) {
                $rawQuery='';
                $rawQueryBroker='';
                if ($date) {
                    $condition_v=(strlen($rawQuery) > 0)? " AND ":" ";
                    $queryString='td_mutual_fund_trans_aum.trans_date';
                    $rawQuery.=$condition_v.$queryString."=(SELECT MAX(trans_date) FROM td_mutual_fund_trans_aum WHERE trans_date <='".$date."')";
                }
                if ($product_code) {
                    $queryString='td_mutual_fund_trans_aum.product_code';
                    $rawQuery.=Helper::WhereRawQuery($product_code,$rawQuery,$queryString);
                }

                $queryString='md_scheme.amc_id';
                $rawQuery.=Helper::WhereRawQuery($amc_id,$rawQuery,$queryString);
                $queryString='md_scheme.category_id';
                $rawQuery.=Helper::WhereRawQuery($cat_id,$rawQuery,$queryString);
                $queryString='md_scheme.subcategory_id';
                $rawQuery.=Helper::WhereRawQuery($sub_cat_id,$rawQuery,$queryString);
                $queryString='md_scheme_isin.scheme_id';
                $rawQuery.=Helper::WhereRawQuery($scheme_id,$rawQuery,$queryString);
            }

            // $all_data=ClientFamily::
            // with('client')
            // with(['videos' => function($q) {
            //     $q->select('first_client_name', 'first_client_pan');
            //     $q->where('first_client_pan', '=', 'md_client.pan');
            // }])  
            
            // ->
            // leftjoin('md_client','md_client.id','=','md_client_family.family_id')
            // ->leftjoin('td_mutual_fund_trans_aum','md_client.pan','=','td_mutual_fund_trans_aum.first_client_pan')
            // // ->leftJoin('td_mutual_fund_trans_aum', function ($join) {
            // //     $join->on('md_client.pan', '=', 'td_mutual_fund_trans_aum.first_client_pan');
            // //     $join->on('md_client.name','>=',DB::raw("'%td_mutual_fund_trans_aum.name%'"));

            // // })
            //     ->select('md_client_family.type as family_head_type','md_client_family.client_id as family_head_group_id',
            //     'md_client.client_name as first_client_name','md_client.pan as first_client_pan')
            //     ->get();
            // return $all_data;
            // WHERE trans_date <= '2025-01-31' 
            // session()->forget('date');
            // session(['date' => $date]);
            // return $rawQuery;
            /******************************************************* */
            // $all_data=DB::select('SELECT *,SUM(total_unit) AS tot_units,SUM(total_inv_cost) as inv_cost FROM td_mutual_fund_trans_aum where trans_date=(SELECT MAX(trans_date) FROM td_mutual_fund_trans_aum WHERE trans_date <="'.$date.'") GROUP BY amc_code,product_code');
            $all_data=AumReport::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_mutual_fund_trans_aum.product_code')
                ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                ->leftJoin('md_client','md_client.pan','=','td_mutual_fund_trans_aum.first_client_pan')
                ->leftJoin('md_client_family','md_client_family.family_id','=','md_client.id')
                // ->selectRaw('td_mutual_fund_trans_aum.*,SUM(td_mutual_fund_trans_aum.total_unit) AS tot_units, SUM(td_mutual_fund_trans_aum.total_inv_cost) as inv_cost')
                ->selectRaw('td_mutual_fund_trans_aum.*,SUM(total_unit) AS tot_units,SUM(total_inv_cost) as inv_cost,md_client.id as client_id,md_client_family.type as family_head_type,md_client_family.client_id as family_head_group_id')
                ->whereRaw($rawQuery)
                ->groupBy('td_mutual_fund_trans_aum.folio_no','td_mutual_fund_trans_aum.product_code')
                ->get();
            // return $all_data;
            /******************************************************* */
            /** start for get nav data */
            $all_trans_product=[];
            foreach ($all_data as $value_product_code) {
                $f_trans_product="(nav_date=(SELECT MAX(nav_date) FROM td_nav_details WHERE product_code='".$value_product_code->product_code."' AND nav_date <='".$date."') AND product_code='".$value_product_code->product_code."')";
                array_push($all_trans_product,$f_trans_product);
            }
            // return $all_trans_product;
            $res_array=[];
            if (count($all_data)>0) {
                $all_trans_product_unique = array_unique($all_trans_product);
                // return count($all_trans_product).' - '.count($all_trans_product_unique);
                $string_version_product_code = implode(',', $all_trans_product_unique);
                // return $string_version_product_code;
                // return 'SELECT product_code,isin_no,DATE_FORMAT(nav_date, "%Y-%m-%d") as nav_date,nav FROM td_nav_details where '.str_replace(",","  OR  ",$string_version_product_code);
                $res_array =DB::connection('mysql_nav')
                    ->select('SELECT product_code,isin_no,DATE_FORMAT(nav_date, "%Y-%m-%d") as nav_date,nav FROM td_nav_details where '.str_replace(",","  OR  ",$string_version_product_code));
            }
            // return $all_data;
            // return $res_array[0];
            // return 'ddd';
            /** end for get nav data */
            // return $all_data[0];
            // $group_amc_data=[];
            // foreach ($all_data as $value_amc_data) {
            //     $group_amc_data[$value_amc_data->amc_code][$value_amc_data->product_code][]=$value_amc_data;
            // }
            // return $all_data;
            $final_data=[];
            foreach ($all_data as $key_group_amc_data => $value_group_amc_data) {  // amc loop
                // return $value_group_amc_data;
                $product_code=$value_group_amc_data->product_code;
                $new='';
                if (count($res_array) > 0) {
                    foreach($res_array as $val_nav){
                        if($val_nav->product_code==$product_code){
                            $new=$val_nav;
                        }
                    }
                }
                $value_group_amc_data->new=$new;
                $value_group_amc_data->curr_nav=isset($new->nav)?$new->nav:0;
                $value_group_amc_data->nav_date=isset($new->nav_date)?$new->nav_date:0;
                $value_group_amc_data->idcw_reinv=0;
                $value_group_amc_data->idcw_paid=0;
                $value_group_amc_data->idcwr=0;
                $value_group_amc_data->curr_aum= number_format((float)($value_group_amc_data->curr_nav * $value_group_amc_data->tot_units), 2, '.', '');
                $value_group_amc_data->gain_loss=number_format((float)(($value_group_amc_data->curr_aum - $value_group_amc_data->inv_cost) + $value_group_amc_data->idcwr), 2, '.', '');
                $value_group_amc_data->abs_rtn= ($value_group_amc_data->gain_loss!=0 && $value_group_amc_data->inv_cost!=0)?number_format((float)(($value_group_amc_data->gain_loss / $value_group_amc_data->inv_cost) * 100), 2, '.', ''):0;
                array_push($final_data,$value_group_amc_data);
            }
            // $final_data = collect($final_data)->map(function($x){ return (array) $x; })->toArray(); 
            // usort($final_data, function($a, $b) {
            //     return $a['first_client_name'] <=> $b['first_client_name'];
            //     // return $a->amc_name <=> $b->amc_name;
            // });
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($final_data);
    }
}