<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Helpers\TransHelper;
use App\Models\{
    MutualFund,
    Client,
    FormReceived,
    MutualFundTransaction,
    MailbackProcess,
    TempMutualFundTransaction,
    MFTransTypeSubType,
    TempNAVDetails,
    NAVDetails
};
use Validator;
use Illuminate\Support\Carbon;
use Excel;
use DB;

class MailBackController extends Controller
{
    public function Details(Request $request)
    {
        try {
            // return $request;
            $paginate=$request->paginate;
            $order=$request->order;
            $field=$request->field;
            $rnt_id=$request->rnt_id;
            if ($paginate=='A') {
                $paginate=999999999;
            }
            if ($order && $field) {
                $rawOrderBy='';
                if ($order > 0) {
                    $rawOrderBy=$field.' ASC';
                } else {
                    $rawOrderBy=$field.' DESC';
                }
                $data=MailbackProcess::leftJoin('md_rnt','md_rnt.id','=','md_mailback_process.rnt_id')
                    ->select('md_mailback_process.*','md_rnt.rnt_name')
                    ->orderByRaw($rawOrderBy)
                    ->paginate($paginate);
            }elseif ($rnt_id) {
                $data=MailbackProcess::leftJoin('md_rnt','md_rnt.id','=','md_mailback_process.rnt_id')
                    ->leftJoin('md_mailback_filetype','md_mailback_filetype.id','=','md_mailback_process.file_type_id')
                    ->leftJoin('md_mailback_filename','md_mailback_filename.id','=','md_mailback_process.file_id')
                    ->select('md_mailback_process.*','md_rnt.rnt_name','md_mailback_filetype.name as file_type_name','md_mailback_filename.name as file_name')
                    ->where('md_mailback_process.rnt_id',$rnt_id)
                    ->where('md_mailback_process.process_type','M')
                    ->orderBy('process_date','DESC')
                    ->paginate($paginate);
            } else {
                $data=MailbackProcess::leftJoin('md_rnt','md_rnt.id','=','md_mailback_process.rnt_id')
                    ->select('md_mailback_process.*','md_rnt.rnt_name')
                    ->where('md_mailback_process.process_type','M')
                    ->orderBy('process_date','DESC')
                    ->paginate($paginate);
            }
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
    public function upload(Request $request)
    {
        try {
            // return $request;
            $rnt_id=$request->rnt_id;
            $file_type_id=$request->file_type_id;
            $file_id=$request->file_id;
            $upload_file=$request->upload_file;
            
            if ($upload_file) {
                $path_extension=$upload_file->getClientOriginalExtension();
                $original_file_name=$upload_file->getClientOriginalName();
                $upload_file_name=(microtime(true) * 10000)."_".$rnt_id.".".$path_extension;
                $upload_file->move(public_path('mailback/manual/'),$upload_file_name);
                $create_dt=MailbackProcess::create(array(
                    'rnt_id'=>$rnt_id,
                    'file_type_id'=>$file_type_id,
                    'file_id'=>$file_id,
                    'original_file_name'=>$original_file_name,
                    'upload_file'=>$upload_file_name,
                    'process_date'=>date('Y-m-d H:i:s'),
                    'process_type'=>'M',
                ));
                $id=$create_dt->id;
            }else {
                $id=$request->row_id;
                $upload_file_name=$request->upload_file_name;
            }
            
            $upload_data=MailbackProcess::leftJoin('md_rnt','md_rnt.id','=','md_mailback_process.rnt_id')
                    ->select('md_mailback_process.*','md_rnt.rnt_name')
                    ->where('md_mailback_process.process_type','M')
                    ->where('md_mailback_process.id',$id)
                    ->orderBy('process_date','DESC')
                    ->first();
            
            $file_name=public_path('mailback/manual/'.$upload_file_name);
            // return  $file_name;

            // $aArray = file($upload_file,FILE_IGNORE_NEW_LINES);
            // $TotalArray = file($file_name,FILE_IGNORE_NEW_LINES);  // for txt file

            $TotalArray = array_map(function($v){return str_getcsv($v, ";");}, file($file_name));  //for csv file

            // return $TotalArray;
            // return count($TotalArray);
            // return $TotalArray[0];
            $start_count=$request->start_count;
            $end_count=$request->end_count;
            if ($end_count==count($TotalArray) || $end_count >= count($TotalArray)) {
                $end_count=count($TotalArray)-1;
            }


            TempMutualFundTransaction::truncate();
            if ($rnt_id==1) { // CAMS
                if ($file_type_id==1 && $file_id=1) {  // transction  WBR2
                    for ($i=$start_count; $i <= $end_count; $i++) { 
                        $value=explode("\t",$TotalArray[$i]);
                        TempMutualFundTransaction::create(array(
                            'rnt_id'=>$rnt_id,
                            'arn_no'=>str_replace("'","",$value[16]),
                            'sub_brk_cd'=>str_replace("'","",$value[17]),
                            'euin_no'=>str_replace("'","",$value[56]),
                            'first_client_name'=>str_replace("'","",$value[4]),
                            'first_client_pan'=>str_replace("'","",$value[42]),
                            'amc_code'=>str_replace("'","",$value[0]),
                            'folio_no'=>str_replace("'","",$value[1]),
                            'product_code'=>str_replace("'","",$value[2]),
                            'trans_no'=>str_replace("'","",$value[6]),
                            'trans_mode'=>str_replace("'","",$value[7]),
                            'trans_status'=>str_replace("'","",$value[8]),
                            'user_trans_no'=>str_replace("'","",$value[10]),
                            'trans_date'=>Carbon::parse(explode("/",str_replace("'","",$value[11]))[1].'-'.explode("/",str_replace("'","",$value[11]))[0].'-'.explode("/",str_replace("'","",$value[11]))[2])->format('Y-m-d H:i:s'),
                            'post_date'=>Carbon::parse(explode("/",str_replace("'","",$value[12]))[1].'-'.explode("/",str_replace("'","",$value[12]))[0].'-'.explode("/",str_replace("'","",$value[12]))[2])->format('Y-m-d H:i:s'),
                            'pur_price'=>str_replace("'","",$value[13]),
                            'units'=>str_replace("'","",$value[14]),
                            'amount'=>str_replace("'","",$value[15]),
                            'rec_date'=>Carbon::parse(explode("/",str_replace("'","",$value[21]))[1].'-'.explode("/",str_replace("'","",$value[21]))[0].'-'.explode("/",str_replace("'","",$value[21]))[2])->format('Y-m-d H:i:s'),
                            'trxn_type'=>str_replace("'","",$value[5]),
                            'trxn_type_flag'=>str_replace("'","",$value[45]),
                            'trxn_nature'=>str_replace("'","",$value[25]),
                            'te_15h'=>str_replace("'","",$value[28]),
                            'micr_code'=>str_replace("'","",$value[29]),
                            'remarks'=>str_replace("'","",$value[30]),
                            'sw_flag'=>str_replace("'","",$value[31]),
                            'old_folio'=>str_replace("'","",$value[32]),
                            'seq_no'=>str_replace("'","",$value[33]),
                            'reinvest_flag'=>str_replace("'","",$value[34]),
                            'stt'=>str_replace("'","",$value[36]),
                            'stamp_duty'=>str_replace("'","",$value[74]),
                            'tds'=>NULL,
                            'acc_no'=>str_replace("'","",$value[63]),
                            'bank_name'=>str_replace("'","",$value[64]),
                        ));
                    }
                }else if ($file_type_id==4 && $file_id=8) {  // historical nav WBR1
                    // return $TotalArray[0];
                    $value=explode(",",$TotalArray[0][0]);
                    // return $value;
                    // return count($TotalArray);
                    for ($i=$start_count; $i <= $end_count; $i++) {
                        $value=explode(",",$TotalArray[$i][0]);
                        TempNAVDetails::create(array(
                            'rnt_id' =>$rnt_id,
                            'amc_code'=>NULL,
                            'product_code'=>str_replace("'","",$value[0]),
                            'nav_date'=>Carbon::parse(explode("/",str_replace("'","",$value[2]))[1].'-'.explode("/",str_replace("'","",$value[2]))[0].'-'.explode("/",str_replace("'","",$value[2]))[2])->format('Y-m-d H:i:s'),
                            'nav'=>str_replace("'","",$value[3]),
                            'isin_no'=>str_replace("'","",$value[7]),
                            'amc_flag'=>'N',
                            'scheme_flag'=>'N',
                        ));
                    }
                }else {
                    # code...
                }
            }else if($rnt_id==2){  // KFINTECH
                if ($file_type_id==1 && $file_id=3) {  // transction MFSD201
                    for ($i=$start_count; $i <= $end_count; $i++) { 
                        $value=explode("~",$TotalArray[$i]);
                        // $value=explode("~",$TotalArray[0]);
                        // return $value;
                        TempMutualFundTransaction::create(array(
                            'rnt_id'=>$rnt_id,
                            'arn_no'=>$value[19],
                            'sub_brk_cd'=>isset($value[20])?$value[20]:NULL,
                            'euin_no'=>isset($value[70])?$value[70]:NULL,
                            'first_client_name'=>$value[9],
                            'first_client_pan'=>$value[47],
                            'amc_code'=>$value[1],
                            'folio_no'=>$value[2],
                            'product_code'=>$value[0],
                            'trans_no'=>$value[6],
                            'trans_mode'=>$value[10],
                            'trans_status'=>$value[11],
                            'user_trans_no'=>$value[39],
                            'trans_date'=>Carbon::parse(str_replace("/","-",$value[14]))->format('Y-m-d H:i:s'),
                            'post_date'=>Carbon::parse(str_replace("/","-",$value[15]))->format('Y-m-d H:i:s'),
                            'pur_price'=>$value[16],
                            'units'=>$value[17],
                            'amount'=>$value[18],
                            'rec_date'=>Carbon::parse(str_replace("/","-",$value[24]))->format('Y-m-d H:i:s'),
                            'kf_trans_type'=>$value[30],
                            'trans_flag'=>$value[37],
                            'trans_desc'=>$value[29], // Transction Description
                            'te_15h'=>NULL,
                            'micr_code'=>NULL,
                            'sw_flag'=>NULL,
                            'old_folio'=>NULL,
                            'seq_no'=>NULL,
                            'reinvest_flag'=>NULL,
                            'stt'=>isset($value[40])?$value[40]:NULL,
                            'stamp_duty'=>isset($value[85])?$value[85]:NULL,
                            'tds'=>isset($value[52])?$value[52]:NULL,
                            'acc_no'=>isset($value[78])?$value[78]:NULL,
                            'bank_name'=>isset($value[64])?$value[64]:NULL,
                            'remarks'=>isset($value[48])?$value[48]:NULL,
                            'dividend_option'=>isset($value[33])?$value[33]:NULL,
                            'isin_no'=>isset($value[66])?$value[66]:NULL,
                        ));
                    }
                }else if ($file_type_id==4 && $file_id=9) {  // historical nav MFSD217
                    // return $TotalArray[0];
                    // $value=explode(",",$TotalArray[0][0]);
                    // return $value;
                    // return count($TotalArray);
                    for ($i=$start_count; $i <= $end_count; $i++) {
                        $value=explode(",",$TotalArray[$i][0]);
                        TempNAVDetails::create(array(
                            'rnt_id' =>$rnt_id,
                            'amc_code'=>$value[0],
                            'product_code'=>$value[3],
                            'nav_date'=>Carbon::parse(str_replace("/","-",$value[4]))->format('Y-m-d H:i:s'),
                            'nav'=>$value[5],
                            'isin_no'=>$value[10],
                            'amc_flag'=>'N',
                            'scheme_flag'=>'N',
                        ));
                    }
                }else {
                    # code...
                }
            }
            
            // =============================start for csv code==============================
            // $end_count=500;
            // $data =  array_map('str_getcsv', file($file_name));
            // // return $data;
            // foreach ($data as $key => $value) {
            //     // return $value;
            //     if ($key > 0) {
            //         if ($key>=$start_count && $key<=$end_count) {

            //             if ($rnt_id==1) { // cams
            //                 if ($file_type_id==1 && $file_id=1) {  // transction  WBR2
            //                     MutualFundTransaction::create(array(
            //                         'arn_no'=>str_replace("'","",$value[16]),
            //                         'sub_brk_cd'=>str_replace("'","",$value[17]),
            //                         'euin_no'=>str_replace("'","",$value[56]),
            //                         'first_client_name'=>str_replace("'","",$value[4]),
            //                         'first_client_pan'=>str_replace("'","",$value[42]),
            //                         'amc_code'=>str_replace("'","",$value[0]),
            //                         'folio_no'=>str_replace("'","",$value[1]),
            //                         'product_code'=>str_replace("'","",$value[2]),
            //                         'trans_no'=>str_replace("'","",$value[6]),
            //                         'trans_mode'=>str_replace("'","",$value[7]),
            //                         'trans_status'=>str_replace("'","",$value[8]),
            //                         'user_trans_no'=>str_replace("'","",$value[10]),
            //                         'trans_date'=>Carbon::parse(str_replace("/","-",str_replace("'","",$value[11])))->format('Y-m-d H:i:s'),
            //                         'post_date'=>Carbon::parse(str_replace("/","-",str_replace("'","",$value[12])))->format('Y-m-d H:i:s'),
            //                         'pur_price'=>str_replace("'","",$value[13]),
            //                         'units'=>str_replace("'","",$value[14]),
            //                         'amount'=>str_replace("'","",$value[15]),
            //                         'rec_date'=>Carbon::parse(str_replace("/","-",str_replace("'","",$value[21])))->format('Y-m-d H:i:s'),
            //                         'trxn_type'=>str_replace("'","",$value[5]),
            //                         'trxn_type_flag'=>str_replace("'","",$value[45]),
            //                         'trxn_nature'=>str_replace("'","",$value[25]),
            //                         'te_15h'=>str_replace("'","",$value[28]),
            //                         'micr_code'=>str_replace("'","",$value[29]),
            //                         'remarks'=>str_replace("'","",$value[30]),
            //                         'sw_flag'=>str_replace("'","",$value[31]),
            //                         'old_folio'=>str_replace("'","",$value[32]),
            //                         'seq_no'=>str_replace("'","",$value[33]),
            //                         'reinvest_flag'=>str_replace("'","",$value[34]),
            //                         'stt'=>str_replace("'","",$value[36]),
            //                         'stamp_duty'=>str_replace("'","",$value[74]),
            //                         'tds'=>NULL,
            //                         'acc_no'=>str_replace("'","",$value[63]),
            //                         'bank_name'=>str_replace("'","",$value[64]),
            //                     ));
            //                 }else {
            //                     # code...
            //                 }
            //             }else if($rnt_id==2){  // Kafe
            //                 if ($file_type_id==1 && $file_id=3) {  // transction MFSD201
            //                     // return Carbon::parse(str_replace("/","-",$value[49]))->format('Y-m-d H:i:s');
            //                     MutualFundTransaction::create(array(
            //                         'arn_no'=>$value[19],
            //                         'sub_brk_cd'=>isset($value[20])?$value[20]:NULL,
            //                         'euin_no'=>isset($value[70])?$value[70]:NULL,
            //                         'first_client_name'=>$value[9],
            //                         'first_client_pan'=>$value[47],
            //                         'amc_code'=>$value[1],
            //                         'folio_no'=>$value[2],
            //                         'product_code'=>$value[0],
            //                         'trans_no'=>$value[6],
            //                         'trans_mode'=>$value[10],
            //                         'trans_status'=>$value[11],
            //                         'user_trans_no'=>$value[39],
            //                         'trans_date'=>Carbon::parse(str_replace("/","-",$value[49]))->format('Y-m-d H:i:s'),
            //                         'post_date'=>Carbon::parse(str_replace("/","-",$value[15]))->format('Y-m-d H:i:s'),
            //                         'pur_price'=>$value[16],
            //                         'units'=>$value[17],
            //                         'amount'=>$value[18],
            //                         'rec_date'=>Carbon::parse(str_replace("/","-",$value[24]))->format('Y-m-d H:i:s'),
            //                         'kf_trans_type'=>$value[30],
            //                         'trans_flag'=>$value[37],
            //                         'trans_desc'=>$value[29], // Transction Description
            //                         'te_15h'=>NULL,
            //                         'micr_code'=>NULL,
            //                         'sw_flag'=>NULL,
            //                         'old_folio'=>NULL,
            //                         'seq_no'=>NULL,
            //                         'reinvest_flag'=>NULL,
            //                         'stt'=>isset($value[40])?$value[40]:NULL,
            //                         'stamp_duty'=>isset($value[85])?$value[85]:NULL,
            //                         'tds'=>isset($value[52])?$value[52]:NULL,
            //                         'acc_no'=>isset($value[78])?$value[78]:NULL,
            //                         'bank_name'=>isset($value[64])?$value[64]:NULL,
            //                         'remarks'=>isset($value[48])?$value[48]:NULL,
            //                     ));
            //                 }else {
            //                     # code...
            //                 }
            //             }
            //         }
            //     }
            // }
            // =============================end for csv code==============================
            
            $dataArray=[];
            $dataArray['upload_data']=$upload_data;
            $dataArray['start_count']=$start_count;
            $dataArray['end_count']=$request->end_count;
            $dataArray['upload_file_name']=$upload_file_name;
            $dataArray['row_id']=$id;
            $dataArray['total_count']=count($TotalArray);
            $dataArray['upload_progressDtls']=json_decode($request->upload_progressDtls);

        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($dataArray);
    }

    public function misMatch(Request $request)
    {
        try {
            $mismatch_flag=$request->mismatch_flag;
            // return $mismatch_flag;
            if ($mismatch_flag=='A') {
                $rawQuery="amc_flag='Y'";
            }elseif ($mismatch_flag=='S') {
                $rawQuery="scheme_flag='Y'";
            }elseif ($mismatch_flag=='P/O') {
                $rawQuery="plan_option_flag='Y'";
            }elseif ($mismatch_flag=='B') {
                $rawQuery="bu_type_flag='Y'";
            }elseif ($mismatch_flag=='D') {
                $rawQuery="divi_mismatch_flag='Y'";
            }

            $all_data=MutualFundTransaction::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_mutual_fund_trans.product_code')
                    ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                    ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
                    ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                    ->leftJoin('md_amc','md_amc.amc_code','=','td_mutual_fund_trans.amc_code')
                    ->leftJoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
                    ->leftJoin('md_option','md_option.id','=','md_scheme_isin.option_id')
                    ->leftJoin('md_employee','md_employee.euin_no','=','td_mutual_fund_trans.euin_no')
                    ->leftJoin('md_branch','md_branch.id','=','md_employee.branch_id')
                    ->select('td_mutual_fund_trans.*','md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name','md_amc.amc_short_name as amc_name',
                    'md_plan.plan_name as plan_name','md_option.opt_name as option_name',
                    'md_employee.emp_name as rm_name','md_branch.brn_name as branch','md_employee.bu_type_id as bu_type_id','md_employee.branch_id as branch_id')
                    // ->selectRaw('sum(amount) as tot_amount')
                    // ->selectRaw('sum(stamp_duty) as tot_stamp_duty')
                    // ->selectRaw('sum(tds) as tot_tds')
                    // ->selectRaw('count(*) as tot_rows')
                    ->where('td_mutual_fund_trans.delete_flag','N')
                    ->whereRaw($rawQuery)
                    ->orderBy('td_mutual_fund_trans.created_at','desc')
                    // ->groupBy('td_mutual_fund_trans.trans_no')
                    // ->groupBy('td_mutual_fund_trans.trxn_type_flag')
                    // ->groupByRaw('IF(substr(trxn_nature,1,19)="Systematic-Reversed","Systematic-Reversed",trxn_nature)')
                    // ->groupBy('td_mutual_fund_trans.trans_desc')
                    // ->groupBy('td_mutual_fund_trans.kf_trans_type')
                    // ->inRandomOrder()
                    ->take(100)
                    ->get();

                    $data=[];
                    foreach ($all_data as $key => $value) {
                        $euin=$value->euin_no;
                        $trans_no=$value->trans_no;
                        $trans_date=$value->trans_date;
                        // MutualFundTransaction::
                        if($euin == ''){
                            $euin_no=MutualFundTransaction::where('folio_no',$value->folio_no)
                            ->where('euin_no','!=','')->first();
                            // return $euin_no;
                            if ($euin_no) {
                                $rm_data=DB::table('md_employee')
                                    ->leftJoin('md_branch','md_branch.id','=','md_employee.branch_id')
                                    ->leftJoin('md_business_type','md_business_type.bu_code','=','md_employee.bu_type_id')
                                    ->select('md_employee.*','md_branch.brn_name as branch_name','md_business_type.bu_type as bu_type')
                                    ->where('md_employee.euin_no',$euin_no->euin_no)
                                    ->first();
                                // return $rm_data;
                                if ($rm_data) {
                                    $value->bu_type=$rm_data->bu_type;
                                    $value->branch=$rm_data->branch_name;
                                    $value->rm_name=$rm_data->emp_name;
                                    $value->euin_no=$rm_data->euin_no;
                                }
                            }
                        }else{
                            $value->bu_type=DB::table('md_business_type')
                                ->where('bu_code',$value->bu_type_id)
                                ->where('branch_id',$value->branch_id)
                                ->value('bu_type');
                        }
                        
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
                        $value->tot_gross_amount= ((float)$value->tot_amount + (float)$value->tot_stamp_duty + (float)$value->tot_tds);
                        $value->transaction_type=$transaction_type;
                        $value->transaction_subtype=$transaction_subtype;
    
                        // if (!empty($trans_type) && in_array($transaction_type ,$trans_type) && !empty($trans_sub_type) && in_array($transaction_subtype ,$trans_sub_type)) {
                        //     array_push($data,$value);
                        // }else if (!empty($trans_type) && in_array($transaction_type ,$trans_type)) {
                        //     array_push($data,$value);
                        // }else if (!empty($transaction_subtype) && in_array($transaction_subtype ,$trans_sub_type)) {
                        //     array_push($data,$value);
                        // }else{
                            array_push($data,$value);
                        // }
                    }
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }


    public function lockTransaction(Request $request)
    {
        try {
            // return $request;
            $id=$request->id;
            // return $id;
            $up_data=MutualFundTransaction::find($id);
            $up_data->divi_mismatch_flag='N';
            $up_data->divi_lock_flag='L';
            $up_data->save();
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($up_data);
    }


    public function fileType(Request $request)
    {
        try {
            $data=DB::table('md_mailback_filetype')->get();
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function fileName(Request $request)
    {
        try {
            $data=DB::table('md_mailback_filename')
                ->where('rnt_id',$request->rnt_id)
                ->where('file_type_id',$request->file_type_id)
                ->get();
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }


    public function misMatchNAV(Request $request)
    {
        try {
            $mismatch_flag=$request->mismatch_flag;
            // return $mismatch_flag;
            $rawQuery='';
            if ($mismatch_flag=='A') {
                $rawQuery="amc_flag='Y'";
            }elseif ($mismatch_flag=='S') {
                $rawQuery="scheme_flag='Y'";
            }
            $data=[];
            $data=NAVDetails::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_nav_details.product_code')
                ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
                ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                ->leftJoin('md_amc','md_amc.id','=','md_scheme.amc_id')
                ->leftJoin('md_amc as md_amc_1','md_amc_1.amc_code','=','td_nav_details.amc_code')
                ->select('td_nav_details.*','md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name',
                'md_amc.amc_short_name as amc_name','md_amc_1.amc_short_name as amc_short_name')
                ->whereRaw($rawQuery)
                ->orderBy('td_nav_details.nav_date','desc')
                ->get();
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}
