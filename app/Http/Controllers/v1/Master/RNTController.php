<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\RNT;
use Validator;
use Excel;
use App\Imports\RNTImport;

class RNTController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search=$request->search;
            $id=$request->id;
            if ($search!='') {
                $data=RNT::where('rnt_name','like', '%' . $search . '%')->get();      
            }else if ($id!='') {
                $data=RNT::where('id',$id)->get();      
            } else {
                $data=RNT::orderBy('updated_at','DESC')->get();      
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
            'rnt_name' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            if ($request->id > 0) {
                $data=RNT::find($request->id);
                $data->rnt_name=$request->rnt_name;
                $data->website=$request->website;
                $data->ofc_addr=$request->ofc_addr;
                $data->cus_care_no=$request->cus_care_no;
                $data->cus_care_email=$request->cus_care_email;
                $data->save();
            }else{
                $data=RNT::create(array(
                    'rnt_name'=>$request->rnt_name,
                    'website'=>$request->website,
                    'ofc_addr'=>$request->ofc_addr,
                    'cus_care_no'=>$request->cus_care_no,
                    'cus_care_email'=>$request->cus_care_email,
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
            // return $data;
            // return gettype($data[0][0]) ;
            // if ($data[0][0] == "rnt_name") {
            if ($data[0][0] == 'rnt_name' && $data[0][1] == 'website' && $data[0][2] == 'ofc_addr' && $data[0][3] == 'cus_care_no' && $data[0][4] == 'cus_care_email') {
                // return "hii";
                Excel::import(new RNTImport,$request->file);
                // Excel::import(new RNTImport,request()->file('file'));
                $data1=[];
            }else {
                // return "else";
                return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
            }
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
        }
        return Helper::SuccessResponse($data1);
    }
    
}
