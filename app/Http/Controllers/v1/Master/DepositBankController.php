<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\DepositBank;
use Validator;

class DepositBankController extends Controller
{
    public function index(Request $request)
    {
        try {  
            $search=$request->search;
            if ($search!='') {
                $data=DepositBank::where('bank_name','like', '%' . $search . '%')->get();      
            }else {
                $data=DepositBank::get();      
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
            'ifs_code' =>'required',
            'bank_name' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            if ($request->id > 0) {
                $data=DepositBank::find($request->id);
                $data->ifs_code=$request->ifs_code;
                $data->bank_name=$request->bank_name;
                $data->save();
            }else{
                $data=DepositBank::create(array(
                    'ifs_code'=>$request->ifs_code,
                    'bank_name'=>$request->bank_name,
                    'deleted_flag'=>'N',
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