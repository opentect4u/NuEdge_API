<?php

namespace App\Http\Controllers\v1\Operation;

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
    TempSipStpTransaction
};
use Validator;
use Illuminate\Support\Carbon;
use Excel;
use App\Helpers\TransHelper;
use DB;

class SipStpTransController extends Controller
{
    public function search(Request $request)
    {
        try {
            // return $request;
            $report_type=$request->report_type;

            $date_range=$request->date_range;
            $folio_no=$request->folio_no;
            $client_id=$request->client_id;
            $pan_no=$request->pan_no;
            // $pan_no=json_decode($request->pan_no);
            $amc_id=json_decode($request->amc_id);
            $cat_id=json_decode($request->cat_id);
            $sub_cat_id=json_decode($request->sub_cat_id);
            $scheme_id=json_decode($request->scheme_id);
            $sub_type=$request->sub_type;
            $sip_swp_stp_type=$request->sip_swp_stp_type;

            $data=[];
            $rawQuery='';
            $queryString='td_sip_stp_trans.auto_trans_type';
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
            
            if ($sip_type || $date_range || $folio_no || $pan_no || !empty($amc_id) || !empty($cat_id) || !empty($sub_cat_id) || !empty($scheme_id)) {
                switch ($sip_type) {
                    case 'L':
                        $rawQuery.=' AND td_sip_stp_trans.cease_terminate_date IS NULL ';
                        // $rawQuery.=' AND td_sip_stp_trans.pause_from_date IS NULL ';
                        // $rawQuery.=' AND td_sip_stp_trans.pause_to_date IS NULL ';
                        $rawQuery.=' AND td_sip_stp_trans.from_date <= "'.date('Y-m-d').'"';
                        $rawQuery.=' AND td_sip_stp_trans.to_date >= "'.date('Y-m-d').'" ';
                        break;
                    case 'R':
                        if ($sub_type=='RR') {
                            $rawQuery.=' AND td_sip_stp_trans.from_date >= "'.date('Y-m-d').'"';
                            if ($request->month && $request->year) {
                                $rawQuery.=' AND YEAR(td_sip_stp_trans.from_date)="'.$request->year.'" ';
                                $rawQuery.=' AND MONTH(td_sip_stp_trans.from_date)="'.$request->month.'" ';
                            }
                            // $rawQuery.=' AND DATE(td_sip_stp_trans.from_date) >"'.date('d').'" ';
                        }else if($sub_type=='RU') {
                            // $rawQuery.=' AND td_sip_stp_trans.from_date >= td_sip_stp_trans.cease_terminate_date';
                            // $rawQuery.=' AND td_sip_stp_trans.from_date >= "'.date('Y-m-d').'"';
                            // (`from_date` >= ? or `from_date` > ?)
                            $rawQuery.='AND (td_sip_stp_trans.from_date >= td_sip_stp_trans.cease_terminate_date OR (SELECT COUNT(*) FROM `md_systematic_unregistered` WHERE remarks=td_sip_stp_trans.remarks AND rnt_id=td_sip_stp_trans.rnt_id) > 0)';
                        }
                        break;
                    case 'T':
                        // cond1 && (cond1 || cond2)
                        // $rawQuery.='AND IF(td_sip_stp_trans.rnt_id=1, td_sip_stp_trans.cease_terminate_date!="", td_sip_stp_trans.cease_terminate_date!="" AND td_sip_stp_trans.f_status="TERMINATED")';
                        $rawQuery.='AND IF(td_sip_stp_trans.rnt_id=1, DATE_FORMAT(td_sip_stp_trans.cease_terminate_date,"Y-m-d")!="", DATE_FORMAT(td_sip_stp_trans.cease_terminate_date,"Y-m-d")!="" AND DATE_FORMAT(td_sip_stp_trans.to_date,"Y-m-d") > DATE_FORMAT(td_sip_stp_trans.cease_terminate_date,"Y-m-d"))';
                        // $rawQuery.='AND td_sip_stp_trans.cease_terminate_date!="" AND td_sip_stp_trans.to_date >= td_sip_stp_trans.cease_terminate_date';
                        $rawQuery.=' AND DATE_FORMAT(td_sip_stp_trans.from_date,"Y-m-d") <= DATE_FORMAT(td_sip_stp_trans.cease_terminate_date,"Y-m-d")';
                        break;
                    case 'M':
                        if ($sub_type=='MM') {  
                            // $rawQuery.=' AND td_sip_stp_trans.to_date <= "'.date('Y-m-d').'" ';
                            // $rawQuery.=' AND td_sip_stp_trans.cease_terminate_date IS NULL ';
                            $rawQuery.='AND IF(td_sip_stp_trans.rnt_id=1, 
                                DATE_FORMAT(td_sip_stp_trans.to_date,"Y-m-d") <= "'.date('Y-m-d').'" AND DATE_FORMAT(td_sip_stp_trans.cease_terminate_date,"Y-m-d") IS NULL, 
                                DATE_FORMAT(td_sip_stp_trans.cease_terminate_date,"Y-m-d")!="" AND DATE_FORMAT(td_sip_stp_trans.to_date,"Y-m-d") <= DATE_FORMAT(td_sip_stp_trans.cease_terminate_date,"Y-m-d")
                                )';
                        }else if ($sub_type=='MT'){
                            // YEAR(date) AS y, MONTH(date)
                            $rawQuery.='AND IF(td_sip_stp_trans.rnt_id=1, 
                                td_sip_stp_trans.to_date >= "'.date('Y-m-d').'" AND td_sip_stp_trans.from_date <= "'.date('Y-m-d').'" AND td_sip_stp_trans.cease_terminate_date IS NULL , 
                                td_sip_stp_trans.to_date >= "'.date('Y-m-d').'" AND td_sip_stp_trans.from_date <= "'.date('Y-m-d').'" AND td_sip_stp_trans.cease_terminate_date IS NULL 
                                )'; 
                            // view_by
                            // upto
                            if ($request->month && $request->year) {
                                $rawQuery.=' AND MONTH(td_sip_stp_trans.to_date)="'.$request->month.'" ';
                                $rawQuery.=' AND YEAR(td_sip_stp_trans.to_date)="'.$request->year.'" ';
                            }
                            if ($request->upto) {
                                $addDays=$request->upto * 30;
                                $calDays = date('Y-m-d', strtotime(date('Y-m-d'). ' + '.$addDays.' days'));
                                $rawQuery.=' AND td_sip_stp_trans.to_date <="'.$calDays.'" ';
                            }
                            // $rawQuery.=' AND DATE(td_sip_stp_trans.to_date) >"'.date('d').'" ';
                        }
                        break;
                    case 'P':
                        $rawQuery.=' AND DATE_FORMAT(td_sip_stp_trans.cease_terminate_date,"Y-m-d") IS NULL ';
                        $rawQuery.=' AND DATE_FORMAT(td_sip_stp_trans.pause_from_date,"Y-m-d")!="" ';
                        $rawQuery.=' AND DATE_FORMAT(td_sip_stp_trans.pause_to_date,"Y-m-d")!="" ';
                        $rawQuery.=' AND DATE_FORMAT(td_sip_stp_trans.to_date,"Y-m-d") >="'.date('Y-m-d').'" ';
                        break;
                    default:
                        break;
                }

                // $queryString='td_sip_stp_trans.folio_no';
                // $rawQuery.=Helper::WhereRawQuery($folio_no,$rawQuery,$queryString);
                // $queryString='td_sip_stp_trans.first_client_pan';
                // $rawQuery.=Helper::WhereRawQuery($pan_no,$rawQuery,$queryString);
                // $queryString='md_scheme.amc_id';
                // $rawQuery.=Helper::WhereRawQuery($amc_id,$rawQuery,$queryString);
                // $queryString='md_scheme.category_id';
                // $rawQuery.=Helper::WhereRawQuery($cat_id,$rawQuery,$queryString);
                // $queryString='md_scheme.subcategory_id';
                // $rawQuery.=Helper::WhereRawQuery($sub_cat_id,$rawQuery,$queryString);
                // $queryString='md_scheme_isin.scheme_id';
                // $rawQuery.=Helper::WhereRawQuery($scheme_id,$rawQuery,$queryString);

                // return $rawQuery;
                $rawQuery=$this->filterCriteria($rawQuery,$folio_no,$pan_no,$amc_id,$cat_id,$sub_cat_id,$scheme_id);

                // return $rawQuery;
            }
            // return $rawQuery;
            // $my_datas=[];
            // DB::enableQueryLog();

