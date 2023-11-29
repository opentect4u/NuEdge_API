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
    TempFolioDetails
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
            $nominee_status=$request->nominee_status;
            $adhaar_pan_link_status=$request->adhaar_pan_link_status;

            $rawQuery='';
            if ($folio_status || $pan_no || $folio_no || $kyc_status || $nominee_status || $adhaar_pan_link_status) {
                $queryString='td_folio_details.pan';
                $rawQuery.=Helper::WhereRawQuery($pan_no,$rawQuery,$queryString);
                $queryString='td_folio_details.folio_no';
                $rawQuery.=Helper::WhereRawQuery($folio_no,$rawQuery,$queryString);
                if ($folio_status) {
                    $condition=(strlen($rawQuery) > 0)? " AND ":" ";
                    // $rawQuery.=$condition.'(IF(td_folio_details.rupee_bal IS NULL || td_folio_details.rupee_bal="" || td_folio_details.rupee_bal="0","Inactive","Active"))="'.$folio_status.'"';
                    $rawQuery.=$condition.'(IF((select SUM(amount) from td_mutual_fund_trans where folio_no=td_folio_details.folio_no and product_code=td_folio_details.product_code) > 0,"Active","Inactive"))="'.$folio_status.'"';
                }
                // if ($kyc_status) {
                //     $rawQuery.='';
                // }
                // if ($nominee_status) {
                //     $rawQuery.='';
                // }
                // if ($adhaar_pan_link_status) {
                //     $rawQuery.='';
                // }
            }

            $data=[];
            // DB::enableQueryLog();
            $data=FolioDetails::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_folio_details.product_code')
                ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                ->leftJoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
                ->leftJoin('md_option','md_option.id','=','md_scheme_isin.option_id')
                ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
                ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                ->leftJoin('md_amc','md_amc.amc_code','=','td_folio_details.amc_code')
                ->leftJoin('md_employee','md_employee.euin_no','=',DB::raw('(select euin_no from td_mutual_fund_trans where folio_no=td_folio_details.folio_no and product_code= td_folio_details.product_code order by trans_date asc limit 1)'))
                ->leftJoin('md_branch','md_branch.id','=','md_employee.branch_id')
                ->leftJoin('md_pincode','md_pincode.id','=','td_folio_details.pincode')
                ->leftJoin('md_states','md_states.id','=','md_pincode.state_id')
                ->leftJoin('md_city_type','md_city_type.id','=','md_pincode.city_type_id')
                ->select('td_folio_details.*','md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name',
                'md_amc.amc_short_name as amc_short_name','md_states.name as state','md_city_type.name as city_type',
                'md_employee.emp_name as rm_name','md_branch.brn_name as branch_name','md_employee.bu_type_id as bu_type_id','md_employee.branch_id as branch_id','md_employee.euin_no as euin_no',
                'md_plan.plan_name','md_option.opt_name as option_name')
                ->selectRaw('(select `bu_type` from `md_business_type` where `bu_code` =md_employee.bu_type_id and `branch_id` =md_employee.branch_id limit 1) as bu_type')
                // ->selectRaw('IF(td_folio_details.rupee_bal IS NULL ||td_folio_details.rupee_bal="" || td_folio_details.rupee_bal="0","Inactive","Active") as folio_status')
                ->selectRaw('IF(td_folio_details.folio_date IS NULL || td_folio_details.folio_date="",(select trans_date from td_mutual_fund_trans where folio_no=td_folio_details.folio_no and product_code= td_folio_details.product_code order by trans_date asc limit 1),td_folio_details.folio_date) as folio_date')

                ->selectRaw('IF(td_folio_details.rnt_id=2,(select status from md_folio_tax_status where status_code=td_folio_details.tax_status limit 1),td_folio_details.tax_status) as tax_status')

                ->selectRaw('IF(td_folio_details.rnt_id=2,
                    IF(td_folio_details.tax_status_2_holder IS NULL || td_folio_details.tax_status_2_holder="",
                        IF(td_folio_details.pan_2_holder IS NULL || td_folio_details.pan_2_holder="","",(select status from md_folio_tax_status where status_code=(select tax_status from td_folio_details where pan=td_folio_details.pan_2_holder limit 1) limit 1)),
                        (select status from md_folio_tax_status where status_code=td_folio_details.tax_status_2_holder limit 1)),
                    IF(td_folio_details.tax_status_2_holder IS NULL || td_folio_details.tax_status_2_holder="",
                        IF(td_folio_details.pan_2_holder IS NULL || td_folio_details.pan_2_holder="","",(select tax_status from td_folio_details where pan=td_folio_details.pan_2_holder limit 1)),
                        td_folio_details.tax_status_2_holder))as tax_status_2_holder')
                ->selectRaw('IF(td_folio_details.rnt_id=2,
                    IF(td_folio_details.tax_status_3_holder IS NULL || td_folio_details.tax_status_3_holder="",
                        IF(td_folio_details.pan_3_holder IS NULL || td_folio_details.pan_3_holder="","",(select status from md_folio_tax_status where status_code=(select tax_status from td_folio_details where pan=td_folio_details.pan_3_holder limit 1) limit 1)),
                        (select status from md_folio_tax_status where status_code=td_folio_details.tax_status_3_holder limit 1)),
                    IF(td_folio_details.tax_status_3_holder IS NULL || td_folio_details.tax_status_3_holder="",
                        IF(td_folio_details.pan_3_holder IS NULL || td_folio_details.pan_3_holder="","",(select tax_status from td_folio_details where pan=td_folio_details.pan_3_holder limit 1)),
                        td_folio_details.tax_status_3_holder))as tax_status_3_holder')
                ->selectRaw('IF(td_folio_details.rnt_id=2,
                    IF(td_folio_details.guardian_tax_status IS NULL || td_folio_details.guardian_tax_status="",
                        IF(td_folio_details.guardian_pan IS NULL || td_folio_details.guardian_pan="","",(select status from md_folio_tax_status where status_code=(select tax_status from td_folio_details where pan=td_folio_details.guardian_pan limit 1) limit 1)),
                        (select status from md_folio_tax_status where status_code=td_folio_details.guardian_tax_status limit 1)),
                    IF(td_folio_details.guardian_tax_status IS NULL || td_folio_details.guardian_tax_status="",
                        IF(td_folio_details.guardian_pan IS NULL || td_folio_details.guardian_pan="","",(select tax_status from td_folio_details where pan=td_folio_details.guardian_pan limit 1)),
                        td_folio_details.guardian_tax_status))as guardian_tax_status')
                
                ->selectRaw('(select SUM(amount) from td_mutual_fund_trans where folio_no=td_folio_details.folio_no and product_code=td_folio_details.product_code) as folio_balance')
                ->selectRaw('IF((select SUM(amount) from td_mutual_fund_trans where folio_no=td_folio_details.folio_no and product_code=td_folio_details.product_code) > 0,"Active","Inactive") as folio_status')

                ->selectRaw('IF(td_folio_details.rnt_id=2,
                (CASE 
                    WHEN td_folio_details.pa_link_ststus_1st="Y" THEN "Aadhaar Linked"
                    WHEN td_folio_details.pa_link_ststus_1st="N" THEN "Aadhaar Not Linked"
                    WHEN td_folio_details.pa_link_ststus_1st="Blank" || pa_link_ststus_1st="BLANK" THEN "Not Applicable"
                    ELSE ""
                END),
                td_folio_details.pa_link_ststus_1st) as pa_link_ststus_1st')
                ->selectRaw('IF(td_folio_details.rnt_id=2,
                (CASE 
                    WHEN td_folio_details.pa_link_ststus_2nd="Y" THEN "Aadhaar Linked"
                    WHEN td_folio_details.pa_link_ststus_2nd="N" THEN "Aadhaar Not Linked"
                    WHEN td_folio_details.pa_link_ststus_2nd="Blank" || pa_link_ststus_2nd="BLANK" THEN "Not Applicable"
                    ELSE ""
                END),
                td_folio_details.pa_link_ststus_2nd) as pa_link_ststus_2nd')
                ->selectRaw('IF(td_folio_details.rnt_id=2,
                (CASE 
                    WHEN td_folio_details.pa_link_ststus_3rd="Y" THEN "Aadhaar Linked"
                    WHEN td_folio_details.pa_link_ststus_3rd="N" THEN "Aadhaar Not Linked"
                    WHEN td_folio_details.pa_link_ststus_3rd="Blank" || pa_link_ststus_3rd="BLANK" THEN "Not Applicable"
                    ELSE ""
                END),
                td_folio_details.pa_link_ststus_3rd) as pa_link_ststus_3rd')
                ->selectRaw('IF(td_folio_details.rnt_id=2,
                (CASE 
                    WHEN td_folio_details.guardian_pa_link_ststus="Y" THEN "Aadhaar Linked"
                    WHEN td_folio_details.guardian_pa_link_ststus="N" THEN "Aadhaar Not Linked"
                    WHEN td_folio_details.guardian_pa_link_ststus="Blank" || guardian_pa_link_ststus="BLANK" THEN "Not Applicable"
                    ELSE ""
                END),
                td_folio_details.guardian_pa_link_ststus) as guardian_pa_link_ststus')
                ->selectRaw('IF(td_folio_details.bank_micr="" || td_folio_details.bank_micr IS NULL,(SELECT micr_code FROM md_deposit_bank WHERE ifs_code=td_folio_details.bank_ifsc limit 1),td_folio_details.bank_micr) as bank_micr')
                ->where('td_folio_details.amc_flag','N')
                ->where('td_folio_details.scheme_flag','N')
                ->whereRaw($rawQuery)
                // ->take(100)
                ->get();
            // dd(DB::getQueryLog());
            
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}
