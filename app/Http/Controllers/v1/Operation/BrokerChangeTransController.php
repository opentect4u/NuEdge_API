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
    BrokerChangeTrans,
    Disclaimer
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
            $type=$request->type;
            $all_client=$request->all_client;
            $view_type=$request->view_type;
            $client_name=$request->client_name;
            $all_client=$request->all_client;
            // $pan_no=json_decode($request->pan_no);
            $amc_id=json_decode($request->amc_id);
            $cat_id=json_decode($request->cat_id);
            $sub_cat_id=json_decode($request->sub_cat_id);
            $scheme_id=json_decode($request->scheme_id);
            $trans_type=json_decode($request->trans_type);
            $trans_sub_type=json_decode($request->trans_sub_type);
            $family_members_pan=json_decode($request->family_members_pan);
            $family_members_name=json_decode($request->family_members_name);

            $brn_cd=json_decode($request->brn_cd);
            $bu_type=json_decode($request->bu_type);
            $rm_id=json_decode($request->rm_id);
            $euin_no=json_decode($request->euin_no);
            $sub_brk_cd=json_decode($request->sub_brk_cd);

            $rawQuery='';
            if ($folio_no || $view_type || !empty($amc_id) || !empty($cat_id) || !empty($sub_cat_id) || !empty($scheme_id)) {
                $queryString='tt_broker_change_trans_report.folio_no';
                $rawQuery.=Helper::WhereRawQuery($folio_no,$rawQuery,$queryString);
                // $queryString='tt_broker_change_trans_report.first_client_pan';
                // $rawQuery.=Helper::WhereRawQuery($pan_no,$rawQuery,$queryString);
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

                if ($view_type=='F') {
                    $queryString='tt_broker_change_trans_report.first_client_pan';
                    $condition=(strlen($rawQuery) > 0)? " AND (":" (";
                    $row_name_string=  "'" .implode("','", $family_members_pan). "'";
                    $rawQuery.=$condition.$queryString." IN (".$row_name_string.")";
                    $queryString='tt_broker_change_trans_report.first_client_name';
                    $condition1=(strlen($rawQuery) > 0)? " OR ":" ";
                    $row_name_string1=  "'" .implode("','", $family_members_name). "'";
                    $rawQuery.=$condition1.$queryString." IN (".$row_name_string1."))";
                }else {
                    // if ($all_client==true) {
                    //     # code...
                    // }
                    if ($pan_no) {
                        $queryString='tt_broker_change_trans_report.first_client_pan';
                        $rawQuery.=Helper::WhereRawQuery($pan_no,$rawQuery,$queryString);
                    }elseif($client_name) {
                        if ($client_name!='undefined') {
                            $queryString='tt_broker_change_trans_report.first_client_name';
                            $rawQuery.=Helper::WhereRawQuery($client_name,$rawQuery,$queryString);
                        }
                    }
                }

                if (!empty($brn_cd)) {
                    $row_name_string=  "'" .implode("','", $brn_cd). "'";
                    $queryString='md_employee.branch_id';
                    $condition=(strlen($rawQuery) > 0)? " AND ":" ";
                    $rawQuery.=$condition.$queryString." IN (".$row_name_string.")";
                }

                if (!empty($bu_type)) {
                    $row_name_string=  "'" .implode("','", $bu_type). "'";
                    $queryString='(select bu_code from md_business_type where bu_code=md_employee.bu_type_id and branch_id=md_employee.branch_id limit 1)';
                    $condition=(strlen($rawQuery) > 0)? " AND ":" ";
                    $rawQuery.=$condition.$queryString." IN (".$row_name_string.")";
                }
                if (!empty($rm_id)) {
                    $row_name_string=  "'" .implode("','", $rm_id). "'";
                    $queryString="md_employee.euin_no";
                    $condition=(strlen($rawQuery) > 0)? " AND ":" ";
                    $rawQuery.=$condition.$queryString." IN (".$row_name_string.")";
                }

                if (!empty($sub_brk_cd)) {
                    // some logicadd for sub broker checking 
                    
                    if (!empty($euin_no)) {
                        $row_name_string=  "'" .implode("','", $euin_no). "'";
                        $queryString="md_employee.euin_no";
                        $condition=(strlen($rawQuery) > 0)? " AND ":" ";
                        $rawQuery.=$condition.$queryString." IN (".$row_name_string.")";
                    }
                }
                if (!empty($euin_no)) {
                    $row_name_string=  "'" .implode("','", $euin_no). "'";
                    $queryString="md_employee.euin_no";
                    $condition=(strlen($rawQuery) > 0)? " AND ":" ";
                    $rawQuery.=$condition.$queryString." IN (".$row_name_string.")";
                }
                // return $rawQuery;
                // return $request;
                // DB::enableQueryLog();
                if(strlen($rawQuery) > 0){
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
                        ->groupBy('tt_broker_change_trans_report.trans_flag')
                        ->get();
                }else {
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
                        ->groupBy('tt_broker_change_trans_report.trans_no')
                        ->groupBy('tt_broker_change_trans_report.trxn_type_flag')
                        ->groupByRaw('IF(substr(trxn_nature,1,19)="Systematic-Reversed","Systematic-Reversed",trxn_nature)')
                        ->groupBy('tt_broker_change_trans_report.trans_desc')
                        ->groupBy('tt_broker_change_trans_report.kf_trans_type')
                        ->groupBy('tt_broker_change_trans_report.trans_flag')
                        ->get();
                }
                // dd(DB::getQueryLog());
            }else {
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
                    // ->whereRaw($rawQuery)
                    ->groupBy('tt_broker_change_trans_report.trans_no')
                    ->groupBy('tt_broker_change_trans_report.trxn_type_flag')
                    ->groupByRaw('IF(substr(trxn_nature,1,19)="Systematic-Reversed","Systematic-Reversed",trxn_nature)')
                    ->groupBy('tt_broker_change_trans_report.trans_desc')
                    ->groupBy('tt_broker_change_trans_report.kf_trans_type')
                    ->groupBy('tt_broker_change_trans_report.trans_flag')
                    ->get();
                // dd(DB::getQueryLog());
            } 
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
                    $change_type='';
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
                        $change_type='Transfer In';
                    }else {
                        $kf_trans_type=$value->kf_trans_type;
                        $trans_flag=$value->trans_flag;
                        if ($trans_flag=='DP' || $trans_flag=='DR') {
                            $get_type_subtype=MFTransTypeSubType::where('c_k_trans_sub_type',$kf_trans_type)
                                ->where('k_divident_flag',$trans_flag)
                                ->first();
                        }
                        // elseif ($trans_flag=='TI') {
                        //     $get_type_subtype='';
                        //     $transaction_type='Transfer In';
                        //     $transaction_subtype='Transfer In';
                        // }elseif ($trans_flag=='TO') {
                        //     $get_type_subtype='';
                        //     $transaction_type='Transfer Out';
                        //     $transaction_subtype='Transfer Out';
                        // } 
                        else {
                            $get_type_subtype=MFTransTypeSubType::where('c_k_trans_sub_type',$kf_trans_type)
                                ->first();
                        }
                        
                        if ($get_type_subtype) {
                            $transaction_type=$get_type_subtype->trans_type;
                            $transaction_subtype=$get_type_subtype->trans_sub_type;
                        }

                        if ($trans_flag=='TI') {
                            $change_type='Transfer In';
                        }elseif ($trans_flag=='TO') {
                            $change_type='Transfer Out';
                        } 
                    }
                    $value->gross_amount= number_format((float)((float)$amount + (float)$value->stamp_duty + (float)$value->tds), 2, '.', '');
                    $value->tot_gross_amount= number_format((float)((float)$value->tot_amount + (float)$value->tot_stamp_duty + (float)$value->tot_tds), 2, '.', '');
                    $value->transaction_type=$transaction_type;
                    $value->transaction_subtype=$transaction_subtype;
                    $value->change_type=$change_type;

                    // if (!empty($trans_type) && in_array($transaction_type ,$trans_type) && !empty($trans_sub_type) && in_array($transaction_subtype ,$trans_sub_type)) {
                    //     array_push($data,$value);
                    // }else if (!empty($trans_type) && in_array($transaction_type ,$trans_type)) {
                    //     array_push($data,$value);
                    // }else if (!empty($transaction_subtype) && in_array($transaction_subtype ,$trans_sub_type)) {
                    //     array_push($data,$value);
                    // }else{
                    //     array_push($data,$value);
                    // }
                    if ($type) {
                        if ($type==$change_type) {
                            array_push($data,$value);
                        }
                    }else {
                        array_push($data,$value);
                    }
                }
                // return $data;
                if (!empty($trans_type) || !empty($trans_sub_type)) {
                    $data1=[];
                    foreach ($data as $key1 => $value1) {
                        $transaction_type=$value1['transaction_type'];
                        $transaction_subtype=$value1['transaction_subtype'];
                        if (in_array($transaction_type ,$trans_type) && in_array($transaction_subtype ,$trans_sub_type)) {
                            array_push($data1,$value);
                        }else if (in_array($transaction_type ,$trans_type)) {
                            array_push($data1,$value);
                        }else if (in_array($transaction_subtype ,$trans_sub_type)) {
                            array_push($data1,$value);
                        }
                    }
                    $data=$data1;
                }

            $disclaimer=Disclaimer::select('dis_des','font_size','color_code')->find(2);
            $mydata=[];
            $mydata['data']=$data;
            $mydata['disclaimer']=$disclaimer;
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($mydata);
    }
}