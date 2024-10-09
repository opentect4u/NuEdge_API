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
    QuerySubType
};
use Validator;
use Illuminate\Support\Carbon;
use Excel;
use App\Helpers\TransHelper;
use DB;

class QuerySubTypeController extends Controller
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
                $data=QuerySubType::where('product_name','like', '%' . $search . '%')->get();      
            }elseif ($flag=='E') {
                $data=QuerySubType::groupBy('query_type')->get();      
            }elseif ($query_type) {
                $data=QuerySubType::where('query_type',$query_type)->get();      
            } else {
                $data=QuerySubType::leftJoin('md_query_type','md_query_type.id','=','md_query_sub_type.query_type_id')
                    ->select('md_query_sub_type.*','md_query_type.query_type','md_query_type.product_id')
                    ->get();      
            }
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function createUpdate(Request $request)
    {
        $validator = Validator::make(request()->all(),[
            'product_id' =>'required',
            'query_type_id' =>'required',
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
                $up_data=QuerySubType::find($request->id);
                $up_data->query_type_id=$request->query_type_id;
                $up_data->query_subtype=$request->query_subtype;
                $up_data->query_tat=$request->query_tat;
                $up_data->updated_by=Helper::modifyUser($request->user());
                $up_data->save();

                $data=QuerySubType::leftJoin('md_query_type','md_query_type.id','=','md_query_sub_type.query_type_id')
                    ->select('md_query_sub_type.*','md_query_type.query_type','md_query_type.product_id')
                    ->where('md_query_sub_type.id',$up_data->id)
                    ->first();
            }else{
                // return $request;
                $is_has=QuerySubType::where('query_type_id',$request->query_type_id)
                    ->where('query_subtype',$request->query_subtype)
                    ->get();
                if (count($is_has) > 0) {
                    return Helper::WarningResponse(parent::ALREADY_EXIST);
                }else {
                    $data=QuerySubType::create(array(
                        'query_type_id'=>$request->query_type_id,
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