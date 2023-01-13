<?php

namespace App\Http\Controllers\v1\Operation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{KYC,Client};
use Validator;

class KYCController extends Controller
{
    public function index(Request $request)
    {
        try {  
            $search=$request->search;
            if ($search!='') {
                $data=KYC::where('client_code',$search)
                    // ->orWhere('mobile','like', '%' . $search . '%')
                    // ->orWhere('email','like', '%' . $search . '%')
                    ->get();      
            }else{
                $data=KYC::whereDate('updated_at',date('Y-m-d'))->get();      
            }
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function showAdd(Request $request)
    {
        try {  
            $search=$request->search;
            $data=Client::with('ClientDoc')->where('client_code',$search)
                // ->orWhere('mobile','like', '%' . $search . '%')
                // ->orWhere('email','like', '%' . $search . '%')
                ->get();      
           
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function createUpdate(Request $request)
    {
        $validator = Validator::make(request()->all(),[
            'temp_tin_id'=>'required',
            'client_code'=>'required',
            'pan_no'=>'required',
            'kyc_type'=>'required',
            'kyc_login_type'=>'required',
            'kyc_login_at'=>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            $is_has=KYC::where('pan_no',$request->pan_no)->get();
            if (count($is_has) > 0) {
                // $data=Client::find($request->id);
                // $data->brn_code=$request->brn_code;
                // $data->brn_name=$request->brn_name;
                // $data->save();
                $data='';
                return Helper::ErrorResponse('PAN no already exist.');
            }else{
                // return $request;
                $product_id=1;
                $trans_type_id=2;
                // 2
                $is_has=KYC::get();
                if (count($is_has)>0) {
                    $tin_no=Helper::GenTIN($product_id,$trans_type_id,(count($is_has)+1));
                } else {
                    $tin_no=Helper::GenTIN($product_id,$trans_type_id,1);
                }

                $data=KYC::create(array(
                    'temp_tin_id'=>$request->temp_tin_id,
                    'tin_no'=>$tin_no,
                    'entry_dt'=>date('Y-m-d'),
                    'client_code'=>$request->client_code,
                    'pan_no'=>$request->pan_no,
                    'present_kyc_status'=>'D',
                    'kyc_type'=>$request->kyc_type,
                    'kyc_login_type'=>$request->kyc_login_type,
                    'kyc_login_at'=>$request->kyc_login_at,
                    'form_scan_status'=>'A',
                    'final_kyc_status'=>'U',
                    'branch_code'=>1,
                    // 'created_by'=>'',
                ));    
            }  
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    
}