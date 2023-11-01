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
                    $rawQuery.=$condition.'(IF(td_folio_details.rupee_bal IS NULL || td_folio_details.rupee_bal="" || td_folio_details.rupee_bal="0","Inactive","Active"))="'.$folio_status.'"';
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
            $data=FolioDetails::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_folio_details.product_code')
                ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
                ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                ->leftJoin('md_amc','md_amc.amc_code','=','td_folio_details.amc_code')
                ->leftJoin('md_employee','md_employee.euin_no','=',DB::raw('(select euin_no from td_mutual_fund_trans where folio_no=td_folio_details.folio_no and product_code= td_folio_details.product_code order by trans_date asc limit 1)'))
                ->leftJoin('md_branch','md_branch.id','=','md_employee.branch_id')
                ->leftJoin('md_pincode','md_pincode.id','=','td_folio_details.pincode')
                ->leftJoin('md_states','md_states.id','=','md_pincode.state_id')
                ->leftJoin('md_city_type','md_city_type.id','=','md_pincode.city_type_id')
                ->select('td_folio_details.*','md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name',
                'md_amc.amc_short_name as amc_short_name','md_states.name as state','md_city_type.name as city_type_name',
                'md_employee.emp_name as rm_name','md_branch.brn_name as branch_name','md_employee.bu_type_id as bu_type_id','md_employee.branch_id as branch_id','md_employee.euin_no as euin_no'
                )
                ->selectRaw('(select `bu_type` from `md_business_type` where `bu_code` =md_employee.bu_type_id and `branch_id` =md_employee.branch_id limit 1) as bu_type')
                ->selectRaw('IF(td_folio_details.rupee_bal IS NULL ||td_folio_details.rupee_bal="" || td_folio_details.rupee_bal="0","Inactive","Active") as folio_status')
                ->selectRaw('IF(td_folio_details.folio_date IS NULL || td_folio_details.rupee_bal="","
                (select trans_date from td_mutual_fund_trans where folio_no=td_folio_details.folio_no and product_code= td_folio_details.product_code order by trans_date asc limit 1)
                ",td_folio_details.folio_date) as folio_date')
                ->where('td_folio_details.amc_flag','N')
                ->where('td_folio_details.scheme_flag','N')
                ->whereRaw($rawQuery)
                // ->take(100)
                ->get();
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}
