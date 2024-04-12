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

class LiveMFSWPController extends Controller
{
    public function search(Request $request)
    {
        try {
            return $request;
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
                // ->selectRaw('(select close from td_benchmark_scheme where benchmark=1 AND DATE(date)=DATE(td_mutual_fund_trans.trans_date)) as nifty50')
                // ->selectRaw('(select close from td_benchmark_scheme where benchmark=70 AND DATE(date)=DATE(td_mutual_fund_trans.trans_date)) as sensex')
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
                $profitloss=$value1->profitloss;
                // if ($data_key==19) {
                // }else {
                //     $mydata=$this->calculate($value1->foliotrans);
                // }
                if ($data_key==20) {
                    // return $profitloss;
                    // $mydata=$this->calculate($value1->foliotrans);
                    // return $mydata;
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
}