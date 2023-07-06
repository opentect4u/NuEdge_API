<?php

namespace App\Http\Controllers\v1\INSOperation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{InsProduct,InsFormReceived,Insurance};
use Validator;

class FormReceivedController extends Controller
{
    public function searchDetails(Request $request)
    {
        try {
            // return $request;
            $paginate=$request->paginate;
            $field=$request->field;
            $order=$request->order;

            $from_date=$request->from_date;
            $to_date=$request->to_date;
            $temp_tin_no=$request->temp_tin_no;
            $proposer_code=$request->proposer_code;
            $ins_bu_type_id=json_decode($request->ins_bu_type_id);
            $ins_type_id=json_decode($request->ins_type_id);
            $recv_from=$request->recv_from;
            
            $brn_cd=json_decode($request->brn_cd);
            $bu_type=json_decode($request->bu_type);
            $rm_id=json_decode($request->rm_id);
            $sub_brk_cd=json_decode($request->sub_brk_cd);
            $euin_no=json_decode($request->euin_no);
            // return $request;
            if ($paginate=='A') {
                $paginate=999999999;
            }

            if ($order && $field) {
                // $rawOrderBy=$field.($order > 0)?' ASC':' DESC';
                $rawOrderBy='';
                if ($order > 0) {
                    $rawOrderBy=$field." ASC";
                } else {
                    $rawOrderBy=$field." DESC";
                }
                if (($from_date && $to_date) || $temp_tin_no || $proposer_code || $ins_bu_type_id || $ins_type_id || $recv_from) {
                    $rawQuery='';
                    $rawQuery=$this->filterCriteria($rawQuery,$from_date,$to_date,$temp_tin_no,$proposer_code,$ins_bu_type_id,$ins_type_id,$recv_from);
                    $data=InsFormReceived::leftJoin('md_client','md_client.id','=','td_ins_form_received.proposer_id')
                        ->leftJoin('md_ins_type','md_ins_type.id','=','td_ins_form_received.ins_type_id')
                        ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_ins_form_received.sub_brk_cd')
                        ->leftJoin('md_ins_company','md_ins_company.id','=','td_ins_form_received.company_id')
                        ->leftJoin('md_branch','md_branch.id','=','td_ins_form_received.branch_code')
                        ->leftJoin('md_employee','md_employee.euin_no','=','td_ins_form_received.euin_no')
                        ->select('td_ins_form_received.*','td_ins_form_received.rec_datetime as entry_date','md_client.client_name as proposer_name','md_client.client_code as proposer_code','md_client.dob as dob','md_client.pan as pan','md_ins_type.type as ins_type_name','md_sub_broker.bro_name as broker_name',
                        'md_ins_company.comp_short_name as comp_short_name','md_ins_company.comp_full_name as comp_full_name','md_branch.brn_name as branch_name','md_employee.emp_name as rm_name','md_employee.emp_name as emp_name')
                        ->where('td_ins_form_received.deleted_flag','N')
                        ->whereRaw($rawQuery)
                        ->orderByRaw($rawOrderBy)
                        ->paginate($paginate);
                }else {
                    $data=InsFormReceived::leftJoin('md_client','md_client.id','=','td_ins_form_received.proposer_id')
                        ->leftJoin('md_ins_type','md_ins_type.id','=','td_ins_form_received.ins_type_id')
                        ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_ins_form_received.sub_brk_cd')
                        ->leftJoin('md_ins_company','md_ins_company.id','=','td_ins_form_received.company_id')
                        ->leftJoin('md_branch','md_branch.id','=','td_ins_form_received.branch_code')
                        ->leftJoin('md_employee','md_employee.euin_no','=','td_ins_form_received.euin_no')
                        ->select('td_ins_form_received.*','td_ins_form_received.rec_datetime as entry_date','md_client.client_name as proposer_name','md_client.client_code as proposer_code','md_client.dob as dob','md_client.pan as pan','md_ins_type.type as ins_type_name','md_sub_broker.bro_name as broker_name',
                        'md_ins_company.comp_short_name as comp_short_name','md_ins_company.comp_full_name as comp_full_name','md_branch.brn_name as branch_name','md_employee.emp_name as rm_name','md_employee.emp_name as emp_name')
                        ->where('td_ins_form_received.deleted_flag','N')
                        ->orderByRaw($rawOrderBy)
                        ->paginate($paginate);
                }
            }elseif (($from_date && $to_date) || $temp_tin_no || $proposer_code || $ins_bu_type_id || $ins_type_id || $recv_from) {
                $rawQuery='';
                $rawQuery=$this->filterCriteria($rawQuery,$from_date,$to_date,$temp_tin_no,$proposer_code,$ins_bu_type_id,$ins_type_id,$recv_from);
                // return $rawQuery;
                $data=InsFormReceived::leftJoin('md_client','md_client.id','=','td_ins_form_received.proposer_id')
                    ->leftJoin('md_ins_type','md_ins_type.id','=','td_ins_form_received.ins_type_id')
                    ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_ins_form_received.sub_brk_cd')
                    ->leftJoin('md_ins_company','md_ins_company.id','=','td_ins_form_received.company_id')
                    ->leftJoin('md_branch','md_branch.id','=','td_ins_form_received.branch_code')
                    ->leftJoin('md_employee','md_employee.euin_no','=','td_ins_form_received.euin_no')
                    ->select('td_ins_form_received.*','td_ins_form_received.rec_datetime as entry_date','md_client.client_name as proposer_name','md_client.client_code as proposer_code','md_client.dob as dob','md_client.pan as pan','md_ins_type.type as ins_type_name','md_sub_broker.bro_name as broker_name',
                    'md_ins_company.comp_short_name as comp_short_name','md_ins_company.comp_full_name as comp_full_name','md_branch.brn_name as branch_name','md_employee.emp_name as rm_name','md_employee.emp_name as emp_name')
                    ->where('td_ins_form_received.deleted_flag','N')
                    ->whereRaw($rawQuery)
                    ->orderBy('td_ins_form_received.updated_at','DESC')
                    ->paginate($paginate);
            }else {
                $data=InsFormReceived::leftJoin('md_client','md_client.id','=','td_ins_form_received.proposer_id')
                    ->leftJoin('md_ins_type','md_ins_type.id','=','td_ins_form_received.ins_type_id')
                    ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_ins_form_received.sub_brk_cd')
                    ->leftJoin('md_ins_company','md_ins_company.id','=','td_ins_form_received.company_id')
                    ->leftJoin('md_branch','md_branch.id','=','td_ins_form_received.branch_code')
                    ->leftJoin('md_employee','md_employee.euin_no','=','td_ins_form_received.euin_no')
                    ->select('td_ins_form_received.*','td_ins_form_received.rec_datetime as entry_date','md_client.client_name as proposer_name','md_client.client_code as proposer_code','md_client.dob as dob','md_client.pan as pan','md_ins_type.type as ins_type_name','md_sub_broker.bro_name as broker_name',
                    'md_ins_company.comp_short_name as comp_short_name','md_ins_company.comp_full_name as comp_full_name','md_branch.brn_name as branch_name','md_employee.emp_name as rm_name','md_employee.emp_name as emp_name')
                    ->where('td_ins_form_received.deleted_flag','N')
                    ->orderBy('td_ins_form_received.updated_at','DESC')
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
            $paginate=$request->paginate;
            $field=$request->field;
            $order=$request->order;

            $from_date=$request->from_date;
            $to_date=$request->to_date;
            $temp_tin_no=$request->temp_tin_no;
            $proposer_code=$request->proposer_code;
            $ins_bu_type_id=json_decode($request->ins_bu_type_id);
            $ins_type_id=json_decode($request->ins_type_id);
            $recv_from=$request->recv_from;
            
            $brn_cd=json_decode($request->brn_cd);
            $bu_type=json_decode($request->bu_type);
            $rm_id=json_decode($request->rm_id);
            $sub_brk_cd=json_decode($request->sub_brk_cd);
            $euin_no=json_decode($request->euin_no);
            // return $request;
            if ($paginate=='A') {
                $paginate=999999999;
            }

            if ($order && $field) {
                // $rawOrderBy=$field.($order > 0)?' ASC':' DESC';
                $rawOrderBy='';
                if ($order > 0) {
                    $rawOrderBy=$field." ASC";
                } else {
                    $rawOrderBy=$field." DESC";
                }
                if (($from_date && $to_date) || $temp_tin_no || $proposer_code || $ins_bu_type_id || $ins_type_id || $recv_from) {
                    $rawQuery='';
                    $rawQuery=$this->filterCriteria($rawQuery,$from_date,$to_date,$temp_tin_no,$proposer_code,$ins_bu_type_id,$ins_type_id,$recv_from);
                    $data=InsFormReceived::leftJoin('md_client','md_client.id','=','td_ins_form_received.proposer_id')
                        ->leftJoin('md_ins_type','md_ins_type.id','=','td_ins_form_received.ins_type_id')
                        ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_ins_form_received.sub_brk_cd')
                        ->leftJoin('md_ins_company','md_ins_company.id','=','td_ins_form_received.company_id')
                        ->leftJoin('md_branch','md_branch.id','=','td_ins_form_received.branch_code')
                        ->leftJoin('md_employee','md_employee.euin_no','=','td_ins_form_received.euin_no')
                        ->select('td_ins_form_received.*','td_ins_form_received.rec_datetime as entry_date','md_client.client_name as proposer_name','md_client.client_code as proposer_code','md_client.dob as dob','md_client.pan as pan','md_ins_type.type as ins_type_name','md_sub_broker.bro_name as broker_name',
                        'md_ins_company.comp_short_name as comp_short_name','md_ins_company.comp_full_name as comp_full_name','md_branch.brn_name as branch_name','md_employee.emp_name as rm_name','md_employee.emp_name as emp_name')
                        ->where('td_ins_form_received.deleted_flag','N')
                        ->whereRaw($rawQuery)
                        ->orderByRaw($rawOrderBy)
                        ->get();
                }else {
                    $data=InsFormReceived::leftJoin('md_client','md_client.id','=','td_ins_form_received.proposer_id')
                        ->leftJoin('md_ins_type','md_ins_type.id','=','td_ins_form_received.ins_type_id')
                        ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_ins_form_received.sub_brk_cd')
                        ->leftJoin('md_ins_company','md_ins_company.id','=','td_ins_form_received.company_id')
                        ->leftJoin('md_branch','md_branch.id','=','td_ins_form_received.branch_code')
                        ->leftJoin('md_employee','md_employee.euin_no','=','td_ins_form_received.euin_no')
                        ->select('td_ins_form_received.*','td_ins_form_received.rec_datetime as entry_date','md_client.client_name as proposer_name','md_client.client_code as proposer_code','md_client.dob as dob','md_client.pan as pan','md_ins_type.type as ins_type_name','md_sub_broker.bro_name as broker_name',
                        'md_ins_company.comp_short_name as comp_short_name','md_ins_company.comp_full_name as comp_full_name','md_branch.brn_name as branch_name','md_employee.emp_name as rm_name','md_employee.emp_name as emp_name')
                        ->where('td_ins_form_received.deleted_flag','N')
                        ->orderByRaw($rawOrderBy)
                        ->get();
                }
            }elseif (($from_date && $to_date) || $temp_tin_no || $proposer_code || $ins_bu_type_id || $ins_type_id || $recv_from) {
                $rawQuery='';
                $rawQuery=$this->filterCriteria($rawQuery,$from_date,$to_date,$temp_tin_no,$proposer_code,$ins_bu_type_id,$ins_type_id,$recv_from);
                // return $rawQuery;
                $data=InsFormReceived::leftJoin('md_client','md_client.id','=','td_ins_form_received.proposer_id')
                    ->leftJoin('md_ins_type','md_ins_type.id','=','td_ins_form_received.ins_type_id')
                    ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_ins_form_received.sub_brk_cd')
                    ->leftJoin('md_ins_company','md_ins_company.id','=','td_ins_form_received.company_id')
                    ->leftJoin('md_branch','md_branch.id','=','td_ins_form_received.branch_code')
                    ->leftJoin('md_employee','md_employee.euin_no','=','td_ins_form_received.euin_no')
                    ->select('td_ins_form_received.*','td_ins_form_received.rec_datetime as entry_date','md_client.client_name as proposer_name','md_client.client_code as proposer_code','md_client.dob as dob','md_client.pan as pan','md_ins_type.type as ins_type_name','md_sub_broker.bro_name as broker_name',
                    'md_ins_company.comp_short_name as comp_short_name','md_ins_company.comp_full_name as comp_full_name','md_branch.brn_name as branch_name','md_employee.emp_name as rm_name','md_employee.emp_name as emp_name')
                    ->where('td_ins_form_received.deleted_flag','N')
                    ->whereRaw($rawQuery)
                    ->orderBy('td_ins_form_received.updated_at','DESC')
                    ->get();
            }else {
                $data=InsFormReceived::leftJoin('md_client','md_client.id','=','td_ins_form_received.proposer_id')
                    ->leftJoin('md_ins_type','md_ins_type.id','=','td_ins_form_received.ins_type_id')
                    ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_ins_form_received.sub_brk_cd')
                    ->leftJoin('md_ins_company','md_ins_company.id','=','td_ins_form_received.company_id')
                    ->leftJoin('md_branch','md_branch.id','=','td_ins_form_received.branch_code')
                    ->leftJoin('md_employee','md_employee.euin_no','=','td_ins_form_received.euin_no')
                    ->select('td_ins_form_received.*','td_ins_form_received.rec_datetime as entry_date','md_client.client_name as proposer_name','md_client.client_code as proposer_code','md_client.dob as dob','md_client.pan as pan','md_ins_type.type as ins_type_name','md_sub_broker.bro_name as broker_name',
                    'md_ins_company.comp_short_name as comp_short_name','md_ins_company.comp_full_name as comp_full_name','md_branch.brn_name as branch_name','md_employee.emp_name as rm_name','md_employee.emp_name as emp_name')
                    ->where('td_ins_form_received.deleted_flag','N')
                    ->orderBy('td_ins_form_received.updated_at','DESC')
                    ->get();
            }
        } catch (\Throwable $th) {
            //throw $th;
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
                $data=InsFormReceived::leftJoin('md_client','md_client.id','=','td_ins_form_received.proposer_id')
                    ->leftJoin('md_ins_type','md_ins_type.id','=','td_ins_form_received.ins_type_id')
                    ->leftJoin('md_employee','md_employee.euin_no','=','td_ins_form_received.euin_no')
                    ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_ins_form_received.sub_brk_cd')
                    ->leftJoin('md_ins_company','md_ins_company.id','=','td_ins_form_received.company_id')
                    ->select('td_ins_form_received.*','md_client.client_name as proposer_name','md_client.client_code as proposer_code','md_client.client_type as proposer_type','md_client.dob as dob','md_client.pan as pan','md_ins_type.type as ins_type_name','md_sub_broker.bro_name as broker_name',
                    'md_ins_company.comp_short_name as comp_short_name','md_ins_company.comp_full_name as comp_full_name',
                    'md_employee.emp_name as emp_name')
                    ->where('td_ins_form_received.deleted_flag','N')
                    ->where('td_ins_form_received.temp_tin_no',$temp_tin_no)
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
                $data=InsFormReceived::leftJoin('md_client','md_client.id','=','td_ins_form_received.proposer_id')
                    ->leftJoin('md_ins_type','md_ins_type.id','=','td_ins_form_received.ins_type_id')
                    ->leftJoin('md_employee','md_employee.euin_no','=','td_ins_form_received.euin_no')
                    ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_ins_form_received.sub_brk_cd')
                    ->leftJoin('md_ins_company','md_ins_company.id','=','td_ins_form_received.company_id')
                    ->select('td_ins_form_received.*','md_client.client_name as proposer_name','md_client.client_code as proposer_code','md_client.client_type as proposer_type','md_client.dob as dob','md_client.pan as pan','md_ins_type.type as ins_type_name','md_sub_broker.bro_name as broker_name',
                    'md_ins_company.comp_short_name as comp_short_name','md_ins_company.comp_full_name as comp_full_name',
                    'md_employee.emp_name as emp_name')
                    ->where('td_ins_form_received.deleted_flag','N')
                    ->where('td_ins_form_received.temp_tin_no','like', '%' . $temp_tin_no . '%')
                    ->get();
            }else {
                $data=InsFormReceived::leftJoin('md_client','md_client.id','=','td_ins_form_received.proposer_id')
                    ->leftJoin('md_ins_type','md_ins_type.id','=','td_ins_form_received.ins_type_id')
                    ->leftJoin('md_employee','md_employee.euin_no','=','td_ins_form_received.euin_no')
                    ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_ins_form_received.sub_brk_cd')
                    ->leftJoin('md_ins_company','md_ins_company.id','=','td_ins_form_received.company_id')
                    ->select('td_ins_form_received.*','md_client.client_name as proposer_name','md_client.client_code as proposer_code','md_client.client_type as proposer_type','md_client.dob as dob','md_client.pan as pan','md_ins_type.type as ins_type_name','md_sub_broker.bro_name as broker_name',
                    'md_ins_company.comp_short_name as comp_short_name','md_ins_company.comp_full_name as comp_full_name',
                    'md_employee.emp_name as emp_name')
                    ->where('td_ins_form_received.deleted_flag','N')
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
            $is_has=InsFormReceived::orderBy('created_at','desc')->get();
            if (count($is_has)>0) {
                $temp_tin_no=Helper::TempTINGen((count($is_has)+1),3); // generate temp tin no
            }else{
                $temp_tin_no=Helper::TempTINGen(1,3); // generate temp tin no
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
                    'ins_type_id'=>$request->ins_type_id,
                    'company_id'=>$request->company_id,
                    'recv_from'=>$request->recv_from,
                    'proposal_no'=>isset($request->proposal_no)?$request->proposal_no:NULL,
                    'branch_code'=>$branch_code,
                    // 'created_by'=>'',
                ));      
              
        } catch (\Throwable $th) {
            throw $th;
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
                $data=InsFormReceived::where('temp_tin_no',$request->temp_tin_no)->update([
                    'bu_type'=>$request->bu_type,
                    'arn_no'=>$arn_no,
                    'euin_no'=>$request->euin_no,
                    'sub_arn_no'=>isset($request->sub_arn_no)?$request->sub_arn_no:NULL,
                    'sub_brk_cd'=>isset($request->sub_brk_cd)?$request->sub_brk_cd:NULL,
                    'proposer_id'=>$request->proposer_id,
                    'insure_bu_type'=>$request->insure_bu_type,
                    'ins_type_id'=>$request->ins_type_id,
                    'recv_from'=>$request->recv_from,
                    'proposal_no'=>isset($request->proposal_no)?$request->proposal_no:NULL,
                    'branch_code'=>$branch_code,
                    // 'created_by'=>'',
                ]);      
                $data=InsFormReceived::leftJoin('md_client','md_client.id','=','td_ins_form_received.proposer_id')
                    ->leftJoin('md_ins_type','md_ins_type.id','=','td_ins_form_received.ins_type_id')
                    ->leftJoin('md_sub_broker','md_sub_broker.code','=','td_ins_form_received.sub_brk_cd')
                    ->leftJoin('md_ins_company','md_ins_company.id','=','td_ins_form_received.company_id')
                    ->select('td_ins_form_received.*','md_client.client_name as proposer_name','md_client.client_code as proposer_code','md_client.dob as dob','md_client.pan as pan','md_ins_type.type as ins_type_name','md_sub_broker.bro_name as broker_name',
                    'md_ins_company.comp_short_name as comp_short_name','md_ins_company.comp_full_name as comp_full_name')
                    ->where('td_ins_form_received.deleted_flag','N')
                    ->where('td_ins_form_received.temp_tin_no',$request->temp_tin_no)
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
                $data=InsFormReceived::where('temp_tin_no',$request->id)->update([
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

    public function filterCriteria($rawQuery,$from_date,$to_date,$temp_tin_no,$proposer_code,$ins_bu_type_id,$ins_type_id,$recv_from)
    {
        $queryString='td_ins_form_received.rec_datetime';
        $rawQuery.=Helper::FrmToDateRawQuery($from_date,$to_date,$rawQuery,$queryString);
        $queryString1='td_ins_form_received.temp_tin_no';
        $rawQuery.=Helper::WhereRawQuery($temp_tin_no,$rawQuery,$queryString1);
        $queryString2='td_ins_form_received.proposer_id';
        $rawQuery.=Helper::WhereRawQuery($proposer_code,$rawQuery,$queryString2);
        $queryString4='td_ins_form_received.insure_bu_type';
        $rawQuery.=Helper::WhereRawQuery($ins_bu_type_id,$rawQuery,$queryString4);
        $queryString5='td_ins_form_received.ins_type_id';
        $rawQuery.=Helper::WhereRawQuery($ins_type_id,$rawQuery,$queryString5);
        $queryString6='td_ins_form_received.recv_from';
        $rawQuery.=Helper::RawQueryLike($recv_from,$rawQuery,$queryString6);
        return $rawQuery;
    }
}
