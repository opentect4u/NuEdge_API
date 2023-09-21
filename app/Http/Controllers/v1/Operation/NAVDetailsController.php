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
    NAVDetails
};
use Validator;
use Illuminate\Support\Carbon;
use Excel;
use App\Helpers\TransHelper;
use DB;

class NAVDetailsController extends Controller
{
    public function search(Request $request)
    {
        try {
            $amc_id=json_decode($request->amc_id);
            $cat_id=json_decode($request->cat_id);
            $subcat_id=json_decode($request->subcat_id);
            $scheme_id=json_decode($request->scheme_id);
            $date_periods=$request->date_periods;
            $date_range=$request->date_range;
            $plan_type=$request->plan_type;

            $rawQuery='';
            $queryString='td_nav_details.nav_date';
            if ($date_periods=='D') {
                $from_date=Carbon::parse(str_replace('/','-',explode("-",$date_range)[0]))->format('Y-m-d') ;
                $to_date=Carbon::parse(str_replace('/','-',explode("-",$date_range)[1]))->format('Y-m-d') ;  
                $rawQuery.=Helper::FrmToDateRawQuery($from_date,$to_date,$rawQuery,$queryString);
            }elseif ($date_periods=='M') {
                $f_date="01-".str_replace('/','-',explode("-",$date_range)[0]);
                $t_date=date('d')."-".str_replace('/','-',explode("-",$date_range)[1]);
                $from_date=Carbon::parse(str_replace(' ','',$f_date))->format('Y-m-d');
                $to_date=Carbon::parse(str_replace(' ','',$t_date))->format('Y-m-d');
                // return  $t_date;
                $rawQuery.=Helper::FrmToDateRawQuery($from_date,$to_date,$rawQuery,$queryString);
                $row_name_string=  "'" .implode("','", $benchmark). "'";
            }elseif ($date_periods=='Y') {
                $f_date="01-01-".str_replace('/','-',explode("-",$date_range)[0]);
                $t_date=date('d-m')."-".str_replace('/','-',explode("-",$date_range)[1]);
                $from_date=Carbon::parse(str_replace(' ','',$f_date))->format('Y-m-d');
                $to_date=Carbon::parse(str_replace(' ','',$t_date))->format('Y-m-d');
                // return  $t_date;
                $rawQuery.=Helper::FrmToDateRawQuery($from_date,$to_date,$rawQuery,$queryString);
                $row_name_string=  "'" .implode("','", $benchmark). "'";
            }

            if ($date_periods) {
                $data=NAVDetails::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_nav_details.product_code')
                    ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                    ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
                    ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                    ->leftJoin('md_amc','md_amc.id','=','md_scheme.amc_id')
                    ->leftJoin('md_amc as md_amc_1','md_amc_1.amc_code','=','td_nav_details.amc_code')
                    ->select('td_nav_details.*','md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name',
                    'md_amc.amc_short_name as amc_name','md_amc_1.amc_short_name as amc_short_name')
                    ->where('td_nav_details.amc_flag','N')
                    ->where('td_nav_details.scheme_flag','N')
                    ->where('md_scheme_isin.plan_id',$plan_type)
                    ->whereRaw($rawQuery)
                    ->orderBy('td_nav_details.nav_date','desc')
                    // ->take(100)
                    ->get();
            }else {
                $data=NAVDetails::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_nav_details.product_code')
                    ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                    ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
                    ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                    ->leftJoin('md_amc','md_amc.id','=','md_scheme.amc_id')
                    ->leftJoin('md_amc as md_amc_1','md_amc_1.amc_code','=','td_nav_details.amc_code')
                    ->select('td_nav_details.*','md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name',
                    'md_amc.amc_short_name as amc_name','md_amc_1.amc_short_name as amc_short_name')
                    ->where('td_nav_details.amc_flag','N')
                    ->where('td_nav_details.scheme_flag','N')
                    ->where('md_scheme_isin.plan_id',$plan_type)
                    ->orderBy('td_nav_details.nav_date','desc')
                    // ->take(100)
                    ->get();
            }
            
        } catch (\Throwable $th) {
            // throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}
