<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{
    MutualFund,
    SystematicFrequency,
    MailbackProcess
};
use Validator;
use Illuminate\Support\Carbon;
use Excel;

class SystematicFrequencyController extends Controller
{
    public function Details(Request $request)
    {
        try {
            // return $request;
            $rnt_id=$request->rnt_id;
            if ($rnt_id) {
                $data=SystematicFrequency::leftJoin('md_rnt','md_rnt.id','=','md_systematic_frequency.rnt_id')
                    ->select('md_systematic_frequency.*','md_rnt.rnt_name as rnt_name')
                    ->where('md_systematic_frequency.rnt_id',$rnt_id)
                    ->orderBy('md_systematic_frequency.created_at','desc')
                    ->get();
            }else {
                $data=SystematicFrequency::leftJoin('md_rnt','md_rnt.id','=','md_systematic_frequency.rnt_id')
                    ->select('md_systematic_frequency.*','md_rnt.rnt_name as rnt_name')
                    ->orderBy('md_systematic_frequency.created_at','desc')
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
                $data=SystematicFrequency::leftJoin('md_rnt','md_rnt.id','=','md_systematic_frequency.rnt_id')
                    ->select('md_systematic_frequency.*','md_rnt.rnt_name as rnt_name')
                    ->orderBy('md_systematic_frequency.created_at','desc')
                    ->whereIn('md_systematic_frequency.trans_type',$arr_trans_type)
                    ->groupBy('md_systematic_frequency.trans_sub_type')
                    ->get();
            }else {
                $data=SystematicFrequency::leftJoin('md_rnt','md_rnt.id','=','md_systematic_frequency.rnt_id')
                    ->select('md_systematic_frequency.*','md_rnt.rnt_name as rnt_name')
                    ->orderBy('md_systematic_frequency.created_at','desc')
                    ->groupBy('md_systematic_frequency.trans_type')
                    // ->groupBy('md_systematic_frequency.trans_sub_type')
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
                $c_data=SystematicFrequency::find($id);
                $c_data->freq_code=$request->freq_code;
                $c_data->freq_name=$request->freq_name;
                $c_data->rnt_id=$request->rnt_id;
                $c_data->save();
            }else {
                // return $request;
                if ($request->rnt_id == 1) {  // cams
                    $is_has=SystematicFrequency::where('freq_name',$request->freq_name)
                        ->where('freq_code',$request->freq_code)
                        ->where('rnt_id',$request->rnt_id)
                        ->get();
                }else {
                    $is_has=SystematicFrequency::where('freq_name',$request->freq_name)
                        ->where('freq_code',$request->freq_code)
                        ->where('rnt_id',$request->rnt_id)
                        ->get();
                }
                if (count($is_has) > 0) {
                    return Helper::WarningResponse(parent::ALREADY_EXIST);
                }else{
                    $c_data=SystematicFrequency::create(array(
                        'freq_code'=>$request->freq_code,
                        'freq_name'=>$request->freq_name,
                        'rnt_id'=>$request->rnt_id,
                    ));
                }
            }
            $data=SystematicFrequency::leftJoin('md_rnt','md_rnt.id','=','md_systematic_frequency.rnt_id')
                    ->select('md_systematic_frequency.*','md_rnt.rnt_name as rnt_name')
                    ->where('md_systematic_frequency.id',$c_data->id)
                    ->first();
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}
