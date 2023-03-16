<?php

namespace App\Http\Controllers\v1\INSOperation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{InsProduct,InsFormReceived};
use Validator;

class FormReceivedController extends Controller
{
    //


    public function create(Request $request)
    {
        $validator = Validator::make(request()->all(),[
            'euin_no' =>'required',
            'product_id' =>'required',
            'bu_type' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            // return $request;
            $is_has=InsFormReceived::orderBy('created_at','desc')->get();
            if (count($is_has)>0) {
                $temp_tin_no=Helper::TempTINGen((count($is_has)+1),1); // generate temp tin no
            }else{
                $temp_tin_no=Helper::TempTINGen(1,1); // generate temp tin no
            }
            
                // $bu_type='D';
                $arn_no=Helper::CommonParamValue(1);
                // $euin_to=Helper::CommonParamValue(2);
                $branch_code=1;
                $data=InsFormReceived::create(array(
                    'rec_datetime'=>date('Y-m-d H:i:s'),
                    'temp_tin_no'=>$temp_tin_no,
                    'bu_type'=>$request->bu_type,
                    'arn_no'=>$arn_no,
                    'euin_no'=>$request->euin_no,
                    'sub_arn_no'=>isset($request->sub_arn_no)?$request->sub_arn_no:NULL,
                    'sub_brk_cd'=>isset($request->sub_brk_cd)?$request->sub_brk_cd:NULL,
                    'proposer_id'=>$request->proposer_id,
                    'insure_bu_type'=>$request->insure_bu_type,
                    'product_id'=>$request->product_id,
                    'recv_from'=>$request->recv_from,
                    'proposal_no'=>isset($request->proposal_no)?$request->proposal_no:NULL,
                    'branch_code'=>$branch_code,
                    // 'created_by'=>'',
                ));      
              
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function update(Request $request)
    {
        $validator = Validator::make(request()->all(),[
            'temp_tin_no' =>'required',
            'product_id' =>'required',
            'trans_id' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
    
        try {
            return $request;
                // $bu_type='D';
                $arn_no=Helper::CommonParamValue(1);
                // $euin_to=Helper::CommonParamValue(2);
                $branch_code=1;
                $data=InsFormReceived::where('temp_tin_no',$request->temp_tin_no)->update([
                    'bu_type'=>$request->bu_type,
                    'arn_no'=>$arn_no,
                    'euin_no'=>$request->euin_no,
                    'sub_arn_no'=>isset($request->sub_arn_no)?$request->sub_arn_no:NULL,
                    'sub_brk_cd'=>isset($request->sub_brk_cd)?$request->sub_brk_cd:NULL,
                    'client_id'=>$request->client_id,
                    'product_id'=>$request->product_id,
                    'trans_id'=>$request->trans_id,
                    'scheme_id'=>$request->scheme_id,
                    'recv_from'=>$request->recv_from,
                    'inv_type'=>$request->inv_type,
                    'application_no'=>isset($request->application_no)?$request->application_no:NULL,
                    'kyc_status'=>$request->kyc_status,
                    'branch_code'=>$branch_code,
                    // 'created_by'=>'',
                ]);      
            $data=InsFormReceived::where('temp_tin_no',$request->temp_tin_no)->first();
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function delete(Request $request){
        $validator = Validator::make(request()->all(),[
            'temp_tin_no' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            // return $request;
            $data=MutualFund::where('temp_tin_no',$request->temp_tin_no)->get();
            if (count($data)>0) {
                $msg='Not delete';
                return Helper::ErrorResponse($msg);
            }else {
                $data=InsFormReceived::where('temp_tin_no',$request->temp_tin_no)->update([
                    'deleted_at'=>date('Y-m-d H:i:s'),
                    'deleted_by'=>1,
                    'deleted_flag'=>'Y',
                ]);
            }
              
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}
