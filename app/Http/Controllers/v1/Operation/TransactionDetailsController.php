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
    MFTransTypeSubType
};
use Validator;
use Illuminate\Support\Carbon;
use Excel;
use App\Helpers\TransHelper;
use DB;

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
                $all_data=MutualFundTransaction::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_mutual_fund_trans.product_code')
                    ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                    ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
                    ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                    ->leftJoin('md_amc','md_amc.amc_code','=','td_mutual_fund_trans.amc_code')
                    ->select('td_mutual_fund_trans.*','md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name','md_amc.amc_short_name as amc_name')
                    ->where('td_mutual_fund_trans.folio_no',$request->folio_no)
                    ->get();
            } else {
                $all_data=MutualFundTransaction::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_mutual_fund_trans.product_code')
                    ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                    ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
                    ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                    ->leftJoin('md_amc','md_amc.amc_code','=','td_mutual_fund_trans.amc_code')
                    ->select('td_mutual_fund_trans.*','md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name','md_amc.amc_short_name as amc_name')
                    ->orderBy('td_mutual_fund_trans.created_at','desc')
                    ->inRandomOrder()
                    ->take(50)
                    ->get();
            }
                $data=[];
                foreach ($all_data as $key => $value) {
                    $trxn_type=$value->trxn_type;
                    $trxn_type_flag=$value->trxn_type_flag;
                    $trxn_nature=$value->trxn_nature;
                    $amount=$value->amount;
                    $transaction_type='';
                    $transaction_subtype='';
                    if ($trxn_type && $trxn_type_flag && $trxn_nature) {  //for cams
                        $trxn_code=TransHelper::transTypeToCodeCAMS($trxn_type);
                        $trxn_nature_code=TransHelper::trxnNatureCodeCAMS($trxn_nature);
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
                        }else {
                            $get_type_subtype=MFTransTypeSubType::where('c_k_trans_sub_type',$kf_trans_type)
                                ->first();
                        }
                        
                        if ($get_type_subtype) {
                            $transaction_type=$get_type_subtype->trans_type;
                            $transaction_subtype=$get_type_subtype->trans_sub_type;
                        }
                    }
                    $value->transaction_type=$transaction_type;
                    $value->transaction_subtype=$transaction_subtype;
                    array_push($data,$value);
                }
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function search_old(Request $request)
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
