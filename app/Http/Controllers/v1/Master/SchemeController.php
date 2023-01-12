<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\Scheme;
use Validator;

class SchemeController extends Controller
{
    public function index(Request $request)
    {
        try {  
            $search=$request->search;
            if ($search!='') {
                $data=Scheme::where('scheme_name','like', '%' . $search . '%')->get();      
            }
            $data=Scheme::whereDate('updated_at',date('Y-m-d'))->get();      
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
            'amc_id' =>'required',
            'category_id' =>'required',
            'subcategory_id' =>'required',
            'scheme_name' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            if ($request->id > 0) {
                $data=Scheme::find($request->id);
                $data->product_id=$request->product_id;
                $data->amc_id=$request->amc_id;
                $data->category_id=$request->category_id;
                $data->subcategory_id=$request->subcategory_id;
                $data->scheme_name=$request->scheme_name;
                $data->save();
            }else{
                $data=Scheme::create(array(
                    'product_id'=>$request->product_id,
                    'amc_id'=>$request->amc_id,
                    'category_id'=>$request->category_id,
                    'subcategory_id'=>$request->subcategory_id,
                    'scheme_name'=>$request->scheme_name,
                    // 'created_by'=>'',
                ));      
            }    
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

  
}
