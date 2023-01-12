<?php

namespace App\Http\Controllers\v1\Operation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{RNT,Product,AMC,Category,SubCategory,FormType,SubBroker};
use Validator;

class CommonController extends Controller
{
    public function showFormType(Request $request)
    {
        try {  
            $data=FormType::where('product_id',$request->product_id)->get();      
        } catch (\Throwable $th) {
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function ShowSubBroker()
    {
        try {  
            $data=SubBroker::groupBy('arn_no')->get();      
        } catch (\Throwable $th) {
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function showSubBrokerCode(Request $request)
    {
        try {  
            $data=SubBroker::where('arn_no',$request->arn_no)->get();      
        } catch (\Throwable $th) {
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}
