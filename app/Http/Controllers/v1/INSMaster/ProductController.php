<?php

namespace App\Http\Controllers\v1\INSMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\InsProduct;
use Validator;

class ProductController extends Controller
{
    public function searchDetails(Request $request)
    {
        try {
            $paginate=$request->paginate;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            $product_name=$request->product_name;
            $company_id=$request->company_id;
            $ins_type_id=$request->ins_type_id;
            if ($paginate=='A') {
                $paginate=999999999;
            }
            if ($sort_by && $column_name) {
                if ($column_name=='ins_type_name') {
                    $data=InsProduct::leftJoin('md_ins_type','md_ins_type.id','=','md_ins_products.ins_type_id')
                        ->leftJoin('md_ins_company','md_ins_company.id','=','md_ins_products.company_id')
                        ->select('md_ins_products.*','md_ins_type.type as ins_type_name','md_ins_company.comp_short_name as comp_short_name','md_ins_company.comp_full_name as comp_full_name')
                        ->where('md_ins_products.delete_flag','N')
                        ->orderBy('md_ins_type.'.$column_name,$sort_by)
                        ->paginate($paginate); 
                }elseif ($column_name=='comp_short_name' || $column_name=='comp_full_name') {
                    $data=InsProduct::leftJoin('md_ins_type','md_ins_type.id','=','md_ins_products.ins_type_id')
                        ->leftJoin('md_ins_company','md_ins_company.id','=','md_ins_products.company_id')
                        ->select('md_ins_products.*','md_ins_type.type as ins_type_name','md_ins_company.comp_short_name as comp_short_name','md_ins_company.comp_full_name as comp_full_name')
                        ->where('md_ins_products.delete_flag','N')
                        ->orderBy('md_ins_company.'.$column_name,$sort_by)
                        ->paginate($paginate); 
                }else {
                    $data=InsProduct::leftJoin('md_ins_type','md_ins_type.id','=','md_ins_products.ins_type_id')
                        ->leftJoin('md_ins_company','md_ins_company.id','=','md_ins_products.company_id')
                        ->select('md_ins_products.*','md_ins_type.type as ins_type_name','md_ins_company.comp_short_name as comp_short_name','md_ins_company.comp_full_name as comp_full_name')
                        ->where('md_ins_products.delete_flag','N')
                        ->orderBy('md_ins_products.'.$column_name,$sort_by)
                        ->paginate($paginate); 
                }
            }elseif ($ins_type_id && $company_id) {
                $data=InsProduct::leftJoin('md_ins_type','md_ins_type.id','=','md_ins_products.ins_type_id')
                    ->leftJoin('md_ins_company','md_ins_company.id','=','md_ins_products.company_id')
                    ->select('md_ins_products.*','md_ins_type.type as ins_type_name','md_ins_company.comp_short_name as comp_short_name','md_ins_company.comp_full_name as comp_full_name')
                    ->where('md_ins_products.delete_flag','N')
                    ->where('md_ins_products.ins_type_id',$ins_type_id)
                    ->where('md_ins_products.company_id',$company_id)
                    ->orderBy('md_ins_products.updated_at','DESC')
                    ->paginate($paginate); 
            }elseif ($ins_type_id) {
                $data=InsProduct::leftJoin('md_ins_type','md_ins_type.id','=','md_ins_products.ins_type_id')
                    ->leftJoin('md_ins_company','md_ins_company.id','=','md_ins_products.company_id')
                    ->select('md_ins_products.*','md_ins_type.type as ins_type_name','md_ins_company.comp_short_name as comp_short_name','md_ins_company.comp_full_name as comp_full_name')
                    ->where('md_ins_products.delete_flag','N')
                    ->where('md_ins_products.ins_type_id',$ins_type_id)
                    ->orderBy('md_ins_products.updated_at','DESC')
                    ->paginate($paginate);  
            }elseif ($company_id) {
                $data=InsProduct::leftJoin('md_ins_type','md_ins_type.id','=','md_ins_products.ins_type_id')
                    ->leftJoin('md_ins_company','md_ins_company.id','=','md_ins_products.company_id')
                    ->select('md_ins_products.*','md_ins_type.type as ins_type_name','md_ins_company.comp_short_name as comp_short_name','md_ins_company.comp_full_name as comp_full_name')
                    ->where('md_ins_products.delete_flag','N')
                    ->where('md_ins_products.company_id',$company_id)
                    ->orderBy('md_ins_products.updated_at','DESC')
                    ->paginate($paginate);  
            }elseif ($product_name) {
                $data=InsProduct::leftJoin('md_ins_type','md_ins_type.id','=','md_ins_products.ins_type_id')
                    ->leftJoin('md_ins_company','md_ins_company.id','=','md_ins_products.company_id')
                    ->select('md_ins_products.*','md_ins_type.type as ins_type_name','md_ins_company.comp_short_name as comp_short_name','md_ins_company.comp_full_name as comp_full_name')
                    ->where('md_ins_products.delete_flag','N')
                    ->where('md_ins_products.product_name','like', '%' . $product_name . '%')
                    ->orderBy('md_ins_products.updated_at','DESC')
                    ->paginate($paginate);  
            } else {
                $data=InsProduct::leftJoin('md_ins_type','md_ins_type.id','=','md_ins_products.ins_type_id')
                    ->leftJoin('md_ins_company','md_ins_company.id','=','md_ins_products.company_id')
                    ->select('md_ins_products.*','md_ins_type.type as ins_type_name','md_ins_company.comp_short_name as comp_short_name','md_ins_company.comp_full_name as comp_full_name')
                    ->where('md_ins_products.delete_flag','N')
                    ->orderBy('md_ins_products.updated_at','DESC')
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
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            $product_name=$request->product_name;
            $company_id=$request->company_id;
            $ins_type_id=$request->ins_type_id;
            
            if ($sort_by && $column_name) {
                if ($column_name=='ins_type_name') {
                    $data=InsProduct::leftJoin('md_ins_type','md_ins_type.id','=','md_ins_products.ins_type_id')
                        ->leftJoin('md_ins_company','md_ins_company.id','=','md_ins_products.company_id')
                        ->select('md_ins_products.*','md_ins_type.type as ins_type_name','md_ins_company.comp_short_name as comp_short_name','md_ins_company.comp_full_name as comp_full_name')
                        ->where('md_ins_products.delete_flag','N')
                        ->orderBy('md_ins_type.'.$column_name,$sort_by)
                        ->get(); 
                }elseif ($column_name=='comp_short_name' || $column_name=='comp_full_name') {
                    $data=InsProduct::leftJoin('md_ins_type','md_ins_type.id','=','md_ins_products.ins_type_id')
                        ->leftJoin('md_ins_company','md_ins_company.id','=','md_ins_products.company_id')
                        ->select('md_ins_products.*','md_ins_type.type as ins_type_name','md_ins_company.comp_short_name as comp_short_name','md_ins_company.comp_full_name as comp_full_name')
                        ->where('md_ins_products.delete_flag','N')
                        ->orderBy('md_ins_company.'.$column_name,$sort_by)
                        ->get(); 
                }else {
                    $data=InsProduct::leftJoin('md_ins_type','md_ins_type.id','=','md_ins_products.ins_type_id')
                        ->leftJoin('md_ins_company','md_ins_company.id','=','md_ins_products.company_id')
                        ->select('md_ins_products.*','md_ins_type.type as ins_type_name','md_ins_company.comp_short_name as comp_short_name','md_ins_company.comp_full_name as comp_full_name')
                        ->where('md_ins_products.delete_flag','N')
                        ->orderBy('md_ins_products.'.$column_name,$sort_by)
                        ->get(); 
                }
            }elseif ($ins_type_id && $company_id) {
                $data=InsProduct::leftJoin('md_ins_type','md_ins_type.id','=','md_ins_products.ins_type_id')
                    ->leftJoin('md_ins_company','md_ins_company.id','=','md_ins_products.company_id')
                    ->select('md_ins_products.*','md_ins_type.type as ins_type_name','md_ins_company.comp_short_name as comp_short_name','md_ins_company.comp_full_name as comp_full_name')
                    ->where('md_ins_products.delete_flag','N')
                    ->where('md_ins_products.ins_type_id',$ins_type_id)
                    ->where('md_ins_products.company_id',$company_id)
                    ->orderBy('md_ins_products.updated_at','DESC')
                    ->get(); 
            }elseif ($ins_type_id) {
                $data=InsProduct::leftJoin('md_ins_type','md_ins_type.id','=','md_ins_products.ins_type_id')
                    ->leftJoin('md_ins_company','md_ins_company.id','=','md_ins_products.company_id')
                    ->select('md_ins_products.*','md_ins_type.type as ins_type_name','md_ins_company.comp_short_name as comp_short_name','md_ins_company.comp_full_name as comp_full_name')
                    ->where('md_ins_products.delete_flag','N')
                    ->where('md_ins_products.ins_type_id',$ins_type_id)
                    ->orderBy('md_ins_products.updated_at','DESC')
                    ->get();  
            }elseif ($company_id) {
                $data=InsProduct::leftJoin('md_ins_type','md_ins_type.id','=','md_ins_products.ins_type_id')
                    ->leftJoin('md_ins_company','md_ins_company.id','=','md_ins_products.company_id')
                    ->select('md_ins_products.*','md_ins_type.type as ins_type_name','md_ins_company.comp_short_name as comp_short_name','md_ins_company.comp_full_name as comp_full_name')
                    ->where('md_ins_products.delete_flag','N')
                    ->where('md_ins_products.company_id',$company_id)
                    ->orderBy('md_ins_products.updated_at','DESC')
                    ->get();  
            }elseif ($product_name) {
                $data=InsProduct::leftJoin('md_ins_type','md_ins_type.id','=','md_ins_products.ins_type_id')
                    ->leftJoin('md_ins_company','md_ins_company.id','=','md_ins_products.company_id')
                    ->select('md_ins_products.*','md_ins_type.type as ins_type_name','md_ins_company.comp_short_name as comp_short_name','md_ins_company.comp_full_name as comp_full_name')
                    ->where('md_ins_products.delete_flag','N')
                    ->where('md_ins_products.product_name','like', '%' . $product_name . '%')
                    ->orderBy('md_ins_products.updated_at','DESC')
                    ->get();  
            } else {
                $data=InsProduct::leftJoin('md_ins_type','md_ins_type.id','=','md_ins_products.ins_type_id')
                    ->leftJoin('md_ins_company','md_ins_company.id','=','md_ins_products.company_id')
                    ->select('md_ins_products.*','md_ins_type.type as ins_type_name','md_ins_company.comp_short_name as comp_short_name','md_ins_company.comp_full_name as comp_full_name')
                    ->where('md_ins_products.delete_flag','N')
                    ->orderBy('md_ins_products.updated_at','DESC')
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
            $id=$request->id;
            $paginate=$request->paginate;
            $ins_type_id=$request->ins_type_id;
            $company_id=$request->company_id;
            $paginate=$request->paginate;
            if ($paginate=='A') {
                $paginate=999999999;
            }
            if ($company_id && $ins_type_id) {
                $data=InsProduct::where('delete_flag','N')
                    ->where('company_id',$company_id)
                    ->where('ins_type_id',$ins_type_id)
                    ->get();      
            }else if ($search!='') {
                $data=InsProduct::where('delete_flag','N')
                ->where('product_name','like', '%' . $search . '%')
                ->get();      
            }else if ($id!='') {
                $data=InsProduct::where('delete_flag','N')->where('id',$id)->get();      
            }elseif ($paginate!='') {
                $data=InsProduct::where('delete_flag','N')->paginate($paginate);      
            } else {
                $data=InsProduct::where('delete_flag','N')->get();      
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
            'ins_type_id' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            if ($request->id > 0) {
                $data=InsProduct::find($request->id);
                $data->ins_type_id=$request->ins_type_id;
                $data->company_id=$request->company_id;
                $data->product_name=$request->product_name;
                $data->save();
            }else{
                $is_has=InsProduct::where('product_name',$request->product_name)->get();
                // return $is_has;
                if (count($is_has)>0) {
                    return Helper::WarningResponse(parent::ALREADY_EXIST);
                }else {
                    $data=InsProduct::create(array(
                        'ins_type_id'=>$request->ins_type_id,
                        'company_id'=>$request->company_id,
                        'product_name'=>$request->product_name,
                        // 'created_by'=>'',
                    ));  
                }  
            } 
            $data1=InsProduct::leftJoin('md_ins_type','md_ins_type.id','=','md_ins_products.ins_type_id')
                    ->leftJoin('md_ins_company','md_ins_company.id','=','md_ins_products.company_id')
                    ->select('md_ins_products.*','md_ins_type.type as ins_type_name','md_ins_company.comp_short_name as comp_short_name','md_ins_company.comp_full_name as comp_full_name')
                    ->where('md_ins_products.id',$data->id)
                    ->first();  
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data1);
    }

    public function delete(Request $request)
    {
        try {
            $id=$request->id;
            $is_has=InsCompany::where('ins_type_id',$id)->get();
            if (count($is_has)>0) {
                return Helper::WarningResponse(parent::DELETE_NOT_ALLOW_ERROR);
            }else {
                $data=InsProduct::find($id);
                $data->delete_flag='Y';
                $data->deleted_date=date('Y-m-d H:i:s');
                $data->deleted_by=1;
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
                    if (str_replace(" ","_",$value[0])=="Product_Name") {
                        // return 'Hii';
                        return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
                    }
                    // return $value;
                }else {
                    // return $value;
                    // return $value[0];
                    InsProduct::create(array(
                        'ins_type_id'=>$request->ins_type_id,
                        'company_id'=>$request->company_id,
                        'product_name'=>$value[0],
                        // 'created_by'=>'',
                    ));    
                }
               
            }
            $data1=[];
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
        }
        return Helper::SuccessResponse($data1);
    }
}

