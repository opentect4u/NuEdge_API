<?php

namespace App\Http\Controllers\v1\INSMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{InsProductType,InsCompany};
use Validator;

class ProductTypeController extends Controller
{
    public function searchDetails(Request $request)
    {
        try {
            $paginate=$request->paginate;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;

            $product_type=json_decode($request->product_type);
            $ins_type_id=json_decode($request->ins_type_id);

            if ($paginate=='A') {
                $paginate=999999999;
            }
            if ($sort_by && $column_name) {
                if ($column_name=='ins_type') {
                    $data=InsProductType::leftJoin('md_ins_type','md_ins_type.id','=','md_ins_product_type.ins_type_id')
                        ->select('md_ins_product_type.*','md_ins_type.type as ins_type')
                        ->where('md_ins_product_type.delete_flag','N')
                        ->orderBy('md_ins_type.type',$sort_by)
                        ->paginate($paginate);  
                }else {
                    $data=InsProductType::leftJoin('md_ins_type','md_ins_type.id','=','md_ins_product_type.ins_type_id')
                        ->select('md_ins_product_type.*','md_ins_type.type as ins_type')
                        ->where('md_ins_product_type.delete_flag','N')
                        ->orderBy('md_ins_product_type.'.$column_name,$sort_by)
                        ->paginate($paginate);  
                }
            }elseif (!empty($product_type) && !empty($ins_type_id)) {
                $parr=[];
                foreach ($product_type as $value) {
                    array_push($parr,$value->id);
                }
                $data=InsProductType::leftJoin('md_ins_type','md_ins_type.id','=','md_ins_product_type.ins_type_id')
                    ->select('md_ins_product_type.*','md_ins_type.type as ins_type')
                    ->where('md_ins_product_type.delete_flag','N')
                    ->whereIn('md_ins_product_type.id',$parr)
                    ->whereIn('md_ins_product_type.ins_type_id',$ins_type_id)
                    ->orderBy('md_ins_product_type.updated_at','DESC')
                    ->paginate($paginate);  
            } elseif (!empty($product_type)) {
                $parr=[];
                foreach ($product_type as $value) {
                    array_push($parr,$value->id);
                }
                $data=InsProductType::leftJoin('md_ins_type','md_ins_type.id','=','md_ins_product_type.ins_type_id')
                    ->select('md_ins_product_type.*','md_ins_type.type as ins_type')
                    ->where('md_ins_product_type.delete_flag','N')
                    ->whereIn('md_ins_product_type.id',$parr)
                    ->orderBy('md_ins_product_type.updated_at','DESC')
                    ->paginate($paginate);  
            }elseif (!empty($ins_type_id)) {
                $data=InsProductType::leftJoin('md_ins_type','md_ins_type.id','=','md_ins_product_type.ins_type_id')
                    ->select('md_ins_product_type.*','md_ins_type.type as ins_type')
                    ->where('md_ins_product_type.delete_flag','N')
                    ->whereIn('md_ins_product_type.ins_type_id',$ins_type_id)
                    ->orderBy('md_ins_product_type.updated_at','DESC')
                    ->paginate($paginate);  
            } else {
                $data=InsProductType::leftJoin('md_ins_type','md_ins_type.id','=','md_ins_product_type.ins_type_id')
                    ->select('md_ins_product_type.*','md_ins_type.type as ins_type')
                    ->where('md_ins_product_type.delete_flag','N')
                    ->orderBy('md_ins_product_type.updated_at','DESC')
                    ->paginate($paginate);  
            }
        } catch (\Throwable $th) {
            // throw $th;
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

            $product_type=json_decode($request->product_type);
            $ins_type_id=json_decode($request->ins_type_id);

            if ($sort_by && $column_name) {
                if ($column_name=='ins_type') {
                    $data=InsProductType::leftJoin('md_ins_type','md_ins_type.id','=','md_ins_product_type.ins_type_id')
                        ->select('md_ins_product_type.*','md_ins_type.type as ins_type')
                        ->where('md_ins_product_type.delete_flag','N')
                        ->orderBy('md_ins_type.type',$sort_by)
                        ->get();  
                }else {
                    $data=InsProductType::leftJoin('md_ins_type','md_ins_type.id','=','md_ins_product_type.ins_type_id')
                        ->select('md_ins_product_type.*','md_ins_type.type as ins_type')
                        ->where('md_ins_product_type.delete_flag','N')
                        ->orderBy('md_ins_product_type.'.$column_name,$sort_by)
                        ->get();  
                }
            }elseif (!empty($product_type) && !empty($ins_type_id)) {
                $parr=[];
                foreach ($product_type as $value) {
                    array_push($parr,$value->id);
                }
                $data=InsProductType::leftJoin('md_ins_type','md_ins_type.id','=','md_ins_product_type.ins_type_id')
                    ->select('md_ins_product_type.*','md_ins_type.type as ins_type')
                    ->where('md_ins_product_type.delete_flag','N')
                    ->whereIn('md_ins_product_type.id',$parr)
                    ->whereIn('md_ins_product_type.ins_type_id',$ins_type_id)
                    ->orderBy('md_ins_product_type.updated_at','DESC')
                    ->get();  
            } elseif (!empty($product_type)) {
                $parr=[];
                foreach ($product_type as $value) {
                    array_push($parr,$value->id);
                }
                $data=InsProductType::leftJoin('md_ins_type','md_ins_type.id','=','md_ins_product_type.ins_type_id')
                    ->select('md_ins_product_type.*','md_ins_type.type as ins_type')
                    ->where('md_ins_product_type.delete_flag','N')
                    ->whereIn('md_ins_product_type.id',$parr)
                    ->orderBy('md_ins_product_type.updated_at','DESC')
                    ->get();  
            }elseif (!empty($ins_type_id)) {
                $data=InsProductType::leftJoin('md_ins_type','md_ins_type.id','=','md_ins_product_type.ins_type_id')
                    ->select('md_ins_product_type.*','md_ins_type.type as ins_type')
                    ->where('md_ins_product_type.delete_flag','N')
                    ->whereIn('md_ins_product_type.ins_type_id',$ins_type_id)
                    ->orderBy('md_ins_product_type.updated_at','DESC')
                    ->get();  
            } else {
                $data=InsProductType::leftJoin('md_ins_type','md_ins_type.id','=','md_ins_product_type.ins_type_id')
                    ->select('md_ins_product_type.*','md_ins_type.type as ins_type')
                    ->where('md_ins_product_type.delete_flag','N')
                    ->orderBy('md_ins_product_type.updated_at','DESC')
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
            $ins_type_id=$request->ins_type_id;
            $paginate=$request->paginate;
            if ($paginate=='A') {
                $paginate=999999999;
            }
            if ($search!='') {
                $data=InsProductType::where('delete_flag','N')->where('type','like', '%' . $search . '%')->get();      
            }else if ($id!='') {
                $data=InsProductType::where('delete_flag','N')->where('id',$id)->get();      
            }elseif ($paginate!='') {
                $data=InsProductType::where('delete_flag','N')->paginate($paginate);      
            }elseif ($ins_type_id!='') {
                $data=InsProductType::where('delete_flag','N')->where('ins_type_id',$ins_type_id)->get();      
            } else {
                $data=InsProductType::where('delete_flag','N')->get();      
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
                $data=InsProductType::find($request->id);
                $data->ins_type_id=$request->ins_type_id;
                $data->product_type=$request->product_type;
                $data->save();
            }else{
                $is_has=InsProductType::where('product_type',$request->product_type)->get();
                if (count($is_has)>0) {
                    return Helper::WarningResponse(parent::ALREADY_EXIST);
                }else {
                    $data=InsProductType::create(array(
                        'ins_type_id'=>$request->ins_type_id,
                        'product_type'=>$request->product_type,
                        // 'created_by'=>'',
                    )); 
                }   
            }  
            $data1=InsProductType::leftJoin('md_ins_type','md_ins_type.id','=','md_ins_product_type.ins_type_id')
                    ->select('md_ins_product_type.*','md_ins_type.type as ins_type')
                    ->where('md_ins_product_type.id',$data->id)
                    ->first();
        } catch (\Throwable $th) {
            // throw $th;
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
                $data=InsProductType::find($id);
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
            // return $data;

            foreach ($data as $key => $value) {
                if ($key==0) {
                    if (str_replace(" ","_",$value[0])!="Product_Type") {
                        return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
                    }
                    // return $value;
                }else {
                    // return $value;
                    // return $value[0];
                    $is_has=InsProductType::where('product_type',$value[0])->get();
                    if (count($is_has) < 0) {
                        InsProductType::create(array(
                            'ins_type_id'=>$request->ins_type_id,
                            'product_type'=>$value[0],
                            // 'created_by'=>'',
                        ));  
                    }  
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

