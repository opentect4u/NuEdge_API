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
            $trns_type=$request->trns_type;
            $data=Transction::join('md_trns_type','md_trns_type.id','=','md_trans.trans_type_id')
                ->select('md_trans.*','md_trns_type.trns_type as trns_type')
                ->where('md_trns_type.product_id',$product_id)
                // ->orWhere()
                ->orderBy('updated_at','DESC')
                ->paginate($paginate);      
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
    public function export(Request $request)
    {
        try {
            $data=Transction::join('md_trns_type','md_trns_type.id','=','md_trans.trans_type_id')
                ->select('md_trans.*','md_trns_type.trns_type as trns_type')
                ->where('md_trns_type.product_id',$product_id)
                ->orderBy('md_trans.updated_at','DESC')
                ->get();          
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
                $data->save();
            }else{
                $data=Transction::create(array(
                    'trans_type_id'=>$request->trans_type_id,
                    'trns_name'=>$request->trns_name,
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