            $my_datas=SipStpTransaction::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_sip_stp_trans.product_code')
                    ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                    ->leftJoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
                    ->leftJoin('md_option','md_option.id','=','md_scheme_isin.option_id')
                    ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
                    ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                    ->leftJoin('md_amc','md_amc.id','=','md_scheme.amc_id')
                    ->leftJoin('md_amc as md_amc_1','md_amc_1.amc_code','=','td_sip_stp_trans.amc_code')
                    // ->leftJoin('md_employee','md_employee.euin_no','=','td_sip_stp_trans.euin_no')
                    ->leftJoin('md_scheme_isin as to_isin','to_isin.product_code','=','td_sip_stp_trans.to_product_code')
                    ->leftJoin('md_scheme as to_scheme','to_scheme.id','=','to_isin.scheme_id')
                    ->leftJoin('md_category as to_category','to_category.id','=','to_scheme.category_id')
                    ->leftJoin('md_subcategory as to_subcategory','to_subcategory.id','=','to_scheme.subcategory_id')
                    // ->leftJoin('md_employee','md_employee.euin_no','=','td_sip_stp_trans.euin_no')
                    ->leftJoin('md_employee','md_employee.euin_no','=',DB::raw('IF(td_sip_stp_trans.euin_no!="",td_sip_stp_trans.euin_no,(select euin_no from td_mutual_fund_trans where folio_no=td_sip_stp_trans.folio_no and product_code= td_sip_stp_trans.product_code order by trans_date asc limit 1))'))
                    ->leftJoin('md_branch','md_branch.id','=','md_employee.branch_id')
                    ->leftJoin('md_systematic_trans_type','md_systematic_trans_type.trans_type_code','=','td_sip_stp_trans.auto_trans_type')
                    ->select('td_sip_stp_trans.*','td_sip_stp_trans.period_day as sip_date','td_sip_stp_trans.auto_amount as amount','td_sip_stp_trans.cease_terminate_date as terminated_date',
                    'td_sip_stp_trans.pause_from_date as pause_start_date','td_sip_stp_trans.pause_to_date as pause_end_date',
                    'md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name',
                    'md_amc.amc_short_name as amc_name','md_amc_1.amc_short_name as amc_short_name','md_plan.plan_name','md_option.opt_name as option_name',
                    'md_employee.emp_name as rm_name','md_branch.brn_name as branch_name','md_employee.bu_type_id as bu_type_id','md_employee.branch_id as branch_id',
                    'md_systematic_trans_type.trans_type','md_systematic_trans_type.trans_sub_type',
                    'to_scheme.scheme_name as to_scheme_name','to_category.cat_name as to_cat_name','to_subcategory.subcategory_name as to_subcat_name')
                    ->selectRaw('(select `bu_type` from `md_business_type` where `bu_code` =md_employee.bu_type_id and `branch_id` =md_employee.branch_id limit 1) as bu_type')
                    ->selectRaw('(select `freq_name` from `md_systematic_frequency` where `rnt_id` =td_sip_stp_trans.rnt_id and `freq_code` =td_sip_stp_trans.periodicity limit 1) as freq')
                    // ->selectRaw('(IF(td_sip_stp_trans.bank!="" || td_sip_stp_trans.bank!= NULL,td_sip_stp_trans.bank,(select bank_name from td_mutual_fund_trans where folio_no=td_sip_stp_trans.folio_no and product_code= td_sip_stp_trans.product_code order by trans_date asc limit 1))) as bank_name')
                    // ->selectRaw('(IF(td_sip_stp_trans.instrm_no!="" || td_sip_stp_trans.instrm_no!= NULL,td_sip_stp_trans.instrm_no,(select acc_no from td_mutual_fund_trans where folio_no=td_sip_stp_trans.folio_no and product_code= td_sip_stp_trans.product_code order by trans_date asc limit 1))) as acc_no')
                    ->where('td_sip_stp_trans.amc_flag','N')
                    ->where('td_sip_stp_trans.scheme_flag','N')
                    ->where('td_sip_stp_trans.bu_type_flag','N')
                    ->where('td_sip_stp_trans.plan_option_flag','N')
                    ->where('td_sip_stp_trans.freq_mismatch_flag','N')
                    ->whereRaw($rawQuery)
                    // ->orderBy('td_sip_stp_trans.nav_date','desc')
                    // ->take(50)
                    ->get();
            // return  $my_datas;  
            // dd(DB::getQueryLog());

