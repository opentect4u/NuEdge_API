<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{
    MutualFund,
    SystematicUnregistered,
    MailbackProcess
};
use Validator;
use Illuminate\Support\Carbon;
use Excel;

class SystematicUnregisterController extends Controller
{
    public function Details(Request $request)
    {
        try {
            // return $request;
            $rnt_id=$request->rnt_id;
            if ($rnt_id) {
                $data=SystematicUnregistered::leftJoin('md_rnt','md_rnt.id','=','md_systematic_unregistered.rnt_id')
                    ->select('md_systematic_unregistered.*','md_rnt.rnt_name as rnt_name')
                    ->where('md_systematic_unregistered.rnt_id',$rnt_id)
                    ->orderBy('md_systematic_unregistered.created_at','desc')
                    ->get();
            }else {
                $data=SystematicUnregistered::leftJoin('md_rnt','md_rnt.id','=','md_systematic_unregistered.rnt_id')
                    ->select('md_systematic_unregistered.*','md_rnt.rnt_name as rnt_name')
                    ->orderBy('md_systematic_unregistered.created_at','desc')
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
                $data=SystematicUnregistered::leftJoin('md_rnt','md_rnt.id','=','md_systematic_unregistered.rnt_id')
                    ->select('md_systematic_unregistered.*','md_rnt.rnt_name as rnt_name')
                    ->orderBy('md_systematic_unregistered.created_at','desc')
                    ->whereIn('md_systematic_unregistered.trans_type',$arr_trans_type)
                    ->get();
            }else {
                $data=SystematicUnregistered::leftJoin('md_rnt','md_rnt.id','=','md_systematic_unregistered.rnt_id')
                    ->select('md_systematic_unregistered.*','md_rnt.rnt_name as rnt_name')
                    ->orderBy('md_systematic_unregistered.created_at','desc')
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
                $modify_data=SystematicUnregistered::find($id);
                $modify_data->remarks=$request->remarks;
                $modify_data->rnt_id=$request->rnt_id;
                // $data->updated_by=Helper::modifyUser($request->user());
                $modify_data->save();
            }else {
                // return $request;
                if ($request->rnt_id == 1) {  // cams
                    $is_has=SystematicUnregistered::where('remarks',$request->remarks)
                        ->where('rnt_id',$request->rnt_id)
                        ->get();
                }else {
                    $is_has=SystematicUnregistered::where('remarks',$request->remarks)
                        ->where('rnt_id',$request->rnt_id)
                        ->get();
                }
                if (count($is_has) > 0) {
                    return Helper::WarningResponse(parent::ALREADY_EXIST);
                }else{
                    $modify_data=SystematicUnregistered::create(array(
                        'remarks'=>$request->remarks,
                        'rnt_id'=>$request->rnt_id,
                        // 'created_by'=>Helper::modifyUser($request->user()),
                    ));
                }
            }
            $data=SystematicUnregistered::leftJoin('md_rnt','md_rnt.id','=','md_systematic_unregistered.rnt_id')
                    ->select('md_systematic_unregistered.*','md_rnt.rnt_name as rnt_name')
                    ->where('md_systematic_unregistered.id',$modify_data->id)
                    ->first();
        } catch (\Throwable $th) {
            // throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}