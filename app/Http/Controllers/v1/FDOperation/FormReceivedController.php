<?php

namespace App\Http\Controllers\v1\FDOperation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{InsProduct,FDFormReceived,Insurance};
use Validator;

class FormReceivedController extends Controller
{
    public function searchDetails(Request $request)
    {
        try {
            // return $request;
            $temp_tin_no=$request->temp_tin_no;
            $proposer_code=$request->proposer_code;
            $recv_from=$request->recv_from;
            $sub_brk_cd=$request->sub_brk_cd;
            $euin_no=$request->euin_no;
            $bu_type=json_decode($request->bu_type);
            $comp_id=json_decode($request->comp_id);
            $start_date=$request->start_date;
            $end_date=$request->end_date;

            $column_name=$request->column_name;
            $sort_by=$request->sort_by;
            $paginate=$request->paginate;
            if ($paginate=='A') {
                $paginate=999999999;
            }

            if ($sort_by && $column_name) {
                if ($column_name=="proposer_name") {
                    $data=FDFormReceived::leftJoin('md_client','md_client.id','=','td_fd_form_received.investor_id')
                        ->leftJoin('md_ins_type','md_ins_type.id','=','td_fd_form_received.comp_id')
                    ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_fd_form_received.sub_brk_cd')
                        ->select('td_fd_form_received.*','md_client.client_name as proposer_name','md_client.client_code as proposer_code','md_client.dob as dob','md_client.pan as pan','md_ins_type.type as ins_type_name','md_sub_broker.bro_name as broker_name')
                        ->where('td_fd_form_received.deleted_flag','N')
                        ->orderBy('md_client.client_name' , $sort_by)
                        ->paginate($paginate);
                }elseif ($column_name=="ins_type_name") {
                    // return "hii";
                    $data=FDFormReceived::leftJoin('md_client','md_client.id','=','td_fd_form_received.investor_id')
                        ->leftJoin('md_ins_type','md_ins_type.id','=','td_fd_form_received.comp_id')
                    ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_fd_form_received.sub_brk_cd')
                        ->select('td_fd_form_received.*','md_client.client_name as proposer_name','md_client.client_code as proposer_code','md_client.dob as dob','md_client.pan as pan','md_ins_type.type as ins_type_name','md_sub_broker.bro_name as broker_name')
                        ->where('td_fd_form_received.deleted_flag','N')
                        ->orderBy('td_fd_form_received.comp_id',$sort_by)
                        ->paginate($paginate);
                } else {
                    $data=FDFormReceived::leftJoin('md_client','md_client.id','=','td_fd_form_received.investor_id')
                        ->leftJoin('md_ins_type','md_ins_type.id','=','td_fd_form_received.comp_id')
                    ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_fd_form_received.sub_brk_cd')
                        ->select('td_fd_form_received.*','md_client.client_name as proposer_name','md_client.client_code as proposer_code','md_client.dob as dob','md_client.pan as pan','md_ins_type.type as ins_type_name','md_sub_broker.bro_name as broker_name')
                        ->where('td_fd_form_received.deleted_flag','N')
                        ->orderBy('td_fd_form_received.'.$column_name , $sort_by)
                        ->paginate($paginate);
                }
            }elseif ($start_date && $end_date) {
                // return 'hii';
                $data=FDFormReceived::leftJoin('md_client','md_client.id','=','td_fd_form_received.investor_id')
                    ->leftJoin('md_ins_type','md_ins_type.id','=','td_fd_form_received.comp_id')
                    ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_fd_form_received.sub_brk_cd')
                    ->select('td_fd_form_received.*','md_client.client_name as proposer_name','md_client.client_code as proposer_code','md_client.dob as dob','md_client.pan as pan','md_ins_type.type as ins_type_name','md_sub_broker.bro_name as broker_name')
                    ->where('td_fd_form_received.deleted_flag','N')
                    ->whereDate('td_fd_form_received.rec_datetime','>=',$start_date)
                    ->whereDate('td_fd_form_received.rec_datetime','<=',$end_date)
                    ->paginate($paginate);
            }elseif ($temp_tin_no!='') {
                $data=FDFormReceived::leftJoin('md_client','md_client.id','=','td_fd_form_received.investor_id')
                    ->leftJoin('md_ins_type','md_ins_type.id','=','td_fd_form_received.comp_id')
                    ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_fd_form_received.sub_brk_cd')
                    ->select('td_fd_form_received.*','md_client.client_name as proposer_name','md_client.client_code as proposer_code','md_client.dob as dob','md_client.pan as pan','md_ins_type.type as ins_type_name','md_sub_broker.bro_name as broker_name')
                    ->where('td_fd_form_received.deleted_flag','N')
                    ->where('td_fd_form_received.temp_tin_no',$temp_tin_no)
                    ->paginate($paginate);
            }elseif ($recv_from!='') {
                $data=FDFormReceived::leftJoin('md_client','md_client.id','=','td_fd_form_received.investor_id')
                    ->leftJoin('md_ins_type','md_ins_type.id','=','td_fd_form_received.comp_id')
                    ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_fd_form_received.sub_brk_cd')
                    ->select('td_fd_form_received.*','md_client.client_name as proposer_name','md_client.client_code as proposer_code','md_client.dob as dob','md_client.pan as pan','md_ins_type.type as ins_type_name','md_sub_broker.bro_name as broker_name')
                    ->where('td_fd_form_received.deleted_flag','N')
                    ->where('td_fd_form_received.recv_from','like', '%' . $recv_from . '%')
                    ->paginate($paginate);
            }elseif ($proposer_code!='') {
                $data=FDFormReceived::leftJoin('md_client','md_client.id','=','td_fd_form_received.investor_id')
                    ->leftJoin('md_ins_type','md_ins_type.id','=','td_fd_form_received.comp_id')
                    ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_fd_form_received.sub_brk_cd')
                    ->select('td_fd_form_received.*','md_client.client_name as proposer_name','md_client.client_code as proposer_code','md_client.dob as dob','md_client.pan as pan','md_ins_type.type as ins_type_name','md_sub_broker.bro_name as broker_name')
                    ->where('td_fd_form_received.deleted_flag','N')
                    ->where('md_client.client_code','like', '%' . $proposer_code . '%')
                    ->orWhere('md_client.client_name','like', '%' . $proposer_code . '%')
                    ->orWhere('md_client.pan','like', '%' . $proposer_code . '%')
                    ->paginate($paginate);
            }elseif (!empty($comp_id)) {
                $data=FDFormReceived::leftJoin('md_client','md_client.id','=','td_fd_form_received.investor_id')
                    ->leftJoin('md_ins_type','md_ins_type.id','=','td_fd_form_received.comp_id')
                    ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_fd_form_received.sub_brk_cd')
                    ->select('td_fd_form_received.*','md_client.client_name as proposer_name','md_client.client_code as proposer_code','md_client.dob as dob','md_client.pan as pan','md_ins_type.type as ins_type_name','md_sub_broker.bro_name as broker_name')
                    ->where('td_fd_form_received.deleted_flag','N')
                    ->whereIn('td_fd_form_received.comp_id',$comp_id)
                    ->paginate($paginate);
            }elseif (!empty($bu_type)) {
                $data=FDFormReceived::leftJoin('md_client','md_client.id','=','td_fd_form_received.investor_id')
                    ->leftJoin('md_ins_type','md_ins_type.id','=','td_fd_form_received.comp_id')
                    ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_fd_form_received.sub_brk_cd')
                    ->select('td_fd_form_received.*','md_client.client_name as proposer_name','md_client.client_code as proposer_code','md_client.dob as dob','md_client.pan as pan','md_ins_type.type as ins_type_name','md_sub_broker.bro_name as broker_name')
                    ->where('td_fd_form_received.deleted_flag','N')
                    ->whereIn('td_fd_form_received.bu_type',$bu_type)
                    ->paginate($paginate);
            }else {
                $data=FDFormReceived::leftJoin('md_client','md_client.id','=','td_fd_form_received.investor_id')
                    ->leftJoin('md_fd_company','md_fd_company.id','=','td_fd_form_received.comp_id')
                    ->leftJoin('md_fd_scheme','md_fd_scheme.id','=','td_fd_form_received.scheme_id')
                    ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_fd_form_received.sub_brk_cd')
                    ->leftJoin('md_employee','md_employee.euin_no','=','td_fd_form_received.euin_no')
                    ->select('td_fd_form_received.*','md_client.client_name as investor_name','md_client.client_code as investor_code','md_client.dob as dob','md_client.pan as pan',
                    'md_fd_company.comp_short_name as comp_short_name','md_fd_company.comp_full_name as comp_full_name','md_fd_scheme.scheme_name as scheme_name','md_sub_broker.bro_name as broker_name',
                    'md_employee.emp_name as emp_name')
                    ->where('td_fd_form_received.deleted_flag','N')
                    ->paginate($paginate);
            }
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function export(Request $request)
    {
        try {
            // return $request;
            $temp_tin_no=$request->temp_tin_no;
            $proposer_code=$request->proposer_code;
            $recv_from=$request->recv_from;
            $sub_brk_cd=$request->sub_brk_cd;
            $euin_no=$request->euin_no;
            $bu_type=json_decode($request->bu_type);
            $comp_id=json_decode($request->comp_id);
            $start_date=$request->start_date;
            $end_date=$request->end_date;

            $column_name=$request->column_name;
            $sort_by=$request->sort_by;
            $paginate=$request->paginate;
            

            if ($sort_by && $column_name) {
                if ($column_name=="proposer_name") {
                    $data=FDFormReceived::leftJoin('md_client','md_client.id','=','td_fd_form_received.investor_id')
                        ->leftJoin('md_ins_type','md_ins_type.id','=','td_fd_form_received.comp_id')
                    ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_fd_form_received.sub_brk_cd')
                        ->select('td_fd_form_received.*','md_client.client_name as proposer_name','md_client.client_code as proposer_code','md_client.dob as dob','md_client.pan as pan','md_ins_type.type as ins_type_name','md_sub_broker.bro_name as broker_name')
                        ->where('td_fd_form_received.deleted_flag','N')
                        ->orderBy('md_client.client_name' , $sort_by)
                        ->get();
                }elseif ($column_name=="ins_type_name") {
                    // return "hii";
                    $data=FDFormReceived::leftJoin('md_client','md_client.id','=','td_fd_form_received.investor_id')
                        ->leftJoin('md_ins_type','md_ins_type.id','=','td_fd_form_received.comp_id')
                    ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_fd_form_received.sub_brk_cd')
                        ->select('td_fd_form_received.*','md_client.client_name as proposer_name','md_client.client_code as proposer_code','md_client.dob as dob','md_client.pan as pan','md_ins_type.type as ins_type_name','md_sub_broker.bro_name as broker_name')
                        ->where('td_fd_form_received.deleted_flag','N')
                        ->orderBy('td_fd_form_received.comp_id',$sort_by)
                        ->get();
                } else {
                    $data=FDFormReceived::leftJoin('md_client','md_client.id','=','td_fd_form_received.investor_id')
                        ->leftJoin('md_ins_type','md_ins_type.id','=','td_fd_form_received.comp_id')
                    ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_fd_form_received.sub_brk_cd')
                        ->select('td_fd_form_received.*','md_client.client_name as proposer_name','md_client.client_code as proposer_code','md_client.dob as dob','md_client.pan as pan','md_ins_type.type as ins_type_name','md_sub_broker.bro_name as broker_name')
                        ->where('td_fd_form_received.deleted_flag','N')
                        ->orderBy('td_fd_form_received.'.$column_name , $sort_by)
                        ->get();
                }
            }elseif ($start_date && $end_date) {
                // return 'hii';
                $data=FDFormReceived::leftJoin('md_client','md_client.id','=','td_fd_form_received.investor_id')
                    ->leftJoin('md_ins_type','md_ins_type.id','=','td_fd_form_received.comp_id')
                    ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_fd_form_received.sub_brk_cd')
                    ->select('td_fd_form_received.*','md_client.client_name as proposer_name','md_client.client_code as proposer_code','md_client.dob as dob','md_client.pan as pan','md_ins_type.type as ins_type_name','md_sub_broker.bro_name as broker_name')
                    ->where('td_fd_form_received.deleted_flag','N')
                    ->whereDate('td_fd_form_received.rec_datetime','>=',$start_date)
                    ->whereDate('td_fd_form_received.rec_datetime','<=',$end_date)
                    ->get();
            }elseif ($temp_tin_no!='') {
                $data=FDFormReceived::leftJoin('md_client','md_client.id','=','td_fd_form_received.investor_id')
                    ->leftJoin('md_ins_type','md_ins_type.id','=','td_fd_form_received.comp_id')
                    ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_fd_form_received.sub_brk_cd')
                    ->select('td_fd_form_received.*','md_client.client_name as proposer_name','md_client.client_code as proposer_code','md_client.dob as dob','md_client.pan as pan','md_ins_type.type as ins_type_name','md_sub_broker.bro_name as broker_name')
                    ->where('td_fd_form_received.deleted_flag','N')
                    ->where('td_fd_form_received.temp_tin_no',$temp_tin_no)
                    ->get();
            }elseif ($recv_from!='') {
                $data=FDFormReceived::leftJoin('md_client','md_client.id','=','td_fd_form_received.investor_id')
                    ->leftJoin('md_ins_type','md_ins_type.id','=','td_fd_form_received.comp_id')
                    ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_fd_form_received.sub_brk_cd')
                    ->select('td_fd_form_received.*','md_client.client_name as proposer_name','md_client.client_code as proposer_code','md_client.dob as dob','md_client.pan as pan','md_ins_type.type as ins_type_name','md_sub_broker.bro_name as broker_name')
                    ->where('td_fd_form_received.deleted_flag','N')
                    ->where('td_fd_form_received.recv_from','like', '%' . $recv_from . '%')
                    ->get();
            }elseif ($proposer_code!='') {
                $data=FDFormReceived::leftJoin('md_client','md_client.id','=','td_fd_form_received.investor_id')
                    ->leftJoin('md_ins_type','md_ins_type.id','=','td_fd_form_received.comp_id')
                    ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_fd_form_received.sub_brk_cd')
                    ->select('td_fd_form_received.*','md_client.client_name as proposer_name','md_client.client_code as proposer_code','md_client.dob as dob','md_client.pan as pan','md_ins_type.type as ins_type_name','md_sub_broker.bro_name as broker_name')
                    ->where('td_fd_form_received.deleted_flag','N')
                    ->where('md_client.client_code','like', '%' . $proposer_code . '%')
                    ->orWhere('md_client.client_name','like', '%' . $proposer_code . '%')
                    ->orWhere('md_client.pan','like', '%' . $proposer_code . '%')
                    ->get();
            }elseif (!empty($comp_id)) {
                $data=FDFormReceived::leftJoin('md_client','md_client.id','=','td_fd_form_received.investor_id')
                    ->leftJoin('md_ins_type','md_ins_type.id','=','td_fd_form_received.comp_id')
                    ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_fd_form_received.sub_brk_cd')
                    ->select('td_fd_form_received.*','md_client.client_name as proposer_name','md_client.client_code as proposer_code','md_client.dob as dob','md_client.pan as pan','md_ins_type.type as ins_type_name','md_sub_broker.bro_name as broker_name')
                    ->where('td_fd_form_received.deleted_flag','N')
                    ->whereIn('td_fd_form_received.comp_id',$comp_id)
                    ->get();
            }elseif (!empty($bu_type)) {
                $data=FDFormReceived::leftJoin('md_client','md_client.id','=','td_fd_form_received.investor_id')
                    ->leftJoin('md_ins_type','md_ins_type.id','=','td_fd_form_received.comp_id')
                    ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_fd_form_received.sub_brk_cd')
                    ->select('td_fd_form_received.*','md_client.client_name as proposer_name','md_client.client_code as proposer_code','md_client.dob as dob','md_client.pan as pan','md_ins_type.type as ins_type_name','md_sub_broker.bro_name as broker_name')
                    ->where('td_fd_form_received.deleted_flag','N')
                    ->whereIn('td_fd_form_received.bu_type',$bu_type)
                    ->get();
            }else {
                $data=FDFormReceived::leftJoin('md_client','md_client.id','=','td_fd_form_received.investor_id')
                    ->leftJoin('md_ins_type','md_ins_type.id','=','td_fd_form_received.comp_id')
                    ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_fd_form_received.sub_brk_cd')
                    ->select('td_fd_form_received.*','md_client.client_name as proposer_name','md_client.client_code as proposer_code','md_client.dob as dob','md_client.pan as pan','md_ins_type.type as ins_type_name','md_sub_broker.bro_name as broker_name')
                    ->where('td_fd_form_received.deleted_flag','N')
                    ->get();
            }
        } catch (\Throwable $th) {
            // throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function show(Request $request)
    {
        try {
            $temp_tin_no=$request->temp_tin_no;
            $flag=$request->flag;

            if ($temp_tin_no && $flag=='C') {
                // return 'Hii';
                $data=FDFormReceived::leftJoin('md_client','md_client.id','=','td_fd_form_received.investor_id')
                    ->leftJoin('md_ins_type','md_ins_type.id','=','td_fd_form_received.comp_id')
                    ->leftJoin('md_employee','md_employee.euin_no','=','td_fd_form_received.euin_no')
                    ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_fd_form_received.sub_brk_cd')
                    ->select('td_fd_form_received.*','md_client.client_name as proposer_name','md_client.client_code as proposer_code','md_client.client_type as proposer_type','md_client.dob as dob','md_client.pan as pan','md_ins_type.type as ins_type_name','md_sub_broker.bro_name as broker_name',
                    'md_employee.emp_name as emp_name')
                    ->where('td_fd_form_received.deleted_flag','N')
                    ->where('td_fd_form_received.temp_tin_no',$temp_tin_no)
                    ->get();
                // return $data;
                if (count($data)>0) {
                    $data1=Insurance::where('delete_flag','N')
                        ->where('temp_tin_no', $temp_tin_no)
                        ->get();
                        // return $data1;
                    if (count($data1)>0) {
                        $data=[];
                        return Helper::SuccessResponse($data);
                    }
                }   
            }elseif ($temp_tin_no) {
                $data=FDFormReceived::leftJoin('md_client','md_client.id','=','td_fd_form_received.investor_id')
                    ->leftJoin('md_fd_company','md_fd_company.id','=','td_fd_form_received.comp_id')
                    ->leftJoin('md_fd_scheme','md_fd_scheme.id','=','td_fd_form_received.scheme_id')
                    ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_fd_form_received.sub_brk_cd')
                    ->leftJoin('md_employee','md_employee.euin_no','=','td_fd_form_received.euin_no')
                    ->select('td_fd_form_received.*','md_client.client_name as investor_name','md_client.client_code as investor_code','md_client.dob as dob','md_client.pan as pan',
                    'md_fd_company.comp_short_name as comp_short_name','md_fd_company.comp_full_name as comp_full_name','md_fd_scheme.scheme_name as scheme_name','md_sub_broker.bro_name as broker_name',
                    'md_employee.emp_name as emp_name')
                    ->where('td_fd_form_received.deleted_flag','N')
                    ->where('td_fd_form_received.temp_tin_no',$temp_tin_no)
                    ->get();
            }else {
                $data=FDFormReceived::leftJoin('md_client','md_client.id','=','td_fd_form_received.investor_id')
                    ->leftJoin('md_fd_company','md_fd_company.id','=','td_fd_form_received.comp_id')
                    ->leftJoin('md_fd_scheme','md_fd_scheme.id','=','td_fd_form_received.scheme_id')
                    ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_fd_form_received.sub_brk_cd')
                    ->leftJoin('md_employee','md_employee.euin_no','=','td_fd_form_received.euin_no')
                    ->select('td_fd_form_received.*','md_client.client_name as investor_name','md_client.client_code as investor_code','md_client.dob as dob','md_client.pan as pan',
                    'md_fd_company.comp_short_name as comp_short_name','md_fd_company.comp_full_name as comp_full_name','md_fd_scheme.scheme_name as scheme_name','md_sub_broker.bro_name as broker_name',
                    'md_employee.emp_name as emp_name')
                    ->where('td_fd_form_received.deleted_flag','N')
                    ->get();
            }
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function create(Request $request)
    {
        $validator = Validator::make(request()->all(),[
            'euin_no' =>'required',
            'bu_type' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            // return $request;
            $is_has=FDFormReceived::orderBy('created_at','desc')->get();
            if (count($is_has)>0) {
                $temp_tin_no=Helper::TempTINGen((count($is_has)+1),4); // generate temp tin no
            }else{
                $temp_tin_no=Helper::TempTINGen(1,4); // generate temp tin no
            }
            
                // $bu_type='D';
                $arn_no=Helper::CommonParamValue(1);
                // $euin_to=Helper::CommonParamValue(2);
                $branch_code=1;
                $data=FDFormReceived::create(array(
                    'rec_datetime'=>date('Y-m-d H:i:s'),
                    'temp_tin_no'=>$temp_tin_no,
                    'bu_type'=>$request->bu_type,
                    'arn_no'=>$arn_no,
                    'euin_no'=>$request->euin_no,
                    'sub_arn_no'=>isset($request->sub_arn_no)?$request->sub_arn_no:NULL,
                    'sub_brk_cd'=>isset($request->sub_brk_cd)?$request->sub_brk_cd:NULL,
                    'investor_id'=>$request->investor_id,
                    'fd_bu_type'=>$request->fd_bu_type,
                    'comp_id'=>$request->company_id,
                    'scheme_id'=>$request->scheme_id,
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
                $data=FDFormReceived::where('temp_tin_no',$request->temp_tin_no)->update([
                    'bu_type'=>$request->bu_type,
                    'arn_no'=>$arn_no,
                    'euin_no'=>$request->euin_no,
                    'sub_arn_no'=>isset($request->sub_arn_no)?$request->sub_arn_no:NULL,
                    'sub_brk_cd'=>isset($request->sub_brk_cd)?$request->sub_brk_cd:NULL,
                    'investor_id'=>$request->investor_id,
                    'fd_bu_type'=>$request->fd_bu_type,
                    'comp_id'=>$request->comp_id,
                    'scheme_id'=>$request->scheme_id,
                    'recv_from'=>$request->recv_from,
                    'proposal_no'=>isset($request->proposal_no)?$request->proposal_no:NULL,
                    'branch_code'=>$branch_code,
                    // 'created_by'=>'',
                ]);      
                $data=FDFormReceived::leftJoin('md_client','md_client.id','=','td_fd_form_received.investor_id')
                    ->leftJoin('md_ins_type','md_ins_type.id','=','td_fd_form_received.comp_id')
                    ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_fd_form_received.sub_brk_cd')
                    ->select('td_fd_form_received.*','md_client.client_name as proposer_name','md_client.client_code as proposer_code','md_client.dob as dob','md_client.pan as pan','md_ins_type.type as ins_type_name','md_sub_broker.bro_name as broker_name')
                    ->where('td_fd_form_received.deleted_flag','N')
                    ->where('td_fd_form_received.temp_tin_no',$request->temp_tin_no)
                    ->first();
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function delete(Request $request){
        $validator = Validator::make(request()->all(),[
            'id' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            // return $request;
            $data=Insurance::where('temp_tin_no',$request->id)->get();
            if (count($data)>0) {
                $msg='Not delete';
                return Helper::ErrorResponse($msg);
            }else {
                $data=FDFormReceived::where('temp_tin_no',$request->id)->update([
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

