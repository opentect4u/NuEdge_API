<?php

namespace App\Http\Controllers\v1\INSOperation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Insurance,Client,InsFormReceived};
use Validator;
use Illuminate\Support\Carbon;
use Mail;
use App\Mail\Master\SendAckEmail;
use App\Models\Email;

class AcknowledgementController extends Controller
{
    public function searchDetails(Request $request)
    {
        try {
            
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function export(Request $request)
    {
        try {
            
            
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function update(Request $request)
    {
        // return $request;
        // return Carbon::parse($request->rnt_login_cutt_off)->format('Y-m-d H:i:s');
        try {
            // return $request;
            $data1=Insurance::where('tin_no',$request->tin_no)->first();
            $ack_copy_scan=$request->ack_copy_scan;
            if ($ack_copy_scan) {
                $path_extension=$ack_copy_scan->getClientOriginalExtension();
                $ack_copy_scan_name=microtime().".".$path_extension;
                $ack_copy_scan->move(public_path('ins-acknowledgement-copy/'),$ack_copy_scan_name);
            }else{
                $ack_copy_scan_name=$data1->ack_copy_scan;
                // return $doc_name;
            }
            // ack_copy_scan
                // rnt_login_time

                if (Carbon::parse($request->rnt_login_time)->format('H') < 15) {
                    $rnt_login_cutt_off = Carbon::parse($request->rnt_login_date)->format('Y-m-d');
                }else {
                    $rnt_login_cutt_off = Carbon::parse($request->rnt_login_date);
                    $rnt_login_cutt_off->addDays(1);
                    $rnt_login_cutt_off->format("Y-m-d");
                }
                // return $rnt_login_cutt_off;
                Insurance::where('tin_no',$request->tin_no)->update(array(
                    'comp_login_dt'=>Carbon::parse($request->rnt_login_date)->format('Y-m-d').' '.Carbon::parse($request->rnt_login_time)->format('H:i:s'),
                    'comp_login_cutt_off'=>Carbon::parse($rnt_login_cutt_off)->format('Y-m-d'),
                    // 'rnt_login_cutt_off'=>$rnt_login_cutt_off,
                    'ack_copy_scan'=>$ack_copy_scan_name,
                    'ack_remarks'=>$request->ack_remarks,
                    'form_status'=>'A',
                    // 'updated_at'
                ));   
            // return $data1;
            // return $request->tin_no;
            $data=Insurance::where('tin_no',$request->tin_no)->first();
            // return $data;
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function finalSubmit(Request $request)
    {
        try {
            $trans_type_id=$request->trans_type_id;
            // return $request;
            $data=Insurance::join('td_form_received','td_form_received.temp_tin_no','=','td_mutual_fund.temp_tin_no')
                ->join('md_trans','md_trans.id','=','td_mutual_fund.trans_id')
                ->select('td_mutual_fund.*','md_trans.trns_name as trans_name','md_trans.trans_type_id as trans_type_id',
                'td_form_received.arn_no as arn_no','td_form_received.euin_no as euin_no')
                ->where('md_trans.trans_type_id',$trans_type_id)
                ->whereDate('td_mutual_fund.updated_at',date('Y-m-d'))
                ->where('td_mutual_fund.form_status','=','A')
                ->get();   

            // return $data;
            $sort_arr=[];
            foreach ($data as $key => $item) {
                $sort_arr[$item['first_client_id']][$key] = $item;
            }
            ksort($sort_arr, SORT_NUMERIC);
            // return $sort_arr;

            $data1["email"] = "cmaity905@gmail.com";
            $data1["title"] = "From NuEdge Testing";
            $data1["body"] = "This is Demo";
     
            foreach ($sort_arr as $key => $value) {
                // return $value;
                $files = [];
                foreach ($value as $key => $value1) {
                    // return $value1;
                    $filePath=public_path('acknowledgement-copy/'.$value1->ack_copy_scan);
                    // $filePath = public_path('test1.pdf');
                    $filePath1 = public_path($value1->first_client_id.'_'.$key.'_'.'encrypt_documented.pdf');
                    // return $filePath1;
                    $password='1234';
                    Helper::encrypt($filePath, $filePath1, $password);
                    // return $filePath1;
                    array_push($files,$filePath1);
                }
                // return $files;
                // ================= start mail send code =================
                // $email=Email::find(2);
                // Mail::to($request->email)->send(new SendAckEmail($client_name,$email->subject,$email->body));

                // Mail::send('emails.operation.ack-copy', $data1, function($message)use($data1, $files) {
                //     $message->to($data1["email"], $data1["email"])
                //             ->subject($data1["title"]);
                //     foreach ($files as $file){
                //         $message->attach($file);
                //     }
                    
                // });
                // ==========end mail send code ==========================
                // start remove file
                foreach ($files as $file){
                    if (file_exists($file) != null) {
                        unlink($file);
                    }
                }
                // end remove file
            }
            
        } catch (\Throwable $th) {
            //throw $th;
           $msg="Email Sending Error.";
            return Helper::ErrorResponse($msg);
        }
        return Helper::SuccessResponse($data);
    }
}
