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
    SchemeISIN
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
}