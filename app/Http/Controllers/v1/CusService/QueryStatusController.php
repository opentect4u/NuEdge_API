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
    QueryStatus
};
use Validator;
use Illuminate\Support\Carbon;
use Excel;
use App\Helpers\TransHelper;
use DB;


class QueryStatusController extends Controller
{
    public function index(Request $request)
    {
        try {  
            $search=$request->search;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            if ($search!='') {
                $data=QueryStatus::where('product_name','like', '%' . $search . '%')->get();      
            }else {
                $data=QueryStatus::get();      
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
            'status_name' =>'required',
            'color_code' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            if ($request->id > 0) {
                $data=QueryStatus::find($request->id);
                $data->status_name=$request->status_name;
                $data->color_code=$request->color_code;
                $data->updated_by=Helper::modifyUser($request->user());
                $data->save();
            }else{
                $data=QueryStatus::create(array(
                    'status_name'=>$request->status_name,
                    'color_code'=>$request->color_code,
                    'created_by'=>Helper::modifyUser($request->user()),
                    'updated_by'=>Helper::modifyUser($request->user()),
                ));      
            }    
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}