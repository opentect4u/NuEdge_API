<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{
    MutualFund,
    FolioTaxStaus,
    MailbackProcess
};
use Validator;
use Illuminate\Support\Carbon;
use Excel;

class FolioTaxStatusController extends Controller
{
    public function Details(Request $request)
    {
        try {
            // return $request;
            $rnt_id=$request->rnt_id;
            if ($rnt_id) {
                $data=FolioTaxStaus::leftJoin('md_rnt','md_rnt.id','=','md_folio_tax_status.rnt_id')
                    ->select('md_folio_tax_status.*','md_rnt.rnt_name as rnt_name')
                    ->where('md_folio_tax_status.rnt_id',$rnt_id)
                    ->orderBy('md_folio_tax_status.created_at','desc')
                    ->get();
            }else {
                $data=FolioTaxStaus::leftJoin('md_rnt','md_rnt.id','=','md_folio_tax_status.rnt_id')
                    ->select('md_folio_tax_status.*','md_rnt.rnt_name as rnt_name')
                    ->orderBy('md_folio_tax_status.created_at','desc')
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
                $data=FolioTaxStaus::leftJoin('md_rnt','md_rnt.id','=','md_folio_tax_status.rnt_id')
                    ->select('md_folio_tax_status.*','md_rnt.rnt_name as rnt_name')
                    ->orderBy('md_folio_tax_status.created_at','desc')
                    ->get();
            }else {
                $data=FolioTaxStaus::leftJoin('md_rnt','md_rnt.id','=','md_folio_tax_status.rnt_id')
                    ->select('md_folio_tax_status.*','md_rnt.rnt_name as rnt_name')
                    ->orderBy('md_folio_tax_status.created_at','desc')
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
                $c_data=FolioTaxStaus::find($id);
                $c_data->status=$request->status;
                $c_data->status_code=$request->status_code;
                $c_data->rnt_id=$request->rnt_id;
                $c_data->updated_by=Helper::modifyUser($request->user());
                $c_data->save();
            }else {
                // return $request;
                if ($request->rnt_id == 1) {  // cams
                    $is_has=FolioTaxStaus::where('status',$request->status)
                        ->where('status_code',$request->status_code)
                        ->where('rnt_id',$request->rnt_id)
                        ->get();
                }else {
                    $is_has=FolioTaxStaus::where('status',$request->status)
                        ->where('status_code',$request->status_code)
                        ->where('rnt_id',$request->rnt_id)
                        ->get();
                }
                if (count($is_has) > 0) {
                    return Helper::WarningResponse(parent::ALREADY_EXIST);
                }else{
                    $c_data=FolioTaxStaus::create(array(
                        'status'=>$request->status,
                        'status_code'=>$request->status_code,
                        'rnt_id'=>$request->rnt_id,
                        'created_by'=>Helper::modifyUser($request->user()),
                    ));
                }
            }
            $data=FolioTaxStaus::leftJoin('md_rnt','md_rnt.id','=','md_folio_tax_status.rnt_id')
                    ->select('md_folio_tax_status.*','md_rnt.rnt_name as rnt_name')
                    ->where('md_folio_tax_status.id',$c_data->id)
                    ->first();
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}
