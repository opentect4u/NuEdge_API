<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\Client;
use Validator;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        try {  
            $search=$request->search;
            $client_code=$request->client_code;
            if ($search!='') {
                $data=Client::orWhere('client_name','like', '%' . $search . '%')
                    ->orWhere('client_code','like', '%' . $search . '%')
                    ->orWhere('pan','like', '%' . $search . '%')
                    ->orWhere('mobile','like', '%' . $search . '%')
                    ->orWhere('email','like', '%' . $search . '%')
                    ->get();      
            }elseif ($client_code!='') {
                $data=Client::leftJoin('td_kyc','td_kyc.client_code','=','md_client.client_code')
                    ->select('md_client.*','td_kyc.final_kyc_status as final_kyc_status')
                    ->where('md_client.client_code',$client_code)
                    ->get();      
            } else{
                $data=Client::
                // whereDate('updated_at',date('Y-m-d'))->
                get();      
            }
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function createUpdate(Request $request)
    {
        $validator = Validator::make(request()->all(),[
            'client_name'=>'required',
            'dob'=>'required',
            'add_line_1'=>'required',
            'city'=>'required',
            'dist'=>'required',
            'state'=>'required',
            'pincode'=>'required',
            'pan'=>'required',
            'mobile'=>'required',
            'email'=>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            if ($request->id > 0) {
                // $data=Client::find($request->id);
                // $data->brn_code=$request->brn_code;
                // $data->brn_name=$request->brn_name;
                // $data->save();
                $data='';
            }else{
                $client_name=ucwords($request->client_name);
                $words = explode(" ",$client_name);
                $client_code="";
                $client_code_1 = mb_substr($words[0], 0, 1).mb_substr($words[(count($words)-1)], 0, 1);;
                
                $is_has=Client::where('client_code',$client_code)->get();
                if (count($is_has)>0) {
                    $client_code=$client_code_1.date('dmy',strtotime($request->dob))."-".count($is_has);
                }else {
                    $client_code=$client_code_1.date('dmy',strtotime($request->dob));
                }
                
                $already=Client::where('pan',$request->pan)->get();
                if (count($already)>0) {
                    $ms='PAN no already exist.';
                    return Helper::ErrorResponse($ms);
                }else{
                    $data=Client::create(array(
                        'client_code'=>$client_code,
                        'client_name'=>$client_name,
                        'dob'=>$request->dob,
                        'add_line_1'=>$request->add_line_1,
                        'add_line_2'=>$request->add_line_2,
                        'city'=>$request->city,
                        'dist'=>$request->dist,
                        'state'=>$request->state,
                        'pincode'=>$request->pincode,
                        'pan'=>$request->pan,
                        'mobile'=>$request->mobile,
                        'sec_mobile'=>$request->sec_mobile,
                        'email'=>$request->email,
                        'sec_email'=>$request->sec_email,
                        // 'created_by'=>'',
                    ));  
                }  
            }  
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    
}
