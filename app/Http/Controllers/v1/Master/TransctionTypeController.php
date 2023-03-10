<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\TransctionType;
use Validator;

class TransctionTypeController extends Controller
{
    public function searchDetails(Request $request)
    {
        try {
            $paginate=$request->paginate;
            $trns_type=$request->trns_type;
            $product_id=$request->product_id;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            if ($paginate=='A') {
                $paginate=999999999;
            }
            if ($trns_type) {
                $data=TransctionType::where('product_id',$product_id)
                    ->where('trns_type','like', '%' . $trns_type . '%')
                    ->orderBy('updated_at','DESC')
                    ->paginate($paginate); 
            } else {
                $data=TransctionType::where('product_id',$product_id)
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
            $trns_type=$request->trns_type;
            $product_id=$request->product_id;
            if ($trns_type) {
                $data=TransctionType::where('product_id',$product_id)
                    ->where('trns_type','like', '%' . $trns_type . '%')
                    ->orderBy('updated_at','DESC')
                    ->get(); 
            } else {
                $data=TransctionType::where('product_id',$product_id)
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
            // $search=$request->search;
            $paginate=$request->paginate;
            $product_id=$request->product_id;
            if ($search!='') {
                $data=TransctionType::where('product_id',$product_id)
                    ->orWhere('trns_type','like', '%' . $search . '%')->get();      
            }elseif ($paginate!='') {
                $data=TransctionType::where('product_id',$product_id)
                    ->orderBy('updated_at','DESC')
                    ->paginate($paginate);      
            }else{
                $data=TransctionType::where('product_id',$product_id)->get();      
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
            'trns_type' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            if ($request->id > 0) {
                $data=TransctionType::find($request->id);
                $data->product_id=$request->product_id;
                $data->trns_type=$request->trns_type;
                $data->save();
            }else{
                $data=TransctionType::create(array(
                    'product_id'=>$request->product_id,
                    'trns_type'=>$request->trns_type,
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
