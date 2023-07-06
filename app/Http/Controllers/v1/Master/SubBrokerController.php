<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\SubBroker;
use Validator;

class SubBrokerController extends Controller
{
    public function index(Request $request)
    {
        try {  
            $search=$request->search;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            $arr_rm_id=json_decode($request->arr_rm_id);
            $arr_sub_brk_id=json_decode($request->arr_sub_brk_id);
            if ($search!='') {
                $data=SubBroker::where('arn_no','like', '%' . $search . '%')
                    ->orWhere('code','like', '%' . $search . '%')
                    ->orWhere('bro_name','like', '%' . $search . '%')
                    ->get();      
            }elseif (!empty($arr_rm_id)) {
                $data=SubBroker::whereIn('emp_id',$arr_rm_id)->get();   
            }elseif (!empty($arr_sub_brk_id)) {
                $data=SubBroker::whereIn('id',$arr_sub_brk_id)->get();   
            } else{
                $data=SubBroker::whereDate('updated_at',date('Y-m-d'))->get();   
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
            'arn_no' =>'required',
            'code' =>'required',
            'bro_name' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            if ($request->id > 0) {
                $data=SubBroker::find($request->id);
                $data->arn_no=$request->arn_no;
                $data->code=$request->code;
                $data->bro_name=$request->bro_name;
                $data->save();
            }else{
                $data=SubBroker::create(array(
                    'arn_no'=>$request->arn_no,
                    'code'=>$request->code,
                    'bro_name'=>$request->bro_name,
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

