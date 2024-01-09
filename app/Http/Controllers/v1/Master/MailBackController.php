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
    NAVDetails,
    TempSipStpTransaction,
    SipStpTransaction,
    FolioDetails,
    TempFolioDetails,
    FolioDetailsReport,
    TempBrokerChangeTrans,
    BrokerChangeTrans,
    SipStpSwpReport
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
                    'created_by'=>Helper::modifyUser($request->user()),
                ));
                $id=$create_dt->id;
            }else {
                $id=$request->row_id;
                $upload_file_name=$request->upload_file_name;
            }
            
            $upload_data=[];
            $file_name=public_path('mailback/manual/'.$upload_file_name);
            $info = pathinfo($file_name);
            if ($info['extension']=='txt') {
                // $aArray = file($upload_file,FILE_IGNORE_NEW_LINES);
                $TotalArray = file($file_name,FILE_IGNORE_NEW_LINES);  // for txt file
            }else {
                $TotalArray = array_map(function($v){return str_getcsv($v, ";");}, file($file_name));  //for csv file
            }

            // return $TotalArray;
            // return count($TotalArray);
            // return $TotalArray[0];
            $start_count=$request->start_count;
            $end_count=$request->end_count;
            if ($end_count==count($TotalArray) || $end_count >= count($TotalArray)) {
                $end_count=count($TotalArray)-1;
                $upload_data=MailbackProcess::leftJoin('md_rnt','md_rnt.id','=','md_mailback_process.rnt_id')
                    ->leftJoin('md_mailback_filetype','md_mailback_filetype.id','=','md_mailback_process.file_type_id')
                    ->leftJoin('md_mailback_filename','md_mailback_filename.id','=','md_mailback_process.file_id')
                    ->select('md_mailback_process.*','md_rnt.rnt_name','md_mailback_filetype.name as file_type_name','md_mailback_filename.name as file_name')
                    ->where('md_mailback_process.id',$id)
                    ->where('md_mailback_process.process_type','M')
                    ->orderBy('process_date','DESC')
                    ->first();
            }

            // tata nua plus 
            // TempMutualFundTransaction::truncate();
            if ($rnt_id==1) { // CAMS
                if ($file_type_id=='1' && $file_id=='1') {  // transction  WBR2
                    TempMutualFundTransaction::truncate();
                    $array_set=[];
                    for ($i=$start_count; $i <= $end_count; $i++) { 
                        $value=explode("\t",$TotalArray[$i]);
                        // TempMutualFundTransaction::create(array(
                        $single_array=[
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
                            'created_at'=>date('Y-m-d H:i:s'),
                            'updated_at'=>date('Y-m-d H:i:s'),
                        ];
                        // ));
                        array_push($array_set,$single_array);
                    }

                    $array_trans_no = array_map(function ($result){
                        return $result['trans_no'];
                    }, $array_set);
                    // DB::enableQueryLog();
                    $array_set_form_db=MutualFundTransaction::select('*')
                            ->selectRaw('DATE(created_at) as aa_for_create_at')->where('rnt_id',$rnt_id)
                            ->whereIn('trans_no',array_unique($array_trans_no)) 
                            // ->whereDate('created_at',date('Y-m-d'))
                            ->get()->toArray();
                            // ->get();
                    // dd(DB::getQueryLog());

                    // return $array_set_form_db;
                    // return $array_set;
                    $final_array=[];
                    
                    foreach ($array_set as $key => $value) {
                        // return $value;
                        if (in_array($value['trans_no'], array_column($array_set_form_db, 'trans_no'))) {
                            // return 
                            $var1=date('Y-m-d',strtotime($value['created_at']));
                            $var2=$value['trans_no'];
                            $filtered_array = array_filter($array_set_form_db, function($val) use($var1, $var2){
                                return ($val['aa_for_create_at']==$var1 && $val['trans_no']==$var2);
                            });
                            if ($filtered_array) {
                                array_push($final_array,$value);
                            }
                        }else {
                            array_push($final_array,$value);
                        }
                    }
                    // return $final_array;
                    if (count($final_array) > 0) {
                        TempMutualFundTransaction::insert($final_array);
                    }
                }else if ($file_type_id=='6' && $file_id=='2') {  // transction WBR2A
                    TempBrokerChangeTrans::truncate();
                    // return $TotalArray[0];
                    // $value=explode("\t",$TotalArray[0]);
                    // return $value;
                    $array_set=[];
                    for ($i=$start_count; $i <= $end_count; $i++) { 
                        $value=explode("\t",$TotalArray[$i]);
                        $single_array=[
                            'rnt_id'=>$rnt_id,
                            'arn_no'=>str_replace("'","",$value[16]),
                            'sub_brk_cd'=>str_replace("'","",$value[17]),
                            'euin_no'=>NULL,
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
                            'created_at'=>date('Y-m-d H:i:s'),
                            'updated_at'=>date('Y-m-d H:i:s'),
                        ];
                        array_push($array_set,$single_array);
                    }

                    $array_trans_no = array_map(function ($result){
                        return $result['trans_no'];
                    }, $array_set);
                    // DB::enableQueryLog();
                    $array_set_form_db=BrokerChangeTrans::select('*')
                            ->selectRaw('DATE(created_at) as aa_for_create_at')->where('rnt_id',$rnt_id)
                            ->whereIn('trans_no',array_unique($array_trans_no)) 
                            // ->whereDate('created_at',date('Y-m-d'))
                            ->get()->toArray();
                    // return $array_set_form_db;
                    // return $array_set;

                    $final_array=[];
                    foreach ($array_set as $key => $value) {
                        // return $value;
                        if (in_array($value['trans_no'], array_column($array_set_form_db, 'trans_no'))) {
                            // return 
                            $var1=date('Y-m-d',strtotime($value['created_at']));
                            $var2=$value['trans_no'];
                            $filtered_array = array_filter($array_set_form_db, function($val) use($var1, $var2){
                                return ($val['aa_for_create_at']==$var1 && $val['trans_no']==$var2);
                            });
                            if ($filtered_array) {
                                array_push($final_array,$value);
                            }
                        }else {
                            array_push($final_array,$value);
                        }
                    }
                    // return $final_array;
                    if (count($final_array) > 0) {
                        TempBrokerChangeTrans::insert($final_array);
                    }
                }else if ($file_type_id=='4' && $file_id=='8') {  // historical nav WBR1
                    TempNAVDetails::truncate();
                    // return $TotalArray[0];
                    // $value=explode(",",$TotalArray[0][0]);
                    // return $value;
                    // return count($TotalArray);
                        
                    // $array_slice=array_slice($TotalArray,$start_count,$end_count);
                    // $array_set = array_map(function ($result){
                    //     $value=explode(",",$result[0]);
                    //     $nav_date=Carbon::parse(explode("/",str_replace("'","",$value[2]))[1].'-'.explode("/",str_replace("'","",$value[2]))[0].'-'.explode("/",str_replace("'","",$value[2]))[2])->format('Y-m-d H:i:s');
                    //     $single_array=[
                    //         'rnt_id' =>1,
                    //         'amc_code'=>NULL,
                    //         'product_code'=>str_replace("'","",$value[0]),
                    //         'nav_date'=>$nav_date,
                    //         'nav'=>str_replace("'","",$value[3]),
                    //         'isin_no'=>str_replace("'","",$value[7]),
                    //         'amc_flag'=>'N',
                    //         'scheme_flag'=>'N', 
                    //     ];
                    //     return $single_array;
                    // }, $array_slice);
                    // return $array_set;
                    $array_set=[];
                    for ($i=$start_count; $i <= $end_count; $i++) {
                        $value=explode(",",$TotalArray[$i][0]); // 10/09/2023 12:00:00 am
                        $nav_date=Carbon::parse(explode("/",str_replace("'","",$value[2]))[1].'-'.explode("/",str_replace("'","",$value[2]))[0].'-'.explode("/",str_replace("'","",$value[2]))[2])->format('Y-m-d H:i:s');
                        $single_array=[
                            'rnt_id' =>$rnt_id,
                            'amc_code'=>NULL,
                            'product_code'=>str_replace("'","",$value[0]),
                            'nav_date'=>$nav_date,
                            'nav'=>str_replace("'","",$value[3]),
                            'isin_no'=>str_replace("'","",$value[7]),
                            'amc_flag'=>'N',
                            'scheme_flag'=>'N', 
                        ];
                        array_push($array_set,$single_array);
                    }
                    // return $array_set;
                    // $array_product_code=array_reduce($array_set, function ($result, $item) {
                    //     $result[] = $item['product_code'];
                    //     return $result;
                    // },array());
                    // $array_nav_date = array_reduce($array_set, function ($result, $item) {
                    //     $result[] = $item['nav_date'];
                    //     return $result;
                    // }, array());
                    
                    $array_product_code = array_map(function ($result){
                        return $result['product_code'];
                    }, $array_set);
                    $array_nav_date = array_map(function ($result){
                        return $result['nav_date'];
                    }, $array_set);

                    $array_set_form_db=NAVDetails::where('rnt_id',$rnt_id)
                            ->whereIn('product_code',array_unique($array_product_code)) 
                            ->whereIn('nav_date',array_unique($array_nav_date))
                            ->get()->toArray();
                
                    $final_array =  array_udiff(
                        $array_set,
                        $array_set_form_db,
                        fn($a, $b) => $a['rnt_id'] <=> $b['rnt_id'] && 
                        $a['product_code'] <=> $b['product_code'] && 
                        $a['nav_date'] <=> $b['nav_date']
                    );
                    // return $final_array;
                    if (count($final_array) > 0) {
                        TempNAVDetails::insert($final_array);
                    }
                }elseif ($file_type_id=='2' && $file_id=='3') {  // sip stp report WBR49
                    TempSipStpTransaction::truncate();
                    // return $TotalArray[0];
                    // $value=explode("\t",$TotalArray[0]);
                    // return $value;
                    $array_set=[];
                    for ($i=$start_count; $i <= $end_count; $i++) { 
                        $value=explode("\t",$TotalArray[$i]);
                        $pause_from_date=str_replace("'","",$value[38]);
                        $pause_to_date=str_replace("'","",$value[39]);
                        $cease_date=str_replace("'","",$value[9]);
                        // TempSipStpTransaction::create(array(
                        $single_array=[
                            'rnt_id'=>$rnt_id,
                            'arn_no'=>NULL,
                            'product_code'=>str_replace("'","",$value[0]),
                            'folio_no'=>str_replace("'","",$value[2]),
                            'first_client_name'=>str_replace("'","",$value[3]),
                            'auto_trans_type'=>str_replace("'","",$value[4]),
                            'auto_trans_no'=>str_replace("'","",$value[5]),
                            'auto_amount'=>$value[6],
                            'from_date'=>Carbon::parse(explode("/",str_replace("'","",$value[7]))[1].'-'.explode("/",str_replace("'","",$value[7]))[0].'-'.explode("/",str_replace("'","",$value[7]))[2])->format('Y-m-d H:i:s'),
                            'to_date'=>Carbon::parse(explode("/",str_replace("'","",$value[8]))[1].'-'.explode("/",str_replace("'","",$value[8]))[0].'-'.explode("/",str_replace("'","",$value[8]))[2])->format('Y-m-d H:i:s'),
                            'cease_terminate_date'=>(isset($cease_date) && strlen($cease_date)>0)? Carbon::parse(explode("/",str_replace("'","",$value[9]))[1].'-'.explode("/",str_replace("'","",$value[9]))[0].'-'.explode("/",str_replace("'","",$value[9]))[2])->format('Y-m-d'):NULL,
                            'periodicity'=>str_replace("'","",$value[10]),
                            'period_day'=>str_replace("'","",$value[11]),
                            'inv_iin'=>str_replace("'","",$value[12]),
                            'payment_mode'=>str_replace("'","",$value[13]),
                            'reg_date'=>Carbon::parse(explode("/",str_replace("'","",$value[15]))[1].'-'.explode("/",str_replace("'","",$value[15]))[0].'-'.explode("/",str_replace("'","",$value[15]))[2])->format('Y-m-d H:i:s'),
                            'sub_brk_cd'=>str_replace("'","",$value[16]),
                            'euin_no'=>str_replace("'","",$value[28]),
                            'remarks'=>str_replace("'","",$value[17]),
                            'top_up_req'=>str_replace("'","",$value[18]),
                            'top_up_amount'=>str_replace("'","",$value[19]),
                            'ac_type'=>str_replace("'","",$value[20]),
                            'bank'=>str_replace("'","",$value[21]),
                            'bank_branch'=>str_replace("'","",$value[22]),
                            'instrm_no'=>str_replace("'","",$value[23]),
                            'chq_micr_no'=>str_replace("'","",$value[24]),
                            'first_client_pan'=>str_replace("'","",$value[26]),
                            'amc_code'=>str_replace("'","",$value[33]),
                            'sub_trans_desc'=>str_replace("'","",$value[37]),
                            'pause_from_date'=>(isset($pause_from_date) && strlen($pause_from_date)>0)? Carbon::parse(explode("/",str_replace("'","",$value[38]))[1].'-'.explode("/",str_replace("'","",$value[38]))[0].'-'.explode("/",str_replace("'","",$value[38]))[2])->format('Y-m-d H:i:s'):NULL,
                            'pause_to_date'=>(isset($pause_to_date) && strlen($pause_to_date)>0)? Carbon::parse(explode("/",str_replace("'","",$value[39]))[1].'-'.explode("/",str_replace("'","",$value[39]))[0].'-'.explode("/",str_replace("'","",$value[39]))[2])->format('Y-m-d H:i:s'):NULL,
                            'req_ref_no'=>str_replace("'","",$value[22]),
                            'frequency'=>str_replace("'","",$value[43]),
                            'to_product_code'=>(str_replace("'","",$value[32])=='')?NULL:str_replace("'","",$value[33]).str_replace("'","",$value[32]),
                            'to_scheme_code'=>(str_replace("'","",$value[32])=='')?NULL:str_replace("'","",$value[32]),
                            'f_status'=>NULL,
                            'created_at'=>date('Y-m-d H:i:s'),
                            'updated_at'=>date('Y-m-d H:i:s'),
                        ];
                        // ));
                        array_push($array_set,$single_array);
                    }

                    $array_product_code = array_map(function ($result){
                        return $result['product_code'];
                    }, $array_set);
                    $array_folio_no = array_map(function ($result){
                        return $result['folio_no'];
                    }, $array_set);
                    $array_first_client_pan= array_map(function ($result){
                        return $result['first_client_pan'];
                    }, $array_set);

                    $array_set_form_db=SipStpTransaction::where('rnt_id',$rnt_id)
                            ->whereIn('product_code',array_unique($array_product_code)) 
                            ->whereIn('folio_no',array_unique($array_folio_no)) 
                            ->whereIn('first_client_pan',array_unique($array_first_client_pan)) 
                            ->get()->toArray();
                    // return $array_set;
                    // return $array_set_form_db;
                    $final_array = [];
                    foreach ($array_set as $key => $value) {
                        $var1=$value['product_code'];
                        $var2=$value['folio_no'];
                        $var3=$value['first_client_pan'];
                        $var4=$value['from_date'];
                        $var5=$value['reg_date'];
                        $var6=$value['cease_terminate_date'];
                        $var7=$value['to_product_code'];
                        $var8=$value['rnt_id'];
                        $filtered_array = array_filter($array_set_form_db, function($val) use($var1, $var2,$var3,$var4,$var5,$var6,$var7,$var8){
                            return (
                                $val['product_code']==$var1 
                                && $val['folio_no']==$var2
                                && $val['first_client_pan']==$var3
                                && $val['from_date']==$var4
                                && $val['reg_date']==$var5
                                && $val['cease_terminate_date']==$var6
                                && $val['to_product_code']==$var7
                                && $val['rnt_id']==$var8
                            );
                        });
                        if (!$filtered_array) {
                            array_push($final_array,$value);
                        }
                    }

                    // $final_array =  array_udiff(
                    //     $array_set,
                    //     $array_set_form_db,
                    //     fn($a, $b) => $a['rnt_id'] <=> $b['rnt_id'] 
                    //     && $a['product_code'] <=> $b['product_code'] 
                    //     && $a['folio_no'] <=> $b['folio_no']
                    //     && $a['first_client_pan'] <=> $b['first_client_pan']
                    //     && $a['from_date'] <=> $b['from_date']
                    //     && $a['reg_date'] <=> $b['reg_date']
                    //     && $a['cease_terminate_date'] <=> $b['cease_terminate_date']
                    //     && $a['to_product_code'] <=> $b['to_product_code']
                    // );
                    // return $final_array;
                    if (count($final_array) > 0) {
                        TempSipStpTransaction::insert($final_array);
                    }
                }elseif ($file_type_id=='3' && $file_id=='4') {  // folio master report WBR9C
                    TempFolioDetails::truncate();
                    // return $TotalArray[0];
                    // $value=explode("\t",$TotalArray[0]);
                    // return $value;
                    for ($i=$start_count; $i <= $end_count; $i++) { 
                        $value=explode("\t",$TotalArray[$i]);
                        // return $value[31];
                        if (isset($value[1]) && isset($value[31]) && isset($value[47]) && isset($value[74])) {
                            $folio_date=str_replace("'","",$value[47]);
                            $folio_dtt=explode("/",str_replace("'","",$folio_date));
                            if (!isset($folio_dtt[1])) {
                                $folio_dt=NULL;
                            }else {
                                $folio_dt=Carbon::parse(explode("/",str_replace("'","",$folio_date))[1].'-'.explode("/",str_replace("'","",$folio_date))[0].'-'.explode("/",str_replace("'","",$folio_date))[2])->format('Y-m-d H:i:s');
                            }

                            $dob=str_replace("'","",$value[31]);
                            $dd=explode("/",str_replace("'","",$dob));
                            if (!isset($dd[1])) {
                                $mydob=NULL;
                            }else {
                                $mydob=Carbon::parse(explode("/",str_replace("'","",$dob))[1].'-'.explode("/",str_replace("'","",$dob))[0].'-'.explode("/",str_replace("'","",$dob))[2])->format('Y-m-d');
                            }

                            $dob_2nd_holder=str_replace("'","",$value[52]);
                            $dd2=explode("/",str_replace("'","",$dob_2nd_holder));
                            if (!isset($dd2[1])) {
                                $mydob2=NULL;
                            }else {
                                $mydob2=Carbon::parse(explode("/",str_replace("'","",$dob_2nd_holder))[1].'-'.explode("/",str_replace("'","",$dob_2nd_holder))[0].'-'.explode("/",str_replace("'","",$dob_2nd_holder))[2])->format('Y-m-d');
                            }

                            $dob_3rd_holder=str_replace("'","",$value[53]);
                            $dd3=explode("/",str_replace("'","",$dob_3rd_holder));
                            if (!isset($dd3[1])) {
                                $mydob3=NULL;
                            }else {
                                $mydob3=Carbon::parse(explode("/",str_replace("'","",$dob_3rd_holder))[1].'-'.explode("/",str_replace("'","",$dob_3rd_holder))[0].'-'.explode("/",str_replace("'","",$dob_3rd_holder))[2])->format('Y-m-d');
                            }

                            $guardian_dob=str_replace("'","",$value[54]);
                            $dd4=explode("/",str_replace("'","",$guardian_dob));
                            if (!isset($dd4[1])) {
                                $mydob4=NULL;
                            }else {
                                $mydob4=Carbon::parse(explode("/",str_replace("'","",$guardian_dob))[1].'-'.explode("/",str_replace("'","",$guardian_dob))[0].'-'.explode("/",str_replace("'","",$guardian_dob))[2])->format('Y-m-d');
                            }
                            
                            TempFolioDetails::create(array(
                                'rnt_id'=>$rnt_id,
                                'product_code'=>str_replace("'","",$value[1]),
                                'amc_code'=>str_replace("'","",$value[0]),
                                'folio_no'=>str_replace("'","",$value[2]),
                                'folio_date'=>($folio_dt)?date('Y-m-d',strtotime($folio_dt)):NULL,
                                'dividend_option'=>NULL,
                                'first_client_name'=>str_replace("'","",$value[3]),
                                'joint_name_1'=>str_replace("'","",$value[9]),
                                'joint_name_2'=>str_replace("'","",$value[10]),
                                'add_1'=>str_replace("'","",$value[4]),
                                'add_2'=>str_replace("'","",$value[5]),
                                'add_3'=>str_replace("'","",$value[6]),
                                'city'=>str_replace("'","",$value[7]),
                                'pincode'=>str_replace("'","",$value[8]),
                                // 'rupee_bal'=>(str_replace("'","",$value[78])!='')?str_replace("'","",$value[78]):NULL,
                                'state'=>NULL,
                                'country'=>NULL,
                                'tpin'=>NULL,
                                'f_name'=>NULL,
                                'dob'=>$mydob,
                                'dob_2nd_holder'=>$mydob2,
                                'dob_3rd_holder'=>$mydob3,
                                'm_name'=>NULL,
                                'phone_residence'=>str_replace("'","",$value[12]),
                                'phone_res_1'=>NULL,
                                'phone_res_2'=>NULL,
                                'phone_ofc'=>str_replace("'","",$value[11]),
                                'phone_ofc_1'=>NULL,
                                'phone_ofc_2'=>NULL,
                                'fax_residence'=>NULL,
                                'fax_ofc'=>NULL,
                                'tax_status'=>str_replace("'","",$value[19]),
                                'tax_status_2_holder'=>NULL,
                                'tax_status_3_holder'=>NULL,
                                'occ_code'=>NULL,
                                'email'=>str_replace("'","",$value[13]),
                                'email_2nd_holder'=>str_replace("'","",$value[59]),
                                'email_3rd_holder'=>str_replace("'","",$value[61]),
                                'bank_acc_no'=>str_replace("'","",$value[24]),
                                'bank_name'=>str_replace("'","",$value[21]),
                                'bank_ifsc'=>str_replace("'","",$value[25]),
                                'bank_micr'=>NULL,
                                'acc_type'=>str_replace("'","",$value[23]),
                                'bank_branch'=>str_replace("'","",$value[22]),
                                'bank_add_1'=>str_replace("'","",$value[26]),
                                'bank_add_2'=>str_replace("'","",$value[27]),
                                'bank_add_3'=>str_replace("'","",$value[28]),
                                'bank_city'=>str_replace("'","",$value[29]),
                                'bank_phone'=>NULL,
                                'bank_state'=>NULL,
                                'bank_country'=>NULL,
                                'bank_pincode'=>str_replace("'","",$value[30]),
                                'invs_id'=>NULL,
                                'arn_no'=>NULL,
                                'pan'=>str_replace("'","",$value[15]),
                                'pan_2_holder'=>str_replace("'","",$value[16]),
                                'pan_3_holder'=>str_replace("'","",$value[17]),
                                'mobile'=>str_replace("'","",$value[32]),
                                'mobile_2nd_holder'=>str_replace("'","",$value[58]),
                                'mobile_3rd_holder'=>str_replace("'","",$value[60]),
                                'report_date'=>NULL,
                                'report_time'=>NULL,
                                'occupation_des'=>str_replace("'","",$value[33]),

                                // 'INV_IIN'=>str_replace("'","",$value[34]), to be add if required
                                // 'GST_STATE_CODE'=>str_replace("'","",$value[55]),
                                // 'FOLIO_OLD'=>str_replace("'","",$value[56]),
                                // 'SCHEME_FOLIO_NUMBER'=>str_replace("'","",$value[57]),
                                // 'FH_MOBILE_FD'=>str_replace("'","",$value[62]),
                                // 'FH_EMAIL_FD'=>str_replace("'","",$value[63]),
                                // 'JH1_MOBILE_FD'=>str_replace("'","",$value[64]),
                                // 'JH1_EMAIL_FD'=>str_replace("'","",$value[65]),
                                // 'JH2_MOBILE_FD'=>str_replace("'","",$value[66]),
                                // 'JH2_EMAIL_FD'=>str_replace("'","",$value[67]),

                                'occupation_des_2nd'=>NULL,
                                'occupation_des_3rd'=>NULL,
                                'mode_of_holding'=>str_replace("'","",$value[14]),
                                'mode_of_holding_des'=>NULL,
                                'mapin_id'=>NULL,
                                'aadhaar_1_holder'=>NULL,
                                'aadhaar_2_holder'=>NULL,
                                'aadhaar_3_holder'=>NULL,
                                'guardian_name'=>str_replace("'","",$value[45]),
                                'guardian_dob'=>$mydob4,
                                'guardian_aadhaar'=>NULL,
                                'guardian_pan'=>str_replace("'","",$value[18]),
                                'guardian_mobile'=>NULL,
                                'guardian_email'=>NULL,
                                'guardian_relation'=>str_replace("'","",$value[46]),
                                'guardian_ckyc_no'=>str_replace("'","",$value[51]),
                                'guardian_tax_status'=>NULL,
                                'guardian_occu_des'=>NULL,
                                'guardian_pa_link_ststus'=>str_replace("'","",$value[72]),
                                'reinvest_flag'=>str_replace("'","",$value[20]),
                                'nom_optout_status'=>str_replace("'","",$value[35]),
                                'nom_name_1'=>str_replace("'","",$value[36]),
                                'nom_relation_1'=>str_replace("'","",$value[37]),
                                'nom_per_1'=>str_replace("'","",$value[38]),
                                'nom_name_2'=>str_replace("'","",$value[39]),
                                'nom_relation_2'=>str_replace("'","",$value[40]),
                                'nom_per_2'=>str_replace("'","",$value[41]),
                                'nom_name_3'=>str_replace("'","",$value[42]),
                                'nom_relation_3'=>str_replace("'","",$value[43]),
                                'nom_per_3'=>str_replace("'","",$value[44]),
                                'nom_pan_1'=>NULL,
                                'nom_pan_2'=>NULL,
                                'nom_pan_3'=>NULL,
                                'ckyc_no_1st'=>str_replace("'","",$value[48]),
                                'ckyc_no_2nd'=>str_replace("'","",$value[49]),
                                'ckyc_no_3rd'=>str_replace("'","",$value[50]),
                                'pa_link_ststus_1st'=>str_replace("'","",$value[72]),
                                'pa_link_ststus_2nd'=>str_replace("'","",$value[73]),
                                'pa_link_ststus_3rd'=>str_replace("'","",$value[74]),
                                'kyc_status_1st'=>str_replace("'","",$value[68]),
                                'kyc_status_2nd'=>str_replace("'","",$value[70]),
                                'kyc_status_3rd'=>str_replace("'","",$value[71]),
                                'guardian_kyc_status'=>str_replace("'","",$value[69]),
                            ));
                        }
                    }
                }elseif ($file_type_id=='3' && $file_id=='10') {  // folio master report WBR9
                    TempFolioDetails::truncate();
                    // $value=explode("\t",$TotalArray[0]);
                    // return $value;
                    for ($i=$start_count; $i <= $end_count; $i++) { 
                        $value=explode("\t",$TotalArray[$i]);
                        // return $value[31];
                        if (isset($value[1]) && isset($value[31]) && isset($value[47]) && isset($value[74])) {
                            $folio_date=str_replace("'","",$value[81]);
                            $folio_dd=explode("/",str_replace("'","",$folio_date));
                            if (!isset($folio_dd[1])) {
                                $folio_dt=NULL;
                            }else {
                                $folio_dt=Carbon::parse(explode("/",str_replace("'","",$folio_date))[1].'-'.explode("/",str_replace("'","",$folio_date))[0].'-'.explode("/",str_replace("'","",$folio_date))[2])->format('Y-m-d');
                            }

                            $report_date=str_replace("'","",$value[9]);
                            $report_dd=explode("/",str_replace("'","",$report_date));
                            if (!isset($report_dd[1])) {
                                $report_dt=NULL;
                            }else {
                                $report_dt=Carbon::parse(explode("/",str_replace("'","",$report_date))[1].'-'.explode("/",str_replace("'","",$report_date))[0].'-'.explode("/",str_replace("'","",$report_date))[2])->format('Y-m-d');
                            }
                            
                            $dob=str_replace("'","",$value[36]);
                            $dd=explode("/",str_replace("'","",$dob));
                            if (!isset($dd[1])) {
                                $mydob=NULL;
                            }else {
                                $mydob=Carbon::parse(explode("/",str_replace("'","",$dob))[1].'-'.explode("/",str_replace("'","",$dob))[0].'-'.explode("/",str_replace("'","",$dob))[2])->format('Y-m-d');
                            }

                            $dob1=str_replace("'","",$value[88]);
                            $dd1=explode("/",str_replace("'","",$dob1));
                            if (!isset($dd1[1])) {
                                $mydob1=NULL;
                            }else {
                                $mydob1=Carbon::parse(explode("/",str_replace("'","",$dob1))[1].'-'.explode("/",str_replace("'","",$dob1))[0].'-'.explode("/",str_replace("'","",$dob1))[2])->format('Y-m-d');
                            }

                            $dob2=str_replace("'","",$value[89]);
                            $dd2=explode("/",str_replace("'","",$dob2));
                            if (!isset($dd2[1])) {
                                $mydob2=NULL;
                            }else {
                                $mydob2=Carbon::parse(explode("/",str_replace("'","",$dob2))[1].'-'.explode("/",str_replace("'","",$dob2))[0].'-'.explode("/",str_replace("'","",$dob2))[2])->format('Y-m-d');
                            }

                            $dob3=str_replace("'","",$value[90]);
                            $dd3=explode("/",str_replace("'","",$dob3));
                            if (!isset($dd3[1])) {
                                $mydob3=NULL;
                            }else {
                                $mydob3=Carbon::parse(explode("/",str_replace("'","",$dob3))[1].'-'.explode("/",str_replace("'","",$dob3))[0].'-'.explode("/",str_replace("'","",$dob3))[2])->format('Y-m-d');
                            }

                            TempFolioDetails::create(array(
                                'rnt_id'=>$rnt_id,
                                'product_code'=>str_replace("'","",$value[7]),
                                'amc_code'=>str_replace("'","",$value[91]),
                                'folio_no'=>str_replace("'","",$value[0]),
                                'folio_date'=>$folio_dt,
                                'dividend_option'=>NULL,
                                'first_client_name'=>str_replace("'","",$value[1]),
                                'joint_name_1'=>str_replace("'","",$value[12]),
                                'joint_name_2'=>str_replace("'","",$value[13]),
                                'add_1'=>str_replace("'","",$value[2]),
                                'add_2'=>str_replace("'","",$value[3]),
                                'add_3'=>str_replace("'","",$value[4]),
                                'city'=>str_replace("'","",$value[5]),
                                'pincode'=>str_replace("'","",$value[6]),
                                // 'rupee_bal'=>(str_replace("'","",$value[6])!='')?str_replace("'","",$value[6]):NULL,
                                'state'=>NULL,
                                'country'=>NULL,
                                'tpin'=>NULL,
                                'f_name'=>NULL,
                                'dob'=>$mydob,
                                'dob_2nd_holder'=>$mydob1,
                                'dob_3rd_holder'=>$mydob2,
                                'm_name'=>NULL,
                                'phone_residence'=>str_replace("'","",$value[15]),
                                'phone_res_1'=>NULL,
                                'phone_res_2'=>NULL,
                                'phone_ofc'=>str_replace("'","",$value[14]),
                                'phone_ofc_1'=>NULL,
                                'phone_ofc_2'=>NULL,
                                'fax_residence'=>NULL,
                                'fax_ofc'=>NULL,
                                'tax_status'=>str_replace("'","",$value[23]),
                                'tax_status_2_holder'=>NULL,
                                'tax_status_3_holder'=>NULL,
                                'occ_code'=>NULL,
                                'email'=>str_replace("'","",$value[16]),
                                'email_2nd_holder'=>NULL,
                                'email_3rd_holder'=>NULL,
                                'bank_acc_no'=>str_replace("'","",$value[30]),
                                'bank_name'=>str_replace("'","",$value[27]),
                                'bank_ifsc'=>str_replace("'","",$value[76]),
                                'bank_micr'=>NULL,
                                'acc_type'=>str_replace("'","",$value[29]),
                                'bank_branch'=>str_replace("'","",$value[28]),
                                'bank_add_1'=>str_replace("'","",$value[31]),
                                'bank_add_2'=>str_replace("'","",$value[32]),
                                'bank_add_3'=>str_replace("'","",$value[33]),
                                'bank_city'=>str_replace("'","",$value[34]),
                                'bank_phone'=>NULL,
                                'bank_state'=>NULL,
                                'bank_country'=>NULL,
                                'bank_pincode'=>str_replace("'","",$value[35]),
                                'invs_id'=>NULL,
                                'arn_no'=>str_replace("'","",$value[24]),
                                'pan'=>str_replace("'","",$value[19]),
                                'pan_2_holder'=>str_replace("'","",$value[20]),
                                'pan_3_holder'=>str_replace("'","",$value[21]),
                                'mobile'=>str_replace("'","",$value[37]),
                                'mobile_2nd_holder'=>NULL,
                                'mobile_3rd_holder'=>NULL,
                                'report_date'=>$report_dt,
                                'report_time'=>NULL,
                                'occupation_des'=>str_replace("'","",$value[38]),

                                // 'INV_IIN'=>str_replace("'","",$value[39]), to be add if required

                                'occupation_des_2nd'=>NULL,
                                'occupation_des_3rd'=>NULL,
                                'mode_of_holding'=>str_replace("'","",$value[17]),
                                'mode_of_holding_des'=>NULL,
                                'mapin_id'=>NULL,
                                'aadhaar_1_holder'=>NULL,
                                'aadhaar_2_holder'=>NULL,
                                'aadhaar_3_holder'=>NULL,
                                'guardian_name'=>str_replace("'","",$value[79]),
                                'guardian_dob'=>$mydob3,
                                'guardian_aadhaar'=>NULL,
                                'guardian_pan'=>str_replace("'","",$value[22]),
                                'guardian_mobile'=>NULL,
                                'guardian_email'=>NULL,
                                'guardian_relation'=>NULL,
                                'guardian_ckyc_no'=>str_replace("'","",$value[87]),
                                'guardian_tax_status'=>NULL,
                                'guardian_occu_des'=>NULL,
                                'guardian_pa_link_ststus'=>NULL,
                                'reinvest_flag'=>str_replace("'","",$value[26]),
                                'nom_optout_status'=>NULL,
                                'nom_name_1'=>str_replace("'","",$value[40]),
                                'nom_relation_1'=>str_replace("'","",$value[41]),
                                'nom_per_1'=>str_replace("'","",$value[51]),
                                'nom_name_2'=>str_replace("'","",$value[52]),
                                'nom_relation_2'=>str_replace("'","",$value[53]),
                                'nom_per_2'=>str_replace("'","",$value[63]),
                                'nom_name_3'=>str_replace("'","",$value[64]),
                                'nom_relation_3'=>str_replace("'","",$value[65]),
                                'nom_per_3'=>str_replace("'","",$value[75]),
                                'nom_pan_1'=>NULL,
                                'nom_pan_2'=>NULL,
                                'nom_pan_3'=>NULL,
                                'ckyc_no_1st'=>str_replace("'","",$value[84]),
                                'ckyc_no_2nd'=>str_replace("'","",$value[85]),
                                'ckyc_no_3rd'=>str_replace("'","",$value[86]),
                                'pa_link_ststus_1st'=>str_replace("'","",$value[82]),
                                'pa_link_ststus_2nd'=>NULL,
                                'pa_link_ststus_3rd'=>NULL,
                            ));
                        }
                    }
                } else {
                    # code...
                }
            }else if($rnt_id==2){  // KFINTECH
                if ($file_type_id==1 && $file_id==5) {  // transction MFSD201
                    TempMutualFundTransaction::truncate();
                    $array_set=[];
                    for ($i=$start_count; $i <= $end_count; $i++) { 
                        $value=explode("~",$TotalArray[$i]);
                        // $value=explode("~",$TotalArray[0]);
                        // return $value;
                        // TempMutualFundTransaction::create(array(
                        $single_array=[
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
                            'created_at'=>date('Y-m-d H:i:s'),
                            'updated_at'=>date('Y-m-d H:i:s'),
                        ];
                        // ));
                        array_push($array_set,$single_array);
                    }

                    $array_trans_no = array_map(function ($result){
                        return $result['trans_no'];
                    }, $array_set);
                    // DB::enableQueryLog();
                    $array_set_form_db=MutualFundTransaction::select('*')
                            ->selectRaw('DATE(created_at) as aa_for_create_at')->where('rnt_id',$rnt_id)
                            ->whereIn('trans_no',array_unique($array_trans_no)) 
                            // ->whereDate('created_at',date('Y-m-d'))
                            ->get()->toArray();
                            // ->get();
                    // dd(DB::getQueryLog());
                    // return $array_set_form_db;
                    // return $array_set;
                    $final_array=[];
                    
                    foreach ($array_set as $key => $value) {
                        // return $value;
                        if (in_array($value['trans_no'], array_column($array_set_form_db, 'trans_no'))) {
                            // return 
                            $var1=date('Y-m-d',strtotime($value['created_at']));
                            $var2=$value['trans_no'];
                            $filtered_array = array_filter($array_set_form_db, function($val) use($var1, $var2){
                                return ($val['aa_for_create_at']==$var1 && $val['trans_no']==$var2);
                            });
                            if ($filtered_array) {
                                array_push($final_array,$value);
                            }
                        }else {
                            array_push($final_array,$value);
                        }
                    }
                    // return $final_array;
                    if (count($final_array) > 0) {
                        TempMutualFundTransaction::insert($final_array);
                    }
                }else if ($file_type_id==4 && $file_id==9) {  // historical nav MFSD217
                    TempNAVDetails::truncate();
                    // return $TotalArray[0];
                    // $value=explode(",",$TotalArray[0][0]);
                    // return $value;
                    // return count($TotalArray);

                    // $array_slice=array_slice($TotalArray,$start_count,300,true);
                    // $array_set = array_map(function ($result){
                    //     $value=explode(",",$result[0]);
                    //     $nav_date=Carbon::parse(str_replace("/","-",$value[4]))->format('Y-m-d H:i:s');
                    //     $single_array=[
                    //         'rnt_id' =>2,
                    //         'amc_code'=>$value[0],
                    //         'product_code'=>$value[3],
                    //         'nav_date'=>$nav_date,
                    //         'nav'=>$value[5],
                    //         'isin_no'=>$value[10],
                    //         'amc_flag'=>'N',
                    //         'scheme_flag'=>'N',
                    //     ];
                    //     return $single_array;
                    // }, $array_slice);
                    // return $array_set;
                    $array_set=[];
                    for ($i=$start_count; $i <= $end_count; $i++) {
                        $value=explode(",",$TotalArray[$i][0]);
                        $nav_date=Carbon::parse(str_replace("/","-",$value[4]))->format('Y-m-d H:i:s');
                        $single_array=[
                            'rnt_id' =>$rnt_id,
                            'amc_code'=>$value[0],
                            'product_code'=>$value[3],
                            'nav_date'=>$nav_date,
                            'nav'=>$value[5],
                            'isin_no'=>$value[10],
                            'amc_flag'=>'N',
                            'scheme_flag'=>'N',
                        ];
                        array_push($array_set,$single_array);
                    }

                    $array_product_code = array_map(function ($result){
                        return $result['product_code'];
                    }, $array_set);
                    $array_nav_date = array_map(function ($result){
                        return $result['nav_date'];
                    }, $array_set);

                    $array_set_form_db=NAVDetails::where('rnt_id',$rnt_id)
                            ->whereIn('product_code',array_unique($array_product_code)) 
                            ->whereIn('nav_date',array_unique($array_nav_date))
                            ->get()->toArray();
                
                    $final_array =  array_udiff(
                        $array_set,
                        $array_set_form_db,
                        fn($a, $b) => $a['rnt_id'] <=> $b['rnt_id'] && 
                        $a['product_code'] <=> $b['product_code'] && 
                        $a['nav_date'] <=> $b['nav_date']
                    );
                    // return $final_array;
                    if (count($final_array) > 0) {
                        TempNAVDetails::insert($final_array);
                    }
                }elseif ($file_type_id==2 && $file_id==6) {  // sip stp report MFSD243
                    TempSipStpTransaction::truncate();
                    // return $TotalArray[0];
                    // $value=explode("~",$TotalArray[0]);
                    // return $value;
                    $array_set=[];
                    for ($i=$start_count; $i <= $end_count; $i++) { 
                        // return $TotalArray[$i];
                        $value=explode("~",$TotalArray[$i]);
                        // return $value;
                        // return date('Y-m-d',strtotime($value[6]));
                        // TempSipStpTransaction::create(array(
                        $single_array=[
                            'rnt_id'=>$rnt_id,
                            'arn_no'=>$value[13],
                            'product_code'=>str_replace("'","",$value[21]),
                            'folio_no'=>str_replace("'","",$value[4]),
                            'first_client_name'=>str_replace("'","",$value[5]),
                            'auto_trans_type'=>str_replace("'","",$value[23]),
                            'auto_trans_no'=> (str_replace("'","",$value[34]))?str_replace("'","",$value[34]):str_replace("'","",$value[3]),  // RegSlno  and ihno
                            'auto_amount'=>$value[10],
                            'from_date'=>date('Y-m-d H:i:s',strtotime($value[7])),
                            'to_date'=>date('Y-m-d H:i:s',strtotime($value[8])),
                            // 'period_day'=>str_replace("'","",$value[11]),
                            'period_day'=>0,
                            'reg_date'=>date('Y-m-d H:i:s',strtotime($value[6])),
                            'sub_brk_cd'=>str_replace("'","",$value[15]),
                            'euin_no'=>NULL,
                            'remarks'=>str_replace("'","",$value[27]),
                            'bank'=>str_replace("'","",$value[31]),
                            'bank_branch'=>NULL,
                            'instrm_no'=>str_replace("'","",$value[32]),
                            'chq_micr_no'=>str_replace("'","",$value[30]),
                            'first_client_pan'=>str_replace("'","",$value[17]),
                            'amc_code'=>str_replace("'","",$value[20]),
                            'sub_trans_desc'=>str_replace("'","",$value[37]),
                            'pause_from_date'=>NULL,
                            'pause_to_date'=>NULL,
                            'req_ref_no'=>NULL,
                            'frequency'=>str_replace("'","",$value[22]),
                            'cease_terminate_date'=>(isset($value[26]) && strlen($value[26])>0)?date('Y-m-d',strtotime($value[26])):NULL,
                            'f_status'=>$value[27],
                            'no_of_installment'=>$value[9],
                            'to_product_code'=>isset($value[28])?$value[28]:NULL,
                            'to_scheme_code'=>NULL,
                            'created_at'=>date('Y-m-d H:i:s'),
                            'updated_at'=>date('Y-m-d H:i:s'),
                        ];
                        // ));
                        array_push($array_set,$single_array);
                    }

                    $array_product_code = array_map(function ($result){
                        return $result['product_code'];
                    }, $array_set);
                    $array_folio_no = array_map(function ($result){
                        return $result['folio_no'];
                    }, $array_set);
                    $array_first_client_pan= array_map(function ($result){
                        return $result['first_client_pan'];
                    }, $array_set);

                    $array_set_form_db=SipStpTransaction::where('rnt_id',$rnt_id)
                            ->whereIn('product_code',array_unique($array_product_code)) 
                            ->whereIn('folio_no',array_unique($array_folio_no)) 
                            ->whereIn('first_client_pan',array_unique($array_first_client_pan)) 
                            ->get()->toArray();
                    // return $array_set;
                    // return $array_set_form_db;
                    $final_array = [];
                    foreach ($array_set as $key => $value) {
                        $var1=$value['product_code'];
                        $var2=$value['folio_no'];
                        $var3=$value['first_client_pan'];
                        $var4=$value['from_date'];
                        $var5=$value['reg_date'];
                        $var6=$value['cease_terminate_date'];
                        $var7=$value['to_product_code'];
                        $var8=$value['rnt_id'];
                        $filtered_array = array_filter($array_set_form_db, function($val) use($var1, $var2,$var3,$var4,$var5,$var6,$var7,$var8){
                            return (
                                $val['product_code']==$var1 
                                && $val['folio_no']==$var2
                                && $val['first_client_pan']==$var3
                                && $val['from_date']==$var4
                                && $val['reg_date']==$var5
                                && $val['cease_terminate_date']==$var6
                                && $val['to_product_code']==$var7
                                && $val['rnt_id']==$var8
                            );
                        });
                        if (!$filtered_array) {
                            array_push($final_array,$value);
                        }
                    }
                    // return $final_array;
                    if (count($final_array) > 0) {
                        TempSipStpTransaction::insert($final_array);
                    }
                }elseif ($file_type_id==3 && $file_id==7) {  // folio master report MFSD211
                    TempFolioDetails::truncate();
                    // return $TotalArray[0];
                    $value=explode("~",$TotalArray[0]);
                    // return $value;
                    for ($i=$start_count; $i <= $end_count; $i++) { 
                        // return $TotalArray[$i];
                        $value=explode("~",$TotalArray[$i]);

                        $dd=str_replace("/","-",$value[15]);
                        if ($dd=='' || $dd==' ') {
                            $mydob=NULL;
                        }else {
                            $mydob=Carbon::parse($dd)->format('Y-m-d');
                            // DATE_FORMAT($mydob,"Y-m-d")
                        }
                        TempFolioDetails::create(array(
                            'rnt_id'=>$rnt_id,
                            'product_code'=>$value[0],
                            'amc_code'=>$value[1],
                            'folio_no'=>$value[2],
                            // 'folio_date'=>NULL,
                            'dividend_option'=>$value[46],
                            'first_client_name'=>$value[4],
                            'joint_name_1'=>$value[5],
                            'joint_name_2'=>$value[6],
                            'add_1'=>$value[7],
                            'add_2'=>$value[8],
                            'add_3'=>$value[9],
                            'city'=>$value[10],
                            'pincode'=>$value[11],
                            'state'=>$value[12],
                            'country'=>$value[13],
                            'tpin'=>$value[14],
                            'f_name'=>$value[16],
                            'dob'=>$mydob,
                            'm_name'=>$value[17],
                            'phone_residence'=>$value[18],
                            'phone_res_1'=>$value[19],
                            'phone_res_2'=>$value[20],
                            'phone_ofc'=>$value[21],
                            'phone_ofc_1'=>$value[22],
                            'phone_ofc_2'=>$value[23],
                            'fax_residence'=>$value[24],
                            'fax_ofc'=>$value[25],
                            'tax_status'=>$value[26],
                            'occ_code'=>$value[27],
                            'email'=>$value[28],
                            'bank_acc_no'=>$value[29],
                            'bank_name'=>$value[30],
                            'bank_ifsc'=>$value[59],
                            'acc_type'=>$value[31],
                            'branch'=>$value[32],
                            'bank_add_1'=>$value[33],
                            'bank_add_2'=>$value[34],
                            'bank_add_3'=>$value[35],
                            'bank_city'=>$value[36],
                            'bank_phone'=>$value[37],
                            'bank_state'=>$value[38],
                            'bank_country'=>$value[39],
                            'invs_id'=>$value[40],
                            'arn_no'=>$value[41],
                            'pan'=>$value[44],
                            'pan_2_holder'=>$value[50],
                            'pan_3_holder'=>$value[51],
                            'mobile'=>$value[45],
                            'report_date'=>NULL,
                            'report_time'=>NULL,
                            'occupation_des'=>$value[47],
                            'mode_of_holding'=>$value[48],
                            'mode_of_holding_des'=>$value[48],
                            'mapin_id'=>$value[49],
                            'aadhaar_1_holder'=>$value[74],
                            'aadhaar_2_holder'=>$value[75],
                            'aadhaar_3_holder'=>$value[76],
                            'guardian_name'=>$value[53],
                            'guardian_aadhaar'=>$value[77],
                            'guardian_pan'=>$value[65],
                            'guardian_relation'=>NULL,
                            'reinvest_flag'=>NULL,
                            'nom_optout_status'=>$value[111],
                            'nom_name_1'=>$value[54],
                            'nom_relation_1'=>$value[68],
                            'nom_per_1'=>$value[71],
                            'nom_name_2'=>$value[60],
                            'nom_relation_2'=>$value[69],
                            'nom_per_2'=>$value[72],
                            'nom_name_3'=>$value[61],
                            'nom_relation_3'=>$value[70],
                            'nom_per_3'=>$value[73],
                            'kyc_status_1st'=>$value[62],
                            'kyc_status_2nd'=>$value[63],
                            'kyc_status_3rd'=>$value[64],
                            'guardian_kyc_status'=>$value[109],
                            'ckyc_no_1st'=>$value[102],
                            'ckyc_no_2nd'=>$value[103],
                            'ckyc_no_3rd'=>$value[104],
                            'guardian_ckyc_no'=>$value[105]
                        ));
                    }
                }elseif ($file_type_id==5 && $file_id==11) {  // sip stp pause report MFSD231
                    // $value=explode("~",$TotalArray[1]);
                    // return $value;
                    for ($i=$start_count; $i <= $end_count; $i++) { 
                        // return $TotalArray[$i];
                        $value=explode("~",$TotalArray[$i]);
                        $product_code=$value[0].$value[1].$value[2];
                        $folio_no=$value[4];
                        $PAUSEFROM=Carbon::parse(str_replace("/","-",$value[9]))->format('Y-m-d');
                        $PAUSETO=Carbon::parse(str_replace("/","-",$value[10]))->format('Y-m-d');
                        // return Carbon::parse(str_replace("/","-",$value[9]))->format('Y-m-d');
                        $data=SipStpTransaction::where('product_code',$product_code)->where('folio_no',$folio_no)
                        ->update([
                            'pause_from_date'=>$PAUSEFROM,
                            'pause_to_date'=>$PAUSETO,
                        ]);
                        // ->get();
                        // return $data;
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
                    ->leftJoin('md_employee','md_employee.euin_no','=',DB::raw('IF(td_mutual_fund_trans.euin_no!="",td_mutual_fund_trans.euin_no,(select euin_no from td_mutual_fund_trans where folio_no=td_mutual_fund_trans.folio_no and product_code=td_mutual_fund_trans.product_code AND euin_no!="" limit 1))'))
                    ->leftJoin('md_branch','md_branch.id','=','md_employee.branch_id')
                    ->select('td_mutual_fund_trans.*','md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name','md_amc.amc_short_name as amc_name',
                    'md_plan.plan_name as plan_name','md_option.opt_name as option_name','md_amc.id as amc_id',
                    'md_employee.emp_name as rm_name','md_branch.brn_name as branch','md_employee.bu_type_id as bu_type_id','md_employee.branch_id as branch_id')
                    // ->selectRaw('sum(amount) as tot_amount')
                    // ->selectRaw('sum(stamp_duty) as tot_stamp_duty')
                    // ->selectRaw('sum(tds) as tot_tds')
                    ->selectRaw('amount as tot_amount')
                    ->selectRaw('stamp_duty as tot_stamp_duty')
                    ->selectRaw('tds as tot_tds')
                    ->selectRaw('(select bu_type from md_business_type where bu_code=md_employee.bu_type_id and branch_id=md_employee.branch_id limit 1) as bu_type')
                    ->where('td_mutual_fund_trans.delete_flag','N')
                    ->whereRaw($rawQuery)
                    ->orderBy('td_mutual_fund_trans.created_at','desc')
                    // ->groupBy('td_mutual_fund_trans.trans_no')
                    // ->groupBy('td_mutual_fund_trans.trxn_type_flag')
                    // ->groupByRaw('IF(substr(trxn_nature,1,19)="Systematic-Reversed","Systematic-Reversed",trxn_nature)')
                    // ->groupBy('td_mutual_fund_trans.trans_desc')
                    // ->groupBy('td_mutual_fund_trans.kf_trans_type')
                    // ->inRandomOrder()
                    // ->take(5)
                    ->get();

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
                        $value->tot_gross_amount= number_format((float)((float)$value->tot_amount + (float)$value->tot_stamp_duty + (float)$value->tot_tds), 2, '.', '');
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


    public function lockTransaction(Request $request)
    {
        try {
            // return $request;
            $id=$request->id;
            $file_type=$request->file_type;
            $sub_file_type=$request->sub_file_type;
            switch ($file_type) {
                case 'T':
                    if ($sub_file_type=='B') {
                        // return $request;
                        // lock folio using folio and product code 
                        // $up_data=MutualFundTransaction::where('folio_no',$request->folio_no)
                        //     ->where('product_code',$request->product_code)
                        //     ->where('euin_no',$request->euin_no)
                        //     ->update([
                        //         'old_euin_no'=>$request->euin_no,
                        //         'euin_no'=>$request->new_euin_no,
                        //         'bu_type_flag'=>'N',
                        //         'bu_type_lock_flag'=>'L'
                        //     ]);
                        $up_data=MutualFundTransaction::where('folio_no',$request->folio_no)
                            ->where('product_code',$request->product_code)
                            ->where('bu_type_flag','Y')
                            ->get();
                        foreach ($up_data as $key => $single_data) {
                            $single_data_update=MutualFundTransaction::find($single_data->id);
                            $single_data_update->old_euin_no=$single_data_update->euin_no;
                            $single_data_update->euin_no=$request->new_euin_no;
                            $single_data_update->sub_brk_cd=NULL;
                            $single_data_update->bu_type_flag='N';
                            $single_data_update->bu_type_lock_flag='L';
                            $single_data_update->update();
                        }
                    }elseif ($sub_file_type=='D') {
                        $up_data=MutualFundTransaction::find($id);
                        $up_data->divi_mismatch_flag='N';
                        $up_data->divi_lock_flag='L';
                        $up_data->save();
                    }
                    break;
                case 'N':
                    break;
                case 'S':
                    if ($sub_file_type=='P/O') {
                        // return $request;
                        $up_data=SipStpTransaction::where('folio_no',$request->folio_no)
                        ->where('product_code',$request->product_code)
                        ->update([
                            'isin_no'=>$request->new_isin_no,
                            'plan_option_flag'=>'N',
                            'plan_option_lock_flag'=>'L'
                        ]);
                    }elseif ($sub_file_type=='B') {
                        // return $request;
                        $up_data=SipStpTransaction::where('folio_no',$request->folio_no)
                            ->where('product_code',$request->product_code)
                            ->where('bu_type_flag','Y')
                            ->get();
                        // return $up_data;
                        foreach ($up_data as $key => $single_data) {
                            $single_data_update=SipStpTransaction::find($single_data->id);
                            $single_data_update->old_euin_no=$single_data_update->euin_no;
                            $single_data_update->euin_no=$request->new_euin_no;
                            $single_data_update->sub_brk_cd=NULL;
                            $single_data_update->bu_type_flag='N';
                            $single_data_update->bu_type_lock_flag='L';
                            $single_data_update->update();
                        }
                    }
                    break;
                case 'F':
                    if ($sub_file_type=='P/O') {
                        // return $request;
                        $up_data=FolioDetails::where('folio_no',$request->folio_no)
                        ->where('product_code',$request->product_code)
                        ->update([
                            'isin_no'=>$request->isin_no,
                            'plan_option_flag'=>'N',
                            'plan_option_lock_flag'=>'L'
                        ]);
                    }
                    break;
                default:
                    break;
            }

            // if ($file_type=='T') {
                // $up_data=MutualFundTransaction::find($id);
                // $up_data->divi_mismatch_flag='N';
                // $up_data->divi_lock_flag='L';
                // $up_data->save();
            // }
            // $up_data=MutualFundTransaction::where('folio_no',$request->folio_no)
            //     ->where('product_code',$request->product_code)
            //     ->where('euin_no',$request->euin_no)
            //     ->update([
            //         'old_euin_no'=>$request->euin_no,
            //         'euin_no'=>$request->new_euin_no,
            //         'bu_type_flag'=>'N',
            //         'bu_type_lock_flag'=>'L'
            //     ]);


        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($up_data);
    }

    public function unlockTransaction(Request $request)
    {
        try {
            // return $request;
            $id=$request->id;
            // return $id;
            $up_data=MutualFundTransaction::find($id);
            if ($up_data->divi_lock_flag=='L') {
                $up_data->divi_mismatch_flag='Y';
                $up_data->divi_lock_flag='N';
            }
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

    public function misMatchSipStp(Request $request)
    {
        try {
            $mismatch_flag=$request->mismatch_flag;
            // return $mismatch_flag;
            $rawQuery='';
            if ($mismatch_flag=='A') {
                $rawQuery="amc_flag='Y'";
            }elseif ($mismatch_flag=='S') {
                $rawQuery="scheme_flag='Y'";
            }elseif ($mismatch_flag=='P/O') {
                $rawQuery="plan_option_flag='Y'";
            }elseif ($mismatch_flag=='B') {
                $rawQuery="bu_type_flag='Y'";
            }elseif ($mismatch_flag=='F') {
                $rawQuery="freq_mismatch_flag='Y'";
            }

            $data=[];
            // $data=SipStpTransaction::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_sip_stp_trans.product_code')
            //     ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
            //     ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
            //     ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
            //     ->leftJoin('md_amc','md_amc.amc_code','=','td_sip_stp_trans.amc_code')
            //     ->leftJoin('md_amc as md_amc_1','md_amc_1.amc_code','=','td_sip_stp_trans.amc_code')
            //     ->leftJoin('md_systematic_trans_type','md_systematic_trans_type.trans_type_code','=','td_sip_stp_trans.auto_trans_type')
            //     ->select('td_sip_stp_trans.*','md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name',
            //     'md_amc.amc_short_name as amc_name','md_amc_1.amc_short_name as amc_short_name',
            //     'md_systematic_trans_type.trans_type','md_systematic_trans_type.trans_sub_type')
            //     ->whereRaw($rawQuery)
            //     ->get();

            $my_datas=SipStpSwpReport::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','tt_sip_stp_swp_report.product_code')
                ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                ->leftJoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
                ->leftJoin('md_option','md_option.id','=','md_scheme_isin.option_id')
                ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
                ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                ->leftJoin('md_amc','md_amc.amc_code','=','tt_sip_stp_swp_report.amc_code')
                ->leftJoin('md_amc as md_amc_1','md_amc_1.amc_code','=','tt_sip_stp_swp_report.amc_code')
                ->leftJoin('md_scheme_isin as to_isin','to_isin.product_code','=','tt_sip_stp_swp_report.to_product_code')
                ->leftJoin('md_scheme as to_scheme','to_scheme.id','=','to_isin.scheme_id')
                ->leftJoin('md_category as to_category','to_category.id','=','to_scheme.category_id')
                ->leftJoin('md_subcategory as to_subcategory','to_subcategory.id','=','to_scheme.subcategory_id')
                ->leftJoin('md_employee','md_employee.euin_no','=','tt_sip_stp_swp_report.euin_no')
                ->leftJoin('md_branch','md_branch.id','=','md_employee.branch_id')
                ->leftJoin('md_systematic_trans_type','md_systematic_trans_type.trans_type_code','=','tt_sip_stp_swp_report.auto_trans_type')
                ->select('tt_sip_stp_swp_report.*','tt_sip_stp_swp_report.period_day as sip_date','tt_sip_stp_swp_report.auto_amount as amount','tt_sip_stp_swp_report.cease_terminate_date as terminated_date',
                'tt_sip_stp_swp_report.pause_from_date as pause_start_date','tt_sip_stp_swp_report.pause_to_date as pause_end_date','tt_sip_stp_swp_report.bank as bank_name','tt_sip_stp_swp_report.instrm_no as acc_no',
                'md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name',
                'md_amc.amc_short_name as amc_name','md_amc_1.amc_short_name as amc_short_name','md_plan.plan_name','md_option.opt_name as option_name',
                'md_employee.emp_name as rm_name','md_branch.brn_name as branch_name','md_employee.bu_type_id as bu_type_id','md_employee.branch_id as branch_id',
                'md_systematic_trans_type.trans_type','md_systematic_trans_type.trans_sub_type',
                'to_scheme.scheme_name as to_scheme_name','to_category.cat_name as to_cat_name','to_subcategory.subcategory_name as to_subcat_name')
                ->selectRaw('(select `bu_type` from `md_business_type` where `bu_code`=md_employee.bu_type_id and `branch_id`=md_employee.branch_id limit 1) as bu_type')
                ->selectRaw('(select `freq_name` from `md_systematic_frequency` where `rnt_id`=tt_sip_stp_swp_report.rnt_id and `freq_code`=tt_sip_stp_swp_report.periodicity limit 1) as freq')
                ->whereRaw($rawQuery)
                // ->take(50)
                ->get();
        // return  $my_datas;  
        // dd(DB::getQueryLog());

        foreach ($my_datas as $key => $my_data) {
            $my_data->reg_no =$my_data->auto_trans_no;
            if ($my_data->rnt_id==2) {
                if(($my_data->frequency=="Daily") || ($my_data->frequency=="WEEKLY") || ($my_data->frequency=="Fortnightly")){
                    $my_data->sip_date=$my_data->frequency;
                    $my_data->stp_date=$my_data->frequency;
                    $my_data->swp_date=$my_data->frequency;
                }else {
                    $my_data->sip_date=number_format((float)date('d',strtotime($my_data->from_date)), 0, '.', '');
                    $my_data->stp_date=number_format((float)date('d',strtotime($my_data->from_date)), 0, '.', '');
                    $my_data->swp_date=number_format((float)date('d',strtotime($my_data->from_date)), 0, '.', '');
                }
                $my_data->freq=$my_data->frequency;
                $my_data->duration =$my_data->no_of_installment;
            }elseif ($my_data->rnt_id==1) {
                // return $my_data;
                if(($my_data->freq=="Daily") || ($my_data->freq=="Weekly") || ($my_data->freq=="Fortnightly")){
                    // return $my_data;
                    $my_data->sip_date=$my_data->freq;
                    $my_data->stp_date=$my_data->freq;
                    $my_data->swp_date=$my_data->freq;
                }else {
                    $my_data->sip_date=number_format((float)$my_data->period_day, 0, '.', '');
                    $my_data->stp_date=number_format((float)$my_data->period_day, 0, '.', '');
                    $my_data->swp_date=number_format((float)$my_data->period_day, 0, '.', '');
                }

                // if ($my_data->auto_trans_type=='P') {
                //     if(($my_data->freq=="Daily") || ($my_data->freq=="Weekly") || ($my_data->freq=="Fortnightly")){
                //         $my_data->from_date = $my_data->freq;
                //     }else {
                //         $my_data->from_date =date('Y-m', strtotime('+1 month', (strtotime($my_data->reg_date)))).'-'.$my_data->period_day;
                //     }
                // }
                /***************for duration calculation****************************/
                if ($my_data->auto_trans_type=='R' || $my_data->auto_trans_type=='SWP') {  // if swp 
                    $my_data->duration =(int)abs((strtotime($my_data->from_date) - strtotime($my_data->to_date))/(60*60*24*30));
                }else {
                    $calculation_day =(int)abs((strtotime($my_data->reg_date) - strtotime($my_data->from_date))/(60*60*24));
                    $my_data->calculation_day =$calculation_day;
                    if ($calculation_day <= 30) {
                        $my_data->duration =(int)abs((strtotime($my_data->from_date) - strtotime($my_data->to_date))/(60*60*24*30));
                    }else {
                        $my_data->duration =(int)abs((strtotime($my_data->reg_date) - strtotime($my_data->to_date))/(60*60*24*30));
                    }
                }
            }
            if ($my_data->pause_end_date!='' && $my_data->pause_end_date < date('Y-m-d')) {  // if pause to date more then to date
                $my_data->pause_start_date=NULL;
                $my_data->pause_end_date=NULL;
            }
            array_push($data,$my_data);
        }
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function misMatchFolio(Request $request)
    {
        try {
            $mismatch_flag=$request->mismatch_flag;
            // return $mismatch_flag;
            $rawQuery='';
            if ($mismatch_flag=='A') {
                $rawQuery="amc_flag='Y'";
            }elseif ($mismatch_flag=='S') {
                $rawQuery="scheme_flag='Y'";
            }elseif ($mismatch_flag=='P/O') {
                $rawQuery="plan_option_flag='Y'";
            }
            $data=[];
            $data=FolioDetailsReport::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','tt_folio_details_reports.product_code')
                ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                ->leftJoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
                ->leftJoin('md_option','md_option.id','=','md_scheme_isin.option_id')
                ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
                ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                ->leftJoin('md_amc','md_amc.amc_code','=','tt_folio_details_reports.amc_code')
                ->leftJoin('md_employee','md_employee.euin_no','=','tt_folio_details_reports.euin_no')
                ->leftJoin('md_branch','md_branch.id','=','md_employee.branch_id')
                ->leftJoin('md_pincode','md_pincode.id','=','tt_folio_details_reports.pincode')
                ->leftJoin('md_states','md_states.id','=','md_pincode.state_id')
                ->leftJoin('md_city_type','md_city_type.id','=','md_pincode.city_type_id')
                ->leftJoin('md_deposit_bank','md_deposit_bank.ifs_code','=','tt_folio_details_reports.bank_ifsc')
                ->select('tt_folio_details_reports.*','md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name',
                'md_amc.amc_short_name as amc_short_name','md_states.name as state','md_city_type.name as city_type',
                'md_employee.emp_name as rm_name','md_branch.brn_name as branch_name','md_employee.bu_type_id as bu_type_id','md_employee.branch_id as branch_id',
                'md_plan.plan_name','md_option.opt_name as option_name','md_deposit_bank.micr_code as bank_micr')
                ->selectRaw('(select `bu_type` from `md_business_type` where `bu_code` =md_employee.bu_type_id and `branch_id` =md_employee.branch_id limit 1) as bu_type')

                ->selectRaw('IF(tt_folio_details_reports.rnt_id=2,
                (CASE 
                    WHEN tt_folio_details_reports.pa_link_ststus_1st="Y" THEN "Aadhaar Linked"
                    WHEN tt_folio_details_reports.pa_link_ststus_1st="N" THEN "Aadhaar Not Linked"
                    WHEN tt_folio_details_reports.pa_link_ststus_1st="Blank" || pa_link_ststus_1st="BLANK" THEN "Not Applicable"
                    ELSE ""
                END),
                tt_folio_details_reports.pa_link_ststus_1st) as pa_link_ststus_1st')
                ->selectRaw('IF(tt_folio_details_reports.rnt_id=2,
                (CASE 
                    WHEN tt_folio_details_reports.pa_link_ststus_2nd="Y" THEN "Aadhaar Linked"
                    WHEN tt_folio_details_reports.pa_link_ststus_2nd="N" THEN "Aadhaar Not Linked"
                    WHEN tt_folio_details_reports.pa_link_ststus_2nd="Blank" || pa_link_ststus_2nd="BLANK" THEN "Not Applicable"
                    ELSE ""
                END),
                tt_folio_details_reports.pa_link_ststus_2nd) as pa_link_ststus_2nd')
                ->selectRaw('IF(tt_folio_details_reports.rnt_id=2,
                (CASE 
                    WHEN tt_folio_details_reports.pa_link_ststus_3rd="Y" THEN "Aadhaar Linked"
                    WHEN tt_folio_details_reports.pa_link_ststus_3rd="N" THEN "Aadhaar Not Linked"
                    WHEN tt_folio_details_reports.pa_link_ststus_3rd="Blank" || pa_link_ststus_3rd="BLANK" THEN "Not Applicable"
                    ELSE ""
                END),
                tt_folio_details_reports.pa_link_ststus_3rd) as pa_link_ststus_3rd')
                ->selectRaw('IF(tt_folio_details_reports.rnt_id=2,
                (CASE 
                    WHEN tt_folio_details_reports.guardian_pa_link_ststus="Y" THEN "Aadhaar Linked"
                    WHEN tt_folio_details_reports.guardian_pa_link_ststus="N" THEN "Aadhaar Not Linked"
                    WHEN tt_folio_details_reports.guardian_pa_link_ststus="Blank" || guardian_pa_link_ststus="BLANK" THEN "Not Applicable"
                    ELSE ""
                END),
                tt_folio_details_reports.guardian_pa_link_ststus) as guardian_pa_link_ststus')
                ->whereRaw($rawQuery)
                ->groupBy('tt_folio_details_reports.product_code')
                ->groupBy('tt_folio_details_reports.folio_no')
                // ->take(100)
                ->get();
            // dd(DB::getQueryLog());
            // $data=FolioDetails::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_folio_details.product_code')
            //     ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
            //     ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
            //     ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
            //     ->leftJoin('md_amc','md_amc.amc_code','=','td_folio_details.amc_code')
            //     ->select('td_folio_details.*','md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name',
            //     'md_amc.amc_short_name as amc_short_name')
            //     ->whereRaw($rawQuery)
            //     ->get();
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }


    public function allMismatch(Request $request)
    {
        try {
            $amc_count=MutualFundTransaction::where('delete_flag','N')->where('amc_flag','Y')->count();
            $scheme_count=MutualFundTransaction::where('delete_flag','N')->where('scheme_flag','Y')->count();
            $plan_option_count=MutualFundTransaction::where('delete_flag','N')->where('plan_option_flag','Y')->count();
            $idcw_count=MutualFundTransaction::where('delete_flag','N')->where('divi_mismatch_flag','Y')->count();

            $nav_amc_count=NAVDetails::where('amc_flag','Y')->count();
            $nav_scheme_count=NAVDetails::where('scheme_flag','Y')->count();
                        
            $sip_amc_count=SipStpTransaction::where('amc_flag','Y')->count();
            $sip_scheme_count=SipStpTransaction::where('scheme_flag','Y')->count();

            $folio_amc_count=FolioDetails::where('amc_flag','Y')->count();
            $folio_scheme_count=FolioDetails::where('scheme_flag','Y')->count();

            $data=[
                [   
                    'id'=>1,
                    'tab_name'=>'Transaction File',
                    'file_type'=>[
                        [
                            'name'=>'AMC',
                            'count'=>$amc_count
                        ],
                        [
                            'name'=>'SCHEME',
                            'count'=>$scheme_count
                        ],
                        [
                            'name'=>'PLAN & OPTION',
                            'count'=>$plan_option_count
                        ],
                        [
                            'name'=>'IDCW',
                            'count'=>$idcw_count
                        ]
                    ]
                ],[   
                    'id'=>2,
                    'tab_name'=>'NAV File',
                    'file_type'=>[
                        [
                            'name'=>'AMC',
                            'count'=>$nav_amc_count
                        ],
                        [
                            'name'=>'SCHEME',
                            'count'=>$nav_scheme_count
                        ]
                    ]
                ],[   
                    'id'=>3,
                    'tab_name'=>'SIP/STP/SWP File',
                    'file_type'=>[
                        [
                            'name'=>'AMC',
                            'count'=>$sip_amc_count
                        ],
                        [
                            'name'=>'SCHEME',
                            'count'=>$sip_scheme_count
                        ]
                    ]
                ],[   
                    'id'=>4,
                    'tab_name'=>'Folio Master File',
                    'file_type'=>[
                        [
                            'name'=>'AMC',
                            'count'=>$folio_amc_count
                        ],
                        [
                            'name'=>'SCHEME',
                            'count'=>$folio_scheme_count
                        ]
                    ]
                ]         
                
            ];
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}
