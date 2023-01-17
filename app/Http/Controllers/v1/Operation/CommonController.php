<?php

namespace App\Http\Controllers\v1\Operation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{RNT,Product,AMC,Category,SubCategory,FormType,SubBroker,Transction};
use Validator;
use DB;

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

    // get transction using type id
    public function showTrans(Request $request)
    {
        try {  
            $data=Transction::where('trans_type_id',$request->trans_type_id)->get();      
        } catch (\Throwable $th) {
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }


    public function showTransInFormRec(Request $request)
    {
        try {  
            // return 
            $product_id=$request->product_id;
            $data=DB::table('md_trns_type')
                ->join('md_trans','md_trans.trans_type_id','=','md_trns_type.id')
                ->select('md_trans.*')
                ->where('md_trns_type.product_id',$product_id)->get();
            // return $data;
            // $data=Transction::where('trans_id',$request->trans_id)->get();      
        } catch (\Throwable $th) {
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}
