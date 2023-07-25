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
            $date_range=$request->date_range;
            $folio_no=$request->folio_no;
            $client_id=$request->client_id;
            $pan_no=$request->pan_no;
            $amc_id=json_decode($request->amc_id);
            $cat_id=json_decode($request->cat_id);
            $sub_cat_id=json_decode($request->sub_cat_id);
            $scheme_id=json_decode($request->scheme_id);
            $trans_type_id=json_decode($request->trans_type_id);
            $trans_sub_type_id=json_decode($request->trans_sub_type_id);

            if ($date_range || $folio_no || $pan_no || !empty($amc_id) || !empty($cat_id) || !empty($sub_cat_id) || !empty($scheme_id)) {
                $rawQuery='';
                if ($date_range) {
                    $from_date=Carbon::parse(str_replace('/','-',explode("-",$date_range)[0]))->format('Y-m-d') ;
                    $to_date=Carbon::parse(str_replace('/','-',explode("-",$date_range)[1]))->format('Y-m-d') ;
                    // return $to_date;
                    $queryString='td_mutual_fund_trans.trans_date';
                    $rawQuery.=Helper::FrmToDateRawQuery($from_date,$to_date,$rawQuery,$queryString);
                }

                
                $queryString='td_mutual_fund_trans.folio_no';
                $rawQuery.=Helper::WhereRawQuery($folio_no,$rawQuery,$queryString);
                $queryString='td_mutual_fund_trans.first_client_pan';
                $rawQuery.=Helper::WhereRawQuery($pan_no,$rawQuery,$queryString);
                $queryString='md_scheme.amc_id';
                $rawQuery.=Helper::WhereRawQuery($amc_id,$rawQuery,$queryString);
                $queryString='md_scheme.category_id';
                $rawQuery.=Helper::WhereRawQuery($cat_id,$rawQuery,$queryString);
                $queryString='md_scheme.subcategory_id';
                $rawQuery.=Helper::WhereRawQuery($sub_cat_id,$rawQuery,$queryString);
                $queryString='md_scheme_isin.scheme_id';
                $rawQuery.=Helper::WhereRawQuery($scheme_id,$rawQuery,$queryString);
                // return $rawQuery;
                // $rawQuery=$this->filterCriteria($rawQuery,$from_date,$to_date,$tin_no,$proposer_name,$ins_type_id,$company_id,$product_type_id,$product_id,$insured_bu_type,$ack_status);

                $all_data=MutualFundTransaction::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_mutual_fund_trans.product_code')
                    ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                    ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
                    ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                    ->leftJoin('md_amc','md_amc.amc_code','=','td_mutual_fund_trans.amc_code')
                    ->select('td_mutual_fund_trans.*','md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name','md_amc.amc_short_name as amc_name')
                    // ->where('td_mutual_fund_trans.folio_no',$folio_no)
                    ->whereRaw($rawQuery)
                    ->get();
            }else {
                $all_data=MutualFundTransaction::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_mutual_fund_trans.product_code')
                    ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                    ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
                    ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                    ->leftJoin('md_amc','md_amc.amc_code','=','td_mutual_fund_trans.amc_code')
                    ->select('td_mutual_fund_trans.*','md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name','md_amc.amc_short_name as amc_name')
                    ->orderBy('td_mutual_fund_trans.created_at','desc')
                    ->inRandomOrder()
                    ->take(200)
                    ->get();
            }
            // return $all_data;
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
                        } else {
                            $get_type_subtype=MFTransTypeSubType::where('c_k_trans_sub_type',$kf_trans_type)
                                ->first();
                        }
                        
                        if ($get_type_subtype) {
                            $transaction_type=$get_type_subtype->trans_type;
                            $transaction_subtype=$get_type_subtype->trans_sub_type;
                        }
                    }
                    $value->gross_amount= ((float)$amount + (float)$value->stamp_duty + (float)$value->tds);
                    $value->transaction_type=$transaction_type;
                    $value->transaction_subtype=$transaction_subtype;

                    if (!empty($trans_type_id) && in_array($transaction_type ,$trans_type_id) && !empty($trans_sub_type_id) && in_array($transaction_subtype ,$trans_sub_type_id)) {
                        array_push($data,$value);
                    }else if (!empty($trans_type_id) && in_array($transaction_type ,$trans_type_id)) {
                        array_push($data,$value);
                    }else if (!empty($transaction_subtype) && in_array($transaction_subtype ,$trans_sub_type_id)) {
                        array_push($data,$value);
                    }else{
                        array_push($data,$value);
                    }
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


    public function filterCriteria($rawQuery,$from_date,$to_date,$tin_no,$proposer_name,$ins_type_id,$company_id,$product_type_id,$product_id,$insured_bu_type,$ack_status)
    {
        $queryString='td_insurance.entry_date';
        $rawQuery.=Helper::FrmToDateRawQuery($from_date,$to_date,$rawQuery,$queryString);
        $queryString1='td_insurance.tin_no';
        $rawQuery.=Helper::WhereRawQuery($tin_no,$rawQuery,$queryString1);
        $queryString2='td_insurance.proposer_id';
        $rawQuery.=Helper::WhereRawQuery($proposer_name,$rawQuery,$queryString2);
        $queryString3='md_ins_products.ins_type_id';
        $rawQuery.=Helper::WhereRawQuery($ins_type_id,$rawQuery,$queryString3);
        $queryString4='td_insurance.company_id';
        $rawQuery.=Helper::WhereRawQuery($company_id,$rawQuery,$queryString4);
        $queryString5='td_insurance.product_type_id';
        $rawQuery.=Helper::WhereRawQuery($product_type_id,$rawQuery,$queryString5);
        $queryString6='td_insurance.product_id';
        $rawQuery.=Helper::WhereRawQuery($product_id,$rawQuery,$queryString6);
        $queryString7='td_ins_form_received.insure_bu_type';
        $rawQuery.=Helper::WhereRawQuery($insured_bu_type,$rawQuery,$queryString7);
        $queryString8='td_insurance.form_status';
        $rawQuery.=Helper::WhereRawQuery($ack_status,$rawQuery,$queryString8);
        return $rawQuery;
    }
}
