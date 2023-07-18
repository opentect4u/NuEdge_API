<?php

namespace App\Http\Controllers\v1\Operation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{MutualFund,Client,FormReceived,MutualFundTransaction};
use Validator;
use Illuminate\Support\Carbon;
use Excel;

class TransactionDetailsController extends Controller
{
    public function upload(Request $request)
    {
        try {
            $datas = Excel::toArray([],  $request->file('file'));
            // return $datas;
            $data=$datas[0];
            // return $data;
            foreach ($data as $key => $value) {
                // return $data;
                if ($key > 0) {
                    // return $value;
                    // return str_replace("'","", $value[11]);
                    // return Carbon::parse(str_replace("'","",$value[11]))->format('Y-m-d H:i:s');
                    MutualFundTransaction::create(array(
                        'folio_no'=>str_replace("'","",$value[1]),
                        'trans_no'=>str_replace("'","",$value[6]),
                        'trans_mode'=>str_replace("'","",$value[7]),
                        'trans_status'=>str_replace("'","",$value[8]),
                        'user_trans_no'=>str_replace("'","",$value[10]),
                        'trad_date'=>Carbon::parse(str_replace("'","",$value[11]))->format('Y-m-d H:i:s'),
                        'post_date'=>Carbon::parse(str_replace("'","",$value[12]))->format('Y-m-d H:i:s'),
                        'pur_price'=>str_replace("'","",$value[13]),
                        'units'=>str_replace("'","",$value[14]),
                        'amount'=>str_replace("'","",$value[15]),
                        'rec_date'=>Carbon::parse(str_replace("'","",$value[21]))->format('Y-m-d H:i:s'),
                        'trans_sub_type'=>str_replace("'","",$value[23]),
                        'trans_nature'=>str_replace("'","",$value[25]),
                        'te_15h'=>str_replace("'","",$value[28]),
                        'micr_code'=>str_replace("'","",$value[29]),
                        'remarks'=>str_replace("'","",$value[30]),
                        'sw_flag'=>str_replace("'","",$value[31]),
                        'old_folio'=>str_replace("'","",$value[32]),
                        'seq_no'=>str_replace("'","",$value[33]),
                        'reinvest_flag'=>str_replace("'","",$value[34]),
                        'stt'=>str_replace("'","",$value[36]),
                    ));
                }
            }
            // $data=[];
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function uploadwithprocess(Request $request)
    {
        try {
            $datas = Excel::toArray([],  $request->file('file'));
            // return $datas;
            $data=$datas[0];
            // return $data;
            foreach ($data as $key => $value) {
                // return $data;
                if ($key > 0) {
                    // return $value;
                    // return str_replace("'","", $value[11]);
                    // return Carbon::parse(str_replace("'","",$value[11]))->format('Y-m-d H:i:s');
                    MutualFundTransaction::create(array(
                        'folio_no'=>str_replace("'","",$value[1]),
                        'trans_no'=>str_replace("'","",$value[6]),
                        'trans_mode'=>str_replace("'","",$value[7]),
                        'trans_status'=>str_replace("'","",$value[8]),
                        'user_trans_no'=>str_replace("'","",$value[10]),
                        'trad_date'=>Carbon::parse(str_replace("'","",$value[11]))->format('Y-m-d H:i:s'),
                        'post_date'=>Carbon::parse(str_replace("'","",$value[12]))->format('Y-m-d H:i:s'),
                        'pur_price'=>str_replace("'","",$value[13]),
                        'units'=>str_replace("'","",$value[14]),
                        'amount'=>str_replace("'","",$value[15]),
                        'rec_date'=>Carbon::parse(str_replace("'","",$value[21]))->format('Y-m-d H:i:s'),
                        'trans_sub_type'=>str_replace("'","",$value[23]),
                        'trans_nature'=>str_replace("'","",$value[25]),
                        'te_15h'=>str_replace("'","",$value[28]),
                        'micr_code'=>str_replace("'","",$value[29]),
                        'remarks'=>str_replace("'","",$value[30]),
                        'sw_flag'=>str_replace("'","",$value[31]),
                        'old_folio'=>str_replace("'","",$value[32]),
                        'seq_no'=>str_replace("'","",$value[33]),
                        'reinvest_flag'=>str_replace("'","",$value[34]),
                        'stt'=>str_replace("'","",$value[36]),
                    ));
                }
            }
            // $data=[];
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function search(Request $request)
    {
        try {
            if ($request->folio_no) {
                $data=$data=MutualFundTransaction::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_mutual_fund_trans.product_code')
                    ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                    ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
                    ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                    ->leftJoin('md_amc','md_amc.amc_code','=','td_mutual_fund_trans.amc_code')
                    ->select('td_mutual_fund_trans.*','md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name','md_amc.amc_short_name as amc_name')
                    ->where('td_mutual_fund_trans.folio_no',$request->folio_no)
                    ->get();
            } else {
                $data=MutualFundTransaction::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_mutual_fund_trans.product_code')
                    ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                    ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
                    ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                    ->leftJoin('md_amc','md_amc.amc_code','=','td_mutual_fund_trans.amc_code')
                    ->select('td_mutual_fund_trans.*','md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name','md_amc.amc_short_name as amc_name')
                    ->take(10)
                    ->get();
            }
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

}
