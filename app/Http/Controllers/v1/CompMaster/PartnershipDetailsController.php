<?php

namespace App\Http\Controllers\v1\CompMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\CompPartnershipDetails;
use Validator;

class PartnershipDetailsController extends Controller
{
    public function index(Request $request)
    {
        try {  
            $search=$request->search;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            if ($search!='') {
                $data=CompPartnershipDetails::where('bank_name','like', '%' . $search . '%')->get();      
            }else {
                $data=CompPartnershipDetails::get();      
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
            'product_id' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            if ($request->id > 0) {
                $data=CompPartnershipDetails::find($request->id);
                $data->cm_profile_id=$request->cm_profile_id;
                $data->name=$request->name;
                $data->dob=$request->dob;
                $data->pan=$request->pan;
                $data->mob=$request->mob;
                $data->email=$request->email;
                $data->add_1=$request->add_1;
                $data->add_2=$request->add_2;
                $data->country_id=$request->country_id;
                $data->state_id=$request->state_id;
                $data->district_id=$request->district_id;
                $data->city_id=$request->city_id;
                $data->pincode=$request->pincode;
                $data->percentage=$request->percentage;
                $data->save();
            }else{
                $data=CompPartnershipDetails::create(array(
                    'cm_profile_id'=>$request->cm_profile_id,
                    'name'=>$request->name,
                    'dob'=>$request->dob,
                    'pan'=>$request->pan,
                    'mob'=>$request->mob,
                    'email'=>$request->email,
                    'add_1'=>$request->add_1,
                    'add_2'=>$request->add_2,
                    'country_id'=>$request->country_id,
                    'state_id'=>$request->state_id,
                    'district_id'=>$request->district_id,
                    'city_id'=>$request->city_id,
                    'pincode'=>$request->pincode,
                    'percentage'=>$request->percentage,
                ));      
            }    
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}
