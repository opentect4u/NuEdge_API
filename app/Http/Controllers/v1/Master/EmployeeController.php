<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\Employee;
use Validator;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        try {  
            $search=$request->search;
            if ($search!='') {
                $data=Employee::where('emp_name','like', '%' . $search . '%')->get();      
            }else {
                $data=Employee::get();      
            }
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function create(Request $request)
    {
        $validator = Validator::make(request()->all(),[
            'emp_code' =>'required',
            'emp_name' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
           
                $is_has=Employee::where('emp_code',$request->emp_code)->get();
                if (count($is_has) > 0) {
                    return Helper::ErrorResponse(parent::ALREADY_EXIST);
                }
                $data=Employee::create(array(
                    'emp_code'=>$request->emp_code,
                    'emp_name'=>$request->emp_name,
                    // 'created_by'=>'',
                ));      
           
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function update(Request $request)
    {
        $validator = Validator::make(request()->all(),[
            'emp_code' =>'required',
            'emp_name' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            $data=Employee::where('emp_code',$request->emp_code)->update([
                'emp_name'=>$request->emp_name,
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

  
}