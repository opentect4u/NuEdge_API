<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\Product;
use Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {  
            $search=$request->search;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            if ($search!='') {
                $data=Product::where('product_name','like', '%' . $search . '%')->get();      
            }else {
                $data=Product::get();      
            }
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    /**
     * Export the resource.
     *
     * @return \Illuminate\Http\Response
     */
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
                $data=Product::find($request->id);
                $data->product_name=$request->product_name;
                $data->updated_by=Helper::modifyUser($request->user());
                $data->save();
            }else{
                $data=Product::create(array(
                    'product_name'=>$request->product_name,
                    'created_by'=>Helper::modifyUser($request->user()),
                ));      
            }    
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    /**
     * Show the form for creating and update a new resource.
     *
     * @return \Illuminate\Http\Response
     */
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
            $data=Product::find($request->id);
            $data->product_name=$request->product_name;
            $data->updated_by=Helper::modifyUser($request->user());
            $data->save();
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}