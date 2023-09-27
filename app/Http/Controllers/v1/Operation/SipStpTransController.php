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
            $report_type=$request->report_type;
            $sip_type=$request->sip_type;

            $date_range=$request->date_range;
            $folio_no=$request->folio_no;
            $client_id=$request->client_id;
            $pan_no=$request->pan_no;
            // $pan_no=json_decode($request->pan_no);
            $amc_id=json_decode($request->amc_id);
            $cat_id=json_decode($request->cat_id);
            $sub_cat_id=json_decode($request->sub_cat_id);
            $scheme_id=json_decode($request->scheme_id);
            $trans_type=json_decode($request->trans_type);
            $trans_sub_type=json_decode($request->trans_sub_type);

            $data=[];
            if ($report_type || $sip_type || $date_range || $folio_no || $pan_no || !empty($amc_id) || !empty($cat_id) || !empty($sub_cat_id) || !empty($scheme_id)) {
                $rawQuery='';
                $queryString='td_sip_stp_trans.auto_trans_type';
                $rawQuery.=Helper::WhereRawQuery($report_type,$rawQuery,$queryString);

                switch ($sip_type) {
                    case 'T':
                        $rawQuery.=' AND td_sip_stp_trans.cease_date!="" ';
                        break;
                    case 'L':
                        $rawQuery.=' AND td_sip_stp_trans.cease_date IS NULL ';
                    default:
                        # code...
                        break;
                }

                // if ($date_range) {
                //     $from_date=Carbon::parse(str_replace('/','-',explode("-",$date_range)[0]))->format('Y-m-d') ;
                //     $to_date=Carbon::parse(str_replace('/','-',explode("-",$date_range)[1]))->format('Y-m-d') ;
                //     $queryString='td_sip_stp_trans.reg_date';
                //     $rawQuery.=Helper::FrmToDateRawQuery($from_date,$to_date,$rawQuery,$queryString);
                // }
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
                // $rawQuery=$this->filterCriteria($rawQuery,$from_date,$to_date,$tin_no,$proposer_name,$ins_type_id,$company_id,$product_type_id,$product_id,$insured_bu_type,$ack_status);

                // return $rawQuery;

                $data=SipStpTransaction::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_sip_stp_trans.product_code')
                    ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                    ->leftJoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
                    ->leftJoin('md_option','md_option.id','=','md_scheme_isin.option_id')
                    ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
                    ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                    ->leftJoin('md_amc','md_amc.id','=','md_scheme.amc_id')
                    ->leftJoin('md_amc as md_amc_1','md_amc_1.amc_code','=','td_sip_stp_trans.amc_code')
                    ->leftJoin('md_employee','md_employee.euin_no','=','td_sip_stp_trans.euin_no')
                    ->leftJoin('md_branch','md_branch.id','=','md_employee.branch_id')
                    ->select('td_sip_stp_trans.*','td_sip_stp_trans.period_day as sip_date','td_sip_stp_trans.auto_amount as amount','td_sip_stp_trans.bank as bank_name','td_sip_stp_trans.instrm_no as acc_no','td_sip_stp_trans.cease_date as terminated_date',
                    'md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name',
                    'md_amc.amc_short_name as amc_name','md_amc_1.amc_short_name as amc_short_name','md_plan.plan_name','md_option.opt_name as option_name',
                    'md_employee.emp_name as rm_name','md_branch.brn_name as branch_name','md_employee.bu_type_id as bu_type_id','md_employee.branch_id as branch_id')
                    ->selectRaw('(select `bu_type` from `md_business_type` where `bu_code` =md_employee.bu_type_id and `branch_id` =md_employee.branch_id limit 1) as bu_type')
                    ->selectRaw('(select `freq_name` from `md_systematic_frequency` where `rnt_id` =td_sip_stp_trans.rnt_id and `freq_code` =td_sip_stp_trans.periodicity limit 1) as freq')
                    ->where('td_sip_stp_trans.amc_flag','N')
                    ->where('td_sip_stp_trans.scheme_flag','N')
                    ->whereRaw($rawQuery)
                    // ->orderBy('td_sip_stp_trans.nav_date','desc')
                    // ->take(100)
                    ->get();
            }
                // $data=SipStpTransaction::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_sip_stp_trans.product_code')
                //     ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                //     ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
                //     ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                //     ->leftJoin('md_amc','md_amc.id','=','md_scheme.amc_id')
                //     ->leftJoin('md_amc as md_amc_1','md_amc_1.amc_code','=','td_sip_stp_trans.amc_code')
                //     ->select('td_sip_stp_trans.*','md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name',
                //     'md_amc.amc_short_name as amc_name','md_amc_1.amc_short_name as amc_short_name')
                //     ->where('td_sip_stp_trans.amc_flag','N')
                //     ->where('td_sip_stp_trans.scheme_flag','N')
                //     // ->where('md_scheme_isin.plan_type',$plan_type)
                //     // ->whereRaw($rawQuery)
                //     // ->orderBy('td_sip_stp_trans.nav_date','desc')
                //     ->take(100)
                //     ->get();

            
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}
