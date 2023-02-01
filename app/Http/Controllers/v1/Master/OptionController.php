<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\Option;
use Validator;

class OptionController extends Controller
{
    public function index(Request $request)
    {
        try {  
            $search=$request->search;
            if ($search!='') {
                $data=Option::where('opt_name','like', '%' . $search . '%')->get();      
            }else {
                $data=Option::get();      
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
            'opt_name' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            if ($request->id > 0) {
                $data=Option::find($request->id);
                $data->opt_name=$request->opt_name;
                $data->save();
            }else{
                $data=Option::create(array(
                    'opt_name'=>$request->opt_name,
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

