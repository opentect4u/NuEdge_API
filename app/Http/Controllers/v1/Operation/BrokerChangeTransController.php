<?php

namespace App\Http\Controllers\V1\Operation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{
    MutualFund,
    Client,
    FormReceived,
    MutualFundTransaction,
    MFTransTypeSubType,
    BrokerChangeTransReport,
    BrokerChangeTrans
};
use Validator;
use Illuminate\Support\Carbon;
use Excel;
use App\Helpers\TransHelper;
use DB;

class BrokerChangeTransController extends Controller
{
    public function search(Request $request)
    {
        try {
            // return $request;
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

            // $date_range='01/12/2023 - 08/12/2023';

            $rawQuery='';
            if ($date_range || $folio_no || $pan_no || !empty($amc_id) || !empty($cat_id) || !empty($sub_cat_id) || !empty($scheme_id)) {
                if ($date_range) {
                    $from_date=Carbon::parse(str_replace('/','-',explode("-",$date_range)[0]))->format('Y-m-d') ;
                    $to_date=Carbon::parse(str_replace('/','-',explode("-",$date_range)[1]))->format('Y-m-d') ;
                    // return $to_date;
                    $queryString='tt_broker_change_trans_report.trans_date';
                    $rawQuery.=Helper::FrmToDateRawQuery($from_date,$to_date,$rawQuery,$queryString);
                }
                
                $queryString='tt_broker_change_trans_report.folio_no';
                $rawQuery.=Helper::WhereRawQuery($folio_no,$rawQuery,$queryString);
                $queryString='tt_broker_change_trans_report.first_client_pan';
                $rawQuery.=Helper::WhereRawQuery($pan_no,$rawQuery,$queryString);
                // $queryString='tt_broker_change_trans_report.first_client_name';
                // $rawQuery.=Helper::RawQueryOR($pan_no,$rawQuery,$queryString);
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
                // return $request;
            } 
            // DB::enableQueryLog();
            $all_data=BrokerChangeTransReport::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','tt_broker_change_trans_report.product_code')
                ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
                ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                ->leftJoin('md_amc','md_amc.amc_code','=','tt_broker_change_trans_report.amc_code')
                ->leftJoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
                ->leftJoin('md_option','md_option.id','=','md_scheme_isin.option_id')
                ->leftJoin('md_employee','md_employee.euin_no','=','tt_broker_change_trans_report.euin_no')
                ->leftJoin('md_branch','md_branch.id','=','md_employee.branch_id')
                ->select('tt_broker_change_trans_report.*','md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name','md_amc.amc_short_name as amc_name',
                'md_plan.plan_name as plan_name','md_option.opt_name as option_name',
                'md_employee.emp_name as rm_name','md_branch.brn_name as branch','md_employee.bu_type_id as bu_type_id','md_employee.branch_id as branch_id')
                ->selectRaw('sum(amount) as tot_amount')
                ->selectRaw('sum(stamp_duty) as tot_stamp_duty')
                ->selectRaw('sum(tds) as tot_tds')
                ->selectRaw('count(*) as tot_rows')

                ->selectRaw('(select bu_type from md_business_type where bu_code=md_employee.bu_type_id and branch_id=md_employee.branch_id limit 1) as bu_type')
                // ->selectRaw('IF(tt_broker_change_trans_report.euin_no="",(select euin_no from tt_broker_change_trans_report where folio_no=tt_broker_change_trans_report.folio_no and euin_no!="" limit 1),tt_broker_change_trans_report.euin_no) as my_euin_no')

                ->where('tt_broker_change_trans_report.delete_flag','N')

                ->where('tt_broker_change_trans_report.amc_flag','N')
                ->where('tt_broker_change_trans_report.scheme_flag','N')
                ->where('tt_broker_change_trans_report.plan_option_flag','N')
                ->where('tt_broker_change_trans_report.bu_type_flag','N')
                ->where('tt_broker_change_trans_report.divi_mismatch_flag','N')

                ->whereRaw($rawQuery)
                ->groupBy('tt_broker_change_trans_report.trans_no')
                ->groupBy('tt_broker_change_trans_report.trxn_type_flag')
                ->groupByRaw('IF(substr(trxn_nature,1,19)="Systematic-Reversed","Systematic-Reversed",trxn_nature)')
                ->groupBy('tt_broker_change_trans_report.trans_desc')
                ->groupBy('tt_broker_change_trans_report.kf_trans_type')
                ->get();
            // dd(DB::getQueryLog());

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
                        }elseif ($trans_flag=='TI') {
                            $get_type_subtype='';
                            $transaction_type='Transfer In';
                            $transaction_subtype='Transfer In';
                        }elseif ($trans_flag=='TO') {
                            $get_type_subtype='';
                            $transaction_type='Transfer Out';
                            $transaction_subtype='Transfer Out';
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
                    // number_format((float)$foo, 2, '.', '')
                    $value->tot_gross_amount= number_format((float)((float)$value->tot_amount + (float)$value->tot_stamp_duty + (float)$value->tot_tds), 2, '.', '');
                    $value->transaction_type=$transaction_type;
                    $value->transaction_subtype=$transaction_subtype;

                    if (!empty($trans_type) && in_array($transaction_type ,$trans_type) && !empty($trans_sub_type) && in_array($transaction_subtype ,$trans_sub_type)) {
                        array_push($data,$value);
                    }else if (!empty($trans_type) && in_array($transaction_type ,$trans_type)) {
                        array_push($data,$value);
                    }else if (!empty($transaction_subtype) && in_array($transaction_subtype ,$trans_sub_type)) {
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
}
