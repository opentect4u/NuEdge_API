<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\Plan;
use Validator;

class PlanController extends Controller
{
    public function index(Request $request)
    {
        try {  
            $search=$request->search;
            if ($search!='') {
                $data=Plan::where('plan_name','like', '%' . $search . '%')->get();      
            }else {
                $data=Plan::get();      
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
            'plan_name' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            if ($request->id > 0) {
                $data=Plan::find($request->id);
                $data->plan_name=$request->plan_name;
                $data->save();
            }else{
                $data=Plan::create(array(
                    'plan_name'=>$request->plan_name,
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
