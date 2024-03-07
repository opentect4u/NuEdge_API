<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\Client;
use App\Models\Document;
use App\Models\ClientPertner;
use Validator;
use Excel;
use App\Imports\ClientImport;
use Mail;
use App\Mail\Master\CreatClientEmail;
use App\Models\Email;
use Carbon\Carbon;

class ClientController extends Controller
{
    public function searchDetails(Request $request)
    {
        try {
            $cat_name=$request->cat_name;
            $client_type=$request->client_type;
            $birth_date_month=$request->birth_date_month;
            $anniversary_date_month=$request->anniversary_date_month;

            $rawQuery='';
            if ($client_type || $birth_date_month || $anniversary_date_month) {
                $queryString='md_client.client_type';
                $rawQuery.=Helper::WhereRawQuery($client_type,$rawQuery,$queryString);
            }

            if ($birth_date_month) {
                
                $data=Client::with('ClientDoc')->with('PertnerDetails')
                    ->leftJoin('md_city','md_city.id','=','md_client.city')
                    ->leftJoin('md_district','md_district.id','=','md_client.dist')
                    ->leftJoin('md_states','md_states.id','=','md_client.state')
                    ->leftJoin('md_client_type','md_client_type.id','=','md_client.client_type_mode')
                    ->leftJoin('md_pincode','md_pincode.id','=','md_client.pincode')
                    ->select('md_client.*','md_city.name as city_name','md_district.name as district_name','md_states.name as state_name','md_client_type.type_name as type_name','md_pincode.pincode as pincode')
                    ->where('md_client.client_type',$client_type)
                    ->whereMonth('md_client.dob',$birth_date_month)
                    // ->whereMonth('md_client.dob_actual',$birth_date_month)
                    ->orderBy('md_client.created_at','desc')
                    ->get();
            }elseif ($anniversary_date_month) {
                $data=Client::with('ClientDoc')->with('PertnerDetails')
                    ->leftJoin('md_city','md_city.id','=','md_client.city')
                    ->leftJoin('md_district','md_district.id','=','md_client.dist')
                    ->leftJoin('md_states','md_states.id','=','md_client.state')
                    ->leftJoin('md_client_type','md_client_type.id','=','md_client.client_type_mode')
                    ->leftJoin('md_pincode','md_pincode.id','=','md_client.pincode')
                    ->select('md_client.*','md_city.name as city_name','md_district.name as district_name','md_states.name as state_name','md_client_type.type_name as type_name','md_pincode.pincode as pincode')
                    ->where('md_client.client_type',$client_type)
                    ->whereMonth('md_client.anniversary_date',$anniversary_date_month)
                    ->orderBy('md_client.created_at','desc')
                    ->get();
            } 
            // else {
            //     $data=Client::with('ClientDoc')->with('PertnerDetails')
            //         ->leftJoin('md_city','md_city.id','=','md_client.city')
            //         ->leftJoin('md_district','md_district.id','=','md_client.dist')
            //         ->leftJoin('md_states','md_states.id','=','md_client.state')
            //         ->leftJoin('md_client_type','md_client_type.id','=','md_client.client_type_mode')
            //         ->leftJoin('md_pincode','md_pincode.id','=','md_client.pincode')
            //         ->select('md_client.*','md_city.name as city_name','md_district.name as district_name','md_states.name as state_name','md_client_type.type_name as type_name','md_pincode.pincode as pincode')
            //         ->where('md_client.client_type',$client_type)
            //         ->orderBy('md_client.created_at','desc')
            //         ->get();
            // }
            $data=Client::with('ClientDoc')->with('PertnerDetails')
                    ->leftJoin('md_city','md_city.id','=','md_client.city')
                    ->leftJoin('md_district','md_district.id','=','md_client.dist')
                    ->leftJoin('md_states','md_states.id','=','md_client.state')
                    ->leftJoin('md_client_type','md_client_type.id','=','md_client.client_type_mode')
                    ->leftJoin('md_pincode','md_pincode.id','=','md_client.pincode')
                    ->select('md_client.*','md_city.name as city_name','md_district.name as district_name','md_states.name as state_name','md_client_type.type_name as type_name','md_pincode.pincode as pincode')
                    ->whereRaw($rawQuery)
                    ->orderBy('md_client.created_at','desc')
                    ->get();  
        } catch (\Throwable $th) {
            // throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
    public function export(Request $request)
    {
        try {
            $cat_name=$request->cat_name;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            $client_type=$request->client_type;

            $birth_date_month=$request->birth_date_month;
            $anniversary_date_month=$request->anniversary_date_month;

            if ($sort_by && $column_name) {
                $data=Client::with('ClientDoc')->with('PertnerDetails')
                    ->leftJoin('md_city','md_city.id','=','md_client.city')
                    ->leftJoin('md_district','md_district.id','=','md_client.dist')
                    ->leftJoin('md_states','md_states.id','=','md_client.state')
                    ->leftJoin('md_client_type','md_client_type.id','=','md_client.client_type_mode')
                    ->leftJoin('md_pincode','md_pincode.id','=','md_client.pincode')
                    ->select('md_client.*','md_city.name as city_name','md_district.name as district_name','md_states.name as state_name','md_client_type.type_name as type_name','md_pincode.pincode as pincode')
                    ->where('md_client.client_type',$client_type)
                    ->orderBy('md_client.'.$column_name,$sort_by)
                    ->orderBy('md_client.created_at','desc')
                    ->get();    
            }elseif ($birth_date_month) {
                
                    $data=Client::with('ClientDoc')->with('PertnerDetails')
                        ->leftJoin('md_city','md_city.id','=','md_client.city')
                        ->leftJoin('md_district','md_district.id','=','md_client.dist')
                        ->leftJoin('md_states','md_states.id','=','md_client.state')
                        ->leftJoin('md_client_type','md_client_type.id','=','md_client.client_type_mode')
                        ->leftJoin('md_pincode','md_pincode.id','=','md_client.pincode')
                        ->select('md_client.*','md_city.name as city_name','md_district.name as district_name','md_states.name as state_name','md_client_type.type_name as type_name','md_pincode.pincode as pincode')
                        ->where('md_client.client_type',$client_type)
                        ->whereMonth('md_client.dob',$birth_date_month)
                        // ->whereMonth('md_client.dob_actual',$birth_date_month)
                        ->orderBy('md_client.created_at','desc')
                        ->get();    
            }elseif ($anniversary_date_month) {
                    $data=Client::with('ClientDoc')->with('PertnerDetails')
                        ->leftJoin('md_city','md_city.id','=','md_client.city')
                        ->leftJoin('md_district','md_district.id','=','md_client.dist')
                        ->leftJoin('md_states','md_states.id','=','md_client.state')
                        ->leftJoin('md_client_type','md_client_type.id','=','md_client.client_type_mode')
                        ->leftJoin('md_pincode','md_pincode.id','=','md_client.pincode')
                        ->select('md_client.*','md_city.name as city_name','md_district.name as district_name','md_states.name as state_name','md_client_type.type_name as type_name','md_pincode.pincode as pincode')
                        ->where('md_client.client_type',$client_type)
                        ->whereMonth('md_client.anniversary_date',$anniversary_date_month)
                        ->orderBy('md_client.created_at','desc')
                        ->get();    
            }else {
                $data=Client::with('ClientDoc')->with('PertnerDetails')
                    ->leftJoin('md_city','md_city.id','=','md_client.city')
                    ->leftJoin('md_district','md_district.id','=','md_client.dist')
                    ->leftJoin('md_states','md_states.id','=','md_client.state')
                    ->leftJoin('md_client_type','md_client_type.id','=','md_client.client_type_mode')
                    ->leftJoin('md_pincode','md_pincode.id','=','md_client.pincode')
                    ->select('md_client.*','md_city.name as city_name','md_district.name as district_name','md_states.name as state_name','md_client_type.type_name as type_name','md_pincode.pincode as pincode')
                    ->where('md_client.client_type',$client_type)
                    ->orderBy('md_client.created_at','desc')
                    ->get();    
            }  
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
    public function index(Request $request)
    {
        try {  
            $search=$request->search;
            $client_code=$request->client_code;
            $client_type=$request->client_type;
            $pan=$request->pan;
            $id=$request->id;
            $client_id=$request->client_id;
            $paginate=$request->paginate;
            if ($search!='') {
                $data=Client::with('ClientDoc')
                    // ->where('client_type','!=','E')
                    ->orWhere('client_name','like', '%' . $search . '%')
                    ->orWhere('client_code','like', '%' . $search . '%')
                    ->orWhere('pan','like', '%' . $search . '%')
                    ->orWhere('mobile','like', '%' . $search . '%')
                    ->orWhere('email','like', '%' . $search . '%')
                    ->paginate($paginate);      
            }else if ($client_code!='') {
                $data=Client::leftJoin('td_kyc','td_kyc.client_code','=','md_client.client_code')
                    ->leftJoin('md_pincode','md_pincode.id','=','md_client.pincode')
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
            }else if ($client_id!='') {
                $data=Client::with('ClientDoc')->with('PertnerDetails')
                    ->join('md_city','md_city.id','=','md_client.city')
                    ->join('md_district','md_district.id','=','md_client.dist')
                    ->join('md_states','md_states.id','=','md_client.state')
                    ->leftJoin('md_country','md_country.id','=','md_client.country_id')
                    ->leftJoin('md_pincode','md_pincode.id','=','md_client.pincode')
                    ->leftJoin('md_client_type','md_client_type.id','=','md_client.client_type_mode')
                    ->leftJoin('md_pincode','md_pincode.id','=','md_client.pincode')
                    ->select('md_client.*','md_city.name as city_name','md_district.name as district_name','md_states.name as state_name','md_client_type.type_name as client_type_name',
                    'md_country.name as country_name','md_pincode.pincode as pincode_name')
                    ->where('md_client.id',$client_id)
                    ->get();
            // }else if ($paginate!='') {
            //     $data=Client::with('ClientDoc')->paginate($paginate);    
            } else{
                $data=Client::with('ClientDoc')->orderBy('created_at','desc')
                // whereDate('updated_at',date('Y-m-d'))->
                ->get();      
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
            // return $request;
            if ($request->id > 0) {
                // return $request;
                $id=$request->id;
                $data=[];
                $client_name=ucwords($request->client_name);
                if ($request->client_type=='P') {
                    $datas=Client::find($id);
                    $data['previous_type']=$datas->client_type;

                    if ($datas->pan!=$request->pan && $datas->pan!='') {
                        $ms="PAN can not modified!";
                        return Helper::WarningResponse($ms);
                    }
                    if ($datas->client_code=='' || $datas->client_code==NULL) {
                        $words = explode(" ",$client_name);
                        $client_code="";
                        $client_code_1 = mb_substr($words[0], 0, 1).mb_substr($words[(count($words)-1)], 0, 1);;
                        
                        $is_has=Client::where('client_code',$client_code)->get();
                        if (count($is_has)>0) {
                            $client_code=$client_code_1.date('dmy',strtotime($request->dob)).count($is_has);
                        }else {
                            $client_code=$client_code_1.date('dmy',strtotime($request->dob));
                        }
                    }else {
                        $client_code=$datas->client_code;
                    }
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
                    $datas->client_type=$request->client_type;
                    $datas->karta_name=isset($request->karta_name)?$request->karta_name:NULL;
                    $datas->inc_date=isset($request->inc_date)?$request->inc_date:NULL;
                    $datas->proprietor_name=isset($request->proprietor_name)?$request->proprietor_name:NULL;
                    $datas->date_of_incorporation=isset($request->date_of_incorporation)?$request->date_of_incorporation:NULL;
                    $datas->dob_actual=$request->dob_actual;
                    $datas->anniversary_date=isset($request->anniversary_date)?$request->anniversary_date:NULL;
                    $datas->country_id=$request->country_id;
                    $datas->client_type_mode=$request->client_type_mode;
                    // $data->updated_by=Helper::modifyUser($request->user());
                    $datas->save();
                }elseif ($request->client_type=='N') {
                    $datas=Client::find($id);
                    $data['previous_type']=$datas->client_type;
                    $datas->client_name=$client_name;
                    $datas->karta_name=isset($request->karta_name)?$request->karta_name:NULL;
                    $datas->inc_date=isset($request->inc_date)?$request->inc_date:NULL;
                    $datas->proprietor_name=isset($request->proprietor_name)?$request->proprietor_name:NULL;
                    $datas->date_of_incorporation=isset($request->date_of_incorporation)?$request->date_of_incorporation:NULL;
                    $datas->dob=$request->dob;
                    $datas->dob_actual=$request->dob_actual;
                    $datas->anniversary_date=isset($request->anniversary_date)?$request->anniversary_date:NULL;
                    $datas->add_line_1=$request->add_line_1;
                    $datas->add_line_2=$request->add_line_2;
                    $datas->country_id=$request->country_id;
                    $datas->city=$request->city;
                    $datas->dist=$request->dist;
                    $datas->state=$request->state;
                    $datas->pincode=$request->pincode;
                    $datas->mobile=$request->mobile;
                    $datas->sec_mobile=$request->sec_mobile;
                    $datas->email=$request->email;
                    $datas->sec_email=$request->sec_email;
                    $datas->client_type=$request->client_type;
                    $datas->client_type_mode=$request->client_type_mode;
                    $datas->save();
                }elseif ($request->client_type=='M') {
                    $datas=Client::find($id);
                    $data['previous_type']=$datas->client_type;
                    $datas->client_name=$client_name;
                    $datas->dob=$request->dob;
                    $datas->dob_actual=$request->dob_actual;
                    $datas->anniversary_date=isset($request->anniversary_date)?$request->anniversary_date:NULL;
                    $datas->add_line_1=$request->add_line_1;
                    $datas->add_line_2=$request->add_line_2;
                    $datas->country_id=$request->country_id;
                    $datas->city=$request->city;
                    $datas->dist=$request->dist;
                    $datas->state=$request->state;
                    $datas->pincode=$request->pincode;
                    $datas->pan=$request->pan;
                    $datas->mobile=$request->mobile;
                    $datas->sec_mobile=$request->sec_mobile;
                    $datas->email=$request->email;
                    $datas->sec_email=$request->sec_email;
                    $datas->client_type=$request->client_type;
                    $datas->guardians_pan=$request->guardians_pan;
                    $datas->guardians_name=$request->guardians_name;
                    $datas->relation=$request->relation;
                    $datas->client_type_mode=$request->client_type_mode;
                    $datas->save();
                }

                $doc_name='';
                $files=$request->file;
                // return $files;
                if ($files!='') {
                    foreach ($files as $key => $file) {
                        // return $file;
                        if ($file) {
                            $cv_path_extension=$file->getClientOriginalExtension();
                            $doc_name=microtime(true).'_'.$datas->id.".".$cv_path_extension;
                            $file->move(public_path('client-doc/'.$datas->id."/"),$doc_name);

                            Document::create(array(
                                'client_id'=>$datas->id,
                                'doc_type_id'=>$request->doc_type_id[$key],
                                'doc_name'=>$doc_name,
                                'created_by'=>Helper::modifyUser($request->user()),
                            )); 
                        }
                    }
                }
                $set_data=Client::with('ClientDoc')->where('id',$datas->id)->first();    
                $data['data']=$set_data;
            }else{
                // return $request;
                
                    $client_code="";
                    if ($request->client_type_mode==14) {
                        $client_name = substr($request->client_name, 0, 2);
                        $client_code_1 = strtoupper($client_name);
                    }else {
                        $client_name=ucwords($request->client_name);
                        $words = explode(" ",$client_name);

                        $client_code_1 = mb_substr($words[0], 0, 1).mb_substr($words[(count($words)-1)], 0, 1);
                    }
                    // return $client_code_1;
                    $client_code=$client_code_1.date('dmy',strtotime($request->dob));
                    // return $client_code;
                    $is_has=Client::where('client_code','LIKE','%'.$client_code.'%')->get();
                    // return $is_has;
                    if (count($is_has)>0) {
                        $client_code=$client_code_1.date('dmy',strtotime($request->dob)).count($is_has);
                    }

                    if ($request->client_type=='P') {
                        // return $request;
                        $already=Client::where('pan',$request->pan)->get();
                        if (count($already)>0) {
                            $ms='PAN no already exist.';
                            return Helper::WarningResponse($ms);
                        }else{
                            // return $request;
                            $u_data=Client::create(array(
                                'client_code'=>$client_code,
                                'client_name'=>$client_name,
                                'karta_name'=>isset($request->karta_name)?$request->karta_name:NULL,
                                'inc_date'=>isset($request->inc_date)?$request->inc_date:NULL,
                                'proprietor_name'=>isset($request->proprietor_name)?$request->proprietor_name:NULL,
                                'date_of_incorporation'=>isset($request->date_of_incorporation)?$request->date_of_incorporation:NULL,
                                'dob'=>$request->dob,
                                'dob_actual'=>$request->dob_actual,
                                'anniversary_date'=>isset($request->anniversary_date)?$request->anniversary_date:NULL,
                                'add_line_1'=>$request->add_line_1,
                                'add_line_2'=>$request->add_line_2,
                                'country_id'=>$request->country_id,
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
                                'client_type_mode'=>$request->client_type_mode,
                                'created_by'=>Helper::modifyUser($request->user()),
                            )); 
                            $doc_name='';
                            $files=$request->file;
                            // return $files;
                            if ($files!='') {
                                foreach ($files as $key => $file) {
                                    // return $file;
                                    if ($file) {
                                        $cv_path_extension=$file->getClientOriginalExtension();
                                        $doc_name=microtime(true).'_'.$u_data->id.".".$cv_path_extension;
                                        $file->move(public_path('client-doc/'.$u_data->id."/"),$doc_name);
                                    }
                                    Document::create(array(
                                        'client_id'=>$u_data->id,
                                        'doc_type_id'=>$request->doc_type_id[$key],
                                        'doc_name'=>$doc_name,
                                        'created_by'=>Helper::modifyUser($request->user()),
                                    ));      
                                }
                            }

                            $pertner_details=$request->pertner_details;
                            if ($pertner_details!='') {
                                $pertner_details=json_decode($pertner_details);
                                foreach ($pertner_details as $key => $value10) {
                                    ClientPertner::create(array(
                                        'client_id'=>$u_data->id,
                                        'name'=>$value10->name,
                                        'mobile'=>$value10->mobile,
                                        'email'=>$value10->email,
                                        'dob'=>$value10->dob,
                                        'pan'=>$value10->pan,
                                        'created_by'=>Helper::modifyUser($request->user()),
                                    ));
                                }
                            }

                            $email=Email::find(1);
                            // Mail::to($request->email)->send(new CreatClientEmail($client_name,$email->subject,$email->body));

                            $data=Client::with('ClientDoc')->where('id',$u_data->id)->first();  
                        }  
                    }elseif ($request->client_type=='N') {
                        $u_data=Client::create(array(
                            'client_code'=>$client_code,
                            'client_name'=>$client_name,
                            'karta_name'=>isset($request->karta_name)?$request->karta_name:NULL,
                            'inc_date'=>isset($request->inc_date)?$request->inc_date:NULL,
                            'proprietor_name'=>isset($request->proprietor_name)?$request->proprietor_name:NULL,
                            'date_of_incorporation'=>isset($request->date_of_incorporation)?$request->date_of_incorporation:NULL,
                            'dob'=>$request->dob,
                            'dob_actual'=>$request->dob_actual,
                            'anniversary_date'=>isset($request->anniversary_date)?$request->anniversary_date:NULL,
                            'add_line_1'=>$request->add_line_1,
                            'add_line_2'=>$request->add_line_2,
                            'country_id'=>$request->country_id,
                            'city'=>$request->city,
                            'dist'=>$request->dist,
                            'state'=>$request->state,
                            'pincode'=>$request->pincode,
                            'mobile'=>$request->mobile,
                            'sec_mobile'=>$request->sec_mobile,
                            'email'=>$request->email,
                            'sec_email'=>$request->sec_email,
                            'client_type'=>$request->client_type,
                            'client_type_mode'=>$request->client_type_mode,
                            'created_by'=>Helper::modifyUser($request->user()),
                        )); 
                        $doc_name='';
                        $files=$request->file;
                        // return $files;
                        if ($files!='') {
                            foreach ($files as $key => $file) {
                                // return $file;
                                if ($file) {
                                    $cv_path_extension=$file->getClientOriginalExtension();
                                    $doc_name=microtime(true).'_'.$u_data->id.".".$cv_path_extension;
                                    $file->move(public_path('client-doc/'.$u_data->id."/"),$doc_name);
                                }
                                Document::create(array(
                                    'client_id'=>$u_data->id,
                                    'doc_type_id'=>$request->doc_type_id[$key],
                                    'doc_name'=>$doc_name,
                                    'created_by'=>Helper::modifyUser($request->user()),
                                ));      
                            }
                        }

                        $pertner_details=$request->pertner_details;
                            if ($pertner_details!='') {
                                $pertner_details=json_decode($pertner_details);
                                foreach ($pertner_details as $key => $value10) {
                                    ClientPertner::create(array(
                                        'client_id'=>$u_data->id,
                                        'name'=>$value10->name,
                                        'mobile'=>$value10->mobile,
                                        'email'=>$value10->email,
                                        'dob'=>$value10->dob,
                                        'pan'=>$value10->pan,
                                        'created_by'=>Helper::modifyUser($request->user()),
                                    ));
                                }
                            }

                        $email=Email::find(1);
                        // Mail::to($request->email)->send(new CreatClientEmail($client_name,$email->subject,$email->body));

                        $data=Client::with('ClientDoc')->where('id',$u_data->id)->first();  
                    } else {
                        // return $request;
                        $u_data=Client::create(array(
                            'client_code'=>$client_code,
                            'client_name'=>$client_name,
                            'dob'=>$request->dob,
                            'dob_actual'=>$request->dob_actual,
                            'anniversary_date'=>isset($request->anniversary_date)?$request->anniversary_date:NULL,
                            'add_line_1'=>$request->add_line_1,
                            'add_line_2'=>$request->add_line_2,
                            'country_id'=>$request->country_id,
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
                            'client_type_mode'=>$request->client_type_mode,
                            'created_by'=>Helper::modifyUser($request->user()),
                        ));
                        $doc_name='';
                        $files=$request->file;
                        // return $files;
                        if ($files!='') {
                            foreach ($files as $key => $file) {
                                // return $file;
                                if ($file) {
                                    $cv_path_extension=$file->getClientOriginalExtension();
                                    $doc_name=microtime(true).'_'.$u_data->id.".".$cv_path_extension;
                                    $file->move(public_path('client-doc/'.$u_data->id."/"),$doc_name);
                                }
                                Document::create(array(
                                    'client_id'=>$u_data->id,
                                    'doc_type_id'=>$request->doc_type_id[$key],
                                    'doc_name'=>$doc_name,
                                    'created_by'=>Helper::modifyUser($request->user()),
                                ));      
                            }
                        }
                        $email=Email::find(1);
                        // Mail::to($request->email)->send(new CreatClientEmail($client_name,$email->subject,$email->body));

                        $data=Client::with('ClientDoc')->where('id',$u_data->id)->first();    
                    }
                
            }  
        } catch (\Throwable $th) {
            throw $th;
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
            // return $data;

            foreach ($data as $key => $value) {
                if ($key==0) {
                    if (str_replace(" ","_",$value[0])!="Client_Name" && $value[1]!="PAN") {
                        return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
                    }
                    // return $value;
                }else {
                    // return $value;
                    Client::create(array(
                            'client_name'=>$value[0],
                            'pan'=>$value[1],
                            'client_type'=>'E',
                            // 'created_by'=>'',
                        ));  
                }
            }
            // return gettype($data[0][0]) ;
            // if (in_array("rnt_id", $data)) {
            // if ($data[0][0] == "opt_name") {
            //     return "hii";
                // Excel::import(new ClientImport,$request->file);
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

    public function delete(Request $request)
    {
        try {
            $id=$request->id;
            // $is_has=AMC::where('rnt_id',$id)->get();
            // if (count($is_has)>0) {
                return Helper::WarningResponse(parent::DELETE_NOT_ALLOW_ERROR);
            // }else {
            //     $data=Client::find($id);
            //     $data->delete_flag='Y';
            //     $data->deleted_date=date('Y-m-d H:i:s');
            //     $data->deleted_by=Helper::modifyUser($request->user());
            //     $data->save();
            // }
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DELETE_FAIL_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function searchWithClient(Request $request)
    {
        try {
            // return $request;
            $search=$request->search;
            $view_type=$request->view_type;
            if ($view_type=='C') {
                $data=Client::where('client_name','like', '%' . $search . '%')
                    ->orWhere('client_code','like', '%' . $search . '%')
                    ->orWhere('pan','like', '%' . $search . '%')
                    ->orWhere('mobile','like', '%' . $search . '%')
                    ->orWhere('email','like', '%' . $search . '%')
                    ->get();      
            } else {
                $data=Client::join('md_client_family','md_client_family.family_id','=','md_client.id')
                    ->where('md_client_family.relationship','Head')
                    ->whereRaw('(md_client.client_name LIKE "%'.$search.'%" 
                    OR md_client.pan LIKE "%'.$search.'%" 
                    OR md_client.client_code LIKE "%'.$search.'%"
                    OR md_client.mobile LIKE "%'.$search.'%"
                    OR md_client.email LIKE "%'.$search.'%")')
                    ->get(); 
            }
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function clientWithoutFamily(Request $request)
    {
        try {  
            $search=$request->search;
            if ($search!='') {
                $data=Client::leftJoin('md_client_family','md_client_family.family_id','=','md_client.id')
                    ->select('md_client.*','md_client_family.family_id as family_id','md_client_family.relationship as relationship')
                    ->selectRaw('(select count(*) from md_client_family where family_id=md_client.id)as family_count')
                    ->whereRaw('(md_client.client_name LIKE "%'.$search.'%" 
                    OR md_client.pan LIKE "%'.$search.'%" 
                    OR md_client.client_code LIKE "%'.$search.'%"
                    OR md_client.mobile LIKE "%'.$search.'%"
                    OR md_client.email LIKE "%'.$search.'%")')
                    ->get(); 
            }
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
    
}