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
    public function searchDetails(Request $request)
    {
        try {
            $paginate=$request->paginate;
            $cat_id=$request->cat_id;
            $subcat_id=$request->subcat_id;
            if ($paginate=='A') {
                $paginate=999999999;
            }
            if ($subcat_id && $cat_id) {
                $data=SubCategory::join('md_category','md_category.id','=','md_subcategory.category_id')
                    ->select('md_subcategory.*','md_category.cat_name as cat_name')
                    ->where('md_subcategory.id',$subcat_id)
                    ->where('md_subcategory.category_id',$cat_id)
                    ->orderBy('updated_at','DESC')
                    ->paginate($paginate);    
            }elseif ($subcat_id) {
                $data=SubCategory::join('md_category','md_category.id','=','md_subcategory.category_id')
                    ->select('md_subcategory.*','md_category.cat_name as cat_name')
                    ->where('md_subcategory.id',$subcat_id)
                    ->orderBy('updated_at','DESC')
                    ->paginate($paginate);     
            }elseif ($cat_id) {
                $data=SubCategory::join('md_category','md_category.id','=','md_subcategory.category_id')
                    ->select('md_subcategory.*','md_category.cat_name as cat_name')
                    ->where('md_subcategory.category_id',$cat_id)
                    ->orderBy('updated_at','DESC')
                    ->paginate($paginate);   
            } else {
                $data=SubCategory::join('md_category','md_category.id','=','md_subcategory.category_id')
                    ->select('md_subcategory.*','md_category.cat_name as cat_name')
                    ->orderBy('updated_at','DESC')
                    ->paginate($paginate);     
            }
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
    public function export(Request $request)
    {
        try {
            $paginate=$request->paginate;
            $cat_id=$request->cat_id;
            $subcat_id=$request->subcat_id;
            if ($subcat_id && $cat_id) {
                $data=SubCategory::join('md_category','md_category.id','=','md_subcategory.category_id')
                    ->select('md_subcategory.*','md_category.cat_name as cat_name')
                    ->where('md_subcategory.id',$subcat_id)
                    ->where('md_subcategory.category_id',$cat_id)
                    ->orderBy('updated_at','DESC')
                    ->get();    
            }elseif ($subcat_id) {
                $data=SubCategory::join('md_category','md_category.id','=','md_subcategory.category_id')
                    ->select('md_subcategory.*','md_category.cat_name as cat_name')
                    ->where('md_subcategory.id',$subcat_id)
                    ->orderBy('updated_at','DESC')
                    ->get();     
            }elseif ($cat_id) {
                $data=SubCategory::join('md_category','md_category.id','=','md_subcategory.category_id')
                    ->select('md_subcategory.*','md_category.cat_name as cat_name')
                    ->where('md_subcategory.category_id',$cat_id)
                    ->orderBy('updated_at','DESC')
                    ->get();   
            } else {
                $data=SubCategory::join('md_category','md_category.id','=','md_subcategory.category_id')
                    ->select('md_subcategory.*','md_category.cat_name as cat_name')
                    ->orderBy('updated_at','DESC')
                    ->get();     
            }
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
    public function index(Request $request)
    {
        try {  
            $search=$request->search;
            $category_id=$request->category_id;
            $id=$request->id;
            $paginate=$request->paginate;
            if ($paginate=='A') {
                $paginate=999999999;
            }
            if ($search!='') {
                $data=SubCategory::where('subcategory_name','like', '%' . $search . '%')->get();      
            }else if ($category_id!='') {
                $data=SubCategory::where('category_id',$category_id)->paginate($paginate);      
            }else if ($id!='') {
                $data=SubCategory::where('id',$id)->get();      
            }else if ($paginate!='') {
                $data=SubCategory::join('md_category','md_category.id','=','md_subcategory.category_id')
                    ->select('md_subcategory.*','md_category.cat_name as cat_name')
                    ->orderBy('updated_at','DESC')
                    ->paginate($paginate);   
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
            $data=SubCategory::join('md_category','md_category.id','=','md_subcategory.category_id')
                ->select('md_subcategory.*','md_category.cat_name as cat_name')
                ->where('md_subcategory.id',$data->id)
                ->orderBy('updated_at','DESC')
                ->first();       
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
