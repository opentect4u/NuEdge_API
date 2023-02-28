<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{RNT,Product,AMC,Category,SubCategory,FormType,SubBroker,Transction,FormReceived};
use Validator;
use DB;

class CommonController extends Controller
{
    public function CommonParamValue()
    {
        try {
            $data=DB::table('md_parameters')->get();
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}
