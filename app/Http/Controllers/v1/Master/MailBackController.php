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
            }
            $upload_data=MailbackProcess::leftJoin('md_rnt','md_rnt.id','=','md_mailback_process.rnt_id')
                    ->select('md_mailback_process.*','md_rnt.rnt_name')
                    ->where('md_mailback_process.process_type','M')
                    ->where('md_mailback_process.id',$create_dt->id)
                    ->orderBy('process_date','DESC')
                    ->first();
            
            $file_name=public_path('mailback/manual/'.$upload_file_name);
            $datas = Excel::toArray([],  $file_name);
            // $datas = Excel::toArray([],  $upload_file);
            // return $datas;
            $data=$datas[0];
            // return $data[0];
            foreach ($data as $key => $value) {
                // return $data;
                if ($key > 0) {
                    // return $value;
                    // return str_replace("'","", $value[11]);
                    // return Carbon::parse(str_replace("'","",$value[11]))->format('Y-m-d H:i:s');
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
            // $data=[];
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($upload_data);
    }
}
