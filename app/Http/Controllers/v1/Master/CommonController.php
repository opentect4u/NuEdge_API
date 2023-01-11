<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{RNT,Product,AMC,Category,SubCategory};
use Validator;

class CommonController extends Controller
{
    public function showAMC(Request $request)
    {
        try {  
            $data=AMC::where('product_id',$request->product_id)->get();      
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
    public function showCategory(Request $request)
    {
        try {  
            $data=Category::where('product_id',$request->product_id)->get();      
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
    public function showSubCategory(Request $request)
    {
        try {  
            $data=SubCategory::where('category_id',$request->category_id)->get();      
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function productshow(Request $request)
    {
        try {  
            $data=RNT::get();      
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}