            foreach ($my_datas as $key => $my_data) {
                $my_data->reg_no =$my_data->auto_trans_no;
                if ($my_data->rnt_id==2) {
                    if(($my_data->frequency=="Daily") || ($my_data->frequency=="WEEKLY") || ($my_data->frequency=="Fortnightly")){
                        $my_data->sip_date=$my_data->frequency;
                        $my_data->stp_date=$my_data->frequency;
                        $my_data->swp_date=$my_data->frequency;
                    }else {
                        $my_data->sip_date=date('d',strtotime($my_data->from_date));
                        $my_data->stp_date=date('d',strtotime($my_data->from_date));
                        $my_data->swp_date=date('d',strtotime($my_data->from_date));
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
                        $my_data->sip_date=$my_data->period_day;
                        $my_data->stp_date=$my_data->period_day;
                        $my_data->swp_date=$my_data->period_day;
                    }

                    // if ($my_data->auto_trans_type=='P') {
                    //     if(($my_data->freq=="Daily") || ($my_data->freq=="Weekly") || ($my_data->freq=="Fortnightly")){
                    //         $my_data->from_date = $my_data->freq;
                    //     }else {
                    //         $my_data->from_date =date('Y-m', strtotime('+1 month', (strtotime($my_data->reg_date)))).'-'.$my_data->period_day;
                    //     }
                    // }
                    /***************for duration calculation****************************/
                    if ($report_type=='R') {  // if swp 
                        $my_data->duration =(int)abs((strtotime($my_data->from_date) - strtotime($my_data->to_date))/(60*60*24*30));
                    }else {
                        $calculation_day =(int)abs((strtotime($my_data->reg_date) - strtotime($my_data->from_date))/(60*60*24));
                        $my_data->calculation_day =$calculation_day;
                        if ($calculation_day <= 30) {
                            $my_data->duration =(int)abs((strtotime($my_data->from_date) - strtotime($my_data->to_date))/(60*60*24*30));
                        }else {
                            $my_data->duration =(int)abs((strtotime($my_data->reg_date) - strtotime($my_data->to_date))/(60*60*24*30));
                        }
                    }
                }
                array_push($data,$my_data);
            }

            
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function filterCriteria($rawQuery,$folio_no,$pan_no,$amc_id,$cat_id,$sub_cat_id,$scheme_id)
    {
        $queryString='td_sip_stp_trans.folio_no';
        $rawQuery.=Helper::WhereRawQuery($folio_no,$rawQuery,$queryString);
        $queryString='td_sip_stp_trans.first_client_pan';
        $rawQuery.=Helper::WhereRawQuery($pan_no,$rawQuery,$queryString);
        $queryString='md_scheme.amc_id';
        $rawQuery.=Helper::WhereRawQuery($amc_id,$rawQuery,$queryString);
        $queryString='md_scheme.category_id';
        $rawQuery.=Helper::WhereRawQuery($cat_id,$rawQuery,$queryString);
        $queryString='md_scheme.subcategory_id';
        $rawQuery.=Helper::WhereRawQuery($sub_cat_id,$rawQuery,$queryString);
        $queryString='md_scheme_isin.scheme_id';
        $rawQuery.=Helper::WhereRawQuery($scheme_id,$rawQuery,$queryString);
        return $rawQuery;
    }
}
