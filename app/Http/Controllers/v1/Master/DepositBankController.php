<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\DepositBank;
use Validator;
use Excel;
use App\Imports\DepositBankImport;

class DepositBankController extends Controller
{
    public function searchDetails(Request $request)
    {
        try {
            $paginate=$request->paginate;
            $ifs_code=$request->ifs_code;
            $data=DepositBank::where('ifs_code','like', '%' . $ifs_code . '%')
                ->orderBy('updated_at','DESC')->paginate($paginate);      
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
    public function export(Request $request)
    {
        try {
            $data=DepositBank::orderBy('updated_at','DESC')->get();      
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
            $id=$request->id;
            $paginate=$request->paginate;
            if ($paginate=='A') {
                $paginate=999999999;
            }
            if ($search!='') {
                $data=DepositBank::where('bank_name','like', '%' . $search . '%')
                    ->orWhere('ifs_code','like', '%' . $search . '%')
                    ->orWhere('branch_name','like', '%' . $search . '%')
                    ->orWhere('micr_code','like', '%' . $search . '%')
                    ->get();      
            }elseif ($id!='') {
                $data=DepositBank::where('id',$id)->get();      
            }elseif ($paginate!='') {
                $data=DepositBank::paginate($paginate);      
            } else {
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
            'branch_name' =>'required',
            'micr_code' =>'required',
            'branch_addr' =>'required',
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
                $data->branch_name=$request->branch_name;
                $data->micr_code=$request->micr_code;
                $data->branch_addr=$request->branch_addr;
                $data->save();
            }else{
                $data=DepositBank::create(array(
                    'ifs_code'=>$request->ifs_code,
                    'bank_name'=>$request->bank_name,
                    'branch_name'=>$request->branch_name,
                    'micr_code'=>$request->micr_code,
                    'branch_addr'=>$request->branch_addr,
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

    public function import(Request $request)
    {
        try {
            // return $request;
            $path = $request->file('file')->getRealPath();
            $data = array_map('str_getcsv', file($path));
            // return $data[0][0];
            // return gettype($data[0][0]) ;
            // if (in_array("rnt_id", $data)) {
            // if ($data[0][0] == "rnt_id" && $data[0][1] == "product_id" && $data[0][2] == "amc_name" && $data[0][3] == "website" && $data[0][4] == "ofc_addr") {
            //     return "hii";
                Excel::import(new DepositBankImport,$request->file);
                // Excel::import(new DepositBankImport,request()->file('file'));
                $data1=[];
            // }else {
            //     return "else";
            //     return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
            // }
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
        }
        return Helper::SuccessResponse($data1);
    }
}