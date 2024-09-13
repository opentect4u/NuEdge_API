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
    QueryTypeSubtype
};
use Validator;
use Illuminate\Support\Carbon;
use Excel;
use App\Helpers\TransHelper;
use DB;

class QueryTypeSubtypeController extends Controller
{
    public function index(Request $request)
    {
        try {  
            $search=$request->search;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            $flag=$request->flag;
            $query_type=$request->query_type;
            if ($search!='') {
                $data=QueryTypeSubtype::where('product_name','like', '%' . $search . '%')->get();      
            }elseif ($flag=='E') {
                $data=QueryTypeSubtype::groupBy('query_type')->get();      
            }elseif ($query_type) {
                $data=QueryTypeSubtype::where('query_type',$query_type)->get();      
            } else {
                $data=QueryTypeSubtype::get();      
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
            'query_subtype' =>'required',
            'query_tat' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            // return $request;
            if ((int)$request->id > 0) {
                $data=QueryTypeSubtype::find($request->id);
                $data->product_id=$request->product_id;
                $data->query_type=$request->query_type;
                $data->query_subtype=$request->query_subtype;
                $data->query_tat=$request->query_tat;
                $data->updated_by=Helper::modifyUser($request->user());
                $data->save();
            }else{
                // return $request;
                $is_has=QueryTypeSubtype::where('product_id',$request->product_id)
                    ->where('query_type',$request->query_type)
                    ->where('query_subtype',$request->query_subtype)
                    ->get();
                if (count($is_has) > 0) {
                    return Helper::WarningResponse(parent::ALREADY_EXIST);
                }else {
                    $data=QueryTypeSubtype::create(array(
                        'product_id'=>$request->product_id,
                        'query_type'=>$request->query_type,
                        'query_subtype'=>$request->query_subtype,
                        'query_tat'=>$request->query_tat,
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