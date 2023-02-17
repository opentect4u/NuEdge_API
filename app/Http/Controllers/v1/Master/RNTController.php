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
    public function searchDetails(Request $request)
    {
        try {
            $paginate=$request->paginate;
            $id=$request->rnt_id;
            $data=RNT::where('id',$id)->orderBy('updated_at','DESC')->paginate($paginate);      
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
    public function export(Request $request)
    {
        try {
            $data=RNT::orderBy('updated_at','DESC')->get();      
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
                $data=RNT::where('rnt_name','like', '%' . $search . '%')->get();      
            }else if ($id!='') {
                $data=RNT::where('id',$id)->get();      
            }else if ($paginate!='') {
                $data=RNT::orderBy('updated_at','DESC')->paginate($paginate);      
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
                $data->head_ofc_addr=$request->head_ofc_addr;
                $data->head_ofc_contact_per=$request->head_ofc_contact_per;
                $data->head_contact_per_mob=$request->head_contact_per_mob;
                $data->head_contact_per_email=$request->head_contact_per_email;
                $data->local_ofc_addr=$request->local_ofc_addr;
                $data->local_ofc_contact_per=$request->local_ofc_contact_per;
                $data->local_contact_per_mob=$request->local_contact_per_mob;
                $data->local_contact_per_email=$request->local_contact_per_email;
                $data->cus_care_no=$request->cus_care_no;
                $data->cus_care_email=$request->cus_care_email;
                $data->save();
            }else{
                $data=RNT::create(array(
                    'rnt_name'=>$request->rnt_name,
                    'website'=>$request->website,
                    'head_ofc_addr'=>$request->head_ofc_addr,
                    'head_ofc_contact_per'=>$request->head_ofc_contact_per,
                    'head_contact_per_mob'=>$request->head_contact_per_mob,
                    'head_contact_per_email'=>$request->head_contact_per_email,
                    'local_ofc_addr'=>$request->local_ofc_addr,
                    'local_ofc_contact_per'=>$request->local_ofc_contact_per,
                    'local_contact_per_mob'=>$request->local_contact_per_mob,
                    'local_contact_per_email'=>$request->local_contact_per_email,
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
