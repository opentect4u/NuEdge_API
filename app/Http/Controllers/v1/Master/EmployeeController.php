<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{Employee,SubBroker};
use Validator;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        try {  
            $search=$request->search;
            $sub_arn_no=$request->sub_arn_no;
            $sub_brk_cd=$request->sub_brk_cd;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            if ($search!='' && $sub_arn_no!='') {
                $data=Employee::where('arn_no',$sub_arn_no)
                    ->where('euin_no','like', '%' . $search . '%')
                    ->orWhere('emp_name','like', '%' . $search . '%')
                    ->get();      
            }elseif ($sub_brk_cd && $search) {
                $data=SubBroker::leftJoin('md_employee','md_employee.arn_no','md_sub_broker.arn_no')
                    ->select('md_sub_broker.*','md_employee.emp_name as emp_name','md_employee.euin_no as euin_no')
                    ->where('md_sub_broker.code', $sub_brk_cd)
                    ->orWhere('md_employee.emp_name','like', '%' . $search . '%')
                    ->orWhere('md_employee.euin_no','like', '%' . $search . '%')
                    ->get();
            } elseif ($search!='') {
                $data=Employee::where('euin_no','like', '%' . $search . '%')
                    ->orWhere('emp_name','like', '%' . $search . '%')
                    ->get();
            } else {
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
            'euin_no' =>'required',
            'emp_name' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
           
                $is_has=Employee::where('euin_no',$request->euin_no)->get();
                if (count($is_has) > 0) {
                    return Helper::ErrorResponse(parent::ALREADY_EXIST);
                }
                $data=Employee::create(array(
                    'euin_no'=>$request->euin_no,
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
            'euin_no' =>'required',
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