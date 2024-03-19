<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{SubCategory,Scheme};
use Validator;
use Excel;
use App\Imports\SubCategoryImport;

class SubcategoryController extends Controller
{
    public function searchDetails(Request $request)
    {
        try {
            $paginate=$request->paginate;
            $cat_id=json_decode($request->cat_id);
            $subcat_id=$request->subcat_id;
            $order=$request->order;
            $field=$request->field;
            if ($paginate=='A') {
                $paginate=999999999;
            }
            // if ($order && $field) {
            //     $rawOrderBy='';
            //     if ($order > 0) {
            //         $rawOrderBy=$field.' ASC';
            //     } else {
            //         $rawOrderBy=$field.' DESC';
            //     }
            //     if ($subcat_id || $cat_id) {
            //         $rawQuery='';
            //         if (!empty($cat_id)) {
            //             $cat_id_string= implode(',', $cat_id);
            //             if (strlen($rawQuery) > 0) {
            //                 $rawQuery.=" AND md_subcategory.category_id IN (".$cat_id_string.")";
            //             }else {
            //                 $rawQuery.=" md_subcategory.category_id IN (".$cat_id_string.")";
            //             }
            //         }
            //         if ($subcat_id) {
            //             if (strlen($rawQuery) > 0) {
            //                 $rawQuery.=" AND md_subcategory.id=".$subcat_id;
            //             }else {
            //                 $rawQuery.=" md_subcategory.id=".$subcat_id;
            //             }
            //         }
            //         $data=SubCategory::join('md_category','md_category.id','=','md_subcategory.category_id')
            //             ->select('md_subcategory.*','md_category.cat_name as cat_name')
            //             ->where('md_subcategory.delete_flag','N')
            //             ->whereRaw($rawQuery)
            //             ->orderByRaw($rawOrderBy)
            //             ->paginate($paginate);    
            //     }else {
            //         $data=SubCategory::join('md_category','md_category.id','=','md_subcategory.category_id')
            //             ->select('md_subcategory.*','md_category.cat_name as cat_name')
            //             ->where('md_subcategory.delete_flag','N')
            //             ->orderByRaw($rawOrderBy)
            //             ->paginate($paginate);     
            //     } 
            // }else 
            if ($subcat_id || $cat_id) {
                $rawQuery='';
                if (!empty($cat_id)) {
                    $cat_id_string= implode(',', $cat_id);
                    if (strlen($rawQuery) > 0) {
                        $rawQuery.=" AND md_subcategory.category_id IN (".$cat_id_string.")";
                    }else {
                        $rawQuery.=" md_subcategory.category_id IN (".$cat_id_string.")";
                    }
                }
                if ($subcat_id) {
                    if (strlen($rawQuery) > 0) {
                        $rawQuery.=" AND md_subcategory.id=".$subcat_id;
                    }else {
                        $rawQuery.=" md_subcategory.id=".$subcat_id;
                    }
                }
                $data=SubCategory::join('md_category','md_category.id','=','md_subcategory.category_id')
                    ->select('md_subcategory.*','md_category.cat_name as cat_name')
                    ->where('md_subcategory.delete_flag','N')
                    // ->where('md_subcategory.id',$subcat_id)
                    // ->where('md_subcategory.category_id',$cat_id)
                    ->whereRaw($rawQuery)
                    ->orderBy('updated_at','DESC')
                    ->get();   
            }else {
                $data=SubCategory::join('md_category','md_category.id','=','md_subcategory.category_id')
                    ->select('md_subcategory.*','md_category.cat_name as cat_name')
                    ->where('md_subcategory.delete_flag','N')
                    ->orderBy('updated_at','DESC')
                    ->get();     
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
            $cat_id=json_decode($request->cat_id);
            $subcat_id=$request->subcat_id;
            $order=$request->order;
            $field=$request->field;
           
            if ($order && $field) {
                $rawOrderBy='';
                if ($order > 0) {
                    $rawOrderBy=$field.' ASC';
                } else {
                    $rawOrderBy=$field.' DESC';
                }
                if ($subcat_id || $cat_id) {
                    $rawQuery='';
                    if (!empty($cat_id)) {
                        $cat_id_string= implode(',', $cat_id);
                        if (strlen($rawQuery) > 0) {
                            $rawQuery.=" AND md_subcategory.category_id IN (".$cat_id_string.")";
                        }else {
                            $rawQuery.=" md_subcategory.category_id IN (".$cat_id_string.")";
                        }
                    }
                    if ($subcat_id) {
                        if (strlen($rawQuery) > 0) {
                            $rawQuery.=" AND md_subcategory.id=".$subcat_id;
                        }else {
                            $rawQuery.=" md_subcategory.id=".$subcat_id;
                        }
                    }
                    $data=SubCategory::join('md_category','md_category.id','=','md_subcategory.category_id')
                        ->select('md_subcategory.*','md_category.cat_name as cat_name')
                        ->where('md_subcategory.delete_flag','N')
                        ->whereRaw($rawQuery)
                        ->orderByRaw($rawOrderBy)
                        ->get();    
                }else {
                    $data=SubCategory::join('md_category','md_category.id','=','md_subcategory.category_id')
                        ->select('md_subcategory.*','md_category.cat_name as cat_name')
                        ->where('md_subcategory.delete_flag','N')
                        ->orderByRaw($rawOrderBy)
                        ->get();     
                } 
            }else if ($subcat_id || $cat_id) {
                $rawQuery='';
                if (!empty($cat_id)) {
                    $cat_id_string= implode(',', $cat_id);
                    if (strlen($rawQuery) > 0) {
                        $rawQuery.=" AND md_subcategory.category_id IN (".$cat_id_string.")";
                    }else {
                        $rawQuery.=" md_subcategory.category_id IN (".$cat_id_string.")";
                    }
                }
                if ($subcat_id) {
                    if (strlen($rawQuery) > 0) {
                        $rawQuery.=" AND md_subcategory.id=".$subcat_id;
                    }else {
                        $rawQuery.=" md_subcategory.id=".$subcat_id;
                    }
                }
                $data=SubCategory::join('md_category','md_category.id','=','md_subcategory.category_id')
                    ->select('md_subcategory.*','md_category.cat_name as cat_name')
                    ->where('md_subcategory.delete_flag','N')
                    // ->where('md_subcategory.id',$subcat_id)
                    // ->where('md_subcategory.category_id',$cat_id)
                    ->whereRaw($rawQuery)
                    ->orderBy('updated_at','DESC')
                    ->get();    
            }else {
                $data=SubCategory::join('md_category','md_category.id','=','md_subcategory.category_id')
                    ->select('md_subcategory.*','md_category.cat_name as cat_name')
                    ->where('md_subcategory.delete_flag','N')
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
            $arr_cat_id=json_decode($request->arr_cat_id);
            $id=$request->id;
            $paginate=$request->paginate;
            if ($paginate=='A') {
                $paginate=999999999;
            }
            if ($search && $arr_cat_id) {
                $data=SubCategory::where('delete_flag','N')
                    ->whereIn('category_id',$arr_cat_id)
                    ->where('subcategory_name','like', '%' . $search . '%')
                    ->get();   
            }else if ($search!='') {
                $data=SubCategory::where('delete_flag','N')->where('subcategory_name','like', '%' . $search . '%')->get();      
            }else if ($arr_cat_id) {
                $data=SubCategory::where('delete_flag','N')
                    ->whereIn('category_id',$arr_cat_id)
                    // ->where('subcategory_name','like', '%' . $search . '%')
                    ->orderBy('subcategory_name','asc')
                    ->get();      
            }else if ($category_id!='') {
                $data=SubCategory::where('delete_flag','N')->where('category_id',$category_id)->paginate($paginate);      
            }else if ($id!='') {
                $data=SubCategory::where('delete_flag','N')->where('id',$id)->get();      
            }else if ($paginate!='') {
                $data=SubCategory::join('md_category','md_category.id','=','md_subcategory.category_id')
                    ->select('md_subcategory.*','md_category.cat_name as cat_name')
                    ->where('md_subcategory.delete_flag','N')
                    ->orderBy('md_subcategory.updated_at','DESC')
                    ->paginate($paginate);   
            }else{
                $data=SubCategory::join('md_category','md_category.id','=','md_subcategory.category_id')
                    ->select('md_subcategory.*','md_category.cat_name as cat_name')
                    ->where('md_subcategory.delete_flag','N')
                    ->orderBy('md_subcategory.updated_at','DESC')
                    ->get();   
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
                $data->updated_by=Helper::modifyUser($request->user());
                $data->save();
            }else{
                $is_has=SubCategory::where('subcategory_name',$request->subcategory_name)->where('delete_flag','N')->get();
                if (count($is_has) > 0) {
                    return Helper::WarningResponse(parent::ALREADY_EXIST);
                }else {
                    $data=SubCategory::create(array(
                        'category_id'=>$request->category_id,
                        'subcategory_name'=>$request->subcategory_name,
                        'created_by'=>Helper::modifyUser($request->user()),
                    ));   
                }   
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

    public function delete(Request $request)
    {
        try {
            $id=$request->id;
            $is_has=Scheme::where('subcategory_id',$id)->get();
            if (count($is_has)>0) {
                return Helper::WarningResponse(parent::DELETE_NOT_ALLOW_ERROR);
            }else {
                $data=SubCategory::find($id);
                $data->delete_flag='Y';
                $data->deleted_date=date('Y-m-d H:i:s');
                $data->deleted_by=Helper::modifyUser($request->user());
                $data->save();
            }
        } catch (\Throwable $th) {
            //throw $th;
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
            // return $data;

            foreach ($data as $key => $value) {
                if ($key==0) {
                    if (str_replace(" ","_",$value[0]) == "Sub_Category") {
                        // return $value[0] ;
                        return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
                    }
                } else {
                    // return $value;
                    // return base64_decode($request->product_id);
                    $is_has=SubCategory::where('subcategory_name',$value[0])->get();
                    if (count($is_has) < 0) {
                        SubCategory::create(array(
                            'category_id'=>$request->cat_id,
                            'subcategory_name'=>$value[0],
                        ));   
                    }   
                }
            }
            // return gettype($data[0][0]) ;
            // if (in_array("rnt_id", $data)) {
            // if ($data[0][0] == "rnt_id" && $data[0][1] == "product_id" && $data[0][2] == "amc_name" && $data[0][3] == "website" && $data[0][4] == "ofc_addr") {
            //     return "hii";
                // Excel::import(new SubCategoryImport,$request->file);
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