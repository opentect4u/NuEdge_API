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
    SipStpSwpReport
};
use Validator;
use Illuminate\Support\Carbon;
use Excel;
use App\Helpers\TransHelper;
use DB;
use Session;

class LiveMFSTWController extends Controller
{
    public function search(Request $request)
    {
        try {
            // return $request;
            $valuation_as_on=$request->valuation_as_on;
            $view_type=$request->view_type;
            $pan_no=$request->pan_no;
            $client_name=$request->client_name;
            $sip_type=$request->sip_type;
            $report_type=$request->report_type;
            
            session()->forget('valuation_as_on');
            session(['valuation_as_on' => $valuation_as_on]);
            // return Session::get('valuation_as_on');
            $client_details='';
            $rawQuery='';
            if ($view_type || $valuation_as_on || $sip_type) {
                // if ($valuation_as_on) {
                //     $condition_v=(strlen($rawQuery) > 0)? " AND ":" ";
                //     $queryString='td_mutual_fund_trans.trans_date';
                //     $rawQuery.=$condition_v.$queryString."<= '".$valuation_as_on."'";
                // }
                if ($view_type=='C') {
                    $client_rawQuery='';
                    if (!$pan_no) {
                        $queryString='tt_sip_stp_swp_report.first_client_name';
                        $rawQuery.=Helper::WhereRawQuery($client_name,$rawQuery,$queryString);
                        $client_queryString='md_client.client_name';
                        $client_rawQuery.=Helper::WhereRawQuery($client_name,$client_rawQuery,$client_queryString);
                    }else {
                        $queryString='tt_sip_stp_swp_report.first_client_pan';
                        $rawQuery.=Helper::WhereRawQuery($pan_no,$rawQuery,$queryString);
                        $client_queryString='md_client.pan';
                        $client_rawQuery.=Helper::WhereRawQuery($pan_no,$client_rawQuery,$client_queryString);
                    }
                    $client_details=Client::whereRaw($client_rawQuery)->first();
                }else {
                    $queryString='tt_sip_stp_swp_report.first_client_pan';
                    $condition=(strlen($rawQuery) > 0)? " AND (":" (";
                    $row_name_string=  "'" .implode("','", $family_members_pan). "'";
                    $rawQuery.=$condition.$queryString." IN (".$row_name_string.")";
                    $queryString='tt_sip_stp_swp_report.first_client_name';
                    $condition1=(strlen($rawQuery) > 0)? " OR ":" ";
                    $row_name_string1=  "'" .implode("','", $family_members_name). "'";
                    $rawQuery.=$condition1.$queryString." IN (".$row_name_string1."))";
                }
            } 

            $queryString='tt_sip_stp_swp_report.auto_trans_type';
            if ($report_type=='P') {
                $sip_type=$request->sip_type;
                $my_array=['P','SIP','ISIP'];
                $rawQuery.=Helper::WhereRawQuery($my_array,$rawQuery,$queryString);
            } elseif ($report_type=='R') {
                $sip_type=$request->swp_type;
                $my_array=['R','SWP'];
                $rawQuery.=Helper::WhereRawQuery($my_array,$rawQuery,$queryString);
            } elseif ($report_type=='SO') {
                $sip_type=$request->stp_type;
                $my_array=['SO','STP'];
                $rawQuery.=Helper::WhereRawQuery($my_array,$rawQuery,$queryString);
            }

            if ($sip_type){
                switch ($sip_type) {
                    case 'L':
                        $rawQuery.=' AND tt_sip_stp_swp_report.cease_terminate_date IS NULL ';
                        // $rawQuery.=' AND tt_sip_stp_swp_report.pause_from_date IS NULL ';
                        // $rawQuery.=' AND tt_sip_stp_swp_report.pause_to_date IS NULL ';
                        $rawQuery.=' AND tt_sip_stp_swp_report.from_date <="'.date('Y-m-d').'"';
                        $rawQuery.=' AND tt_sip_stp_swp_report.to_date >="'.date('Y-m-d').'" ';
                        break;
                    case 'I':
                        // *********************start termination logic*********************
                        $rawQuery.=' AND ((IF(tt_sip_stp_swp_report.rnt_id=1, 
                            DATE_FORMAT(tt_sip_stp_swp_report.cease_terminate_date,"Y-m-d")!="", 
                            DATE_FORMAT(tt_sip_stp_swp_report.cease_terminate_date,"Y-m-d")!="" AND tt_sip_stp_swp_report.to_date > tt_sip_stp_swp_report.cease_terminate_date)';
                        $rawQuery.=' AND tt_sip_stp_swp_report.from_date <= tt_sip_stp_swp_report.cease_terminate_date';
                        $rawQuery.=' AND (SELECT COUNT(*) FROM `md_systematic_unregistered` WHERE remarks=tt_sip_stp_swp_report.remarks AND rnt_id=tt_sip_stp_swp_report.rnt_id)=0';
                        if ($report_type=='P') {
                            $rawQuery.=' AND (datediff(tt_sip_stp_swp_report.cease_terminate_date, tt_sip_stp_swp_report.from_date) > 30))';
                        }else {
                            $rawQuery.=' AND (datediff(tt_sip_stp_swp_report.cease_terminate_date, tt_sip_stp_swp_report.from_date) > 15))';
                        }
                        // *********************end termination logic****************************
                        // *********************start matured sip logic*********************
                        $rawQuery.=' OR (IF(tt_sip_stp_swp_report.rnt_id=1, 
                                date(tt_sip_stp_swp_report.to_date) <= "'.date('Y-m-d').'" AND tt_sip_stp_swp_report.cease_terminate_date IS NULL, 
                                date(tt_sip_stp_swp_report.to_date) <= "'.date('Y-m-d').'" AND tt_sip_stp_swp_report.to_date <= tt_sip_stp_swp_report.cease_terminate_date
                                )))';
                        // *********************end matured sip logic*********************
                        break;
                    default:
                        break;
                }
            }
            // return $rawQuery;
            // return $client_details;
            // DB::enableQueryLog();
            $data=[];
            $my_datas=SipStpSwpReport::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','tt_sip_stp_swp_report.product_code')
                    ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                    ->leftJoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
                    ->leftJoin('md_option','md_option.id','=','md_scheme_isin.option_id')
                    ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
                    ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                    ->leftJoin('md_amc','md_amc.id','=','md_scheme.amc_id')
                    ->leftJoin('md_amc as md_amc_1','md_amc_1.amc_code','=','tt_sip_stp_swp_report.amc_code')
                    ->leftJoin('md_scheme_isin as to_isin','to_isin.product_code','=','tt_sip_stp_swp_report.to_product_code')
                    ->leftJoin('md_scheme as to_scheme','to_scheme.id','=','to_isin.scheme_id')
                    ->leftJoin('md_category as to_category','to_category.id','=','to_scheme.category_id')
                    ->leftJoin('md_subcategory as to_subcategory','to_subcategory.id','=','to_scheme.subcategory_id')
                    ->leftJoin('md_systematic_trans_type','md_systematic_trans_type.trans_type_code','=','tt_sip_stp_swp_report.auto_trans_type')
                    ->select('tt_sip_stp_swp_report.*','tt_sip_stp_swp_report.period_day as sip_date','tt_sip_stp_swp_report.auto_amount as amount','tt_sip_stp_swp_report.cease_terminate_date as terminated_date',
                    'tt_sip_stp_swp_report.pause_from_date as pause_start_date','tt_sip_stp_swp_report.pause_to_date as pause_end_date','tt_sip_stp_swp_report.bank as bank_name','tt_sip_stp_swp_report.instrm_no as acc_no',
                    'md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name',
                    'md_amc.amc_short_name as amc_name','md_amc_1.amc_short_name as amc_short_name','md_plan.plan_name','md_option.opt_name as option_name',
                    'md_systematic_trans_type.trans_type','md_systematic_trans_type.trans_sub_type',
                    'to_scheme.scheme_name as to_scheme_name','to_category.cat_name as to_cat_name','to_subcategory.subcategory_name as to_subcat_name')
                    ->selectRaw('(select `freq_name` from `md_systematic_frequency` where `rnt_id`=tt_sip_stp_swp_report.rnt_id and `freq_code`=tt_sip_stp_swp_report.periodicity limit 1) as freq')
                    ->selectRaw('(SELECT COUNT(*) FROM `md_systematic_unregistered` WHERE remarks=tt_sip_stp_swp_report.remarks AND rnt_id=tt_sip_stp_swp_report.rnt_id) as terminate_logic_count')
                    ->selectRaw('datediff(tt_sip_stp_swp_report.cease_terminate_date, tt_sip_stp_swp_report.from_date) as terminate_datediff')
                    ->where('tt_sip_stp_swp_report.amc_flag','N')
                    ->where('tt_sip_stp_swp_report.scheme_flag','N')
                    ->where('tt_sip_stp_swp_report.bu_type_flag','N')
                    ->where('tt_sip_stp_swp_report.plan_option_flag','N')
                    ->where('tt_sip_stp_swp_report.freq_mismatch_flag','N')
                    ->whereRaw($rawQuery)
                    ->orderBy('md_scheme.scheme_name','ASC')
                    ->get();
            // return  $my_datas;  
            // dd(DB::getQueryLog());

            foreach ($my_datas as $key => $my_data) {
                if ($my_data->cease_terminate_date==null && $my_data->from_date <= date('Y-m-d') && $my_data->to_date >= date('Y-m-d')) {
                    $my_data->activate_status='Active';
                }else {
                    if ($my_data->rnt_id==1) {
                        if ($my_data->cease_terminate_date!="" && $my_data->from_date <= $my_data->cease_terminate_date && $my_data->terminate_logic_count==0 && $my_data->terminate_datediff > 30) {
                            $my_data->activate_status='Inactive';
                        }
                    }else {
                        if ($my_data->cease_terminate_date!="" && $my_data->to_date > $my_data->cease_terminate_date && $my_data->from_date <= $my_data->cease_terminate_date && $my_data->terminate_logic_count==0 && $my_data->terminate_datediff > 30) {
                            $my_data->activate_status='Inactive';
                        }
                    }
                    // $my_data->activate_status =$active_status;
                }

                $my_data->reg_no =$my_data->auto_trans_no;
                if ($my_data->rnt_id==2) {
                    if(($my_data->frequency=="Daily") || ($my_data->frequency=="WEEKLY") || ($my_data->frequency=="Fortnightly")){
                        $my_data->sip_date=$my_data->frequency;
                        $my_data->stp_date=$my_data->frequency;
                        $my_data->swp_date=$my_data->frequency;
                    }else {
                        $my_data->sip_date=number_format((float)date('d',strtotime($my_data->from_date)), 0, '.', '');
                        $my_data->stp_date=number_format((float)date('d',strtotime($my_data->from_date)), 0, '.', '');
                        $my_data->swp_date=number_format((float)date('d',strtotime($my_data->from_date)), 0, '.', '');
                    }
                    $my_data->freq=$my_data->frequency;
                    $my_data->duration =$my_data->no_of_installment;
                }elseif ($my_data->rnt_id==1) {
                    // return $my_data;
                    if(($my_data->freq=="Daily") || ($my_data->freq=="Weekly") || ($my_data->freq=="Fortnightly")){
                        // return $my_data;
                        $my_data->sip_date=$my_data->freq;
                        $my_data->stp_date=$my_data->freq;
                        $my_data->swp_date=$my_data->freq;
                    }else {
                        $my_data->sip_date=number_format((float)$my_data->period_day, 0, '.', '');
                        $my_data->stp_date=number_format((float)$my_data->period_day, 0, '.', '');
                        $my_data->swp_date=number_format((float)$my_data->period_day, 0, '.', '');
                    }
                    /***************for duration calculation****************************/
                    if ($report_type=='P') {  // if sip 
                        $calculation_day =(int)abs((strtotime($my_data->reg_date) - strtotime($my_data->from_date))/(60*60*24));
                        $my_data->calculation_day =$calculation_day;
                        if ($calculation_day <= 30) {
                            $my_data->duration =(int)abs((strtotime($my_data->from_date) - strtotime($my_data->to_date))/(60*60*24*30));
                        }else {
                            $my_data->duration =(int)abs((strtotime($my_data->reg_date) - strtotime($my_data->to_date))/(60*60*24*30));
                        }
                    }else {  // swp & stp
                        $calculation_day =(int)abs((strtotime($my_data->reg_date) - strtotime($my_data->from_date))/(60*60*24));
                        $my_data->calculation_day =$calculation_day;
                        if ($calculation_day <= 15) {
                            $my_data->duration =(int)abs((strtotime($my_data->from_date) - strtotime($my_data->to_date))/(60*60*24*30));
                        }else {
                            $my_data->duration =(int)abs((strtotime($my_data->reg_date) - strtotime($my_data->to_date))/(60*60*24*30));
                        }
                    }
                }
                if ($my_data->pause_end_date!='' && $my_data->pause_end_date < date('Y-m-d')) {  // if pause to date more then to date
                    $my_data->pause_start_date=NULL;
                    $my_data->pause_end_date=NULL;
                }
                array_push($data,$my_data);
            }
            $mydata=[];
            $mydata['client_details']=$client_details;
            $mydata['data']=$data;
            $mydata['valuation_as_on']=$valuation_as_on;
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($mydata);
    }

    public function upcomingTrans(Request $request)
    {
        try {
            $valuation_as_on=$request->valuation_as_on;
            $view_type=$request->view_type;
            $pan_no=$request->pan_no;
            $client_name=$request->client_name;
            $sip_type=$request->sip_type;
            $report_type=$request->report_type;
            
            $client_details='';
            $rawQuery='';
            if ($view_type || $valuation_as_on || $sip_type) {
                if ($view_type=='C') {
                    $client_rawQuery='';
                    if (!$pan_no) {
                        $queryString='tt_sip_stp_swp_report.first_client_name';
                        $rawQuery.=Helper::WhereRawQuery($client_name,$rawQuery,$queryString);
                        $client_queryString='md_client.client_name';
                        $client_rawQuery.=Helper::WhereRawQuery($client_name,$client_rawQuery,$client_queryString);
                    }else {
                        $queryString='tt_sip_stp_swp_report.first_client_pan';
                        $rawQuery.=Helper::WhereRawQuery($pan_no,$rawQuery,$queryString);
                        $client_queryString='md_client.pan';
                        $client_rawQuery.=Helper::WhereRawQuery($pan_no,$client_rawQuery,$client_queryString);
                    }
                    $client_details=Client::whereRaw($client_rawQuery)->first();
                }else {
                    $queryString='tt_sip_stp_swp_report.first_client_pan';
                    $condition=(strlen($rawQuery) > 0)? " AND (":" (";
                    $row_name_string=  "'" .implode("','", $family_members_pan). "'";
                    $rawQuery.=$condition.$queryString." IN (".$row_name_string.")";
                    $queryString='tt_sip_stp_swp_report.first_client_name';
                    $condition1=(strlen($rawQuery) > 0)? " OR ":" ";
                    $row_name_string1=  "'" .implode("','", $family_members_name). "'";
                    $rawQuery.=$condition1.$queryString." IN (".$row_name_string1."))";
                }
            } 

            $rawQuery.=' AND tt_sip_stp_swp_report.cease_terminate_date IS NULL ';
            // $rawQuery.=' AND tt_sip_stp_swp_report.pause_from_date IS NULL ';
            // $rawQuery.=' AND tt_sip_stp_swp_report.pause_to_date IS NULL ';
            $rawQuery.=' AND tt_sip_stp_swp_report.from_date <="'.date('Y-m-d').'"';
            $rawQuery.=' AND tt_sip_stp_swp_report.to_date >="'.date('Y-m-d').'" ';

            // DB::enableQueryLog();
            $data=[];
            $my_datas=SipStpSwpReport::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','tt_sip_stp_swp_report.product_code')
                    ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                    ->leftJoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
                    ->leftJoin('md_option','md_option.id','=','md_scheme_isin.option_id')
                    ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
                    ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                    ->leftJoin('md_amc','md_amc.id','=','md_scheme.amc_id')
                    ->leftJoin('md_amc as md_amc_1','md_amc_1.amc_code','=','tt_sip_stp_swp_report.amc_code')
                    ->leftJoin('md_scheme_isin as to_isin','to_isin.product_code','=','tt_sip_stp_swp_report.to_product_code')
                    ->leftJoin('md_scheme as to_scheme','to_scheme.id','=','to_isin.scheme_id')
                    ->leftJoin('md_category as to_category','to_category.id','=','to_scheme.category_id')
                    ->leftJoin('md_subcategory as to_subcategory','to_subcategory.id','=','to_scheme.subcategory_id')
                    ->leftJoin('md_systematic_trans_type','md_systematic_trans_type.trans_type_code','=','tt_sip_stp_swp_report.auto_trans_type')
                    ->select('tt_sip_stp_swp_report.*','tt_sip_stp_swp_report.period_day as sip_date','tt_sip_stp_swp_report.auto_amount as amount','tt_sip_stp_swp_report.cease_terminate_date as terminated_date',
                    'tt_sip_stp_swp_report.pause_from_date as pause_start_date','tt_sip_stp_swp_report.pause_to_date as pause_end_date','tt_sip_stp_swp_report.bank as bank_name','tt_sip_stp_swp_report.instrm_no as acc_no',
                    'md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name',
                    'md_amc.amc_short_name as amc_name','md_amc_1.amc_short_name as amc_short_name','md_plan.plan_name','md_option.opt_name as option_name',
                    'md_systematic_trans_type.trans_type','md_systematic_trans_type.trans_sub_type',
                    'to_scheme.scheme_name as to_scheme_name','to_category.cat_name as to_cat_name','to_subcategory.subcategory_name as to_subcat_name')
                    ->selectRaw('(select `freq_name` from `md_systematic_frequency` where `rnt_id`=tt_sip_stp_swp_report.rnt_id and `freq_code`=tt_sip_stp_swp_report.periodicity limit 1) as freq')
                    ->selectRaw('(SELECT COUNT(*) FROM `md_systematic_unregistered` WHERE remarks=tt_sip_stp_swp_report.remarks AND rnt_id=tt_sip_stp_swp_report.rnt_id) as terminate_logic_count')
                    ->selectRaw('datediff(tt_sip_stp_swp_report.cease_terminate_date, tt_sip_stp_swp_report.from_date) as terminate_datediff')
                    ->where('tt_sip_stp_swp_report.amc_flag','N')
                    ->where('tt_sip_stp_swp_report.scheme_flag','N')
                    ->where('tt_sip_stp_swp_report.bu_type_flag','N')
                    ->where('tt_sip_stp_swp_report.plan_option_flag','N')
                    ->where('tt_sip_stp_swp_report.freq_mismatch_flag','N')
                    ->whereRaw($rawQuery)
                    ->orderBy('md_scheme.scheme_name','ASC')
                    ->get();
            // return  $my_datas;  
            // dd(DB::getQueryLog());

            foreach ($my_datas as $key => $my_data) {
                if ($my_data->cease_terminate_date==null && $my_data->from_date <= date('Y-m-d') && $my_data->to_date >= date('Y-m-d')) {
                    $my_data->activate_status='Active';
                }else {
                    if ($my_data->rnt_id==1) {
                        if ($my_data->cease_terminate_date!="" && $my_data->from_date <= $my_data->cease_terminate_date && $my_data->terminate_logic_count==0 && $my_data->terminate_datediff > 30) {
                            $my_data->activate_status='Inactive';
                        }
                    }else {
                        if ($my_data->cease_terminate_date!="" && $my_data->to_date > $my_data->cease_terminate_date && $my_data->from_date <= $my_data->cease_terminate_date && $my_data->terminate_logic_count==0 && $my_data->terminate_datediff > 30) {
                            $my_data->activate_status='Inactive';
                        }
                    }
                    // $my_data->activate_status =$active_status;
                }

                $my_data->reg_no =$my_data->auto_trans_no;
                if ($my_data->rnt_id==2) {
                    if(($my_data->frequency=="Daily") || ($my_data->frequency=="WEEKLY") || ($my_data->frequency=="Fortnightly")){
                        $my_data->sip_date=$my_data->frequency;
                        $my_data->stp_date=$my_data->frequency;
                        $my_data->swp_date=$my_data->frequency;
                    }else {
                        $my_data->sip_date=number_format((float)date('d',strtotime($my_data->from_date)), 0, '.', '');
                        $my_data->stp_date=number_format((float)date('d',strtotime($my_data->from_date)), 0, '.', '');
                        $my_data->swp_date=number_format((float)date('d',strtotime($my_data->from_date)), 0, '.', '');
                    }
                    $my_data->freq=$my_data->frequency;
                    $my_data->duration =$my_data->no_of_installment;
                }elseif ($my_data->rnt_id==1) {
                    // return $my_data;
                    if(($my_data->freq=="Daily") || ($my_data->freq=="Weekly") || ($my_data->freq=="Fortnightly")){
                        // return $my_data;
                        $my_data->sip_date=$my_data->freq;
                        $my_data->stp_date=$my_data->freq;
                        $my_data->swp_date=$my_data->freq;
                    }else {
                        $my_data->sip_date=number_format((float)$my_data->period_day, 0, '.', '');
                        $my_data->stp_date=number_format((float)$my_data->period_day, 0, '.', '');
                        $my_data->swp_date=number_format((float)$my_data->period_day, 0, '.', '');
                    }
                    /***************for duration calculation****************************/
                    if ($report_type=='P') {  // if sip 
                        $calculation_day =(int)abs((strtotime($my_data->reg_date) - strtotime($my_data->from_date))/(60*60*24));
                        $my_data->calculation_day =$calculation_day;
                        if ($calculation_day <= 30) {
                            $my_data->duration =(int)abs((strtotime($my_data->from_date) - strtotime($my_data->to_date))/(60*60*24*30));
                        }else {
                            $my_data->duration =(int)abs((strtotime($my_data->reg_date) - strtotime($my_data->to_date))/(60*60*24*30));
                        }
                    }else {  // swp & stp
                        $calculation_day =(int)abs((strtotime($my_data->reg_date) - strtotime($my_data->from_date))/(60*60*24));
                        $my_data->calculation_day =$calculation_day;
                        if ($calculation_day <= 15) {
                            $my_data->duration =(int)abs((strtotime($my_data->from_date) - strtotime($my_data->to_date))/(60*60*24*30));
                        }else {
                            $my_data->duration =(int)abs((strtotime($my_data->reg_date) - strtotime($my_data->to_date))/(60*60*24*30));
                        }
                    }
                }
                if ($my_data->pause_end_date!='' && $my_data->pause_end_date < date('Y-m-d')) {  // if pause to date more then to date
                    $my_data->pause_start_date=NULL;
                    $my_data->pause_end_date=NULL;
                }
                array_push($data,$my_data);
            }
            
            $mydata=[];
            $mydata['client_details']=$client_details;
            $mydata['data']=$data;
            $mydata['valuation_as_on']=$valuation_as_on;
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($mydata);
    }

    public function misTrans(Request $request)
    {
        
    }
}