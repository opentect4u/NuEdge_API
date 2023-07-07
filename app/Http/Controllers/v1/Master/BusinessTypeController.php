<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\BusinessType;
use Validator;

class BusinessTypeController extends Controller
{
    public function index(Request $request)
    {
        try {  
            $search=$request->search;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            $branch_id=$request->branch_id;
            $arr_branch_id=json_decode($request->arr_branch_id);
            if ($search!='') {
                $data=BusinessType::where('brn_name','like', '%' . $search . '%')->get();      
            }elseif (!empty($arr_branch_id)) {
                $data=BusinessType::whereIn('branch_id',$arr_branch_id)
                    ->groupBy('bu_code')
                    ->get();      
            }elseif ($branch_id) {
                $data=BusinessType::where('branch_id',$branch_id)->get();      
            } else {
                $data=BusinessType::get();      
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
            'brn_code' =>'required',
            'brn_name' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            if ($request->id > 0) {
                $data=BusinessType::find($request->id);
                $data->brn_code=$request->brn_code;
                $data->brn_name=$request->brn_name;
                $data->save();
            }else{
                $data=BusinessType::create(array(
                    'brn_code'=>$request->brn_code,
                    'brn_name'=>$request->brn_name,
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
