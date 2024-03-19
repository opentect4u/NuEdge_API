<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\Transction;
use Validator;

class TransctionController extends Controller
{
    public function searchDetails(Request $request)
    {
        try {
            $paginate=$request->paginate;
            $product_id=$request->product_id;
            $trns_type_id=$request->trns_type_id;
            $trns_name=$request->trns_name;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            if ($paginate=='A') {
                $paginate=999999999;
            }
            if ($trns_name && $trns_type_id) {
                $data=Transction::join('md_trns_type','md_trns_type.id','=','md_trans.trans_type_id')
                    ->select('md_trans.*','md_trns_type.trns_type as trns_type')
                    ->where('md_trns_type.product_id',$product_id)
                    ->where('md_trans.trans_type_id',$trns_type_id)
                    ->where('md_trans.trns_name','like', '%' . $trns_name . '%')
                    ->orderBy('md_trans.updated_at','DESC')
                    ->get(); 
            } elseif ($trns_name) {
                $data=Transction::join('md_trns_type','md_trns_type.id','=','md_trans.trans_type_id')
                    ->select('md_trans.*','md_trns_type.trns_type as trns_type')
                    ->where('md_trns_type.product_id',$product_id)
                    ->where('md_trans.trns_name','like', '%' . $trns_name . '%')
                    ->orderBy('md_trans.updated_at','DESC')
                    ->get(); 
            }elseif ($trns_type_id) {
                $data=Transction::join('md_trns_type','md_trns_type.id','=','md_trans.trans_type_id')
                    ->select('md_trans.*','md_trns_type.trns_type as trns_type')
                    ->where('md_trns_type.product_id',$product_id)
                    ->where('md_trans.trans_type_id',$trns_type_id)
                    ->orderBy('md_trans.updated_at','DESC')
                    ->get(); 
            } else {
                $data=Transction::join('md_trns_type','md_trns_type.id','=','md_trans.trans_type_id')
                    ->select('md_trans.*','md_trns_type.trns_type as trns_type')
                    ->where('md_trns_type.product_id',$product_id)
                    ->orderBy('md_trans.updated_at','DESC')
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
            $product_id=$request->product_id;
            $trns_type_id=$request->trns_type_id;
            $trns_name=$request->trns_name;
            if ($trns_name && $trns_type_id) {
                $data=Transction::join('md_trns_type','md_trns_type.id','=','md_trans.trans_type_id')
                    ->select('md_trans.*','md_trns_type.trns_type as trns_type')
                    ->where('md_trns_type.product_id',$product_id)
                    ->where('md_trans.trans_type_id',$trns_type_id)
                    ->where('md_trans.trns_name','like', '%' . $trns_name . '%')
                    ->orderBy('md_trans.updated_at','DESC')
                    ->get(); 
            } elseif ($trns_name) {
                $data=Transction::join('md_trns_type','md_trns_type.id','=','md_trans.trans_type_id')
                    ->select('md_trans.*','md_trns_type.trns_type as trns_type')
                    ->where('md_trns_type.product_id',$product_id)
                    ->where('md_trans.trns_name','like', '%' . $trns_name . '%')
                    ->orderBy('md_trans.updated_at','DESC')
                    ->get(); 
            }elseif ($trns_type_id) {
                $data=Transction::join('md_trns_type','md_trns_type.id','=','md_trans.trans_type_id')
                    ->select('md_trans.*','md_trns_type.trns_type as trns_type')
                    ->where('md_trns_type.product_id',$product_id)
                    ->where('md_trans.trans_type_id',$trns_type_id)
                    ->orderBy('md_trans.updated_at','DESC')
                    ->get(); 
            } else {
                $data=Transction::join('md_trns_type','md_trns_type.id','=','md_trans.trans_type_id')
                    ->select('md_trans.*','md_trns_type.trns_type as trns_type')
                    ->where('md_trns_type.product_id',$product_id)
                    ->orderBy('md_trans.updated_at','DESC')
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
            $product_id=$request->product_id;
            $paginate=$request->paginate;
            $trans_type_id=$request->trans_type_id;
            if ($search!='') {
                $data=Transction::join('md_trns_type','md_trns_type.id','=','md_trans.trans_type_id')
                ->select('md_trans.*','md_trns_type.trns_type as trns_type')
                ->where('md_trns_type.product_id',$product_id)
                ->orWhere('md_trans.trns_name','like', '%' . $search . '%')
                ->get();      
            }elseif ($paginate!='') {
                $data=Transction::join('md_trns_type','md_trns_type.id','=','md_trans.trans_type_id')
                    ->select('md_trans.*','md_trns_type.trns_type as trns_type')
                    ->where('md_trns_type.product_id',$product_id)
                    ->orderBy('updated_at','DESC')
                    ->paginate($paginate);  
            }elseif ($trans_type_id) {
                $data=Transction::where('trans_type_id',$trans_type_id)->get();
            } else {
                $data=Transction::join('md_trns_type','md_trns_type.id','=','md_trans.trans_type_id')
                    ->select('md_trans.*','md_trns_type.trns_type as trns_type')
                    ->where('md_trns_type.product_id',$product_id)
                    ->orderBy('updated_at','DESC')
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
            'trans_type_id' =>'required',
            'trns_name' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            if ($request->id > 0) {
                $data=Transction::find($request->id);
                $data->trans_type_id=$request->trans_type_id;
                $data->trns_name=$request->trns_name;
                $data->updated_by=Helper::modifyUser($request->user());
                $data->save();
            }else{
                $data=Transction::create(array(
                    'trans_type_id'=>$request->trans_type_id,
                    'trns_name'=>$request->trns_name,
                    'created_by'=>Helper::modifyUser($request->user()),
                ));      
            }    
            $data=Transction::join('md_trns_type','md_trns_type.id','=','md_trans.trans_type_id')
                    ->select('md_trans.*','md_trns_type.trns_type as trns_type')
                    ->where('md_trans.id',$data->id)
                    ->first();    
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

  
}