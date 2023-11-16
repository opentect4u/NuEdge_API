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
        try {
            // return $request;
            // $all_datas=json_decode($request->pertner_dtls);
            // // return $all_datas;
            // $data=[];
            // foreach ($all_datas as $key => $value) {
            //     // return $value->id;
            //     if ($value->id > 0) {
            //         // return $value->id;
            //         $dt=CompPartnershipDetails::find($value->id);
            //         $dt->cm_profile_id=$value->cm_profile_id;
            //         $dt->name=$value->name;
            //         $dt->dob=$value->dob;
            //         $dt->pan=$value->pan;
            //         $dt->mob=$value->mob;
            //         $dt->email=$value->email;
            //         $dt->add_1=$value->add_1;
            //         $dt->add_2=$value->add_2;
            //         $dt->country_id=$value->country_id;
            //         $dt->state_id=$value->state_id;
            //         $dt->district_id=$value->district_id;
            //         $dt->city_id=$value->city_id;
            //         $dt->pincode=$value->pincode;
            //         $dt->percentage=$value->percentage;
            //         $dt->save();
            //     }else {
            //         $dt=CompPartnershipDetails::create(array(
            //             'cm_profile_id'=>$value->cm_profile_id,
            //             'name'=>$value->name,
            //             'dob'=>$value->dob,
            //             'pan'=>$value->pan,
            //             'mob'=>$value->mob,
            //             'email'=>$value->email,
            //             'add_1'=>$value->add_1,
            //             'add_2'=>$value->add_2,
            //             'country_id'=>$value->country_id,
            //             'state_id'=>$value->state_id,
            //             'district_id'=>$value->district_id,
            //             'city_id'=>$value->city_id,
            //             'pincode'=>$value->pincode,
            //             'percentage'=>$value->percentage,
            //         ));   
            //     }
            //     array_push($data,$dt);   
            // }

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
                $data->updated_by=Helper::modifyUser($request->user());
                $data->save();
            }else{
                // return $request;
                $data=CompPartnershipDetails::create(array(
                    'cm_profile_id'=>$request->cm_profile_id,
                    'name'=>$request->name,
                    'dob'=>$request->dob,
                    'pan'=>$request->pan,
                    'mob'=>$request->mob,
                    'email'=>$request->email,
                    'add_1'=>$request->add_1,
                    'add_2'=>isset($request->add_2)?$request->add_2:NULL,
                    'country_id'=>$request->country_id,
                    'state_id'=>$request->state_id,
                    'district_id'=>$request->district_id,
                    'city_id'=>$request->city_id,
                    'pincode'=>$request->pincode,
                    'percentage'=>$request->percentage,
                    'created_by'=>Helper::modifyUser($request->user()),
                ));      
            }    
        } catch (\Throwable $th) {
            // throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}
