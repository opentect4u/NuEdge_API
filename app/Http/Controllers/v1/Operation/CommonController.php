<?php

namespace App\Http\Controllers\v1\Operation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{RNT,Product,AMC,Category,SubCategory,FormType};
use Validator;

class CommonController extends Controller
{
    public function showFormType(Request $request)
    {
        try {  
            $data=FormType::where('product_id',$request->product_id)->get();      
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}
