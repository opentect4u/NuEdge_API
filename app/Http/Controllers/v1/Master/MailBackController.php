<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{
    MutualFund,
    Client,
    FormReceived,
    MutualFundTransaction,
    MailbackProcess
};
use Validator;
use Illuminate\Support\Carbon;
use Excel;

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
                    ->select('md_mailback_process.*','md_rnt.rnt_name')
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
                $upload_file_name=(microtime(true) * 10000).".".$path_extension;
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

            // $file = fopen($file_name, 'r');
            // $myarray=[];
            // for ($i=0; $i < ($line = fgetcsv($file)); $i++) { 
            //     return $line;
            // }
            // while (($line = fgetcsv($file)) !== FALSE) {
            //     //$line is an array of the csv elements
               
            //     return $line;
            //     print_r($line);
            //     array_push($myarray,$line);
            // }
            // fclose($file);
            // return $myarray;
            // return $arrayFromCSV;

            // $datas = Excel::toArray([],  $file_name);
            // // return $datas;
            // $data=$datas[0];
            // return $data[0];
            $start_count=$request->start_count;
            $end_count=$request->end_count;
            // $end_count=500;
            $data =  array_map('str_getcsv', file($file_name));
            // return $data;
            foreach ($data as $key => $value) {
                // return $value;
                if ($key > 0) {
                    // return $value;
                    // return str_replace("'","", $value[11]);
                    // return Carbon::parse(str_replace("'","",$value[11]))->format('Y-m-d H:i:s');
                    if ($key>=$start_count && $key<=$end_count) {

                        if ($rnt_id==1) { // cams
                            if ($file_type_id==1 && $file_id=1) {  // transction  WBR2
                                MutualFundTransaction::create(array(
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
                                    'trans_date'=>Carbon::parse(str_replace("'","",$value[11]))->format('Y-m-d H:i:s'),
                                    'post_date'=>Carbon::parse(str_replace("'","",$value[12]))->format('Y-m-d H:i:s'),
                                    'pur_price'=>str_replace("'","",$value[13]),
                                    'units'=>str_replace("'","",$value[14]),
                                    'amount'=>str_replace("'","",$value[15]),
                                    'rec_date'=>Carbon::parse(str_replace("'","",$value[21]))->format('Y-m-d H:i:s'),
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
                                    'tds'=>' ',
                                    'acc_no'=>str_replace("'","",$value[63]),
                                    'bank_name'=>str_replace("'","",$value[64]),
                                ));
                            }
                        }else if($rnt_id==2){  // Kafe
                            if ($file_type_id==1 && $file_id=3) {  // transction MFSD201
                                // return Carbon::parse(str_replace("/","-",$value[49]))->format('Y-m-d H:i:s');
                                MutualFundTransaction::create(array(
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
                                    'trans_date'=>Carbon::parse(str_replace("/","-",$value[49]))->format('Y-m-d H:i:s'),
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
                                ));
                            }
                        }
                    }
                }
            }
            
            $dataArray=[];
            $dataArray['upload_data']=$upload_data;
            $dataArray['start_count']=$start_count;
            $dataArray['end_count']=$end_count;
            $dataArray['upload_file_name']=$upload_file_name;
            $dataArray['row_id']=$id;
            $dataArray['total_count']=count($data);
            $dataArray['upload_progressDtls']=json_decode($request->upload_progressDtls);

        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($dataArray);
    }

    public function createData($value)
    {
        MutualFundTransaction::create(array(
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
            'trans_date'=>Carbon::parse(str_replace("'","",$value[11]))->format('Y-m-d H:i:s'),
            'post_date'=>Carbon::parse(str_replace("'","",$value[12]))->format('Y-m-d H:i:s'),
            'pur_price'=>str_replace("'","",$value[13]),
            'units'=>str_replace("'","",$value[14]),
            'amount'=>str_replace("'","",$value[15]),
            'rec_date'=>Carbon::parse(str_replace("'","",$value[21]))->format('Y-m-d H:i:s'),
            'trans_type'=>str_replace("'","",$value[5]),
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
            'stamp_duty'=>str_replace("'","",$value[74]),
            'tds'=>' ',
            'acc_no'=>str_replace("'","",$value[63]),
            'bank_name'=>str_replace("'","",$value[64]),
        ));
    }
}
