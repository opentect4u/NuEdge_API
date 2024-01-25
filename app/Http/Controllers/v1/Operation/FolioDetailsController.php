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
    FolioDetails,
    TempFolioDetails,
    FolioDetailsReport
};
use Validator;
use Illuminate\Support\Carbon;
use Excel;
use App\Helpers\TransHelper;
use DB;

class FolioDetailsController extends Controller
{
    public function search(Request $request)
    {
        try {
            // return $request;
            $pan_no=$request->pan_no;
            $folio_status=$request->folio_status;
            $kyc_status=$request->kyc_status;
            $nominee_status=$request->nominee_status;
            $adhaar_pan_link_status=$request->adhaar_pan_link_status;
            $folio_no=$request->folio_no;
            $investor_static_type=$request->investor_static_type;

            $brn_cd=$request->brn_cd;
            $bu_type_id=$request->bu_type_id;
            $kyc_status=$request->kyc_status;
            $client_name=$request->client_name;

            $rawQuery='';
            if ($folio_status || $client_name || $pan_no || $folio_no || $kyc_status || $nominee_status || $adhaar_pan_link_status) {
                if (!$pan_no) {
                    $queryString='tt_folio_details_reports.first_client_name';
                    $rawQuery.=Helper::WhereRawQuery($client_name,$rawQuery,$queryString);
                }
                $queryString='tt_folio_details_reports.pan';
                $rawQuery.=Helper::WhereRawQuery($pan_no,$rawQuery,$queryString);
                $queryString='tt_folio_details_reports.folio_no';
                $rawQuery.=Helper::WhereRawQuery($folio_no,$rawQuery,$queryString);
                $queryString='tt_folio_details_reports.folio_status';
                $rawQuery.=Helper::WhereRawQuery($folio_status,$rawQuery,$queryString);
                // if ($folio_status) {
                //     $condition=(strlen($rawQuery) > 0)? " AND ":" ";
                //     // $rawQuery.=$condition.'(IF((select SUM(amount) from td_mutual_fund_trans where folio_no=td_folio_details.folio_no and product_code=td_folio_details.product_code) > 0,"Active","Inactive"))="'.$folio_status.'"';
                //     $rawQuery.=$condition.'(IF((select SUM(amount) from td_mutual_fund_trans where folio_no=tt_folio_details_reports.folio_no and product_code=tt_folio_details_reports.product_code) > 0,"Active","Inactive"))="'.$folio_status.'"';
                // }
                if ($kyc_status) {
                    $queryString='tt_folio_details_reports.kyc_status_1st';
                    switch ($kyc_status) {
                        case 'Y':
                            $condition=(strlen($rawQuery) > 0)? " AND ":" ";
                            $rawQuery.=$condition.$queryString."='KYC OK'";
                            $rawQuery.=" OR ".$queryString."='Y' OR ".$queryString."='M'";
                            break;
                        case 'N':
                            // KYC Failed
                            // KYC Not Verified

                            $condition=(strlen($rawQuery) > 0)? " AND ":" ";
                            $rawQuery.=$condition.$queryString."='KYC Failed' OR ".$queryString."='KYC Not Verified'";
                            $rawQuery.=" OR ".$queryString."='H' OR ".$queryString."='R'";
                            break;
                        default:
                            # code...
                            break;
                    }
                }

                if ($nominee_status) {
                    $queryString='tt_folio_details_reports.nom_optout_status';
                    switch ($nominee_status) {
                        case 'Opt-Out':
                            $condition=(strlen($rawQuery) > 0)? " AND ":" ";
                            $rawQuery.=$condition.$queryString."='Y'";
                            break;
                        case 'Opt-In':
                            $condition=(strlen($rawQuery) > 0)? " AND ":" ";
                            $rawQuery.=$condition.$queryString."='N'";
                            break;
                        case 'Pending':
                            $condition=(strlen($rawQuery) > 0)? " AND ":" ";
                            $rawQuery.=$condition.$queryString."=''";
                            $queryString1="tt_folio_details_reports.nom_name_1";
                            $rawQuery.=" AND ".$queryString1."=''";
                            break;
                        default:
                            break;
                    }
                }
                if ($adhaar_pan_link_status) {
                    $queryString='tt_folio_details_reports.pa_link_ststus_1st';
                    switch ($adhaar_pan_link_status) {
                        case 'N':
                            $condition=(strlen($rawQuery) > 0)? " AND ":" ";
                            $rawQuery.=$condition.$queryString."='N'";
                            $rawQuery.=" OR ".$queryString."='Aadhaar Not Linked'";
                            break;
                        case 'Y':
                            $condition=(strlen($rawQuery) > 0)? " AND ":" ";
                            $rawQuery.=$condition.$queryString."='Y'";
                            $rawQuery.=" OR ".$queryString."='Aadhaar Linked'";
                            break;
                        case 'N/A':
                            $condition=(strlen($rawQuery) > 0)? " AND ":" ";
                            $rawQuery.=$condition.$queryString."='Blank'";
                            $rawQuery.=" OR ".$queryString."='Not Applicable' OR ".$queryString."='BLANK'";
                            break;
                        default:
                            break;
                    }
                }
            }
            // return $rawQuery;
            $data=[];
            // DB::enableQueryLog();
            // FolioDetailsReport
            $my_data=FolioDetailsReport::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','tt_folio_details_reports.product_code')
                ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                ->leftJoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
                ->leftJoin('md_option','md_option.id','=','md_scheme_isin.option_id')
                ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
                ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                ->leftJoin('md_amc','md_amc.amc_code','=','tt_folio_details_reports.amc_code')
                ->leftJoin('md_employee','md_employee.euin_no','=','tt_folio_details_reports.euin_no')
                ->leftJoin('md_branch','md_branch.id','=','md_employee.branch_id')
                ->leftJoin('md_pincode','md_pincode.pincode','=','tt_folio_details_reports.pincode')
                ->leftJoin('md_states','md_states.id','=','md_pincode.state_id')
                ->leftJoin('md_city_type','md_city_type.id','=','md_pincode.city_type_id')
                ->leftJoin('md_deposit_bank','md_deposit_bank.ifs_code','=','tt_folio_details_reports.bank_ifsc')
                ->select('tt_folio_details_reports.*','md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name',
                'md_amc.amc_short_name as amc_short_name','md_states.name as state','md_city_type.name as city_type',
                'md_employee.emp_name as rm_name','md_branch.brn_name as branch_name','md_employee.bu_type_id as bu_type_id','md_employee.branch_id as branch_id',
                'md_plan.plan_name','md_option.opt_name as option_name','md_deposit_bank.micr_code as bank_micr')
                ->selectRaw('(select `bu_type` from `md_business_type` where `bu_code` =md_employee.bu_type_id and `branch_id` =md_employee.branch_id limit 1) as bu_type')
                // ->selectRaw('IF(td_folio_details.rupee_bal IS NULL ||td_folio_details.rupee_bal="" || td_folio_details.rupee_bal="0","Inactive","Active") as folio_status')
                // ->selectRaw('IF(DATE_FORMAT(td_folio_details.folio_date,"Y-m-d") IS NULL || DATE_FORMAT(td_folio_details.folio_date,"Y-m-d")="",(select trans_date from td_mutual_fund_trans where folio_no=td_folio_details.folio_no and product_code= td_folio_details.product_code order by trans_date asc limit 1),td_folio_details.folio_date) as folio_date')
                // ->selectRaw('IF(td_folio_details.rnt_id=2,(select status from md_folio_tax_status where status_code=td_folio_details.tax_status limit 1),td_folio_details.tax_status) as tax_status')
                // ->selectRaw('IF(td_folio_details.rnt_id=2,
                //     IF(td_folio_details.tax_status_2_holder IS NULL || td_folio_details.tax_status_2_holder="",
                //         IF(td_folio_details.pan_2_holder IS NULL || td_folio_details.pan_2_holder="","",(select status from md_folio_tax_status where status_code=(select tax_status from td_folio_details where pan=td_folio_details.pan_2_holder limit 1) limit 1)),
                //         (select status from md_folio_tax_status where status_code=td_folio_details.tax_status_2_holder limit 1)),
                //     IF(td_folio_details.tax_status_2_holder IS NULL || td_folio_details.tax_status_2_holder="",
                //         IF(td_folio_details.pan_2_holder IS NULL || td_folio_details.pan_2_holder="","",(select tax_status from td_folio_details where pan=td_folio_details.pan_2_holder limit 1)),
                //         td_folio_details.tax_status_2_holder))as tax_status_2_holder')
                // ->selectRaw('IF(td_folio_details.rnt_id=2,
                //     IF(td_folio_details.tax_status_3_holder IS NULL || td_folio_details.tax_status_3_holder="",
                //         IF(td_folio_details.pan_3_holder IS NULL || td_folio_details.pan_3_holder="","",(select status from md_folio_tax_status where status_code=(select tax_status from td_folio_details where pan=td_folio_details.pan_3_holder limit 1) limit 1)),
                //         (select status from md_folio_tax_status where status_code=td_folio_details.tax_status_3_holder limit 1)),
                //     IF(td_folio_details.tax_status_3_holder IS NULL || td_folio_details.tax_status_3_holder="",
                //         IF(td_folio_details.pan_3_holder IS NULL || td_folio_details.pan_3_holder="","",(select tax_status from td_folio_details where pan=td_folio_details.pan_3_holder limit 1)),
                //         td_folio_details.tax_status_3_holder))as tax_status_3_holder')
                // ->selectRaw('IF(td_folio_details.rnt_id=2,
                //     IF(td_folio_details.guardian_tax_status IS NULL || td_folio_details.guardian_tax_status="",
                //         IF(td_folio_details.guardian_pan IS NULL || td_folio_details.guardian_pan="","",(select status from md_folio_tax_status where status_code=(select tax_status from td_folio_details where pan=td_folio_details.guardian_pan limit 1) limit 1)),
                //         (select status from md_folio_tax_status where status_code=td_folio_details.guardian_tax_status limit 1)),
                //     IF(td_folio_details.guardian_tax_status IS NULL || td_folio_details.guardian_tax_status="",
                //         IF(td_folio_details.guardian_pan IS NULL || td_folio_details.guardian_pan="","",(select tax_status from td_folio_details where pan=td_folio_details.guardian_pan limit 1)),
                //         td_folio_details.guardian_tax_status))as guardian_tax_status')
                // ->selectRaw('(select SUM(amount) from td_mutual_fund_trans where folio_no=td_folio_details.folio_no and product_code=td_folio_details.product_code) as folio_balance')
                // ->selectRaw('IF((select SUM(amount) from td_mutual_fund_trans where folio_no=td_folio_details.folio_no and product_code=td_folio_details.product_code) > 0,"Active","Inactive") as folio_status')

                // ->selectRaw('IF(tt_folio_details_reports.rnt_id=2,
                // (CASE 
                //     WHEN tt_folio_details_reports.pa_link_ststus_1st="Y" THEN "Aadhaar Linked"
                //     WHEN tt_folio_details_reports.pa_link_ststus_1st="N" THEN "Aadhaar Not Linked"
                //     WHEN tt_folio_details_reports.pa_link_ststus_1st="Blank" || pa_link_ststus_1st="BLANK" THEN "Not Applicable"
                //     ELSE ""
                // END),
                // tt_folio_details_reports.pa_link_ststus_1st) as pa_link_ststus_1st')
                // ->selectRaw('IF(tt_folio_details_reports.rnt_id=2,
                // (CASE 
                //     WHEN tt_folio_details_reports.pa_link_ststus_2nd="Y" THEN "Aadhaar Linked"
                //     WHEN tt_folio_details_reports.pa_link_ststus_2nd="N" THEN "Aadhaar Not Linked"
                //     WHEN tt_folio_details_reports.pa_link_ststus_2nd="Blank" || pa_link_ststus_2nd="BLANK" THEN "Not Applicable"
                //     ELSE ""
                // END),
                // tt_folio_details_reports.pa_link_ststus_2nd) as pa_link_ststus_2nd')
                // ->selectRaw('IF(tt_folio_details_reports.rnt_id=2,
                // (CASE 
                //     WHEN tt_folio_details_reports.pa_link_ststus_3rd="Y" THEN "Aadhaar Linked"
                //     WHEN tt_folio_details_reports.pa_link_ststus_3rd="N" THEN "Aadhaar Not Linked"
                //     WHEN tt_folio_details_reports.pa_link_ststus_3rd="Blank" || pa_link_ststus_3rd="BLANK" THEN "Not Applicable"
                //     ELSE ""
                // END),
                // tt_folio_details_reports.pa_link_ststus_3rd) as pa_link_ststus_3rd')
                // ->selectRaw('IF(tt_folio_details_reports.rnt_id=2,
                // (CASE 
                //     WHEN tt_folio_details_reports.guardian_pa_link_ststus="Y" THEN "Aadhaar Linked"
                //     WHEN tt_folio_details_reports.guardian_pa_link_ststus="N" THEN "Aadhaar Not Linked"
                //     WHEN tt_folio_details_reports.guardian_pa_link_ststus="Blank" || guardian_pa_link_ststus="BLANK" THEN "Not Applicable"
                //     ELSE ""
                // END),
                // tt_folio_details_reports.guardian_pa_link_ststus) as guardian_pa_link_ststus')
                // ->selectRaw('IF(tt_folio_details_reports.rnt_id=2,
                // (CASE 
                //     WHEN tt_folio_details_reports.kyc_status_1st="Y" THEN "KYC OK"
                //     WHEN tt_folio_details_reports.kyc_status_1st="H" THEN "KYC HOLD"
                //     WHEN tt_folio_details_reports.kyc_status_1st="R" THEN "KYC REJECTED"
                //     WHEN tt_folio_details_reports.kyc_status_1st="M" THEN "KYC Registered-Modified KYC"
                //     WHEN tt_folio_details_reports.kyc_status_1st="Blank" || tt_folio_details_reports.kyc_status_1st="BLANK" || tt_folio_details_reports.kyc_status_1st=" " 
                //     THEN "Not Applicable"
                //     ELSE ""
                // END),
                // tt_folio_details_reports.kyc_status_1st) as kyc_status_1st')
                // ->selectRaw('IF(tt_folio_details_reports.rnt_id=2,
                // (CASE 
                //     WHEN tt_folio_details_reports.kyc_status_2nd="Y" THEN "KYC OK"
                //     WHEN tt_folio_details_reports.kyc_status_2nd="H" THEN "KYC HOLD"
                //     WHEN tt_folio_details_reports.kyc_status_2nd="R" THEN "KYC REJECTED"
                //     WHEN tt_folio_details_reports.kyc_status_2nd="M" THEN "KYC Registered-Modified KYC"
                //     WHEN tt_folio_details_reports.kyc_status_2nd="Blank" || tt_folio_details_reports.kyc_status_2nd="BLANK" || tt_folio_details_reports.kyc_status_2nd=" " 
                //     THEN "Not Applicable"
                //     ELSE ""
                // END),
                // tt_folio_details_reports.kyc_status_2nd) as kyc_status_2nd')
                // ->selectRaw('IF(tt_folio_details_reports.rnt_id=2,
                // (CASE 
                //     WHEN tt_folio_details_reports.kyc_status_3rd="Y" THEN "KYC OK"
                //     WHEN tt_folio_details_reports.kyc_status_3rd="H" THEN "KYC HOLD"
                //     WHEN tt_folio_details_reports.kyc_status_3rd="R" THEN "KYC REJECTED"
                //     WHEN tt_folio_details_reports.kyc_status_3rd="M" THEN "KYC Registered-Modified KYC"
                //     WHEN tt_folio_details_reports.kyc_status_3rd="Blank" || tt_folio_details_reports.kyc_status_3rd="BLANK" || tt_folio_details_reports.kyc_status_3rd=" " 
                //     THEN "Not Applicable"
                //     ELSE ""
                // END),
                // tt_folio_details_reports.kyc_status_3rd) as kyc_status_3rd')
                // ->selectRaw('IF(tt_folio_details_reports.rnt_id=2,
                // (CASE 
                //     WHEN tt_folio_details_reports.guardian_kyc_status="Y" THEN "KYC OK"
                //     WHEN tt_folio_details_reports.guardian_kyc_status="H" THEN "KYC HOLD"
                //     WHEN tt_folio_details_reports.guardian_kyc_status="R" THEN "KYC REJECTED"
                //     WHEN tt_folio_details_reports.guardian_kyc_status="M" THEN "KYC Registered-Modified KYC"
                //     WHEN tt_folio_details_reports.guardian_kyc_status="Blank" || tt_folio_details_reports.guardian_kyc_status="BLANK" || tt_folio_details_reports.guardian_kyc_status=" " 
                //     THEN "Not Applicable"
                //     ELSE ""
                // END),
                // tt_folio_details_reports.guardian_kyc_status) as guardian_kyc_status')

                ->selectRaw('IF(tt_folio_details_reports.ckyc_no_1st IS NULL || tt_folio_details_reports.ckyc_no_1st="" || tt_folio_details_reports.ckyc_no_1st="" || tt_folio_details_reports.ckyc_no_1st="0",
                IF(tt_folio_details_reports.pan!="",(SELECT ckyc_no_1st FROM td_folio_details WHERE pan=tt_folio_details_reports.pan AND ckyc_no_1st!="" limit 1),""),
                tt_folio_details_reports.ckyc_no_1st) as ckyc_no_1st')
                ->selectRaw('IF(tt_folio_details_reports.ckyc_no_2nd IS NULL || tt_folio_details_reports.ckyc_no_2nd="" || tt_folio_details_reports.ckyc_no_2nd="" || tt_folio_details_reports.ckyc_no_2nd="0",
                IF(tt_folio_details_reports.pan_2_holder!="",(SELECT ckyc_no_1st FROM td_folio_details WHERE pan=tt_folio_details_reports.pan_2_holder and ckyc_no_1st!="" limit 1),""),
                tt_folio_details_reports.ckyc_no_2nd) as ckyc_no_2nd')
                ->selectRaw('IF(tt_folio_details_reports.ckyc_no_3rd IS NULL || tt_folio_details_reports.ckyc_no_3rd="" || tt_folio_details_reports.ckyc_no_3rd="" || tt_folio_details_reports.ckyc_no_3rd="0",
                IF(tt_folio_details_reports.pan_3_holder!="",(SELECT ckyc_no_1st FROM td_folio_details WHERE pan=tt_folio_details_reports.pan_3_holder and ckyc_no_1st!="" limit 1),""),
                tt_folio_details_reports.ckyc_no_3rd) as ckyc_no_3rd')
                ->selectRaw('IF(tt_folio_details_reports.guardian_ckyc_no IS NULL || tt_folio_details_reports.guardian_ckyc_no="" || tt_folio_details_reports.guardian_ckyc_no="" || tt_folio_details_reports.guardian_ckyc_no="0",
                IF(tt_folio_details_reports.guardian_pan!="",(SELECT ckyc_no_1st FROM td_folio_details WHERE pan=tt_folio_details_reports.guardian_pan and ckyc_no_1st!="" limit 1),""),
                tt_folio_details_reports.guardian_ckyc_no) as guardian_ckyc_no')

                ->selectRaw('IF(DATE_FORMAT(tt_folio_details_reports.dob_2nd_holder,"Y-m-d") IS NULL || DATE_FORMAT(tt_folio_details_reports.dob_2nd_holder,"Y-m-d")="" || DATE_FORMAT(tt_folio_details_reports.dob_2nd_holder,"Y-m-d")="",
                IF(tt_folio_details_reports.pan_2_holder!="",(SELECT dob FROM td_folio_details WHERE pan=tt_folio_details_reports.pan_2_holder and DATE_FORMAT(dob,"Y-m-d")!="" limit 1),""),
                tt_folio_details_reports.dob_2nd_holder) as dob_2nd_holder')
                ->selectRaw('IF(DATE_FORMAT(tt_folio_details_reports.dob_3rd_holder,"Y-m-d") IS NULL || DATE_FORMAT(tt_folio_details_reports.dob_3rd_holder,"Y-m-d")="" || DATE_FORMAT(tt_folio_details_reports.dob_3rd_holder,"Y-m-d")="",
                IF(tt_folio_details_reports.pan_3_holder!="",(SELECT dob FROM td_folio_details WHERE pan=tt_folio_details_reports.pan_3_holder and DATE_FORMAT(dob,"Y-m-d")!="" limit 1),""),
                tt_folio_details_reports.dob_3rd_holder) as dob_3rd_holder')
                ->selectRaw('IF(DATE_FORMAT(tt_folio_details_reports.guardian_dob,"Y-m-d") IS NULL || DATE_FORMAT(tt_folio_details_reports.guardian_dob,"Y-m-d")="" || DATE_FORMAT(tt_folio_details_reports.guardian_dob,"Y-m-d")="",
                IF(tt_folio_details_reports.guardian_pan!="",(SELECT dob FROM td_folio_details WHERE pan=tt_folio_details_reports.guardian_pan and DATE_FORMAT(dob,"Y-m-d")!="" limit 1),""),
                tt_folio_details_reports.guardian_dob) as guardian_dob')

                ->selectRaw('IF(tt_folio_details_reports.tax_status IS NULL || tt_folio_details_reports.tax_status="" || tt_folio_details_reports.tax_status="",
                IF(tt_folio_details_reports.pan!="",(SELECT tax_status FROM td_folio_details WHERE pan=tt_folio_details_reports.pan and tax_status!="" limit 1),""),
                tt_folio_details_reports.tax_status) as tax_status')
                ->selectRaw('IF(tt_folio_details_reports.tax_status_2_holder IS NULL || tt_folio_details_reports.tax_status_2_holder="" || tt_folio_details_reports.tax_status_2_holder="",
                IF(tt_folio_details_reports.pan_2_holder!="",(SELECT tax_status FROM td_folio_details WHERE pan=tt_folio_details_reports.pan_2_holder and tax_status!="" limit 1),""),
                tt_folio_details_reports.tax_status_2_holder) as tax_status_2_holder')
                ->selectRaw('IF(tt_folio_details_reports.tax_status_3_holder IS NULL || tt_folio_details_reports.tax_status_3_holder="" || tt_folio_details_reports.tax_status_3_holder="",
                IF(tt_folio_details_reports.pan_3_holder!="",(SELECT tax_status FROM td_folio_details WHERE pan=tt_folio_details_reports.pan_3_holder and tax_status!="" limit 1),""),
                tt_folio_details_reports.tax_status_3_holder) as tax_status_3_holder')
                ->selectRaw('IF(tt_folio_details_reports.guardian_tax_status IS NULL || tt_folio_details_reports.guardian_tax_status="" || tt_folio_details_reports.guardian_tax_status="",
                IF(tt_folio_details_reports.guardian_pan!="",(SELECT tax_status FROM td_folio_details WHERE pan=tt_folio_details_reports.guardian_pan and tax_status!="" limit 1),""),
                tt_folio_details_reports.guardian_tax_status) as guardian_tax_status')
                //Occupation Description 
                ->selectRaw('IF(tt_folio_details_reports.occupation_des IS NULL || tt_folio_details_reports.occupation_des="" || tt_folio_details_reports.occupation_des="" || tt_folio_details_reports.occupation_des="NOT APPLICABLE",
                IF(tt_folio_details_reports.pan!="",(SELECT occupation_des FROM td_folio_details WHERE pan=tt_folio_details_reports.pan and occupation_des!="" and occupation_des!="NOT APPLICABLE" limit 1),""),
                tt_folio_details_reports.occupation_des) as occupation_des')
                ->selectRaw('IF(tt_folio_details_reports.occupation_des_2nd IS NULL || tt_folio_details_reports.occupation_des_2nd="" || tt_folio_details_reports.occupation_des_2nd="" || tt_folio_details_reports.occupation_des_2nd="NOT APPLICABLE",
                IF(tt_folio_details_reports.pan_2_holder!="",(SELECT occupation_des FROM td_folio_details WHERE pan=tt_folio_details_reports.pan_2_holder and occupation_des!="" and occupation_des!="NOT APPLICABLE" limit 1),""),
                tt_folio_details_reports.occupation_des_2nd) as occupation_des_2nd')
                ->selectRaw('IF(tt_folio_details_reports.occupation_des_3rd IS NULL || tt_folio_details_reports.occupation_des_3rd="" || tt_folio_details_reports.occupation_des_3rd="" || tt_folio_details_reports.occupation_des_3rd="NOT APPLICABLE",
                IF(tt_folio_details_reports.pan_3_holder!="",(SELECT occupation_des FROM td_folio_details WHERE pan=tt_folio_details_reports.pan_3_holder and occupation_des!="" and occupation_des!="NOT APPLICABLE" limit 1),""),
                tt_folio_details_reports.occupation_des_3rd) as occupation_des_3rd')
                ->selectRaw('IF(tt_folio_details_reports.guardian_occu_des IS NULL || tt_folio_details_reports.guardian_occu_des="" || tt_folio_details_reports.guardian_occu_des="" || tt_folio_details_reports.guardian_occu_des="NOT APPLICABLE",
                IF(tt_folio_details_reports.guardian_pan!="",(SELECT occupation_des FROM td_folio_details WHERE pan=tt_folio_details_reports.guardian_pan and occupation_des!="" and occupation_des!="NOT APPLICABLE" limit 1),""),
                tt_folio_details_reports.guardian_occu_des) as guardian_occu_des')
                // kyc status
                ->selectRaw('IF(tt_folio_details_reports.kyc_status_1st IS NULL || tt_folio_details_reports.kyc_status_1st="" || tt_folio_details_reports.kyc_status_1st="",
                IF(tt_folio_details_reports.pan!="",(SELECT kyc_status_1st FROM td_folio_details WHERE pan=tt_folio_details_reports.pan and kyc_status_1st!="" limit 1),""),
                tt_folio_details_reports.kyc_status_1st) as kyc_status_1st')
                ->selectRaw('IF(tt_folio_details_reports.kyc_status_2nd IS NULL || tt_folio_details_reports.kyc_status_2nd="" || tt_folio_details_reports.kyc_status_2nd="",
                IF(tt_folio_details_reports.pan_2_holder!="",(SELECT kyc_status_1st FROM td_folio_details WHERE pan=tt_folio_details_reports.pan_2_holder and kyc_status_1st!="" limit 1),""),
                tt_folio_details_reports.kyc_status_2nd) as kyc_status_2nd')
                ->selectRaw('IF(tt_folio_details_reports.kyc_status_3rd IS NULL || tt_folio_details_reports.kyc_status_3rd="" || tt_folio_details_reports.kyc_status_3rd="",
                IF(tt_folio_details_reports.pan_3_holder!="",(SELECT kyc_status_1st FROM td_folio_details WHERE pan=tt_folio_details_reports.pan_3_holder and kyc_status_1st!="" limit 1),""),
                tt_folio_details_reports.kyc_status_3rd) as kyc_status_3rd')
                ->selectRaw('IF(tt_folio_details_reports.guardian_kyc_status IS NULL || tt_folio_details_reports.guardian_kyc_status="" || tt_folio_details_reports.guardian_kyc_status="",
                IF(tt_folio_details_reports.guardian_pan!="",(SELECT kyc_status_1st FROM td_folio_details WHERE pan=tt_folio_details_reports.guardian_pan and kyc_status_1st!="" limit 1),""),
                tt_folio_details_reports.guardian_kyc_status) as guardian_kyc_status')
                //PAN Aadhar link status
                ->selectRaw('IF(tt_folio_details_reports.pa_link_ststus_1st IS NULL || tt_folio_details_reports.pa_link_ststus_1st="" || tt_folio_details_reports.pa_link_ststus_1st=" ",
                IF(tt_folio_details_reports.pan!="",(SELECT pa_link_ststus_1st FROM td_folio_details WHERE pan=tt_folio_details_reports.pan and pa_link_ststus_1st!="" limit 1),""),
                tt_folio_details_reports.pa_link_ststus_1st) as pa_link_ststus_1st')
                ->selectRaw('IF(tt_folio_details_reports.pa_link_ststus_2nd IS NULL || tt_folio_details_reports.pa_link_ststus_2nd="" || tt_folio_details_reports.pa_link_ststus_2nd=" ",
                IF(tt_folio_details_reports.pan_2_holder!="",(SELECT pa_link_ststus_1st FROM td_folio_details WHERE pan=tt_folio_details_reports.pan_2_holder and pa_link_ststus_1st!="" limit 1),""),
                tt_folio_details_reports.pa_link_ststus_2nd) as pa_link_ststus_2nd')
                ->selectRaw('IF(tt_folio_details_reports.pa_link_ststus_3rd IS NULL || tt_folio_details_reports.pa_link_ststus_3rd="" || tt_folio_details_reports.pa_link_ststus_3rd=" ",
                IF(tt_folio_details_reports.pan_3_holder!="",(SELECT pa_link_ststus_1st FROM td_folio_details WHERE pan=tt_folio_details_reports.pan_3_holder and pa_link_ststus_1st!="" limit 1),""),
                tt_folio_details_reports.pa_link_ststus_3rd) as pa_link_ststus_3rd')
                ->selectRaw('IF(tt_folio_details_reports.guardian_pa_link_ststus IS NULL || tt_folio_details_reports.guardian_pa_link_ststus="" || tt_folio_details_reports.guardian_pa_link_ststus=" ",
                IF(tt_folio_details_reports.guardian_pan!="",(SELECT pa_link_ststus_1st FROM td_folio_details WHERE pan=tt_folio_details_reports.guardian_pan and pa_link_ststus_1st!="" limit 1),""),
                tt_folio_details_reports.guardian_pa_link_ststus) as guardian_pa_link_ststus')

                // ->selectRaw('IF(td_folio_details.bank_micr="" || td_folio_details.bank_micr IS NULL,(SELECT micr_code FROM md_deposit_bank WHERE ifs_code=td_folio_details.bank_ifsc limit 1),td_folio_details.bank_micr) as bank_micr')
                ->selectRaw('IF(tt_folio_details_reports.nom_optout_status="",IF(tt_folio_details_reports.nom_name_1!="","N",""),tt_folio_details_reports.nom_optout_status) as nom_optout_status')

                ->where('tt_folio_details_reports.amc_flag','N')
                ->where('tt_folio_details_reports.scheme_flag','N')
                ->where('tt_folio_details_reports.bu_type_flag','N')
                ->where('tt_folio_details_reports.plan_option_flag','N')
                ->whereRaw($rawQuery)
                // ->take(100)
                ->get();
            // dd(DB::getQueryLog());
            // return $data;
            foreach ($my_data as $key => $value) {
                if ($value->rnt_id==2) {
                    // pa_link_ststus_1st
                    if ($value->pa_link_ststus_1st=="Y") {
                        $value->pa_link_ststus_1st="Aadhaar Linked";
                    }elseif ($value->pa_link_ststus_1st=="N") {
                        $value->pa_link_ststus_1st="Aadhaar Not Linked";
                    }elseif ($value->pa_link_ststus_1st=="Blank" || $value->pa_link_ststus_1st=="BLANK") {
                        $value->pa_link_ststus_1st="Not Applicable";
                    }
                    // pa_link_ststus_2nd
                    if ($value->pa_link_ststus_2nd=="Y") {
                        $value->pa_link_ststus_2nd="Aadhaar Linked";
                    }elseif ($value->pa_link_ststus_2nd=="N") {
                        $value->pa_link_ststus_2nd="Aadhaar Not Linked";
                    }elseif ($value->pa_link_ststus_2nd=="Blank" || $value->pa_link_ststus_2nd=="BLANK") {
                        $value->pa_link_ststus_2nd="Not Applicable";
                    }
                    // pa_link_ststus_3rd
                    if ($value->pa_link_ststus_3rd=="Y") {
                        $value->pa_link_ststus_3rd="Aadhaar Linked";
                    }elseif ($value->pa_link_ststus_3rd=="N") {
                        $value->pa_link_ststus_3rd="Aadhaar Not Linked";
                    }elseif ($value->pa_link_ststus_3rd=="Blank" || $value->pa_link_ststus_3rd=="BLANK") {
                        $value->pa_link_ststus_3rd="Not Applicable";
                    }
                    // guardian_pa_link_ststus
                    if ($value->guardian_pa_link_ststus=="Y") {
                        $value->guardian_pa_link_ststus="Aadhaar Linked";
                    }elseif ($value->guardian_pa_link_ststus=="N") {
                        $value->guardian_pa_link_ststus="Aadhaar Not Linked";
                    }elseif ($value->guardian_pa_link_ststus=="Blank" || $value->guardian_pa_link_ststus=="BLANK") {
                        $value->guardian_pa_link_ststus="Not Applicable";
                    }
                    // kyc_status_1st
                    if ($value->kyc_status_1st=="Y" || $value->kyc_status_1st=="M") {
                        $value->kyc_status_1st="KYC OK";
                    }elseif ($value->kyc_status_1st=="H") {
                        $value->kyc_status_1st="KYC HOLD";
                    }elseif ($value->kyc_status_1st=="R") {
                        $value->kyc_status_1st="KYC REJECTED";
                    }elseif ($value->kyc_status_1st=="Blank" || $value->kyc_status_1st=="BLANK" || $value->kyc_status_1st==" ") {
                        $value->kyc_status_1st="Not Applicable";
                    }
                    // kyc_status_2nd
                    if ($value->kyc_status_2nd=="Y" || $value->kyc_status_2nd=="M") {
                        $value->kyc_status_2nd="KYC OK";
                    }elseif ($value->kyc_status_2nd=="H") {
                        $value->kyc_status_2nd="KYC HOLD";
                    }elseif ($value->kyc_status_2nd=="R") {
                        $value->kyc_status_2nd="KYC REJECTED";
                    }elseif ($value->kyc_status_2nd=="Blank" || $value->kyc_status_2nd=="BLANK" || $value->kyc_status_2nd==" ") {
                        $value->kyc_status_2nd="Not Applicable";
                    }
                    // kyc_status_3rd
                    if ($value->kyc_status_3rd=="Y" || $value->kyc_status_3rd=="M") {
                        $value->kyc_status_3rd="KYC OK";
                    }elseif ($value->kyc_status_3rd=="H") {
                        $value->kyc_status_3rd="KYC HOLD";
                    }elseif ($value->kyc_status_3rd=="R") {
                        $value->kyc_status_3rd="KYC REJECTED";
                    }elseif ($value->kyc_status_3rd=="Blank" || $value->kyc_status_3rd=="BLANK" || $value->kyc_status_3rd==" ") {
                        $value->kyc_status_3rd="Not Applicable";
                    }
                    // guardian_kyc_status
                    if ($value->guardian_kyc_status=="Y" || $value->guardian_kyc_status=="M") {
                        $value->guardian_kyc_status="KYC OK";
                    }elseif ($value->guardian_kyc_status=="H") {
                        $value->guardian_kyc_status="KYC HOLD";
                    }elseif ($value->guardian_kyc_status=="R") {
                        $value->guardian_kyc_status="KYC REJECTED";
                    }elseif ($value->guardian_kyc_status=="Blank" || $value->guardian_kyc_status=="BLANK" || $value->guardian_kyc_status==" ") {
                        $value->guardian_kyc_status="Not Applicable";
                    }
                    if ($value->mode_of_holding=="SINGLE") {
                        $value->mode_of_holding="SI";
                    }
                }
                if ($value->guardian_relation=='F') {
                    $value->guardian_relation='Father';
                }elseif ($value->guardian_relation=='M') {
                    $value->guardian_relation='Mother';
                }
                if ($value->nom_relation_1=='Not Provided' || strtolower($value->nom_relation_1)=='na' || $value->nom_relation_1=='Not Given') {
                    $value->nom_relation_1=NULL;
                }
                if($value->guardian_name==""){
                    $value->guardian_dob=NULL;
                }
                array_push($data,$value);
            }
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}
