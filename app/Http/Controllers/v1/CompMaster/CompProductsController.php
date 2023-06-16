<?php

namespace App\Http\Controllers\v1\CompMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\CompProduct;
use Validator;

class CompProductsController extends Controller
{
    public function index(Request $request)
    {
        try {  
            $search=$request->search;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            $cm_profile_id=$request->cm_profile_id;
            if ($search!='') {
                $data=CompProduct::where('product_name','like', '%' . $search . '%')->get();      
            }elseif ($cm_profile_id) {
                $data=CompProduct::where('cm_profile_id',$cm_profile_id)->get();      
            } else {
                $data=CompProduct::get();      
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
            'product_name' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            if ($request->id > 0) {
                $data=CompProduct::find($request->id);
                $data->cm_profile_id=$request->cm_profile_id;
                $data->product_name=$request->product_name;
                $data->save();
            }else{
                $data=CompProduct::create(array(
                    'cm_profile_id'=>$request->cm_profile_id,
                    'product_name'=>$request->product_name,
                    // 'created_by'=>'',
                ));      
            }    
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function update(Request $request)
    {
        $validator = Validator::make(request()->all(),[
            'product_name' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse($errors);
        }
        try {
            $data=CompProduct::find($request->id);
            $data->product_name=$request->product_name;
            $data->save();
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}