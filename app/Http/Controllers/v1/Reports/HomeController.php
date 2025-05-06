<?php

namespace App\Http\Controllers\V1\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{
    MutualFund,
    Client,
    FormReceived,
    MutualFundTransaction,
    MFTransTypeSubType,
    SipStpTransaction,
    TempSipStpTransaction,
    SipStpSwpReport,
    SchemeISIN,
    AumReport
};
use Validator;
use Illuminate\Support\Carbon;
use Excel;
use App\Helpers\TransHelper;
use DB;

class HomeController extends Controller
{
    public function liveSIPAmount(Request $request)
    {
        try {
            $rawQuery='';
            $queryString='tt_sip_stp_swp_report.auto_trans_type';
            $my_array=['P','SIP','ISIP'];
            $rawQuery.=Helper::WhereRawQuery($my_array,$rawQuery,$queryString);
            $rawQuery.=' AND tt_sip_stp_swp_report.cease_terminate_date IS NULL ';
            $rawQuery.=' AND tt_sip_stp_swp_report.from_date <="'.date('Y-m-d').'"';
            $rawQuery.=' AND tt_sip_stp_swp_report.to_date >="'.date('Y-m-d').'" ';

            $my_datas=SipStpSwpReport::where('tt_sip_stp_swp_report.amc_flag','N')
                    ->where('tt_sip_stp_swp_report.scheme_flag','N')
                    ->where('tt_sip_stp_swp_report.bu_type_flag','N')
                    ->where('tt_sip_stp_swp_report.plan_option_flag','N')
                    ->where('tt_sip_stp_swp_report.freq_mismatch_flag','N')
                    ->whereRaw($rawQuery)
                    // ->take(50)
                    ->get();
            // return $my_datas;
            $data=[];
            $total_amount=0;
            $prev_total_amount=0;
            $curr_total_amount=0;
            $date=date('Y-m')."-01";
            // return $date;
            foreach ($my_datas as $key => $my_data) {
                if ($my_data->from_date < $date) {
                    $prev_total_amount=$prev_total_amount+$my_data->auto_amount;
                }else {
                    $curr_total_amount=$curr_total_amount+$my_data->auto_amount;
                }
                $total_amount=$total_amount + $my_data->auto_amount;
            }
            $data['total_amount']=$total_amount;
            $data['prev_total_amount']=$prev_total_amount;
            $data['curr_total_amount']=$curr_total_amount;
            $data['flag']=$request->flag;
            // sleep(50);
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function liveSIPTrend(Request $request)
    {
        try {
            $no_of_month=($request->no_of_month)?$request->no_of_month:12;
            $data=[];
            $categories=[];
            $chart_data=[];
            $rawQuery='';
            for ($i=0; $i < $no_of_month; $i++) { 
                $split_date=date('Y-m', strtotime('-'.$i.' months'));
                array_push($categories,$split_date);

                $queryString='tt_sip_stp_swp_report.auto_trans_type';
                $my_array=['P','SIP','ISIP'];
                $rawQuery.=Helper::WhereRawQuery($my_array,$rawQuery,$queryString);
                $rawQuery.=' AND tt_sip_stp_swp_report.cease_terminate_date IS NULL ';
                // $rawQuery.=' AND tt_sip_stp_swp_report.from_date <="'.date('Y-m-d').'"';
                $rawQuery.=' AND tt_sip_stp_swp_report.to_date >="'.date('Y-m-d').'" ';

                $rawQuery1='';
                if ($i > 0) {
                    $f_date=date('Y-m-t', strtotime('-'.$i.' months'));
                    $rawQuery1.=' AND tt_sip_stp_swp_report.from_date <="'.$f_date.'"';
                }else{
                    $rawQuery1.=' AND tt_sip_stp_swp_report.from_date <="'.date('Y-m-d').'"';
                }
                $myrawQuery=$rawQuery.$rawQuery1;
                // return $myrawQuery;
                $my_datas=SipStpSwpReport::where('tt_sip_stp_swp_report.amc_flag','N')
                    ->where('tt_sip_stp_swp_report.scheme_flag','N')
                    ->where('tt_sip_stp_swp_report.bu_type_flag','N')
                    ->where('tt_sip_stp_swp_report.plan_option_flag','N')
                    ->where('tt_sip_stp_swp_report.freq_mismatch_flag','N')
                    ->whereRaw($myrawQuery)
                    ->get();
                $total_amount=0;
                foreach ($my_datas as $key => $my_data) {
                    $total_amount=$total_amount + $my_data->auto_amount;
                }
                // return $total_amount;
                array_push($chart_data,$total_amount);
            }
            // return $categories;
            $data['categories']=$categories;
            $data['chart_data']=$chart_data;
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function currAum1(Request $request)
    {
        try {
            // return $request->all();
            // $date=date("Y-m-d", strtotime("- 1 day"));
            $date=date("Y-m-d");
            $prev_date = date("Y-m-t", strtotime("-1 month", strtotime($date)));
            // $prev_date=date('Y-m-d');
            // return $prev_date;
            $rawQuery = '';
            if ($date) {
                $condition_v = (strlen($rawQuery) > 0) ? " AND " : " ";
                $queryString = 'td_mutual_fund_trans_aum.trans_date';
                $rawQuery .= $condition_v . $queryString . "=(SELECT MAX(trans_date) FROM td_mutual_fund_trans_aum WHERE trans_date <='" . $date . "')";
            }
            // if ($prev_date) {
            //     $condition_v = (strlen($rawQuery) > 0) ? " AND " : " ";
            //     $queryString = 'td_mutual_fund_trans_aum.trans_date';
            //     $rawQuery .= $condition_v . $queryString . "=(SELECT MAX(trans_date) FROM td_mutual_fund_trans_aum WHERE trans_date <='" . $prev_date . "')";
            // }
            // return $rawQuery;
            $startDates = [$date];
            // return $startDates;
            // DB::enableQueryLog();
            $all_data = AumReport::leftJoin('md_rnt', 'md_rnt.id', '=', 'td_mutual_fund_trans_aum.rnt_id')
                ->selectRaw('td_mutual_fund_trans_aum.*,SUM(td_mutual_fund_trans_aum.total_unit) AS tot_units, SUM(td_mutual_fund_trans_aum.total_inv_cost) as inv_cost,md_rnt.rnt_name')
                ->whereRaw($rawQuery)
                // ->whereIn('td_mutual_fund_trans_aum.trans_date', $startDates)
                // ->groupBy('td_mutual_fund_trans_aum.amc_code', 'td_mutual_fund_trans_aum.product_code')
                ->groupBy('td_mutual_fund_trans_aum.trans_date','td_mutual_fund_trans_aum.amc_code','td_mutual_fund_trans_aum.product_code')
                ->get();
            // dd(DB::getQueryLog());
            // return $all_data;
            /******************************************************* */
            /** start for get nav data */
            $all_trans_product = [];
            foreach ($all_data as $value_product_code) {
                // return $value_product_code;
                // $f_trans_product = "(nav_date=(SELECT MAX(nav_date) FROM td_nav_details WHERE product_code='" . $value_product_code->product_code . "' AND nav_date <='" .  $value_product_code->trans_date . "') AND product_code='" . $value_product_code->product_code . "')";
                $f_trans_product = "(nav_date=(SELECT MAX(nav_date) FROM td_nav_details WHERE product_code='" . $value_product_code->product_code . "' AND nav_date <='" .  $date . "') AND product_code='" . $value_product_code->product_code . "')";
                array_push($all_trans_product, $f_trans_product);
            }
            // return $all_trans_product;
            $res_array = [];
            if (count($all_data) > 0) {
                $all_trans_product_unique = array_unique($all_trans_product);
                $string_version_product_code = implode(',', $all_trans_product_unique);
                $res_array = DB::connection('mysql_nav')
                    ->select('SELECT product_code,isin_no,DATE_FORMAT(nav_date, "%Y-%m-%d") as nav_date,nav FROM td_nav_details where ' . str_replace(",", "  OR  ", $string_version_product_code));
            }
            // return $res_array;
            // return $all_data;
            $grouped_all_data = [];
            foreach ($all_data as $key_grouped_all_data => $value_grouped_all_data) { // amc loop
                $grouped_all_data[$value_grouped_all_data->trans_date][] = $value_grouped_all_data;
            }
            // return $grouped_all_data[0];
            $final_final_data = [];
            foreach ($grouped_all_data as $key_grouped => $value_grouped) { // amc loop
                // return $key_grouped;
                $final_data = [];
                $total_aum = 0;
                foreach ($value_grouped as $key_group_amc_data => $value_group_amc_data) { // amc loop
                    // return $value_group_amc_data;
                    if ($value_group_amc_data->inv_cost > 0) {
                        $product_code = $value_group_amc_data->product_code;
                        $trans_date = $value_group_amc_data->trans_date;
                        $new          = '';
                        if (count($res_array) > 0) {
                            foreach ($res_array as $val_nav) {
                                if ($val_nav->product_code == $product_code ) {
                                    // if ($val_nav->product_code == $product_code && $val_nav->nav_date == $trans_date) {
                                    $new = $val_nav;
                                }
                            }
                        }
                        if($new == ''){
                           return $value_group_amc_data;
                        }
                        $value_group_amc_data->new        = $new;
                        $value_group_amc_data->curr_nav   = isset($new->nav) ? $new->nav : 0;
                        $value_group_amc_data->nav_date   = isset($new->nav_date) ? $new->nav_date : 0;
                        $value_group_amc_data->idcw_reinv = 0;
                        $value_group_amc_data->idcw_paid  = 0;
                        $value_group_amc_data->idcwr      = 0;
                        $value_group_amc_data->curr_aum   = number_format((float) ($value_group_amc_data->curr_nav * $value_group_amc_data->tot_units), 2, '.', '');
                        $value_group_amc_data->gain_loss  = number_format((float) (($value_group_amc_data->curr_aum - $value_group_amc_data->inv_cost) + $value_group_amc_data->idcwr), 2, '.', '');
                        $value_group_amc_data->abs_rtn    = ($value_group_amc_data->gain_loss != 0 && $value_group_amc_data->inv_cost != 0) ? number_format((float) (($value_group_amc_data->gain_loss / $value_group_amc_data->inv_cost) * 100), 2, '.', '') : 0;
                        array_push($final_data, $value_group_amc_data);
                        $total_aum = $total_aum + $value_group_amc_data->curr_aum;
                    }
                }
                // return $total_aum;
                $final_final_data[$key_grouped] = $total_aum;
            }
            $mydatas['data']=$final_final_data;
            $mydatas['flag']=$request->flag;


            // $final_data = [];
            // foreach ($all_data as $key_group_amc_data => $value_group_amc_data) { // amc loop
            //     // return $value_group_amc_data;
            //     if ($value_group_amc_data->inv_cost > 0) {
            //         $product_code = $value_group_amc_data->product_code;
            //         $trans_date = $value_group_amc_data->trans_date;
            //         $new          = '';
            //         if (count($res_array) > 0) {
            //             foreach ($res_array as $val_nav) {
            //                 if ($val_nav->product_code == $product_code && $val_nav->nav_date == $trans_date) {
            //                     $new = $val_nav;
            //                 }
            //             }
            //         }
            //         $value_group_amc_data->new        = $new;
            //         $value_group_amc_data->curr_nav   = isset($new->nav) ? $new->nav : 0;
            //         $value_group_amc_data->nav_date   = isset($new->nav_date) ? $new->nav_date : 0;
            //         $value_group_amc_data->idcw_reinv = 0;
            //         $value_group_amc_data->idcw_paid  = 0;
            //         $value_group_amc_data->idcwr      = 0;
            //         $value_group_amc_data->curr_aum   = number_format((float) ($value_group_amc_data->curr_nav * $value_group_amc_data->tot_units), 2, '.', '');
            //         $value_group_amc_data->gain_loss  = number_format((float) (($value_group_amc_data->curr_aum - $value_group_amc_data->inv_cost) + $value_group_amc_data->idcwr), 2, '.', '');
            //         $value_group_amc_data->abs_rtn    = ($value_group_amc_data->gain_loss != 0 && $value_group_amc_data->inv_cost != 0) ? number_format((float) (($value_group_amc_data->gain_loss / $value_group_amc_data->inv_cost) * 100), 2, '.', '') : 0;
            //         array_push($final_data, $value_group_amc_data);
            //         // $total_aum = $total_aum + $value_group_amc_data->curr_aum;
            //     }
            // }
            // return $final_data;
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($mydatas);
    }

    public function currAum(Request $request)
    {
        try {
            // return $request->all();
            $date=date("Y-m-d", strtotime("- 1 day"));
            $prev_date = date("Y-m-t", strtotime("-1 month", strtotime($date)));
            // $prev_date=date('Y-m-d');
            // return $prev_date;
            $rawQuery = '';
            if ($date) {
                $condition_v = (strlen($rawQuery) > 0) ? " AND " : " ";
                $queryString = 'td_mutual_fund_trans_aum.trans_date';
                $rawQuery .= $condition_v . $queryString . "=(SELECT MAX(trans_date) FROM td_mutual_fund_trans_aum WHERE trans_date <='" . $date . "')";
            }
            if ($prev_date) {
                $condition_v = (strlen($rawQuery) > 0) ? " AND " : " ";
                $queryString = 'td_mutual_fund_trans_aum.trans_date';
                $rawQuery .= $condition_v . $queryString . "=(SELECT MAX(trans_date) FROM td_mutual_fund_trans_aum WHERE trans_date <='" . $prev_date . "')";
            }
            // return $rawQuery;
            $startDates = [$date, $prev_date];
            // return $startDates;
            // DB::enableQueryLog();
            $all_data = AumReport::leftJoin('md_rnt', 'md_rnt.id', '=', 'td_mutual_fund_trans_aum.rnt_id')
                ->selectRaw('td_mutual_fund_trans_aum.*,SUM(td_mutual_fund_trans_aum.total_unit) AS tot_units, SUM(td_mutual_fund_trans_aum.total_inv_cost) as inv_cost,md_rnt.rnt_name')
                // ->whereRaw($rawQuery)
                ->whereIn('td_mutual_fund_trans_aum.trans_date', $startDates)
                // ->groupBy('td_mutual_fund_trans_aum.amc_code', 'td_mutual_fund_trans_aum.product_code')
                ->groupBy('td_mutual_fund_trans_aum.trans_date','td_mutual_fund_trans_aum.product_code')
                ->get();
            // dd(DB::getQueryLog());

            // $all_data = AumReport::leftJoin('md_scheme_isin', 'md_scheme_isin.product_code', '=', 'td_mutual_fund_trans_aum.product_code')
            //     ->leftJoin('md_scheme', 'md_scheme.id', '=', 'md_scheme_isin.scheme_id')
            //     ->leftJoin('md_rnt', 'md_rnt.id', '=', 'td_mutual_fund_trans_aum.rnt_id')
            //     ->selectRaw('td_mutual_fund_trans_aum.*,SUM(td_mutual_fund_trans_aum.total_unit) AS tot_units, SUM(td_mutual_fund_trans_aum.total_inv_cost) as inv_cost,md_rnt.rnt_name')
            //     ->whereRaw($rawQuery)
            //     ->groupBy('td_mutual_fund_trans_aum.amc_code', 'td_mutual_fund_trans_aum.product_code')
            //     ->get();
            // return $all_data;
            /******************************************************* */
            /** start for get nav data */
            $all_trans_product = [];
            foreach ($all_data as $value_product_code) {
                // return $value_product_code;
                $f_trans_product = "(nav_date=(SELECT MAX(nav_date) FROM td_nav_details WHERE product_code='" . $value_product_code->product_code . "' AND nav_date <='" .  $value_product_code->trans_date . "') AND product_code='" . $value_product_code->product_code . "')";
                array_push($all_trans_product, $f_trans_product);
            }
            // return $all_trans_product;
            $res_array = [];
            if (count($all_data) > 0) {
                $all_trans_product_unique = array_unique($all_trans_product);
                $string_version_product_code = implode(',', $all_trans_product_unique);
                $res_array = DB::connection('mysql_nav')
                    ->select('SELECT product_code,isin_no,DATE_FORMAT(nav_date, "%Y-%m-%d") as nav_date,nav FROM td_nav_details where ' . str_replace(",", "  OR  ", $string_version_product_code));
            }
            // return $all_data;
            $grouped_all_data = [];
            foreach ($all_data as $key_grouped_all_data => $value_grouped_all_data) { // amc loop
                $grouped_all_data[$value_grouped_all_data->trans_date][] = $value_grouped_all_data;
            }
            // return $grouped_all_data[0];
            $final_final_data = [];
            $total_aum = 0;
            foreach ($grouped_all_data as $key_grouped => $value_grouped) { // amc loop
                // return $key_grouped;
                $final_data = [];
                $total_aum = 0;
                foreach ($value_grouped as $key_group_amc_data => $value_group_amc_data) { // amc loop
                    // return $value_group_amc_data;
                    if ($value_group_amc_data->inv_cost > 0) {
                        $product_code = $value_group_amc_data->product_code;
                        $trans_date = $value_group_amc_data->trans_date;
                        $new          = '';
                        if (count($res_array) > 0) {
                            foreach ($res_array as $val_nav) {
                                if ($val_nav->product_code == $product_code && $val_nav->nav_date == $trans_date) {
                                    $new = $val_nav;
                                }
                            }
                        }
                        $value_group_amc_data->new        = $new;
                        $value_group_amc_data->curr_nav   = isset($new->nav) ? $new->nav : 0;
                        $value_group_amc_data->nav_date   = isset($new->nav_date) ? $new->nav_date : 0;
                        $value_group_amc_data->idcw_reinv = 0;
                        $value_group_amc_data->idcw_paid  = 0;
                        $value_group_amc_data->idcwr      = 0;
                        $value_group_amc_data->curr_aum   = number_format((float) ($value_group_amc_data->curr_nav * $value_group_amc_data->tot_units), 2, '.', '');
                        $value_group_amc_data->gain_loss  = number_format((float) (($value_group_amc_data->curr_aum - $value_group_amc_data->inv_cost) + $value_group_amc_data->idcwr), 2, '.', '');
                        $value_group_amc_data->abs_rtn    = ($value_group_amc_data->gain_loss != 0 && $value_group_amc_data->inv_cost != 0) ? number_format((float) (($value_group_amc_data->gain_loss / $value_group_amc_data->inv_cost) * 100), 2, '.', '') : 0;
                        array_push($final_data, $value_group_amc_data);
                        $total_aum = $total_aum + $value_group_amc_data->curr_aum;
                    }
                }
                // return $total_aum;
                $final_final_data[$key_grouped] = $total_aum;
            }
            $mydatas['data']=$final_final_data;
            $mydatas['flag']=$request->flag;
            // return $final_final_data;
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($mydatas);
    }

    // currAumTrend
    public function currAumTrend(Request $request)
    {
        try {
            $startDates=[];
            $date=date("Y-m-d", strtotime("- 1 day"));
            array_push($startDates,$date);
            for ($i=1; $i <= 11; $i++) { 
                // $year=(date('Y') - $i).'-03-01';
                // $year=(date('Y') - $i).'-03-01';
                $time = strtotime(date('Y-m-d'));
                $valuation_as_on = date("Y-m-t", strtotime("-".$i." month", $time));

                // $valuation_as_on = date("Y-m-t", strtotime($year));
                // return $valuation_as_on;
                array_push($startDates,$valuation_as_on);
            }
            // return $startDates;
            $all_data = AumReport::leftJoin('md_rnt', 'md_rnt.id', '=', 'td_mutual_fund_trans_aum.rnt_id')
                ->selectRaw('td_mutual_fund_trans_aum.*,SUM(td_mutual_fund_trans_aum.total_unit) AS tot_units, SUM(td_mutual_fund_trans_aum.total_inv_cost) as inv_cost,md_rnt.rnt_name')
                // ->whereRaw($rawQuery)
                ->whereIn('td_mutual_fund_trans_aum.trans_date', $startDates)
                // ->groupBy('td_mutual_fund_trans_aum.amc_code', 'td_mutual_fund_trans_aum.product_code')
                ->groupBy('td_mutual_fund_trans_aum.trans_date','td_mutual_fund_trans_aum.amc_code','td_mutual_fund_trans_aum.product_code')
                ->get();
            // dd(DB::getQueryLog());
            // return $all_data;
            /******************************************************* */
            /** start for get nav data */
            $all_trans_product = [];
            foreach ($all_data as $value_product_code) {
                // return $value_product_code;
                $f_trans_product = "(nav_date=(SELECT MAX(nav_date) FROM td_nav_details WHERE product_code='" . $value_product_code->product_code . "' AND nav_date <='" .  $value_product_code->trans_date . "') AND product_code='" . $value_product_code->product_code . "')";
                array_push($all_trans_product, $f_trans_product);
            }
            // return $all_trans_product;
            $res_array = [];
            if (count($all_data) > 0) {
                $all_trans_product_unique = array_unique($all_trans_product);
                $string_version_product_code = implode(',', $all_trans_product_unique);
                $res_array = DB::connection('mysql_nav')
                    ->select('SELECT product_code,isin_no,DATE_FORMAT(nav_date, "%Y-%m-%d") as nav_date,nav FROM td_nav_details where ' . str_replace(",", "  OR  ", $string_version_product_code));
            }
            // return $all_data;
            $grouped_all_data = [];
            foreach ($all_data as $key_grouped_all_data => $value_grouped_all_data) { // amc loop
                $grouped_all_data[$value_grouped_all_data->trans_date][] = $value_grouped_all_data;
            }
            // return $grouped_all_data[0];
            $final_final_data = [];
            $total_aum = 0;
            foreach ($grouped_all_data as $key_grouped => $value_grouped) { // amc loop
                // return $key_grouped;
                $final_data = [];
                $total_aum = 0;
                foreach ($value_grouped as $key_group_amc_data => $value_group_amc_data) { // amc loop
                    // return $value_group_amc_data;
                    if ($value_group_amc_data->inv_cost > 0) {
                        $product_code = $value_group_amc_data->product_code;
                        $trans_date = $value_group_amc_data->trans_date;
                        $new          = '';
                        if (count($res_array) > 0) {
                            foreach ($res_array as $val_nav) {
                                if ($val_nav->product_code == $product_code && $val_nav->nav_date == $trans_date) {
                                    $new = $val_nav;
                                }
                            }
                        }
                        $value_group_amc_data->new        = $new;
                        $value_group_amc_data->curr_nav   = isset($new->nav) ? $new->nav : 0;
                        $value_group_amc_data->nav_date   = isset($new->nav_date) ? $new->nav_date : 0;
                        $value_group_amc_data->idcw_reinv = 0;
                        $value_group_amc_data->idcw_paid  = 0;
                        $value_group_amc_data->idcwr      = 0;
                        $value_group_amc_data->curr_aum   = number_format((float) ($value_group_amc_data->curr_nav * $value_group_amc_data->tot_units), 2, '.', '');
                        $value_group_amc_data->gain_loss  = number_format((float) (($value_group_amc_data->curr_aum - $value_group_amc_data->inv_cost) + $value_group_amc_data->idcwr), 2, '.', '');
                        $value_group_amc_data->abs_rtn    = ($value_group_amc_data->gain_loss != 0 && $value_group_amc_data->inv_cost != 0) ? number_format((float) (($value_group_amc_data->gain_loss / $value_group_amc_data->inv_cost) * 100), 2, '.', '') : 0;
                        array_push($final_data, $value_group_amc_data);
                        $total_aum = $total_aum + $value_group_amc_data->curr_aum;
                    }
                }
                // return $total_aum;
                $final_final_data[$key_grouped] = $total_aum;
            }
            $mydatas['data']=$final_final_data;
            $mydatas['flag']=$request->flag;
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($mydatas);
    }
}