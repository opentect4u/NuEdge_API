<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{
    MutualFund,
    MFTransTypeSubType,
    MailbackProcess
};
use Validator;
use Illuminate\Support\Carbon;
use Excel;

class MFTransTypeSubTypeController extends Controller
{
    public function Details(Request $request)
    {
        try {
            // return $request;
            $rnt_id=$request->rnt_id;
            if ($rnt_id) {
                $data=MFTransTypeSubType::leftJoin('md_rnt','md_rnt.id','=','md_mf_trans_type_subtype.rnt_id')
                    ->select('md_mf_trans_type_subtype.*','md_rnt.rnt_name as rnt_name')
                    ->where('md_mf_trans_type_subtype.rnt_id',$rnt_id)
                    ->orderBy('md_mf_trans_type_subtype.created_at','desc')
                    ->get();
            }else {
                $data=MFTransTypeSubType::leftJoin('md_rnt','md_rnt.id','=','md_mf_trans_type_subtype.rnt_id')
                    ->select('md_mf_trans_type_subtype.*','md_rnt.rnt_name as rnt_name')
                    ->orderBy('md_mf_trans_type_subtype.created_at','desc')
                    ->get();
            }
        } catch (\Throwable $th) {
            // throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function index(Request $request)
    {
        try {
            $arr_trans_type=json_decode($request->arr_trans_type);
            if (!empty($arr_trans_type)) {
                $data=MFTransTypeSubType::leftJoin('md_rnt','md_rnt.id','=','md_mf_trans_type_subtype.rnt_id')
                    ->select('md_mf_trans_type_subtype.*','md_rnt.rnt_name as rnt_name')
                    ->orderBy('md_mf_trans_type_subtype.created_at','desc')
                    ->whereIn('md_mf_trans_type_subtype.trans_type',$arr_trans_type)
                    ->groupBy('md_mf_trans_type_subtype.trans_sub_type')
                    ->get();
            }else {
                $data=MFTransTypeSubType::leftJoin('md_rnt','md_rnt.id','=','md_mf_trans_type_subtype.rnt_id')
                    ->select('md_mf_trans_type_subtype.*','md_rnt.rnt_name as rnt_name')
                    ->orderBy('md_mf_trans_type_subtype.created_at','desc')
                    ->groupBy('md_mf_trans_type_subtype.trans_type')
                    // ->groupBy('md_mf_trans_type_subtype.trans_sub_type')
                    ->get();
            }
            
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function CreateUpdate(Request $request)
    {
        try {
            // return $request;
            $id=$request->id;
            if ($id > 0) {
                $c_data=MFTransTypeSubType::find($id);
                $c_data->trans_type=$request->trans_type;
                $c_data->trans_sub_type=$request->trans_sub_type;
                $c_data->rnt_id=$request->rnt_id;
                $c_data->c_trans_type_code=$request->c_trans_type_code;
                $c_data->c_k_trans_type=$request->c_k_trans_type;
                $c_data->c_k_trans_sub_type=$request->c_k_trans_sub_type;
                $c_data->k_divident_flag=$request->k_divident_flag;
                $c_data->process_type=$request->process_type;
                $c_data->xirr_process_type=$request->xirr_process_type;
                $c_data->lmf_pl=$request->lmf_pl;
                // $data->updated_by=Helper::modifyUser($request->user());
                $c_data->save();
            }else {
                // return $request;
                if ($request->rnt_id == 1) {  // cams
                    $is_has=MFTransTypeSubType::where('trans_type',$request->trans_type)
                        ->where('trans_sub_type',$request->trans_sub_type)
                        ->where('rnt_id',$request->rnt_id)
                        ->where('c_trans_type_code',$request->c_trans_type_code)
                        ->where('c_k_trans_type',$request->c_k_trans_type)
                        ->where('c_k_trans_sub_type',$request->c_k_trans_sub_type)
                        ->get();
                }else {
                    $is_has=MFTransTypeSubType::where('trans_type',$request->trans_type)
                        ->where('trans_sub_type',$request->trans_sub_type)
                        ->where('rnt_id',$request->rnt_id)
                        ->where('c_k_trans_type',$request->c_k_trans_type)
                        ->where('c_k_trans_sub_type',$request->c_k_trans_sub_type)
                        ->where('k_divident_flag',$request->k_divident_flag)
                        ->get();
                }
                if (count($is_has) > 0) {
                    return Helper::WarningResponse(parent::ALREADY_EXIST);
                }else{
                    $c_data=MFTransTypeSubType::create(array(
                        'trans_type'=>$request->trans_type,
                        'trans_sub_type'=>$request->trans_sub_type,
                        'rnt_id'=>$request->rnt_id,
                        'c_trans_type_code'=>isset($request->c_trans_type_code)?$request->c_trans_type_code:NULL,
                        'c_k_trans_type'=>isset($request->c_k_trans_type)?$request->c_k_trans_type:NULL,
                        'c_k_trans_sub_type'=>isset($request->c_k_trans_sub_type)?$request->c_k_trans_sub_type:NULL,
                        'k_divident_flag'=>isset($request->k_divident_flag)?$request->k_divident_flag:NULL,
                        'process_type'=>isset($request->process_type)?$request->process_type:NULL,
                        'xirr_process_type'=>isset($request->xirr_process_type)?$request->xirr_process_type:NULL,
                        'lmf_pl'=>isset($request->lmf_pl)?$request->lmf_pl:NULL,
                        // 'created_by'=>Helper::modifyUser($request->user()),
                    ));
                }
            }
            $data=MFTransTypeSubType::leftJoin('md_rnt','md_rnt.id','=','md_mf_trans_type_subtype.rnt_id')
                    ->select('md_mf_trans_type_subtype.*','md_rnt.rnt_name as rnt_name')
                    ->where('md_mf_trans_type_subtype.id',$c_data->id)
                    ->first();
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}