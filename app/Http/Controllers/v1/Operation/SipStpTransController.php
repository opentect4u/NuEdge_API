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
            // $report_type=$request->report_type;
            $data=[];

            $data=SipStpTransaction::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_sip_stp_trans.product_code')
                ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
                ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                ->leftJoin('md_amc','md_amc.id','=','md_scheme.amc_id')
                ->leftJoin('md_amc as md_amc_1','md_amc_1.amc_code','=','td_sip_stp_trans.amc_code')
                ->select('td_sip_stp_trans.*','md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name',
                'md_amc.amc_short_name as amc_name','md_amc_1.amc_short_name as amc_short_name')
                ->where('td_sip_stp_trans.amc_flag','N')
                ->where('td_sip_stp_trans.scheme_flag','N')
                // ->where('md_scheme_isin.plan_type',$plan_type)
                // ->whereRaw($rawQuery)
                // ->orderBy('td_sip_stp_trans.nav_date','desc')
                ->take(100)
                ->get();
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}
