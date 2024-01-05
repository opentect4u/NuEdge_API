<?php

namespace App\Http\Controllers\V1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Helpers\TransHelper;
use App\Models\{
    MutualFund,
    Client,
    FormReceived,
    MutualFundTransaction,
    MailbackProcess,
    TempMutualFundTransaction,
    MFTransTypeSubType,
    TempNAVDetails,
    NAVDetails,
    TempSipStpTransaction,
    SipStpTransaction,
    FolioDetails,
    TempFolioDetails,
    FolioDetailsReport,
    TempBrokerChangeTrans,
    BrokerChangeTrans,
    SipStpSwpReport
};
use Validator;
use Illuminate\Support\Carbon;
use Excel;
use DB;

class MailBackReplicaController extends Controller
{
    public function showLockTransaction(Request $request)
    {
        try {
            // return $request;
            $file_type=$request->file_type;
            $mismatch_flag=$request->mismatch_flag;
            $rawQuery='';
            switch ($mismatch_flag) {
                case 'B':
                    $rawQuery="bu_type_lock_flag='L'";
                    break;
                case 'D':
                    $rawQuery="divi_lock_flag='L'";
                    break;
                case 'P/O':
                    $rawQuery="plan_option_lock_flag='L'";
                    break;
                default:
                    break;
            }
            switch ($file_type) {
                case 'T':
                    $all_data=MutualFundTransaction::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_mutual_fund_trans.product_code')
                        ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                        ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
                        ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                        ->leftJoin('md_amc','md_amc.amc_code','=','td_mutual_fund_trans.amc_code')
                        ->leftJoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
                        ->leftJoin('md_option','md_option.id','=','md_scheme_isin.option_id')
                        ->leftJoin('md_employee','md_employee.euin_no','=',DB::raw('IF(td_mutual_fund_trans.old_euin_no!="",td_mutual_fund_trans.old_euin_no,(select euin_no from td_mutual_fund_trans where folio_no=td_mutual_fund_trans.folio_no and product_code=td_mutual_fund_trans.product_code AND euin_no!="" limit 1))'))
                        ->leftJoin('md_branch','md_branch.id','=','md_employee.branch_id')
                        ->select('td_mutual_fund_trans.*','md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name','md_amc.amc_short_name as amc_name',
                        'md_plan.plan_name as plan_name','md_option.opt_name as option_name','md_amc.id as amc_id',
                        'md_employee.emp_name as rm_name','md_branch.brn_name as branch','md_employee.bu_type_id as bu_type_id','md_employee.branch_id as branch_id','td_mutual_fund_trans.old_euin_no as euin_no')
                        ->selectRaw('amount as tot_amount')
                        ->selectRaw('stamp_duty as tot_stamp_duty')
                        ->selectRaw('tds as tot_tds')
                        ->selectRaw('(select bu_type from md_business_type where bu_code=md_employee.bu_type_id and branch_id=md_employee.branch_id limit 1) as bu_type')
                        ->where('td_mutual_fund_trans.delete_flag','N')
                        ->whereRaw($rawQuery)
                        ->orderBy('td_mutual_fund_trans.created_at','desc')
                        // ->groupBy('td_mutual_fund_trans.trans_no')
                        // ->groupBy('td_mutual_fund_trans.trxn_type_flag')
                        // ->groupByRaw('IF(substr(trxn_nature,1,19)="Systematic-Reversed","Systematic-Reversed",trxn_nature)')
                        // ->groupBy('td_mutual_fund_trans.trans_desc')
                        // ->groupBy('td_mutual_fund_trans.kf_trans_type')
                        // ->inRandomOrder()
                        // ->take(5)
                        ->get();
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
                        $value->tot_gross_amount= number_format((float)((float)$value->tot_amount + (float)$value->tot_stamp_duty + (float)$value->tot_tds), 2, '.', '');
                        $value->transaction_type=$transaction_type;
                        $value->transaction_subtype=$transaction_subtype;

                        array_push($data,$value);
                    }
                    break;
                case 'S':
                    $data=[];
                    $my_datas=SipStpSwpReport::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','tt_sip_stp_swp_report.product_code')
                        ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                        ->leftJoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
                        ->leftJoin('md_option','md_option.id','=','md_scheme_isin.option_id')
                        ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
                        ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                        ->leftJoin('md_amc','md_amc.amc_code','=','tt_sip_stp_swp_report.amc_code')
                        ->leftJoin('md_amc as md_amc_1','md_amc_1.amc_code','=','tt_sip_stp_swp_report.amc_code')
                        ->leftJoin('md_scheme_isin as to_isin','to_isin.product_code','=','tt_sip_stp_swp_report.to_product_code')
                        ->leftJoin('md_scheme as to_scheme','to_scheme.id','=','to_isin.scheme_id')
                        ->leftJoin('md_category as to_category','to_category.id','=','to_scheme.category_id')
                        ->leftJoin('md_subcategory as to_subcategory','to_subcategory.id','=','to_scheme.subcategory_id')
                        ->leftJoin('md_employee','md_employee.euin_no','=','tt_sip_stp_swp_report.old_euin_no')
                        ->leftJoin('md_branch','md_branch.id','=','md_employee.branch_id')
                        ->leftJoin('md_systematic_trans_type','md_systematic_trans_type.trans_type_code','=','tt_sip_stp_swp_report.auto_trans_type')
                        ->select('tt_sip_stp_swp_report.*','tt_sip_stp_swp_report.period_day as sip_date','tt_sip_stp_swp_report.auto_amount as amount','tt_sip_stp_swp_report.cease_terminate_date as terminated_date',
                        'tt_sip_stp_swp_report.pause_from_date as pause_start_date','tt_sip_stp_swp_report.pause_to_date as pause_end_date','tt_sip_stp_swp_report.bank as bank_name','tt_sip_stp_swp_report.instrm_no as acc_no',
                        'md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name',
                        'md_amc.amc_short_name as amc_name','md_amc_1.amc_short_name as amc_short_name','md_plan.plan_name','md_option.opt_name as option_name',
                        'md_employee.emp_name as rm_name','md_branch.brn_name as branch_name','md_employee.bu_type_id as bu_type_id','md_employee.branch_id as branch_id',
                        'md_systematic_trans_type.trans_type','md_systematic_trans_type.trans_sub_type','tt_sip_stp_swp_report.old_euin_no as euin_no',
                        'to_scheme.scheme_name as to_scheme_name','to_category.cat_name as to_cat_name','to_subcategory.subcategory_name as to_subcat_name')
                        ->selectRaw('(select `bu_type` from `md_business_type` where `bu_code`=md_employee.bu_type_id and `branch_id`=md_employee.branch_id limit 1) as bu_type')
                        ->selectRaw('(select `freq_name` from `md_systematic_frequency` where `rnt_id`=tt_sip_stp_swp_report.rnt_id and `freq_code`=tt_sip_stp_swp_report.periodicity limit 1) as freq')
                        ->whereRaw($rawQuery)
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
            
                            // if ($my_data->auto_trans_type=='P') {
                            //     if(($my_data->freq=="Daily") || ($my_data->freq=="Weekly") || ($my_data->freq=="Fortnightly")){
                            //         $my_data->from_date = $my_data->freq;
                            //     }else {
                            //         $my_data->from_date =date('Y-m', strtotime('+1 month', (strtotime($my_data->reg_date)))).'-'.$my_data->period_day;
                            //     }
                            // }
                            /***************for duration calculation****************************/
                            if ($my_data->auto_trans_type=='R' || $my_data->auto_trans_type=='SWP') {  // if swp 
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
                        if ($my_data->pause_end_date!='' && $my_data->pause_end_date < date('Y-m-d')) {  // if pause to date more then to date
                            $my_data->pause_start_date=NULL;
                            $my_data->pause_end_date=NULL;
                        }
                        array_push($data,$my_data);
                    }
                    break;
                case 'F':
                    $data=FolioDetailsReport::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','tt_folio_details_reports.product_code')
                        ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                        ->leftJoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
                        ->leftJoin('md_option','md_option.id','=','md_scheme_isin.option_id')
                        ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
                        ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                        ->leftJoin('md_amc','md_amc.amc_code','=','tt_folio_details_reports.amc_code')
                        ->leftJoin('md_employee','md_employee.euin_no','=','tt_folio_details_reports.euin_no')
                        ->leftJoin('md_branch','md_branch.id','=','md_employee.branch_id')
                        ->leftJoin('md_pincode','md_pincode.id','=','tt_folio_details_reports.pincode')
                        ->leftJoin('md_states','md_states.id','=','md_pincode.state_id')
                        ->leftJoin('md_city_type','md_city_type.id','=','md_pincode.city_type_id')
                        ->leftJoin('md_deposit_bank','md_deposit_bank.ifs_code','=','tt_folio_details_reports.bank_ifsc')
                        ->select('tt_folio_details_reports.*','md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name',
                        'md_amc.amc_short_name as amc_short_name','md_states.name as state','md_city_type.name as city_type',
                        'md_employee.emp_name as rm_name','md_branch.brn_name as branch_name','md_employee.bu_type_id as bu_type_id','md_employee.branch_id as branch_id',
                        'md_plan.plan_name','md_option.opt_name as option_name','md_deposit_bank.micr_code as bank_micr')
                        ->selectRaw('(select `bu_type` from `md_business_type` where `bu_code` =md_employee.bu_type_id and `branch_id` =md_employee.branch_id limit 1) as bu_type')

                        ->selectRaw('IF(tt_folio_details_reports.rnt_id=2,
                        (CASE 
                            WHEN tt_folio_details_reports.pa_link_ststus_1st="Y" THEN "Aadhaar Linked"
                            WHEN tt_folio_details_reports.pa_link_ststus_1st="N" THEN "Aadhaar Not Linked"
                            WHEN tt_folio_details_reports.pa_link_ststus_1st="Blank" || pa_link_ststus_1st="BLANK" THEN "Not Applicable"
                            ELSE ""
                        END),
                        tt_folio_details_reports.pa_link_ststus_1st) as pa_link_ststus_1st')
                        ->selectRaw('IF(tt_folio_details_reports.rnt_id=2,
                        (CASE 
                            WHEN tt_folio_details_reports.pa_link_ststus_2nd="Y" THEN "Aadhaar Linked"
                            WHEN tt_folio_details_reports.pa_link_ststus_2nd="N" THEN "Aadhaar Not Linked"
                            WHEN tt_folio_details_reports.pa_link_ststus_2nd="Blank" || pa_link_ststus_2nd="BLANK" THEN "Not Applicable"
                            ELSE ""
                        END),
                        tt_folio_details_reports.pa_link_ststus_2nd) as pa_link_ststus_2nd')
                        ->selectRaw('IF(tt_folio_details_reports.rnt_id=2,
                        (CASE 
                            WHEN tt_folio_details_reports.pa_link_ststus_3rd="Y" THEN "Aadhaar Linked"
                            WHEN tt_folio_details_reports.pa_link_ststus_3rd="N" THEN "Aadhaar Not Linked"
                            WHEN tt_folio_details_reports.pa_link_ststus_3rd="Blank" || pa_link_ststus_3rd="BLANK" THEN "Not Applicable"
                            ELSE ""
                        END),
                        tt_folio_details_reports.pa_link_ststus_3rd) as pa_link_ststus_3rd')
                        ->selectRaw('IF(tt_folio_details_reports.rnt_id=2,
                        (CASE 
                            WHEN tt_folio_details_reports.guardian_pa_link_ststus="Y" THEN "Aadhaar Linked"
                            WHEN tt_folio_details_reports.guardian_pa_link_ststus="N" THEN "Aadhaar Not Linked"
                            WHEN tt_folio_details_reports.guardian_pa_link_ststus="Blank" || guardian_pa_link_ststus="BLANK" THEN "Not Applicable"
                            ELSE ""
                        END),
                        tt_folio_details_reports.guardian_pa_link_ststus) as guardian_pa_link_ststus')
                        ->whereRaw($rawQuery)
                        ->groupBy('tt_folio_details_reports.product_code')
                        ->groupBy('tt_folio_details_reports.folio_no')
                        // ->take(100)
                        ->get();
                    break;
                default:
                    # code...
                    break;
            }

        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}
