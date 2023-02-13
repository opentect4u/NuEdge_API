<?php

namespace App\Http\Controllers\v1\Operation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{MutualFund,Client,FormReceived};
use Validator;
use Illuminate\Support\Carbon;

class FinancialController extends Controller
{
    public function index(Request $request)
    {
        // try {  
            $search=$request->search;
            $trans_type_id=$request->trans_type_id;
            $paginate=$request->paginate;
            if ($search!='') {
                $data=MutualFund::join('md_trans','md_trans.id','=','td_mutual_fund.trans_id')
                    ->select('td_mutual_fund.*','md_trans.trans_type_id as trans_type_id')
                    ->where('md_trans.trans_type_id',$trans_type_id)
                    ->where('td_mutual_fund.tin_no',$search)
                    // ->whereDate('td_mutual_fund.entry_date',date('Y-m-d'))
                    ->get();     
            }elseif ($paginate!='') {
                $data=MutualFund::join('md_trans','md_trans.id','=','td_mutual_fund.trans_id')
                    ->select('td_mutual_fund.*','md_trans.trans_type_id as trans_type_id')
                    ->where('md_trans.trans_type_id',$trans_type_id)
                    ->whereDate('td_mutual_fund.entry_date',date('Y-m-d'))
                    ->paginate($paginate);   
            } else{
                $data=MutualFund::join('md_trans','md_trans.id','=','td_mutual_fund.trans_id')
                ->select('td_mutual_fund.*','md_trans.trans_type_id as trans_type_id')
                ->where('md_trans.trans_type_id',$trans_type_id)
                ->whereDate('td_mutual_fund.entry_date',date('Y-m-d'))
                ->get();      
            }
        // } catch (\Throwable $th) {
        //     return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        // }
        return Helper::SuccessResponse($data);
    }

    public function createShow(Request $request)
    {
        // try {
            $datas=MutualFund::join('md_trans','md_trans.id','=','td_mutual_fund.trans_id')
                    ->select('td_mutual_fund.*','md_trans.trns_name as trans_name')
                    ->where('td_mutual_fund.delete_flag','N')
                    ->where('md_trans.trans_type_id',$request->trans_type_id)
                    ->get(); 
            
            $data=[];
            $trans_id_1_count=0;
            $trans_id_2_count=0;
            $trans_id_3_count=0;
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
            // return $data;
        // } catch (\Throwable $th) {
        //     return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        // }
        return Helper::SuccessResponse($data);
    }

