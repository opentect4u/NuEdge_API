<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Helpers\TransHelper;
use App\Models\{
    MutualFund,
    Client,
    ClientFamily
};
use Validator;
use Illuminate\Support\Carbon;
use Excel;
use DB;

class ClientFamilyController extends Controller
{
    public function searchDetails(Request $request)
    {
        try {
            $data=ClientFamily::leftJoin('md_client','md_client.id','=','md_client_family.client_id')
                ->leftJoin('md_city','md_city.id','=','md_client.city')
                ->leftJoin('md_district','md_district.id','=','md_client.dist')
                ->leftJoin('md_states','md_states.id','=','md_client.state')
                ->leftJoin('md_client_type','md_client_type.id','=','md_client.client_type_mode')
                ->leftJoin('md_pincode','md_pincode.id','=','md_client.pincode')
                ->select('md_client_family.*','md_client.client_name as client_name','md_client.client_code as client_code',
                'md_client.pan as pan','md_client.mobile as mobile','md_client.email as email','md_client.add_line_1 as add_line_1','md_client.add_line_2 as add_line_2',
                'md_city.name as city_name','md_district.name as district_name','md_states.name as state_name','md_client_type.type_name as type_name','md_pincode.pincode as pincode'
                )
                ->where('relationship','Head')
                ->get();
            // return $data;
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function familyDetail(Request $request)
    {
        try {
            $family_head_id=$request->family_head_id;
            $view_type=$request->view_type;
            if ($view_type=='F') {
                $data=ClientFamily::leftJoin('md_client','md_client.id','=','md_client_family.family_id')
                    ->leftJoin('md_city','md_city.id','=','md_client.city')
                    ->leftJoin('md_district','md_district.id','=','md_client.dist')
                    ->leftJoin('md_states','md_states.id','=','md_client.state')
                    ->leftJoin('md_client_type','md_client_type.id','=','md_client.client_type_mode')
                    ->leftJoin('md_pincode','md_pincode.id','=','md_client.pincode')
                    ->select('md_client_family.*','md_client.client_name as client_name','md_client.client_code as client_code',
                    'md_client.pan as pan','md_client.mobile as mobile','md_client.email as email','md_client.add_line_1 as add_line_1','md_client.add_line_2 as add_line_2',
                    'md_city.name as city_name','md_district.name as district_name','md_states.name as state_name','md_client_type.type_name as type_name','md_pincode.pincode as pincode'
                    )
                    ->where('md_client_family.client_id',$family_head_id)
                    ->get();
            }else {
                $data=ClientFamily::leftJoin('md_client','md_client.id','=','md_client_family.family_id')
                    ->leftJoin('md_city','md_city.id','=','md_client.city')
                    ->leftJoin('md_district','md_district.id','=','md_client.dist')
                    ->leftJoin('md_states','md_states.id','=','md_client.state')
                    ->leftJoin('md_client_type','md_client_type.id','=','md_client.client_type_mode')
                    ->leftJoin('md_pincode','md_pincode.id','=','md_client.pincode')
                    ->select('md_client_family.*','md_client.client_name as client_name','md_client.client_code as client_code',
                    'md_client.pan as pan','md_client.mobile as mobile','md_client.email as email','md_client.add_line_1 as add_line_1','md_client.add_line_2 as add_line_2',
                    'md_city.name as city_name','md_district.name as district_name','md_states.name as state_name','md_client_type.type_name as type_name','md_pincode.pincode as pincode'
                    )
                    ->where('md_client_family.client_id',$family_head_id)
                    ->where('relationship','!=','Head')
                    ->get();
            }
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function createUpdate(Request $request)
    {
        // $validator = Validator::make(request()->all(),[
        //     'client_name'=>'required',
        //     // 'mobile'=>'required',
        //     // 'email'=>'required',
        // ]);
    
        // if($validator->fails()) {
        //     $errors = $validator->errors();
        //     return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        // }
        try {
            // return $request;
            $family_members=json_decode($request->family_members);
            $family_head_id=$request->family_head_id;
            $data=[];
            if ($request->id > 0) {
                // return $request;
            }else{
                // return $request;
                $is_has=ClientFamily::where('client_id',$request->family_head_id)->where('family_id',$request->family_head_id)->get();
                if (count($is_has)==0) {
                    ClientFamily::create(array(
                        'client_id'=>$family_head_id,
                        'family_id'=>$family_head_id,
                        'relationship'=>"Head",
                        'created_by'=>Helper::modifyUser($request->user()),
                    )); 
                }
                foreach ($family_members as $key => $value) {
                    // return $value;
                    $is_has=ClientFamily::where('client_id',$request->family_head_id)->where('family_id',$value->id)->get();
                    if (count($is_has)==0) {
                        ClientFamily::create(array(
                            'client_id'=>$family_head_id,
                            'family_id'=>$value->id,
                            'relationship'=>$value->relationship,
                            'created_by'=>Helper::modifyUser($request->user()),
                        )); 
                    }
                }
            }  
        } catch (\Throwable $th) {
            // throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}
