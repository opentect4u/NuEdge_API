<?php

namespace App\Http\Controllers\v1\CompMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\CompProfile;
use Validator;

class CompProfileController extends Controller
{
    // show company profile also use for dropdown list
    public function index(Request $request)
    {
        try {  
            $search=$request->search;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            $show_type_comp=$request->show_type_comp;
            if ($search!='') {
                $data=CompProfile::where('name','like', '%' . $search . '%')->get();      
            }elseif ($show_type_comp=='D') {
                $data=CompProfile::where('type_of_comp','!=','3')->get();      
            }elseif ($show_type_comp=='P') {
                $data=CompProfile::where('type_of_comp','3')->get();      
            }else {
                $data=CompProfile::get();      
            }
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    // create and update company profile
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
            if ($request->comp_details_id > 0) {
                // return $request;
                $data=CompProfile::find($request->comp_details_id);
                if ($request->logo) {
                    $logo_path_extension=$request->logo->getClientOriginalExtension();
                    $logo=(microtime(true) * 100).".".$logo_path_extension;
                    $request->logo->move(public_path('company/'),$logo);

                    if($data->logo!=null){
                        $filelogo = public_path('company/') . $data->logo;
                        if (file_exists($filelogo) != null) {
                            unlink($filelogo);
                        }
                    } 
                }else{
                    $logo=$data->logo;
                }
                $data->type_of_comp=$request->type_of_comp;
                $data->name=$request->name;
                $data->cin_no=$request->cin_no;
                $data->date_of_inc=$request->date_of_inc;
                $data->pan=$request->pan;
                $data->gstin=$request->gstin;
                $data->contact_no=$request->contact_no;
                $data->email=$request->email;
                $data->add_1=$request->add_1;
                $data->add_2=$request->add_2;
                $data->country_id=$request->country_id;
                $data->state_id=$request->state_id;
                $data->district_id=$request->district_id;
                $data->city_id=$request->city_id;
                $data->pincode=$request->pincode;
                $data->logo=$logo;
                $data->website=$request->website;
                $data->facebook=$request->facebook;
                $data->linkedin=$request->linkedin;
                $data->twitter=$request->twitter;
                $data->instagram=$request->instagram;
                $data->blog=$request->blog;
                $data->save();
            }else{
                // return $request;

                $logo='';
                if ($request->logo) {
                    $logo_path_extension=$request->logo->getClientOriginalExtension();
                    $logo=(microtime(true) * 100).".".$logo_path_extension;
                    $request->logo->move(public_path('company/'),$logo);
                }
                $data=CompProfile::create(array(
                    'type_of_comp'=>$request->type_of_comp,
                    'name'=>$request->name,
                    'cin_no'=>$request->cin_no,
                    'date_of_inc'=>$request->date_of_inc,
                    'pan'=>$request->pan,
                    'gstin'=>$request->gstin,
                    'contact_no'=>$request->contact_no,
                    'email'=>$request->email,
                    'add_1'=>$request->add_1,
                    'add_2'=>$request->add_2,
                    'country_id'=>$request->country_id,
                    'state_id'=>$request->state_id,
                    'district_id'=>$request->district_id,
                    'city_id'=>$request->city_id,
                    'pincode'=>$request->pincode,
                    'logo'=>$logo,
                    'website'=>$request->website,
                    'facebook'=>$request->facebook,
                    'linkedin'=>$request->linkedin,
                    'twitter'=>$request->twitter,
                    'instagram'=>$request->instagram,
                    'blog'=>$request->blog
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