<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\Client;
use App\Models\Document;
use Validator;
use Excel;
use App\Imports\ClientImport;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        try {  
            $search=$request->search;
            $client_code=$request->client_code;
            $client_type=$request->client_type;
            $pan=$request->pan;
            $id=$request->id;
            $paginate=$request->paginate;
            if ($search!='') {
                $data=Client::orWhere('client_name','like', '%' . $search . '%')
                    ->orWhere('client_code','like', '%' . $search . '%')
                    ->orWhere('pan','like', '%' . $search . '%')
                    ->orWhere('mobile','like', '%' . $search . '%')
                    ->orWhere('email','like', '%' . $search . '%')
                    ->get();      
            }else if ($client_code!='') {
                $data=Client::leftJoin('td_kyc','td_kyc.client_code','=','md_client.client_code')
                    ->select('md_client.*','td_kyc.final_kyc_status as final_kyc_status')
                    ->where('md_client.client_code',$client_code)
                    ->get();      
            }else if ($client_type!='') {
                $data=Client::with('ClientDoc')->where('client_type',$client_type)
                    ->orderBy('updated_at','DESC')->paginate($paginate);
            }else if ($pan!='') {
                $data=Client::with('ClientDoc')->where('pan',$pan)->get();
            }else if ($id!='') {
                $data=Client::with('ClientDoc')->where('id',$id)->get();
            // }else if ($paginate!='') {
            //     $data=Client::with('ClientDoc')->paginate($paginate);    
            } else{
                $data=Client::with('ClientDoc')->
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
            // 'dob'=>'required',
            // 'add_line_1'=>'required',
            // 'city'=>'required',
            // 'dist'=>'required',
            // 'state'=>'required',
            // 'pincode'=>'required',
            // 'pan'=>'required',
            // 'mobile'=>'required',
            // 'email'=>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            if ($request->id > 0) {
                // return $request;
                if ($request->client_type=='E') {
                    // return $request;
                    $id=$request->id;
                    $client_name=ucwords($request->client_name);
                    $words = explode(" ",$client_name);
                    $client_code="";
                    $client_code_1 = mb_substr($words[0], 0, 1).mb_substr($words[(count($words)-1)], 0, 1);;
                    
                    $is_has=Client::where('client_code',$client_code)->get();
                    if (count($is_has)>0) {
                        $client_code=$client_code_1.date('dmy',strtotime($request->dob)).count($is_has);
                    }else {
                        $client_code=$client_code_1.date('dmy',strtotime($request->dob));
                    }
                    $datas=Client::find($id);
                    $datas->client_code=$client_code;
                    $datas->client_name=$client_name;
                    $datas->dob=$request->dob;
                    $datas->add_line_1=$request->add_line_1;
                    $datas->add_line_2=$request->add_line_2;
                    $datas->city=$request->city;
                    $datas->dist=$request->dist;
                    $datas->state=$request->state;
                    $datas->pincode=$request->pincode;
                    $datas->pan=$request->pan;
                    $datas->mobile=$request->mobile;
                    $datas->sec_mobile=$request->sec_mobile;
                    $datas->email=$request->email;
                    $datas->sec_email=$request->sec_email;
                    $datas->client_type='P';
                    $datas->save();

                    $doc_name='';
                        $files=$request->file;
                        // return $files;
                        if ($files!='') {
                            foreach ($files as $key => $file) {
                                // return $file;
                                if ($file) {
                                    $cv_path_extension=$file->getClientOriginalExtension();
                                    $doc_name=microtime().'_'.$datas->id.".".$cv_path_extension;
                                    $file->move(public_path('client-doc/'.$datas->id."/"),$doc_name);
                                }
                                Document::create(array(
                                    'client_id'=>$datas->id,
                                    'doc_type_id'=>$request->doc_type_id[$key],
                                    'doc_name'=>$doc_name,
                                    // 'created_by'=>'',
                                ));      
                            }
                        }
                    $data=Client::with('ClientDoc')->where('id',$datas->id)->first();    
                }else {
                    if ($request->client_type=='P') {

                    }elseif ($request->client_type=='N') {

                    }elseif ($request->client_type=='M') {
                        
                    }
                    $data='';
                }
            }else{
                if ($request->client_type=='E') {
                    $already=Client::where('pan',$request->pan)->get();
                    if (count($already)>0) {
                        $ms='PAN no already exist.';
                        return Helper::ErrorResponse($ms);
                    }else{
                        $u_data=Client::create(array(
                            'client_name'=>$request->client_name,
                            'pan'=>$request->pan,
                            'client_type'=>$request->client_type,
                            // 'created_by'=>'',
                        ));  
                    }
                    $data=Client::with('ClientDoc')->where('id',$u_data->id)->first();    
                }else {
                    $client_name=ucwords($request->client_name);
                    $words = explode(" ",$client_name);
                    $client_code="";
                    $client_code_1 = mb_substr($words[0], 0, 1).mb_substr($words[(count($words)-1)], 0, 1);;
                    
                    $is_has=Client::where('client_code',$client_code_1)->get();
                    if (count($is_has)>0) {
                        $client_code=$client_code_1.date('dmy',strtotime($request->dob)).count($is_has);
                    }else {
                        $client_code=$client_code_1.date('dmy',strtotime($request->dob));
                    }
                    
                    if ($request->client_type=='P') {
                        // return $request;
                        $already=Client::where('pan',$request->pan)->get();
                        if (count($already)>0) {
                            $ms='PAN no already exist.';
                            return Helper::ErrorResponse($ms);
                        }else{
                            // return $request;
                            $u_data=Client::create(array(
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
                                'client_type'=>$request->client_type,
                            )); 
                            $doc_name='';
                            $files=$request->file;
                            // return $files;
                            if ($files!='') {
                                foreach ($files as $key => $file) {
                                    // return $file;
                                    if ($file) {
                                        $cv_path_extension=$file->getClientOriginalExtension();
                                        $doc_name=microtime().'_'.$u_data->id.".".$cv_path_extension;
                                        $file->move(public_path('client-doc/'.$u_data->id."/"),$doc_name);
                                    }
                                    Document::create(array(
                                        'client_id'=>$u_data->id,
                                        'doc_type_id'=>$request->doc_type_id[$key],
                                        'doc_name'=>$doc_name,
                                        // 'created_by'=>'',
                                    ));      
                                }
                            }
                            $data=Client::with('ClientDoc')->where('id',$u_data->id)->first();  
                        }  
                    }else {
                        // return $request;
                        $u_data=Client::create(array(
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
                            'client_type'=>$request->client_type,
                            'guardians_pan'=>$request->guardians_pan,
                            'guardians_name'=>$request->guardians_name,
                            'relation'=>$request->relation,
                            // 'created_by'=>'',
                        ));
                        $doc_name='';
                        $files=$request->file;
                        // return $files;
                        if ($files!='') {
                            foreach ($files as $key => $file) {
                                // return $file;
                                if ($file) {
                                    $cv_path_extension=$file->getClientOriginalExtension();
                                    $doc_name=microtime().'_'.$u_data->id.".".$cv_path_extension;
                                    $file->move(public_path('client-doc/'.$u_data->id."/"),$doc_name);
                                }
                                Document::create(array(
                                    'client_id'=>$u_data->id,
                                    'doc_type_id'=>$request->doc_type_id[$key],
                                    'doc_name'=>$doc_name,
                                    // 'created_by'=>'',
                                ));      
                            }
                        }
                        $data=Client::with('ClientDoc')->where('id',$u_data->id)->first();    
                    }
                }
            }  
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function import(Request $request)
    {
        try {
            // return $request;
            $path = $request->file('file')->getRealPath();
            $data = array_map('str_getcsv', file($path));
            // return $data[0][0];
            // return gettype($data[0][0]) ;
            // if (in_array("rnt_id", $data)) {
            // if ($data[0][0] == "opt_name") {
            //     return "hii";
                Excel::import(new ClientImport,$request->file);
                // Excel::import(new ClientImport,request()->file('file'));
                $data1=[];
            // }else {
            //     return "else";
            //     return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
            // }
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
        }
        return Helper::SuccessResponse($data1);
    }
    
}
