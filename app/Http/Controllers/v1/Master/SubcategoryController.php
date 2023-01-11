<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\SubCategory;
use Validator;

class SubcategoryController extends Controller
{
    public function index(Request $request)
    {
        try {  
            $search=$request->search;
            if ($search!='') {
                $data=SubCategory::where('subcategory_name','like', '%' . $search . '%')->get();      
            }
            $data=SubCategory::get();      
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function createUpdate(Request $request)
    {
        $validator = Validator::make(request()->all(),[
            'category_id' =>'required',
            'subcategory_name' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            if ($request->id > 0) {
                $data=SubCategory::find($request->id);
                $data->category_id=$request->category_id;
                $data->subcategory_name=$request->subcategory_name;
                $data->save();
            }else{
                $data=SubCategory::create(array(
                    'category_id'=>$request->category_id,
                    'subcategory_name'=>$request->subcategory_name,
                    'created_by'=>'',
                ));      
            }    
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

   
}
