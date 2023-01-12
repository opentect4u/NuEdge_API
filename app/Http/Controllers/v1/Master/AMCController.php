<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\AMC;
use Validator;

class AMCController extends Controller
{
    public function index(Request $request)
    {
        try {  
            $search=$request->search;
            if ($search!='') {
                $data=AMC::where('amc_name','like', '%' . $search . '%')->get();      
            }else {
                $data=AMC::orderBy('updated_at','DESC')->get();      
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
            'rnt_id' =>'required',
            'product_id' =>'required',
            'amc_name' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            if ($request->id > 0) {
                $data=AMC::find($request->id);
                $data->rnt_id=$request->rnt_id;
                $data->product_id=$request->product_id;
                $data->amc_name=$request->amc_name;
                $data->save();
            }else{
                $data=AMC::create(array(
                    'rnt_id'=>$request->rnt_id,
                    'product_id'=>$request->product_id,
                    'amc_name'=>$request->amc_name,
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