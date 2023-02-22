<?php

namespace App\Http\Controllers\v1\Operation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{FormReceived,MutualFund};
use Validator;

class FormReceivedController extends Controller
{
    public function searchDetails(Request $request)
    {
        try {
            $paginate=$request->paginate;
            $trans_type_id=$request->trans_type_id;

            $temp_tin_no=$request->temp_tin_no;
            $client_code=$request->client_code;
            $recv_from=$request->recv_from;
            $sub_brk_cd=$request->sub_brk_cd;
            $euin_no=$request->euin_no;
            $inv_type=$request->inv_type;
            $trans_type=$request->trans_type;
            $bu_type=json_decode($request->bu_type);
            $kyc_status=json_decode($request->kyc_status);
            // return $bu_type;

            if ($paginate=='A' || $paginate=='undefined') {
                $paginate=999999999;
            }
            if ($temp_tin_no!='') {
                $data=FormReceived::join('md_trans','md_trans.id','=','td_form_received.trans_id')
                    ->join('md_trns_type','md_trns_type.id','=','md_trans.trans_type_id')
                    ->join('md_scheme','md_scheme.id','=','td_form_received.scheme_id')
                    ->leftJoin('md_scheme as md_scheme_2','md_scheme_2.id','=','td_form_received.scheme_id_to')
                    ->join('md_client','md_client.id','=','td_form_received.client_id')
                    ->select('td_form_received.*','md_trans.trns_name as trans_name','md_trans.trans_type_id as trans_type_id','md_trns_type.trns_type as trans_type','md_scheme.scheme_name as scheme_name','md_scheme_2.scheme_name as scheme_name_to','md_client.client_code as client_code','md_client.client_name as client_name','md_client.client_type as client_type')
                    ->where('td_form_received.temp_tin_no',$temp_tin_no)
                    ->where('td_form_received.deleted_flag','N')
                    ->where('md_trans.trans_type_id',$trans_type_id)
                    ->orderBy('td_form_received.updated_at','DESC')
                    ->paginate($paginate);      
            }elseif (!empty($client_code)) {
                $data=FormReceived::join('md_trans','md_trans.id','=','td_form_received.trans_id')
                    ->join('md_trns_type','md_trns_type.id','=','md_trans.trans_type_id')
                    ->join('md_scheme','md_scheme.id','=','td_form_received.scheme_id')
                    ->leftJoin('md_scheme as md_scheme_2','md_scheme_2.id','=','td_form_received.scheme_id_to')
                    ->join('md_client','md_client.id','=','td_form_received.client_id')
                    ->select('td_form_received.*','md_trans.trns_name as trans_name','md_trans.trans_type_id as trans_type_id','md_trns_type.trns_type as trans_type','md_scheme.scheme_name as scheme_name','md_scheme_2.scheme_name as scheme_name_to','md_client.client_code as client_code','md_client.client_name as client_name','md_client.client_type as client_type')
                    ->where('td_form_received.deleted_flag','N')
                    ->where('md_trans.trans_type_id',$trans_type_id)
                    ->where('md_client.client_code','like', '%' . $client_code . '%')
                    ->orWhere('md_client.client_name','like', '%' . $client_code . '%')
                    ->orWhere('md_client.pan','like', '%' . $client_code . '%')
                    ->orderBy('td_form_received.updated_at','DESC')
                    ->paginate($paginate);      
            }elseif (!empty($bu_type)) {
                // return $bu_type;
                $data=FormReceived::join('md_trans','md_trans.id','=','td_form_received.trans_id')
                    ->join('md_trns_type','md_trns_type.id','=','md_trans.trans_type_id')
                    ->join('md_scheme','md_scheme.id','=','td_form_received.scheme_id')
                    ->leftJoin('md_scheme as md_scheme_2','md_scheme_2.id','=','td_form_received.scheme_id_to')
                    ->join('md_client','md_client.id','=','td_form_received.client_id')
                    ->select('td_form_received.*','md_trans.trns_name as trans_name','md_trans.trans_type_id as trans_type_id','md_trns_type.trns_type as trans_type','md_scheme.scheme_name as scheme_name','md_scheme_2.scheme_name as scheme_name_to','md_client.client_code as client_code','md_client.client_name as client_name','md_client.client_type as client_type')
                    ->where('td_form_received.deleted_flag','N')
                    ->where('md_trans.trans_type_id',$trans_type_id)
                    ->whereIn('td_form_received.bu_type',$bu_type)
                    ->orderBy('td_form_received.updated_at','DESC')
                    ->paginate($paginate);      
            } else {
                $data=FormReceived::join('md_trans','md_trans.id','=','td_form_received.trans_id')
                    ->join('md_trns_type','md_trns_type.id','=','md_trans.trans_type_id')
                    ->join('md_scheme','md_scheme.id','=','td_form_received.scheme_id')
                    ->leftJoin('md_scheme as md_scheme_2','md_scheme_2.id','=','td_form_received.scheme_id_to')
                    ->join('md_client','md_client.id','=','td_form_received.client_id')
                    ->select('td_form_received.*','md_trans.trns_name as trans_name','md_trans.trans_type_id as trans_type_id','md_trns_type.trns_type as trans_type','md_scheme.scheme_name as scheme_name','md_scheme_2.scheme_name as scheme_name_to','md_client.client_code as client_code','md_client.client_name as client_name','md_client.client_type as client_type')
                    // ->whereDate('td_form_received.updated_at',date('Y-m-d'))
                    ->where('td_form_received.deleted_flag','N')
                    ->where('md_trans.trans_type_id',$trans_type_id)
                    ->orderBy('td_form_received.updated_at','DESC')
                    ->paginate($paginate); 
            }
                 
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
    public function export(Request $request)
    {
        try {
            $trans_type_id=$request->trans_type_id;
            $data=FormReceived::join('md_trans','md_trans.id','=','td_form_received.trans_id')
                    ->join('md_trns_type','md_trns_type.id','=','md_trans.trans_type_id')
                    ->join('md_scheme','md_scheme.id','=','td_form_received.scheme_id')
                    ->leftJoin('md_scheme as md_scheme_2','md_scheme_2.id','=','td_form_received.scheme_id_to')
                    ->join('md_client','md_client.id','=','td_form_received.client_id')
                    ->select('td_form_received.*','md_trans.trns_name as trans_name','md_trns_type.trns_type as trans_type','md_scheme.scheme_name as scheme_name','md_scheme_2.scheme_name as scheme_name_to','md_client.client_code as client_code','md_client.client_name as client_name','md_client.client_type as client_type')
                    ->whereDate('td_form_received.updated_at',date('Y-m-d'))
                    ->where('td_form_received.deleted_flag','N')
                    ->where('md_trans.trans_type_id',$trans_type_id)
                    ->orderBy('td_form_received.updated_at','DESC')
                    ->get();        
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
            $temp_tin_no=$request->temp_tin_no;
            $trans_type_id=$request->trans_type_id;
            $trans_id=$request->trans_id;
            $flag=$request->flag;
            $paginate=$request->paginate;
            if ($paginate=='A') {
                $paginate=999999999;
            }
            // $flag=$request->flag;

            if ($search!='') {
                $data=FormReceived::join('md_products','md_products.id','=','td_form_received.product_id')
                    ->join('md_trans','md_trans.id','=','td_form_received.trans_id')
                    ->join('md_trns_type','md_trns_type.id','=','md_trans.trans_type_id')
                    // ->join('md_client','md_client.id','=','td_form_received.client_id')
                    ->select('td_form_received.*','md_products.product_name as product_name','md_trans.trns_name as trans_name','md_trns_type.trns_type as trans_type')
                    ->where('td_form_received.deleted_flag','N')
                    ->orWhere('td_form_received.temp_tin_no','like', '%' . $search . '%')
                    // ->orWhere('md_client.pan','like', '%' . $search . '%')
                    // ->orWhere('md_client.mobile','like', '%' . $search . '%')
                    // ->orWhere('md_client.email','like', '%' . $search . '%')
                    ->orWhere('td_form_received.application_no','like', '%' . $search . '%')
                    ->get();      
            }else if ($temp_tin_no!='' && $trans_type_id!='' && $flag=='C') {
                // return $temp_tin_no;
                $data=FormReceived::join('md_trans','md_trans.id','=','td_form_received.trans_id')
                    // leftJoin('md_products','md_products.id','=','td_form_received.product_id')
                    ->join('md_scheme','md_scheme.id','=','td_form_received.scheme_id')
                    ->leftJoin('md_scheme as md_scheme_2','md_scheme_2.id','=','td_form_received.scheme_id_to')
                    ->join('md_client','md_client.id','=','td_form_received.client_id')
                    ->join('md_employee','md_employee.euin_no','=','td_form_received.euin_no')
                    // ->join('md_employee as md_employee1','md_employee1.emp_code','=','td_form_received.euin_to')
                    // ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_form_received.sub_brk_cd')
                    ->select('td_form_received.*','md_scheme.scheme_name as scheme_name','md_scheme_2.scheme_name as scheme_name_to','md_scheme.amc_id as amc_id','md_scheme.pip_fresh_min_amt as pip_fresh_min_amt','md_scheme.sip_fresh_min_amt as sip_fresh_min_amt','md_scheme.pip_add_min_amt as pip_add_min_amt','md_scheme.pip_add_min_amt as pip_add_min_amt','md_client.client_code as client_code','md_client.client_name as client_name','md_client.client_type as client_type','md_trans.trns_name as trans_name','md_employee.emp_name as emp_name')
                    // ->select('td_form_received.*','md_products.product_name as product_name','md_employee.emp_name as euin_from_name','md_employee1.emp_name as euin_to_name','md_sub_broker.bro_name as sub_bro_name')
                    ->where('td_form_received.deleted_flag','N')
                    ->where('td_form_received.temp_tin_no', $temp_tin_no)
                    ->where('md_trans.trans_type_id',$trans_type_id)
                    ->get(); 
                // return $data;
                if (count($data)>0) {
                    $data1=MutualFund::where('temp_tin_no', $temp_tin_no)->get();
                    // return $data1;
                    if (count($data1)>0) {
                        $data=[];
                        return Helper::SuccessResponse($data);
                    }
                }   
            }else if ($temp_tin_no!='' && $trans_type_id!='' && $flag=='U') {
                // return $temp_tin_no;
                $data=FormReceived::join('md_products','md_products.id','=','td_form_received.product_id')
                    ->join('md_employee','md_employee.emp_code','=','td_form_received.euin_from')
                    ->join('md_employee as md_employee1','md_employee1.emp_code','=','td_form_received.euin_to')
                    ->join('md_trans','md_trans.id','=','td_form_received.trans_id')
                    ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_form_received.sub_brk_cd')
                    ->select('td_form_received.*','md_products.product_name as product_name','md_employee.emp_name as euin_from_name','md_employee1.emp_name as euin_to_name','md_sub_broker.bro_name as sub_bro_name')
                    ->where('td_form_received.deleted_flag','N')
                    ->where('td_form_received.temp_tin_no', $temp_tin_no)
                    ->where('md_trans.trans_type_id',$trans_type_id)
                    ->get(); 
            }else if ($paginate!='' && $trans_id!='') {
                $data=FormReceived::join('md_trans','md_trans.id','=','td_form_received.trans_id')
                    ->join('md_trns_type','md_trns_type.id','=','md_trans.trans_type_id')
                    ->join('md_scheme','md_scheme.id','=','td_form_received.scheme_id')
                    ->select('td_form_received.*','md_trans.trns_name as trans_name','md_trns_type.trns_type as trans_type','md_scheme.scheme_name as scheme_name')
                    ->where('td_form_received.trans_id',$trans_id)
                    ->where('td_form_received.deleted_flag','N')
                    ->orderBy('td_form_received.updated_at','DESC')
                    ->paginate($paginate);    
            }else if ($paginate!='') {
                $data=FormReceived::join('md_trans','md_trans.id','=','td_form_received.trans_id')
                    ->join('md_trns_type','md_trns_type.id','=','md_trans.trans_type_id')
                    ->join('md_scheme','md_scheme.id','=','td_form_received.scheme_id')
                    ->leftJoin('md_scheme as md_scheme_2','md_scheme_2.id','=','td_form_received.scheme_id_to')
                    ->join('md_client','md_client.id','=','td_form_received.client_id')
                    ->select('td_form_received.*','md_trans.trns_name as trans_name','md_trns_type.trns_type as trans_type','md_scheme.scheme_name as scheme_name','md_scheme_2.scheme_name as scheme_name_to','md_client.client_code as client_code','md_client.client_name as client_name','md_client.client_type as client_type')
                    // ->whereDate('td_form_received.updated_at',date('Y-m-d'))
                    ->where('td_form_received.deleted_flag','N')
                    ->where('md_trans.trans_type_id',$trans_type_id)
                    ->orderBy('td_form_received.updated_at','DESC')
                    ->paginate($paginate);    
            }else if ($temp_tin_no!='') {
                $data=FormReceived::with('ClientDoc')
                    ->join('md_trans','md_trans.id','=','td_form_received.trans_id')
                    ->join('md_trns_type','md_trns_type.id','=','md_trans.trans_type_id')
                    ->join('md_scheme','md_scheme.id','=','td_form_received.scheme_id')
                    ->leftJoin('md_scheme as md_scheme_2','md_scheme_2.id','=','td_form_received.scheme_id_to')
                    ->join('md_client','md_client.id','=','td_form_received.client_id')
                    ->select('td_form_received.*','md_trans.trns_name as trans_name','md_trns_type.trns_type as trans_type','md_scheme.scheme_name as scheme_name','md_scheme_2.scheme_name as scheme_name_to','md_client.client_code as client_code','md_client.client_name as client_name','md_client.client_type as client_type')
                    ->where('td_form_received.deleted_flag','N')
                    ->where('td_form_received.temp_tin_no',$temp_tin_no)
                    ->orderBy('td_form_received.updated_at','DESC')
                    ->get();    
            }else {
                $data=FormReceived::join('md_trans','md_trans.id','=','td_form_received.trans_id')
                    ->join('md_trns_type','md_trns_type.id','=','md_trans.trans_type_id')
                    ->join('md_scheme','md_scheme.id','=','md_trans.scheme_id')
                    ->select('td_form_received.*','md_trans.trns_name as trans_name','md_trns_type.trns_type as trans_type')
                    ->whereDate('td_form_received.updated_at',date('Y-m-d'))
                    ->where('td_form_received.deleted_flag','N')
                    ->orderBy('td_form_received.updated_at','DESC')
                    ->get();      
                // $data=FormReceived::get();      
            }
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function createShow(Request $request)
    {
        try {
            $datas=FormReceived::join('md_products','md_products.id','=','td_form_received.product_id')
                    ->join('md_trans','md_trans.id','=','td_form_received.trans_id')
                    ->select('td_form_received.*','md_products.product_name as product_name','md_trans.trns_name as trans_name')
                    ->where('td_form_received.deleted_flag','N')
                    ->where('td_form_received.product_id', $request->product_id)
                    ->where('md_trans.trans_type_id',$request->trans_type_id)
                    ->get(); 
            
            $data=[];
            $trans_id_1_count=0;
            $trans_id_2_count=0;
            $trans_id_3_count=0;
            if ($request->trans_type_id==4) {
                foreach($datas as $dd){
                    if($dd->trans_id==4){
                        $trans_id_1_count=$trans_id_1_count+1; 
                    }elseif ($dd->trans_id==5) {
                        $trans_id_2_count=$trans_id_2_count+1; 
                    }elseif ($dd->trans_id==6) {
                        $trans_id_3_count=$trans_id_3_count+1; 
                    }
                }
                $trans_data_1['id']=4;
                $trans_data_1['name']='PIP';
                $trans_data_1['count']=$trans_id_1_count;
                array_push($data,$trans_data_1);
                $trans_data_2['id']=5;
                $trans_data_2['name']='SIP';
                $trans_data_2['count']=$trans_id_2_count;
                array_push($data,$trans_data_2);
                $trans_data_3['id']=6;
                $trans_data_3['name']='Switch';
                $trans_data_3['count']=$trans_id_3_count;
                array_push($data,$trans_data_3);
            }elseif ($request->trans_type_id==5) {
                # code...
            } else {
                foreach($datas as $dd){
                    if($dd->trans_id==1){
                        $trans_id_1_count=$trans_id_1_count+1; 
                    }elseif ($dd->trans_id==2) {
                        $trans_id_2_count=$trans_id_2_count+1; 
                    }elseif ($dd->trans_id==3) {
                        $trans_id_3_count=$trans_id_3_count+1; 
                    }
                }
                $trans_data_1['id']=1;
                $trans_data_1['name']='PIP';
                $trans_data_1['count']=$trans_id_1_count;
                array_push($data,$trans_data_1);
                $trans_data_2['id']=2;
                $trans_data_2['name']='SIP';
                $trans_data_2['count']=$trans_id_2_count;
                array_push($data,$trans_data_2);
                $trans_data_3['id']=3;
                $trans_data_3['name']='Switch';
                $trans_data_3['count']=$trans_id_3_count;
                array_push($data,$trans_data_3);
            }
            // return $data;
        } catch (\Throwable $th) {
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function create(Request $request)
    {
        $validator = Validator::make(request()->all(),[
            'euin_no' =>'required',
            'product_id' =>'required',
            'trans_id' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            // return $request;
            $is_has=FormReceived::orderBy('created_at','desc')->get();
            if (count($is_has)>0) {
                $temp_tin_no=Helper::TempTINGen((count($is_has)+1)); // generate temp tin no
            }else{
                $temp_tin_no=Helper::TempTINGen(1); // generate temp tin no
            }
            
                // $bu_type='D';
                $arn_no=Helper::CommonParamValue(1);
                // $euin_to=Helper::CommonParamValue(2);
                $branch_code=1;
                $data=FormReceived::create(array(
                    'rec_datetime'=>date('Y-m-d H:i:s'),
                    'temp_tin_no'=>$temp_tin_no,
                    'bu_type'=>$request->bu_type,
                    'arn_no'=>$arn_no,
                    'euin_no'=>$request->euin_no,
                    'sub_arn_no'=>isset($request->sub_arn_no)?$request->sub_arn_no:NULL,
                    'sub_brk_cd'=>isset($request->sub_brk_cd)?$request->sub_brk_cd:NULL,
                    'client_id'=>$request->client_id,
                    'product_id'=>$request->product_id,
                    'trans_id'=>$request->trans_id,
                    'scheme_id'=>$request->scheme_id,
                    'scheme_id_to'=>isset($request->scheme_id_to)?$request->scheme_id_to:NULL,
                    'recv_from'=>$request->recv_from,
                    'inv_type'=>$request->inv_type,
                    'application_no'=>isset($request->application_no)?$request->application_no:NULL,
                    'folio_no'=>isset($request->folio_no)?$request->folio_no:NULL,
                    'kyc_status'=>$request->kyc_status,
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
            // return $request;
                // $bu_type='D';
                $arn_no=Helper::CommonParamValue(1);
                // $euin_to=Helper::CommonParamValue(2);
                $branch_code=1;
                $data=FormReceived::where('temp_tin_no',$request->temp_tin_no)->update([
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
            $data=FormReceived::where('temp_tin_no',$request->temp_tin_no)->first();
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
                $data=FormReceived::where('temp_tin_no',$request->temp_tin_no)->update([
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