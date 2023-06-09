<?php

namespace App\Http\Controllers\v1\CompMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\CompShareHolder;
use Validator;

class SharedHolderController extends Controller
{
    public function index(Request $request)
    {
        try {  
            $search=$request->search;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            if ($search!='') {
                $data=CompShareHolder::where('bank_name','like', '%' . $search . '%')->get();      
            }else {
                $data=CompShareHolder::get();      
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
            'cm_profile_id' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            // return $request;
            if ($request->id > 0) {
                // return $request;
                $data=CompShareHolder::find($request->id);

            
                $type=$request->type;

                $data->cm_profile_id=$request->cm_profile_id;
                $data->name=$request->name;
                $data->dob=$request->dob;
                $data->pan=$request->pan;
                $data->mob=$request->mob;
                $data->email=$request->email;
                $data->add_1=$request->add_1;
                $data->add_2=isset($request->add_2)?$request->add_2:NULL;
                $data->country_id=$request->country_id;
                $data->state_id=$request->state_id;
                $data->district_id=$request->district_id;
                $data->city_id=$request->city_id;
                $data->pincode=$request->pincode;
                $data->percentage=$request->percentage;
                $data->certificate_no=$request->certificate_no;
                $data->date=$request->date;
                $data->no_of_share=$request->no_of_share;
                $data->registered_folio=$request->registered_folio;
                $data->distinctive_no_from=$request->distinctive_no_from;
                $data->distinctive_no_to=$request->distinctive_no_to;
                $data->nominee=$request->nominee;
                $data->type=$type;
                $data->save();
            }else{
                if ($request->type=='T') {
                    // return $request;
                    $upload_scan=$request->upload_scan;
                    $logo='';
                    if ($upload_scan) {
                        $logo_path_extension=$upload_scan->getClientOriginalExtension();
                        $logo=(microtime(true) * 100).".".$logo_path_extension;
                        $upload_scan->move(public_path('company/shared-doc/'),$logo);
                    }

                    $data=CompShareHolder::create(array(
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
                        'certificate_no'=>$request->certificate_no,
                        'date'=>$request->date,
                        'no_of_share'=>$request->no_of_share,
                        'registered_folio'=>$request->registered_folio,
                        'distinctive_no_from'=>$request->distinctive_no_from,
                        'distinctive_no_to'=>$request->distinctive_no_to,
                        'nominee'=>$request->nominee,
                        'type'=>$request->type,
                        'transfer_id'=>$request->trans_from_id,
                        'upload_scan'=>isset($logo)?$logo:NULL,
                    ));      
                    $find=CompShareHolder::find($request->trans_from_id);

                    $no_of_share=$find->no_of_share - $request->no_of_share;
                    $find->no_of_share=$no_of_share;

                    // $find->distinctive_no_from=$request->distinctive_no_from;
                    // $find->distinctive_no_to=$request->distinctive_no_to;
                    $find->save();

                } else {
                    $type=$request->type;

                    $data=CompShareHolder::create(array(
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
                        'certificate_no'=>$request->certificate_no,
                        'date'=>$request->date,
                        'no_of_share'=>$request->no_of_share,
                        'registered_folio'=>$request->registered_folio,
                        'distinctive_no_from'=>$request->distinctive_no_from,
                        'distinctive_no_to'=>$request->distinctive_no_to,
                        'nominee'=>$request->nominee,
                        'type'=>$type,
                    ));      
                }
                
            }    
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}
