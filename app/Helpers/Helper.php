<?php
namespace App\Helpers;
use App\Http\Controllers\Controller;
use DB;

class Helper{

    public function SuccessResponse($data)
    {
        return response()->json([
            'suc'=>1,
            'msg'=>Controller::DATA_FETCH_SUCCESS,
            'data'=>$data
        ],200);
    }

    public function ErrorResponse($msg)
    {
        return response()->json([
            'suc'=>0,
            'msg'=>$msg,
            'data'=>[]
        ],400);
    }
    public function ipWhitelist($ip)
    {
        return response()->json([
            'suc'=>10,
            'msg'=>Controller::IP_WHITELIST_ERROR.$ip,
            'data'=>[]
        ],400);
    }

    public function CommonParamValue($id)
    {
        return DB::table('md_parameters')->where('sl_no',$id)->value('param_value');
    }

    public function TempTINGen($val)
    {
        $tin='T00'.$val;
        return $tin;
    }

    public function GenTIN($product,$trans_type,$val)
    {
        if ($product==1) {  // MUTUAL FUND
            if ($trans_type==1) {
                $tin='F00'.$val;
            // }else if (condition) {
            }
        }
        return $tin;
    }

}