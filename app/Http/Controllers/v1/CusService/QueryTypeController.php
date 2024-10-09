<?php

namespace App\Http\Controllers\v1\CusService;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{
    MutualFund,
    Client,
    FormReceived,
    MutualFundTransaction,
    MFTransTypeSubType,
    Disclaimer,
    QueryType
};
use Validator;
use Illuminate\Support\Carbon;
use Excel;
use App\Helpers\TransHelper;
use DB;

class QueryTypeController extends Controller
{
    public function index(Request $request)
    {
        try {  
            $search=$request->search;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            $flag=$request->flag;
            $query_type=$request->query_type;
            $product_id=$request->product_id;
            if ($search!='') {
                $data=QueryType::where('product_name','like', '%' . $search . '%')->get();      
            }elseif ($flag=='E') {
                $data=QueryType::groupBy('query_type')->get();      
            }elseif ($product_id) {
                $data=QueryType::where('product_id',$product_id)->get();      
            }elseif ($query_type) {
                $data=QueryType::where('query_type',$query_type)->get();      
            } else {
                $data=QueryType::leftJoin('md_products','md_products.id','=','md_query_type.product_id')
                    ->select('md_query_type.*','md_products.product_name')
                    ->get();      
            }
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function createUpdate(Request $request)
    {
        $validator = Validator::make(request()->all(),[
            'product_id' =>'required',
            'query_type' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            // return $request;
            if ((int)$request->id > 0) {
                $data=QueryType::find($request->id);
                // $data->product_id=$request->product_id;
                $data->query_type=$request->query_type;
                $data->updated_by=Helper::modifyUser($request->user());
                $data->save();
            }else{
                // return $request;
                $is_has=QueryType::where('product_id',$request->product_id)
                    ->where('query_type',$request->query_type)
                    ->get();
                if (count($is_has) > 0) {
                    return Helper::WarningResponse(parent::ALREADY_EXIST);
                }else {
                    $data=QueryType::create(array(
                        'product_id'=>$request->product_id,
                        'query_type'=>$request->query_type,
                        'created_by'=>Helper::modifyUser($request->user()),
                        'updated_by'=>Helper::modifyUser($request->user()),
                    )); 
                }     
            }    
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}