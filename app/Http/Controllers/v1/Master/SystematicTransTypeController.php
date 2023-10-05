<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{
    MutualFund,
    SystematicTransType,
    MailbackProcess
};
use Validator;
use Illuminate\Support\Carbon;
use Excel;

class SystematicTransTypeController extends Controller
{
    public function Details(Request $request)
    {
        try {
            // return $request;
            $rnt_id=$request->rnt_id;
            if ($rnt_id) {
                $data=SystematicTransType::leftJoin('md_rnt','md_rnt.id','=','md_systematic_trans_type.rnt_id')
                    ->select('md_systematic_trans_type.*','md_rnt.rnt_name as rnt_name')
                    ->where('md_systematic_trans_type.rnt_id',$rnt_id)
                    ->orderBy('md_systematic_trans_type.created_at','desc')
                    ->get();
            }else {
                $data=SystematicTransType::leftJoin('md_rnt','md_rnt.id','=','md_systematic_trans_type.rnt_id')
                    ->select('md_systematic_trans_type.*','md_rnt.rnt_name as rnt_name')
                    ->orderBy('md_systematic_trans_type.created_at','desc')
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
                $data=SystematicTransType::leftJoin('md_rnt','md_rnt.id','=','md_systematic_trans_type.rnt_id')
                    ->select('md_systematic_trans_type.*','md_rnt.rnt_name as rnt_name')
                    ->orderBy('md_systematic_trans_type.created_at','desc')
                    ->whereIn('md_systematic_trans_type.trans_type',$arr_trans_type)
                    ->groupBy('md_systematic_trans_type.trans_sub_type')
                    ->get();
            }else {
                $data=SystematicTransType::leftJoin('md_rnt','md_rnt.id','=','md_systematic_trans_type.rnt_id')
                    ->select('md_systematic_trans_type.*','md_rnt.rnt_name as rnt_name')
                    ->orderBy('md_systematic_trans_type.created_at','desc')
                    ->groupBy('md_systematic_trans_type.trans_type')
                    // ->groupBy('md_systematic_trans_type.trans_sub_type')
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
                $c_data=SystematicTransType::find($id);
                $c_data->trans_type=$request->trans_type;
                $c_data->trans_sub_type=$request->trans_sub_type;
                $c_data->trans_type_code=$request->trans_type_code;
                $c_data->rnt_id=$request->rnt_id;
                $c_data->save();
            }else {
                // return $request;
                if ($request->rnt_id == 1) {  // cams
                    $is_has=SystematicTransType::where('trans_type',$request->trans_type)
                        ->where('trans_sub_type',$request->trans_sub_type)
                        ->where('trans_type_code',$request->trans_type_code)
                        ->where('rnt_id',$request->rnt_id)
                        ->get();
                }else {
                    $is_has=SystematicTransType::where('trans_type',$request->trans_type)
                        ->where('trans_sub_type',$request->trans_sub_type)
                        ->where('trans_type_code',$request->trans_type_code)
                        ->where('rnt_id',$request->rnt_id)
                        ->get();
                }
                if (count($is_has) > 0) {
                    return Helper::WarningResponse(parent::ALREADY_EXIST);
                }else{
                    $c_data=SystematicTransType::create(array(
                        'trans_type'=>$request->trans_type,
                        'trans_sub_type'=>$request->trans_sub_type,
                        'trans_type_code'=>$request->trans_type_code,
                        'rnt_id'=>$request->rnt_id,
                    ));
                }
            }
            $data=SystematicTransType::leftJoin('md_rnt','md_rnt.id','=','md_systematic_trans_type.rnt_id')
                    ->select('md_systematic_trans_type.*','md_rnt.rnt_name as rnt_name')
                    ->where('md_systematic_trans_type.id',$c_data->id)
                    ->first();
        } catch (\Throwable $th) {
            // throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}
