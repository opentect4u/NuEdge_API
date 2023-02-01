<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\SubCategory;
use Validator;
use Excel;
use App\Imports\SubCategoryImport;

class SubcategoryController extends Controller
{
    public function index(Request $request)
    {
        try {  
            $search=$request->search;
            $category_id=$request->category_id;
            $id=$request->id;
            if ($search!='') {
                $data=SubCategory::where('subcategory_name','like', '%' . $search . '%')->get();      
            }else if ($category_id!='') {
                $data=SubCategory::where('category_id',$category_id)->get();      
            }else if ($id!='') {
                $data=SubCategory::where('id',$id)->get();      
            }else{
                $data=SubCategory::get();   
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

    public function import(Request $request)
    {
        try {
            // return $request;
            $path = $request->file('file')->getRealPath();
            $data = array_map('str_getcsv', file($path));
            // return $data[0][0];
            // return gettype($data[0][0]) ;
            // if (in_array("rnt_id", $data)) {
            // if ($data[0][0] == "rnt_id" && $data[0][1] == "product_id" && $data[0][2] == "amc_name" && $data[0][3] == "website" && $data[0][4] == "ofc_addr") {
            //     return "hii";
                Excel::import(new SubCategoryImport,$request->file);
                // Excel::import(new SubCategoryImport,request()->file('file'));
                $data1=[];
            // }else {
            //     return "else";
            //     return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
            // }
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
        }
        return Helper::SuccessResponse($data1);
    }
   
}