    public function create(Request $request)
    {
        // $validator = Validator::make(request()->all(),[
        //     'client_name'=>'required',
        //     'dob'=>'required',
        //     'add_line_1'=>'required',
        //     'city'=>'required',
        //     'dist'=>'required',
        //     'state'=>'required',
        //     'pincode'=>'required',
        //     'pan'=>'required',
        //     'mobile'=>'required',
        //     'email'=>'required',
        // ]);
    
        // if($validator->fails()) {
        //     $errors = $validator->errors();
        //     return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        // }
        try {
            // return $request;
            // return $request->app_form_scan;
            $product_id=1;
            $tin_no='';
            $trans_type_id=$request->trans_type_id;
            if ($trans_type_id==1) { // Financial
                $is_has=MutualFund::join('md_trans','md_trans.id','=','td_mutual_fund.trans_id')
                    ->select('td_mutual_fund.*','md_trans.trans_type_id as trans_type_id')
                    ->where('md_trans.trans_type_id',$trans_type_id)
                    ->get();
                // return $is_has; 
                if (count($is_has)>0) {
                    $tin_no=Helper::GenTIN($product_id,$trans_type_id,(count($is_has)+1));
                } else {
                    $tin_no=Helper::GenTIN($product_id,$trans_type_id,1);
                }
            }else if ($trans_type_id==3) { // Non Financial
                $is_has=MutualFund::join('md_trans','md_trans.id','=','td_mutual_fund.trans_id')
                    ->select('td_mutual_fund.*','md_trans.trans_type_id as trans_type_id')
                    ->where('md_trans.trans_type_id',$trans_type_id)
                    ->get();
                // return $is_has; 
                if (count($is_has)>0) {
                    $tin_no=Helper::GenTIN($product_id,$trans_type_id,(count($is_has)+1));
                } else {
                    $tin_no=Helper::GenTIN($product_id,$trans_type_id,1);
                }
            }else if ($trans_type_id==4) {  // NFO
                $is_has=MutualFund::join('md_trans','md_trans.id','=','td_mutual_fund.trans_id')
                    ->select('td_mutual_fund.*','md_trans.trans_type_id as trans_type_id')
                    ->where('md_trans.trans_type_id',$trans_type_id)
                    ->get();
                // return $is_has; 
                if (count($is_has)>0) {
                    $tin_no=Helper::GenTIN($product_id,$trans_type_id,(count($is_has)+1));
                } else {
                    $tin_no=Helper::GenTIN($product_id,$trans_type_id,1);
                }
            }
            // return $tin_no;
            // return $request;
            
            if($request->temp_tin_no!='' && $request->tin_status=='Y'){  // with TTIN NO
                // return $request;
                $has=MutualFund::where('temp_tin_no',$request->temp_tin_no)->get();
                if (count($has)>0) {
                    $error='Temporary TIN no already exist.';
                    return Helper::ErrorResponse($error);
                }else {
                    $second_client_id=$request->second_client_id;
                    $second_client_name=$request->second_client_name;
                    $second_client_pan=$request->second_client_pan;
                    if ($second_client_id=='' && $second_client_name!='' && $second_client_pan!='') {
                        $s_c_data=Client::create(array(
                            'client_name'=>$second_client_name,
                            'pan'=>$second_client_pan,
                            'client_type'=>'E',
                            // 'created_by'=>'',
                        ));  
                        $second_client_id=$s_c_data->id;
                    }

                    $third_client_id=$request->third_client_id;
                    $third_client_name=$request->third_client_name;
                    $third_client_pan=$request->third_client_pan;
                    if ($second_client_id=='' && $third_client_name!='' && $third_client_pan!='') {
                        $t_c_data=Client::create(array(
                            'client_name'=>$third_client_name,
                            'pan'=>$third_client_pan,
                            'client_type'=>'E',
                            // 'created_by'=>'',
                        ));  
                        $third_client_id=$t_c_data->id;
                    }

                    $app_form_scan=$request->app_form_scan;
                    $doc_name='';
                    if ($app_form_scan) {
                        $cv_path_extension=$app_form_scan->getClientOriginalExtension();
                        $doc_name=microtime().".".$cv_path_extension;
                        $app_form_scan->move(public_path('application-form/'),$doc_name);
                    }
                    $data=MutualFund::create(array(
                        'temp_tin_no' =>$request->temp_tin_no,
                        'tin_no'=> $tin_no,
                        'entry_date'=> date('Y-m-d'),
                        'first_client_id'=>$request->first_client_id,
                        'first_kyc'=>$request->first_kyc,
                        'second_client_id'=>isset($second_client_id)?$second_client_id:NULL,
                        'second_kyc'=>isset($request->second_kyc)?$request->second_kyc:NULL,
                        'third_client_id'=>isset($third_client_id)?$third_client_id:NULL,
                        'third_kyc'=>isset($request->third_kyc)?$request->third_kyc:NULL,
                        'trans_scheme_from'=>$request->scheme_id,
                        'trans_scheme_to'=>isset($request->scheme_id_to)?$request->scheme_id_to:NULL,
                        'option_id'=>isset($request->option)?$request->option:NULL,
                        'plan_id'=>isset($request->plan)?$request->plan:NULL,
                        'option_id_to'=>isset($request->option_to)?$request->option_to:NULL,
                        'plan_id_to'=>isset($request->plan_to)?$request->plan_to:NULL,
                        'folio_no'=>isset($request->folio_no)?$request->folio_no:NULL,
                        'amount'=>isset($request->amount)?$request->amount:'',
                        'unit'=>isset($request->unit)?$request->unit:'',
                        'switch_by'=>isset($request->switch_by)?$request->switch_by:NULL,
                        'trans_id'=>$request->trans_id,
                        'sip_start_date'=>isset($request->sip_start_date)?date('Y-m-d',strtotime($request->sip_start_date)):NULL,
                        'sip_end_date'=>isset($request->sip_end_date)?date('Y-m-d',strtotime($request->sip_end_date)):NULL,
                        'chq_no'=>$request->chq_no,
                        'chq_bank'=>$request->chq_bank,
                        // 'rnt_login_at'=>$request->rnt_login_at,
                        'app_form_scan'=>$doc_name,
                        'form_scan_status'=>$request->form_scan_status,
                        'remarks'=>$request->remarks,
                        'form_status'=>'P',
                        // 'created_by'=>'',
                    ));    
                }
            } else {
                // return $request; // if temp tin not exist
                // return $tin_no;
                // craete TTIN no
                $is_has=FormReceived::orderBy('created_at','desc')->get();
                if (count($is_has)>0) {
                    $temp_tin_no=Helper::TempTINGen((count($is_has)+1)); // generate temp tin no
                }else{
                    $temp_tin_no=Helper::TempTINGen(1); // generate temp tin no
                }
                $arn_no=Helper::CommonParamValue(1);
                $branch_code=1;
                $fr_data=FormReceived::create(array(
                    'rec_datetime'=>date('Y-m-d H:i:s'),
                    'temp_tin_no'=>$temp_tin_no,
                    'bu_type'=>$request->bu_type,
                    'arn_no'=>$arn_no,
                    'euin_no'=>$request->euin_no,
                    'sub_arn_no'=>isset($request->sub_arn_no)?$request->sub_arn_no:NULL,
                    'sub_brk_cd'=>isset($request->sub_brk_cd)?$request->sub_brk_cd:NULL,
                    'client_id'=>$request->first_client_id,
                    'product_id'=>1,
                    'trans_id'=>$request->trans_id,
                    'scheme_id'=>$request->scheme_id,
                    'scheme_id_to'=>isset($request->scheme_id_to)?$request->scheme_id_to:NULL,
                    'recv_from'=>$request->recv_from,
                    'inv_type'=>isset($request->inv_type)?$request->inv_type:'N',
                    'application_no'=>isset($request->application_no)?$request->application_no:NULL,
                    'kyc_status'=>$request->kyc_status,
                    'branch_code'=>$branch_code,
                    // 'created_by'=>'',
                ));   
                // return $fr_data;
                $ttin_no=$fr_data->temp_tin_no;

                    $second_client_id=$request->second_client_id;
                    $second_client_name=$request->second_client_name;
                    $second_client_pan=$request->second_client_pan;
                    if ($second_client_id=='' && $second_client_name!='' && $second_client_pan!='') {
                        $s_c_data=Client::create(array(
                            'client_name'=>$second_client_name,
                            'pan'=>$second_client_pan,
                            'client_type'=>'E',
                            // 'created_by'=>'',
                        ));  
                        $second_client_id=$s_c_data->id;
                    }

                    $third_client_id=$request->third_client_id;
                    $third_client_name=$request->third_client_name;
                    $third_client_pan=$request->third_client_pan;
                    if ($second_client_id=='' && $third_client_name!='' && $third_client_pan!='') {
                        $t_c_data=Client::create(array(
                            'client_name'=>$third_client_name,
                            'pan'=>$third_client_pan,
                            'client_type'=>'E',
                            // 'created_by'=>'',
                        ));  
                        $third_client_id=$t_c_data->id;
                    }

                    $app_form_scan=$request->app_form_scan;
                    $doc_name='';
                    if ($app_form_scan) {
                        $cv_path_extension=$app_form_scan->getClientOriginalExtension();
                        $doc_name=microtime().".".$cv_path_extension;
                        $app_form_scan->move(public_path('application-form/'),$doc_name);
                    }
                    $data=MutualFund::create(array(
                        'temp_tin_no' =>$ttin_no,
                        'tin_no'=> $tin_no,
                        'entry_date'=> date('Y-m-d'),
                        'first_client_id'=>$request->first_client_id,
                        'first_kyc'=>$request->first_kyc,
                        'second_client_id'=>isset($second_client_id)?$second_client_id:NULL,
                        'second_kyc'=>isset($request->second_kyc)?$request->second_kyc:NULL,
                        'third_client_id'=>isset($third_client_id)?$third_client_id:NULL,
                        'third_kyc'=>isset($request->third_kyc)?$request->third_kyc:NULL,
                        'trans_scheme_from'=>$request->scheme_id,
                        'trans_scheme_to'=>isset($request->scheme_id_to)?$request->scheme_id_to:NULL,
                        'option_id'=>isset($request->option)?$request->option:NULL,
                        'plan_id'=>isset($request->plan)?$request->plan:NULL,
                        'option_id_to'=>isset($request->option_to)?$request->option_to:NULL,
                        'plan_id_to'=>isset($request->plan_to)?$request->plan_to:NULL,
                        'folio_no'=>isset($request->folio_no)?$request->folio_no:NULL,
                        'amount'=>isset($request->amount)?$request->amount:'',
                        'unit'=>isset($request->unit)?$request->unit:'',
                        'switch_by'=>isset($request->switch_by)?$request->switch_by:NULL,
                        'trans_id'=>$request->trans_id,
                        'sip_start_date'=>isset($request->sip_start_date)?date('Y-m-d',strtotime($request->sip_start_date)):NULL,
                        'sip_end_date'=>isset($request->sip_end_date)?date('Y-m-d',strtotime($request->sip_end_date)):NULL,
                        'chq_no'=>$request->chq_no,
                        'chq_bank'=>$request->chq_bank,
                        // 'rnt_login_at'=>$request->rnt_login_at,
                        'app_form_scan'=>$doc_name,
                        'form_scan_status'=>$request->form_scan_status,
                        'remarks'=>$request->remarks,
                        'form_status'=>'P',
                        // 'created_by'=>'',
                    )); 
            }
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function update(Request $request)
    {
        // return $request;
        // return Carbon::parse($request->rnt_login_cutt_off)->format('Y-m-d H:i:s');
        try {
            // return $request;
            $data1=MutualFund::where('tin_no',$request->tin_no)->first();
            $ack_copy_scan=$request->ack_copy_scan;
            if ($ack_copy_scan) {
                $path_extension=$ack_copy_scan->getClientOriginalExtension();
                $ack_copy_scan_name=microtime().".".$path_extension;
                $ack_copy_scan->move(public_path('acknowledgement-copy/'),$ack_copy_scan_name);
            }else{
                $ack_copy_scan_name=$data1->ack_copy_scan;
                // return $doc_name;
            }
            // ack_copy_scan
                MutualFund::where('tin_no',$request->tin_no)->update(array(
                    'rnt_login_dt'=>date('Y-m-d',strtotime($request->rnt_login_date)),
                    'rnt_login_cutt_off'=>Carbon::parse($request->rnt_login_cutt_off)->format('Y-m-d H:i:s'),
                    'ack_copy_scan'=>$ack_copy_scan_name,
                    'form_status'=>'A',
                ));   
            // return $data1;
            $data=MutualFund::where('tin_no',$request->tin_no)->first();
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function updateOld(Request $request)
    {
        return $request;
        try {
            // return $request;
            $data1=MutualFund::where('tin_no',$request->tin_no)->first();
            $app_form_scan=$request->app_form_scan;
            if ($app_form_scan) {
                $cv_path_extension=$app_form_scan->getClientOriginalExtension();
                $doc_name=microtime().".".$cv_path_extension;
                $app_form_scan->move(public_path('acknowledgement-copy/'),$doc_name);
            }else{
                $doc_name=$data1->app_form_scan;
                // return $doc_name;
            }
            // ack_copy_scan
                MutualFund::where('tin_no',$request->tin_no)->update(array(
                    // 'temp_tin_id' =>$request->temp_tin_id,
                    // 'tin_no'=> $tin_no,
                    // 'entry_date'=> date('Y-m-d',strtotime($request->entry_date)),
                    'first_client_code'=>$request->first_client_code,
                    'first_pan'=>$request->first_pan,
                    'first_kyc'=>$request->first_kyc,
                    'second_client_code'=>isset($request->second_client_code)?$request->second_client_code:'',
                    'second_pan'=>isset($request->second_pan)?$request->second_pan:'',
                    'second_kyc'=>isset($request->second_kyc)?$request->second_kyc:'',
                    'third_client_code'=>isset($request->third_client_code)?$request->third_client_code:'',
                    'third_pan'=>isset($request->third_pan)?$request->third_pan:'',
                    'third_kyc'=>isset($request->third_kyc)?$request->third_kyc:'',
                    'amc_id'=>$request->amc_id,
                    'trans_catg'=>$request->trans_catg,
                    'trans_subcat'=>$request->trans_subcatg,
                    'trans_scheme_from'=>isset($request->trans_scheme_from)?$request->trans_scheme_from:'',
                    'trans_scheme_to'=>isset($request->scheme_name)?$request->scheme_name:$request->trans_scheme_to,
                    // 'folio_no',
                    'amount'=>$request->amount,
                    // 'unit',
                    'trans_type'=>$request->trans_type,
                    'sip_start_date'=>isset($request->sip_start_date)?date('Y-m-d',strtotime($request->sip_start_date)):'',
                    'sip_end_date'=>isset($request->sip_end_date)?date('Y-m-d',strtotime($request->sip_end_date)):'',
                    'chq_no'=>$request->chq_no,
                    'chq_bank'=>$request->chq_bank,
                    'rnt_login_at'=>$request->rnt_login_at,
                    'app_form_scan'=>$doc_name,
                    'form_scan_status'=>$request->form_scan_status,
                    'remarks'=>$request->remarks,
                    // 'created_by'=>'',
                ));   
            // return $data1;
            $data=MutualFund::where('tin_no',$request->tin_no)->first();
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}
