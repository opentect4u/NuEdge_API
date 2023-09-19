<?php

namespace App\Http\Controllers\v1\Cron;

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
    MFTransTypeSubType
};
use Validator;
use Illuminate\Support\Carbon;
use Excel;
use DB;
use File;

class TransFileUploadController extends Controller
{
    public function upload(Request $request)
    {
        try {
            // KFINTECH transction file MFSD201  for CSV file
            // return $request;
           
            

            $file_name='C:\Users\Chitta\Documents\Nuedge-Online\Mailback_file\Historical_NAV_Report\15_09_2023\4171025632029053B_4FDNLF7W0YFBDIMK7H1MIJFD1P659625451417BMB154520936R1\15092023170037_154520936R1.csv';
            // $file_name='C:\Users\Chitta\Documents\Nuedge-Online\Mailback_file\Historical_NAV_Report\15_09_2023\4171025632029053B_4FDNLF7W0YFBDIMK7H1MIJFD1P659625451417BMB154520936R1\15092023170037_154520936R1 - Copy.csv';
            // $file_name=public_path('mailback/autoupload/'.$upload_file_name);
            // return  $file_name;
            // $handle = fopen($file_name, "r");
            // $raw_string = fgets($handle);
            // $row = str_getcsv($raw_string);
            // return count($row);
            $datas = Excel::toArray([],  $file_name);
            return $datas[0];
            // $TotalArray =  array_map('str_getcsv', file($file_name));
            $TotalArray = array_map(function($v){return str_getcsv($v, ";");}, file($file_name));

            // return $TotalArray;
            return count($TotalArray);
            // return $TotalArray[0];
            $start_count=1;
            $end_count=count($TotalArray);
            if ($end_count==count($TotalArray) || $end_count >= count($TotalArray)) {
                $end_count=count($TotalArray)-1;
            }
            // return  $end_count;

            $up_data=MailbackProcess::find($id);
            $up_data->total_count=count($TotalArray);
            $up_data->save();


            // 'TRUNCATE TABLE `admin_nuedge`.`tt_mutual_fund_trans`'

                // if ($file_type_id==1 && $file_id=3) {  // transction MFSD201
                    for ($i=$start_count; $i <= $end_count; $i++) { 
                        // return $TotalArray[$i];
                        // return $TotalArray[$i][0];
                        // return $TotalArray[0][0];
                        // if ($i >100) {
                        //     break;
                        // }
                        TempMutualFundTransaction::create(array(
                            'rnt_id'=>$rnt_id,
                            'arn_no'=>$TotalArray[$i][19],
                            'sub_brk_cd'=>isset($TotalArray[$i][20])?$TotalArray[$i][20]:NULL,
                            'euin_no'=>isset($TotalArray[$i][70])?$TotalArray[$i][70]:NULL,
                            'first_client_name'=>$TotalArray[$i][9],
                            'first_client_pan'=>$TotalArray[$i][47],
                            'amc_code'=>$TotalArray[$i][1],
                            'folio_no'=>$TotalArray[$i][2],
                            'product_code'=>$TotalArray[$i][0],
                            'trans_no'=>$TotalArray[$i][6],
                            'trans_mode'=>$TotalArray[$i][10],
                            'trans_status'=>$TotalArray[$i][11],
                            'user_trans_no'=>$TotalArray[$i][39],
                            'trans_date'=>Carbon::parse(str_replace("/","-",$TotalArray[$i][14]))->format('Y-m-d H:i:s'),
                            'post_date'=>Carbon::parse(str_replace("/","-",$TotalArray[$i][15]))->format('Y-m-d H:i:s'),
                            'pur_price'=>$TotalArray[$i][16],
                            'units'=>$TotalArray[$i][17],
                            'amount'=>$TotalArray[$i][18],
                            'rec_date'=>Carbon::parse(str_replace("/","-",$TotalArray[$i][24]))->format('Y-m-d H:i:s'),
                            'kf_trans_type'=>$TotalArray[$i][30],
                            'trans_flag'=>$TotalArray[$i][37],
                            'trans_desc'=>$TotalArray[$i][29], // Transction Description
                            'te_15h'=>NULL,
                            'micr_code'=>NULL,
                            'sw_flag'=>NULL,
                            'old_folio'=>NULL,
                            'seq_no'=>NULL,
                            'reinvest_flag'=>NULL,
                            'stt'=>isset($TotalArray[$i][40])?$TotalArray[$i][40]:NULL,
                            'stamp_duty'=>isset($TotalArray[$i][85])?$TotalArray[$i][85]:NULL,
                            'tds'=>isset($TotalArray[$i][52])?$TotalArray[$i][52]:NULL,
                            'acc_no'=>isset($TotalArray[$i][78])?$TotalArray[$i][78]:NULL,
                            'bank_name'=>isset($TotalArray[$i][64])?$TotalArray[$i][64]:NULL,
                            'remarks'=>isset($TotalArray[$i][48])?$TotalArray[$i][48]:NULL,
                            'dividend_option'=>isset($TotalArray[$i][33])?$TotalArray[$i][33]:NULL,
                            'isin_no'=>isset($TotalArray[$i][66])?$TotalArray[$i][66]:NULL,
                        ));

                        $up_data=MailbackProcess::find($id);
                        $up_data->process_count=$i;
                        $up_data->save();
                    }
                // }else {
                //     # code...
                // }

            
            $dataArray=[];
            // $dataArray['upload_data']=$upload_data;
            $dataArray['start_count']=$start_count;
            $dataArray['end_count']=$end_count;
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

    public function upload___(Request $request)
    {
        try {
            // KFINTECH transction file MFSD201  for CSV file
            // return $request;
            $rnt_id=2;
            $file_type_id=1;
            $file_id=3;
            $upload_file=$request->upload_file;

            $folderPath='C:\Users\Chitta\OneDrive\Documents\KF\\';
            $countFile=0;
            $totalFiles=glob($folderPath."*");
            // return $totalFiles;
            if($totalFiles){
                $countFile=count($totalFiles);
            }
            // return $countFile;
            foreach ($totalFiles as $filename) {
                $fileName = pathinfo($filename);
                $extension=$fileName['extension'];
                $original_filename=$fileName['filename'];
                return $extension." - ".$original_filename;
            }
 
            $upload_file_name=(microtime(true) * 10000)."_".$rnt_id.".csv";
            // $filecv='C:\Users\Chitta\OneDrive\Documents\KF\MFSD201_200.csv';
            // $filecv='C:\Users\Chitta\Documents\Nuedge-Online\MFSD201_WBTRN11215159_1749006538766RN1121515984\1749006538766RN1121515984.csv';
            $filecv='C:\Users\Administrator\Documents\KF\1749006538766RN1121515984.csv';
            $original_file_name='1749006538766RN1121515984.csv';
            if (file_exists($filecv) != null) {
                File::copy($filecv, public_path('mailback/autoupload/'.$upload_file_name));
                // unlink($filecv);
                // $create_dt=MailbackProcess::create(array(
                //     'rnt_id'=>$rnt_id,
                //     'file_type_id'=>$file_type_id,
                //     'file_id'=>$file_id,
                //     'original_file_name'=>$original_file_name,
                //     'upload_file'=>$upload_file_name,
                //     'process_date'=>date('Y-m-d H:i:s'),
                //     'process_type'=>'A',
                // ));
                // $id=$create_dt->id;
            }
            // return $request;

            $file_name=public_path('mailback/autoupload/'.$upload_file_name);
            // return  $file_name;

            // $TotalArray =  array_map('str_getcsv', file($file_name));
            $TotalArray = array_map(function($v){return str_getcsv($v, ";");}, file($file_name));

            // return $TotalArray;
            return count($TotalArray);
            // return $TotalArray[0];
            $start_count=1;
            $end_count=count($TotalArray);
            if ($end_count==count($TotalArray) || $end_count >= count($TotalArray)) {
                $end_count=count($TotalArray)-1;
            }
            // return  $end_count;

            $up_data=MailbackProcess::find($id);
            $up_data->total_count=count($TotalArray);
            $up_data->save();


            // 'TRUNCATE TABLE `admin_nuedge`.`tt_mutual_fund_trans`'

                // if ($file_type_id==1 && $file_id=3) {  // transction MFSD201
                    for ($i=$start_count; $i <= $end_count; $i++) { 
                        // return $TotalArray[$i];
                        // return $TotalArray[$i][0];
                        // return $TotalArray[0][0];
                        // if ($i >100) {
                        //     break;
                        // }
                        TempMutualFundTransaction::create(array(
                            'rnt_id'=>$rnt_id,
                            'arn_no'=>$TotalArray[$i][19],
                            'sub_brk_cd'=>isset($TotalArray[$i][20])?$TotalArray[$i][20]:NULL,
                            'euin_no'=>isset($TotalArray[$i][70])?$TotalArray[$i][70]:NULL,
                            'first_client_name'=>$TotalArray[$i][9],
                            'first_client_pan'=>$TotalArray[$i][47],
                            'amc_code'=>$TotalArray[$i][1],
                            'folio_no'=>$TotalArray[$i][2],
                            'product_code'=>$TotalArray[$i][0],
                            'trans_no'=>$TotalArray[$i][6],
                            'trans_mode'=>$TotalArray[$i][10],
                            'trans_status'=>$TotalArray[$i][11],
                            'user_trans_no'=>$TotalArray[$i][39],
                            'trans_date'=>Carbon::parse(str_replace("/","-",$TotalArray[$i][14]))->format('Y-m-d H:i:s'),
                            'post_date'=>Carbon::parse(str_replace("/","-",$TotalArray[$i][15]))->format('Y-m-d H:i:s'),
                            'pur_price'=>$TotalArray[$i][16],
                            'units'=>$TotalArray[$i][17],
                            'amount'=>$TotalArray[$i][18],
                            'rec_date'=>Carbon::parse(str_replace("/","-",$TotalArray[$i][24]))->format('Y-m-d H:i:s'),
                            'kf_trans_type'=>$TotalArray[$i][30],
                            'trans_flag'=>$TotalArray[$i][37],
                            'trans_desc'=>$TotalArray[$i][29], // Transction Description
                            'te_15h'=>NULL,
                            'micr_code'=>NULL,
                            'sw_flag'=>NULL,
                            'old_folio'=>NULL,
                            'seq_no'=>NULL,
                            'reinvest_flag'=>NULL,
                            'stt'=>isset($TotalArray[$i][40])?$TotalArray[$i][40]:NULL,
                            'stamp_duty'=>isset($TotalArray[$i][85])?$TotalArray[$i][85]:NULL,
                            'tds'=>isset($TotalArray[$i][52])?$TotalArray[$i][52]:NULL,
                            'acc_no'=>isset($TotalArray[$i][78])?$TotalArray[$i][78]:NULL,
                            'bank_name'=>isset($TotalArray[$i][64])?$TotalArray[$i][64]:NULL,
                            'remarks'=>isset($TotalArray[$i][48])?$TotalArray[$i][48]:NULL,
                            'dividend_option'=>isset($TotalArray[$i][33])?$TotalArray[$i][33]:NULL,
                            'isin_no'=>isset($TotalArray[$i][66])?$TotalArray[$i][66]:NULL,
                        ));

                        $up_data=MailbackProcess::find($id);
                        $up_data->process_count=$i;
                        $up_data->save();
                    }
                // }else {
                //     # code...
                // }

            
            $dataArray=[];
            // $dataArray['upload_data']=$upload_data;
            $dataArray['start_count']=$start_count;
            $dataArray['end_count']=$end_count;
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

    public function upload_csv(Request $request)
    {
        try {
            // KFINTECH transction file MFSD201  for CSV file
            // return $request;
            $rnt_id=2;
            $file_type_id=1;
            $file_id=3;
            $upload_file=$request->upload_file;

            $upload_file_name=(microtime(true) * 10000)."_".$rnt_id.".csv";
            // $filecv='C:\Users\Chitta\OneDrive\Documents\KF\MFSD201_200.csv';
            // $filecv='C:\Users\Chitta\Documents\Nuedge-Online\MFSD201_WBTRN11215159_1749006538766RN1121515984\1749006538766RN1121515984.csv';
            $filecv='C:\Users\Administrator\Documents\KF\1749006538766RN1121515984.csv';
            $original_file_name='1749006538766RN1121515984.csv';
            if (file_exists($filecv) != null) {
                File::copy($filecv, public_path('mailback/autoupload/'.$upload_file_name));
                // unlink($filecv);
                // $create_dt=MailbackProcess::create(array(
                //     'rnt_id'=>$rnt_id,
                //     'file_type_id'=>$file_type_id,
                //     'file_id'=>$file_id,
                //     'original_file_name'=>$original_file_name,
                //     'upload_file'=>$upload_file_name,
                //     'process_date'=>date('Y-m-d H:i:s'),
                //     'process_type'=>'A',
                // ));
                // $id=$create_dt->id;
            }
            // return $request;

            $file_name=public_path('mailback/autoupload/'.$upload_file_name);
            // return  $file_name;

            // $TotalArray =  array_map('str_getcsv', file($file_name));
            $TotalArray = array_map(function($v){return str_getcsv($v, ";");}, file($file_name));

            // return $TotalArray;
            return count($TotalArray);
            // return $TotalArray[0];
            $start_count=1;
            $end_count=count($TotalArray);
            if ($end_count==count($TotalArray) || $end_count >= count($TotalArray)) {
                $end_count=count($TotalArray)-1;
            }
            // return  $end_count;

            $up_data=MailbackProcess::find($id);
            $up_data->total_count=count($TotalArray);
            $up_data->save();


            // 'TRUNCATE TABLE `admin_nuedge`.`tt_mutual_fund_trans`'

                // if ($file_type_id==1 && $file_id=3) {  // transction MFSD201
                    for ($i=$start_count; $i <= $end_count; $i++) { 
                        // return $TotalArray[$i];
                        // return $TotalArray[$i][0];
                        // return $TotalArray[0][0];
                        // if ($i >100) {
                        //     break;
                        // }
                        TempMutualFundTransaction::create(array(
                            'rnt_id'=>$rnt_id,
                            'arn_no'=>$TotalArray[$i][19],
                            'sub_brk_cd'=>isset($TotalArray[$i][20])?$TotalArray[$i][20]:NULL,
                            'euin_no'=>isset($TotalArray[$i][70])?$TotalArray[$i][70]:NULL,
                            'first_client_name'=>$TotalArray[$i][9],
                            'first_client_pan'=>$TotalArray[$i][47],
                            'amc_code'=>$TotalArray[$i][1],
                            'folio_no'=>$TotalArray[$i][2],
                            'product_code'=>$TotalArray[$i][0],
                            'trans_no'=>$TotalArray[$i][6],
                            'trans_mode'=>$TotalArray[$i][10],
                            'trans_status'=>$TotalArray[$i][11],
                            'user_trans_no'=>$TotalArray[$i][39],
                            'trans_date'=>Carbon::parse(str_replace("/","-",$TotalArray[$i][14]))->format('Y-m-d H:i:s'),
                            'post_date'=>Carbon::parse(str_replace("/","-",$TotalArray[$i][15]))->format('Y-m-d H:i:s'),
                            'pur_price'=>$TotalArray[$i][16],
                            'units'=>$TotalArray[$i][17],
                            'amount'=>$TotalArray[$i][18],
                            'rec_date'=>Carbon::parse(str_replace("/","-",$TotalArray[$i][24]))->format('Y-m-d H:i:s'),
                            'kf_trans_type'=>$TotalArray[$i][30],
                            'trans_flag'=>$TotalArray[$i][37],
                            'trans_desc'=>$TotalArray[$i][29], // Transction Description
                            'te_15h'=>NULL,
                            'micr_code'=>NULL,
                            'sw_flag'=>NULL,
                            'old_folio'=>NULL,
                            'seq_no'=>NULL,
                            'reinvest_flag'=>NULL,
                            'stt'=>isset($TotalArray[$i][40])?$TotalArray[$i][40]:NULL,
                            'stamp_duty'=>isset($TotalArray[$i][85])?$TotalArray[$i][85]:NULL,
                            'tds'=>isset($TotalArray[$i][52])?$TotalArray[$i][52]:NULL,
                            'acc_no'=>isset($TotalArray[$i][78])?$TotalArray[$i][78]:NULL,
                            'bank_name'=>isset($TotalArray[$i][64])?$TotalArray[$i][64]:NULL,
                            'remarks'=>isset($TotalArray[$i][48])?$TotalArray[$i][48]:NULL,
                            'dividend_option'=>isset($TotalArray[$i][33])?$TotalArray[$i][33]:NULL,
                            'isin_no'=>isset($TotalArray[$i][66])?$TotalArray[$i][66]:NULL,
                        ));

                        $up_data=MailbackProcess::find($id);
                        $up_data->process_count=$i;
                        $up_data->save();
                    }
                // }else {
                //     # code...
                // }

            
            $dataArray=[];
            // $dataArray['upload_data']=$upload_data;
            $dataArray['start_count']=$start_count;
            $dataArray['end_count']=$end_count;
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

    public function upload_txt_format(Request $request)
    {
        try {
            // KFINTECH transction file MFSD201 txt format
            // return $request;
            $rnt_id=2;
            $file_type_id=1;
            $file_id=3;
            $upload_file=$request->upload_file;

            $upload_file_name=(microtime(true) * 10000)."_".$rnt_id.".txt";
            $filecv='C:\Users\Chitta\OneDrive\Documents\KF\1240318598766RN1136777384_400.txt';
            $original_file_name='1240318598766RN1136777384_400.txt';
            if (file_exists($filecv) != null) {
                File::copy($filecv, public_path('mailback/autoupload/'.$upload_file_name));
                // unlink($filecv);
                $create_dt=MailbackProcess::create(array(
                    'rnt_id'=>$rnt_id,
                    'file_type_id'=>$file_type_id,
                    'file_id'=>$file_id,
                    'original_file_name'=>$original_file_name,
                    'upload_file'=>$upload_file_name,
                    'process_date'=>date('Y-m-d H:i:s'),
                    'process_type'=>'A',
                ));
                $id=$create_dt->id;
            }
            // return $request;

            $file_name=public_path('mailback/autoupload/'.$upload_file_name);
            // return  $file_name;

            $TotalArray = file($file_name,FILE_IGNORE_NEW_LINES);
            // return $TotalArray;
            // return count($TotalArray);
            // return $TotalArray[0];
            $start_count=1;
            $end_count=count($TotalArray);
            if ($end_count==count($TotalArray) || $end_count >= count($TotalArray)) {
                $end_count=count($TotalArray)-1;
            }
            // return  $end_count;

            $up_data=MailbackProcess::find($id);
            $up_data->total_count=count($TotalArray);
            $up_data->save();


            // 'TRUNCATE TABLE `admin_nuedge`.`tt_mutual_fund_trans`'

                // if ($file_type_id==1 && $file_id=3) {  // transction MFSD201
                    for ($i=$start_count; $i <= $end_count; $i++) { 
                        // if ($i >100) {
                        //     break;
                        // }
                        // $process_count=$i;
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

                        $up_data=MailbackProcess::find($id);
                        $up_data->process_count=$i;
                        $up_data->save();
                    }
                // }else {
                //     # code...
                // }

            
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
            // $dataArray['upload_data']=$upload_data;
            $dataArray['start_count']=$start_count;
            $dataArray['end_count']=$end_count;
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

    public function upload_old(Request $request)
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
            $TotalArray = file($file_name,FILE_IGNORE_NEW_LINES);
            // return $TotalArray;
            // return count($TotalArray);
            // return $TotalArray[0];
            $start_count=$request->start_count;
            $end_count=$request->end_count;
            if ($end_count==count($TotalArray) || $end_count >= count($TotalArray)) {
                $end_count=count($TotalArray)-1;
            }


            // 'TRUNCATE TABLE `admin_nuedge`.`tt_mutual_fund_trans`'
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
}
