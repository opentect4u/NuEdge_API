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
                $c_data->save();
            }else {
                $c_data=MFTransTypeSubType::create(array(
                    'trans_type'=>$request->trans_type,
                    'trans_sub_type'=>$request->trans_sub_type,
                    'rnt_id'=>$request->rnt_id,
                    'c_trans_type_code'=>isset($request->c_trans_type_code)?$request->c_trans_type_code:NULL,
                    'c_k_trans_type'=>isset($request->c_k_trans_type)?$request->c_k_trans_type:NULL,
                    'c_k_trans_sub_type'=>isset($request->c_k_trans_sub_type)?$request->c_k_trans_sub_type:NULL,
                    'k_divident_flag'=>isset($request->k_divident_flag)?$request->k_divident_flag:NULL,
                ));
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