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
    QueryNature,
    QueryGivenBy,
    QueryRecGivenThrogh
};
use Validator;
use Illuminate\Support\Carbon;
use Excel;
use App\Helpers\TransHelper;
use DB;

class QueryNatureController extends Controller
{
    public function index(Request $request)
    {
        try {  
            $search=$request->search;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            if ($search!='') {
                $data=QueryNature::where('product_name','like', '%' . $search . '%')->get();      
            }else {
                $data=QueryNature::get();      
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
            'query_nature' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            if ($request->id > 0) {
                $data=QueryNature::find($request->id);
                $data->query_nature=$request->query_nature;
                $data->updated_by=Helper::modifyUser($request->user());
                $data->save();
            }else{
                $data=QueryNature::create(array(
                    'query_nature'=>$request->query_nature,
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

    public function indexGivenBy(Request $request)
    {
        try {  
            $search=$request->search;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            if ($search!='') {
                $data=QueryGivenBy::where('product_name','like', '%' . $search . '%')->get();      
            }else {
                $data=QueryGivenBy::get();      
            }
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function createUpdateGivenBy(Request $request)
    {
        $validator = Validator::make(request()->all(),[
            'name' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            if ($request->id > 0) {
                $data=QueryGivenBy::find($request->id);
                $data->name=$request->name;
                $data->updated_by=Helper::modifyUser($request->user());
                $data->save();
            }else{
                $data=QueryGivenBy::create(array(
                    'name'=>$request->name,
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

    public function indexGivenThrough(Request $request)
    {
        try {  
            $search=$request->search;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            if ($search!='') {
                $data=QueryRecGivenThrogh::where('product_name','like', '%' . $search . '%')->get();      
            }else {
                $data=QueryRecGivenThrogh::get();      
            }
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function createUpdateGivenThrough(Request $request)
    {
        $validator = Validator::make(request()->all(),[
            'name' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            if ($request->id > 0) {
                $data=QueryRecGivenThrogh::find($request->id);
                $data->name=$request->name;
                $data->updated_by=Helper::modifyUser($request->user());
                $data->save();
            }else{
                $data=QueryRecGivenThrogh::create(array(
                    'name'=>$request->name,
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