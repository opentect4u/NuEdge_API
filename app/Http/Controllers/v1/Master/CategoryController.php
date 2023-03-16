<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{Category,Scheme};
use Validator;
use Excel;
use App\Imports\CategoryImport;

class CategoryController extends Controller
{
    public function searchDetails(Request $request)
    {
        try {
            $paginate=$request->paginate;
            $cat_name=$request->cat_name;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            if ($paginate=='A') {
                $paginate=999999999;
            }
            if ($cat_name) {
                $data=Category::where('delete_flag','N')->where('cat_name','like', '%' . $cat_name . '%')
                ->orderBy('updated_at','DESC')->paginate($paginate);  
            } else {
                $data=Category::where('delete_flag','N')->orderBy('updated_at','DESC')->paginate($paginate);  
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
            $cat_name=$request->cat_name;
            if ($cat_name) {
                $data=Category::where('delete_flag','N')->where('cat_name','like', '%' . $cat_name . '%')
                    ->orderBy('updated_at','DESC')->get();  
            } else {
                $data=Category::where('delete_flag','N')->orderBy('updated_at','DESC')->get();  
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
            $product_id=$request->product_id;
            $id=$request->id;
            $paginate=$request->paginate;
            if ($paginate=='A') {
                $paginate=999999999;
            }
            if ($search!='') {
                $data=Category::where('delete_flag','N')->where('cat_name','like', '%' . $search . '%')->get();      
            }else if ($product_id!='') {
                $data=Category::where('delete_flag','N')->where('product_id',$product_id)->get();      
            }else if ($id!='') {
                $data=Category::where('delete_flag','N')->where('id',$id)->get();      
            }else if ($paginate!='') {
                $data=Category::where('delete_flag','N')->paginate($paginate);      
            }else {
                $data=Category::where('delete_flag','N')->get();      
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
            'cat_name' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            if ($request->id > 0) {
                $data=Category::find($request->id);
                $data->product_id=$request->product_id;
                $data->cat_name=$request->cat_name;
                $data->save();
            }else{
                $is_has=Category::where('cat_name',$request->cat_name)->get();
                if (count($is_has) > 0) {
                    return Helper::WarningResponse(parent::ALREADY_EXIST);
                }else {
                    $data=Category::create(array(
                        'product_id'=>$request->product_id,
                        'cat_name'=>$request->cat_name,
                        // 'created_by'=>'',
                    ));    
                }  
            }    
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function delete(Request $request)
    {
        try {
            $id=$request->id;
            $is_has=Scheme::where('category_id',$id)->get();
            if (count($is_has)>0) {
                return Helper::WarningResponse(parent::DELETE_NOT_ALLOW_ERROR);
            }else {
                $data=Category::find($id);
                $data->delete_flag='Y';
                $data->deleted_date=date('Y-m-d H:i:s');
                $data->deleted_by=1;
                $data->save();
            }
        } catch (\Throwable $th) {
            // throw $th;
            return Helper::ErrorResponse(parent::DELETE_FAIL_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function import(Request $request)
    {
        try {
            // return $request;
            $path = $request->file('file')->getRealPath();
            $data = array_map('str_getcsv', file($path));
            // return $data ;

            foreach ($data as $key => $value) {
                if ($key==0) {
                    if ($value[0] == "Category") {
                        // return $value[0] ;
                        return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
                    }
                } else {
                    // return $value[0];
                    // return $request->product_id;
                    // return base64_decode($request->product_id);
                    $is_has=Category::where('cat_name',$value[0])->get();
                    if (count($is_has) < 0) {
                        Category::create(array(
                            'product_id'=>base64_decode($request->product_id),
                            'cat_name'=>$value[0],
                        ));    
                    }  
                }
            }
            // return $data[0][0];
            // return gettype($data[0][0]) ;
            // if (in_array("rnt_id", $data)) {
            // if ($data[0][0] == "rnt_id" && $data[0][1] == "product_id" && $data[0][2] == "amc_name" && $data[0][3] == "website" && $data[0][4] == "ofc_addr") {
            //     return "hii";
                // Excel::import(new CategoryImport,$request->file);
                // Excel::import(new CategoryImport,request()->file('file'));
                $data1=[];
            // }else {
            //     return "else";
            //     return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
            // }
        } catch (\Throwable $th) {
            // throw $th;
            return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
        }
        return Helper::SuccessResponse($data1);
    }
   
}