<?php

namespace App\Http\Controllers\v1\CompMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\CompDirectorDetails;
use Validator;
use DB;

class DirectorDetailsController extends Controller
{
    //show director deatils also use for dropdown list
    public function index(Request $request)
    {
        try {  
            $search=$request->search;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            $show_type_comp=$request->show_type_comp;
            if ($search!='') {
                $data=CompDirectorDetails::where('name','like', '%' . $search . '%')->get();      
                
            } else {
                // DB::enableQueryLog();
                $data=CompDirectorDetails::get();      
                // dd(DB::getQueryLog());
            }
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    //create and update director deatils
    public function createUpdate(Request $request)
    {
        $validator = Validator::make(request()->all(),[
            'name' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            // return $request;
            if ($request->id > 0) {
                $data=CompDirectorDetails::find($request->id);
                $data->cm_profile_id=$request->cm_profile_id;
                $data->name=$request->name;
                $data->dob=$request->dob;
                $data->pan=$request->pan;
                $data->email=$request->email;
                $data->add_1=$request->add_1;
                $data->add_2=$request->add_2;
                $data->country_id=$request->country_id;
                $data->state_id=$request->state_id;
                $data->district_id=$request->district_id;
                $data->city_id=$request->city_id;
                $data->pincode=$request->pincode;
                $data->mob=$request->mob;
                $data->email=$request->email;
                $data->din_no=$request->din_no;
                $data->valid_from=$request->valid_from;
                $data->valid_to=$request->valid_to;
                $data->save();
            }else{

                $logo='';
                if ($request->logo) {
                    $logo_path_extension=$file->getClientOriginalExtension();
                    $logo=(microtime(true) * 100).".".$logo_path_extension;
                    $file->move(public_path('company/'),$logo);
                }
                $data=CompDirectorDetails::create(array(
                    'cm_profile_id'=>$request->cm_profile_id,
                    'name'=>$request->name,
                    'dob'=>$request->dob,
                    'pan'=>$request->pan,
                    'email'=>$request->email,
                    'add_1'=>$request->add_1,
                    'add_2'=>$request->add_2,
                    'country_id'=>$request->country_id,
                    'state_id'=>$request->state_id,
                    'district_id'=>$request->district_id,
                    'city_id'=>$request->city_id,
                    'pincode'=>$request->pincode,
                    'mob'=>$request->mob,
                    'email'=>$request->email,
                    'din_no'=>$request->din_no,
                    'valid_from'=>$request->valid_from,
                    'valid_to'=>$request->valid_to
                    // 'created_by'=>$request->instagram=>'',
                ));      
            }    
        } catch (\Throwable $th) {
            // throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}
