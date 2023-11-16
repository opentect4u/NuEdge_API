<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\Pincode;
use App\Models\CityType;
use Validator;
use Excel;

class PincodeController extends Controller
{
    public function index(Request $request)
    {
        try {  
            $search=$request->search;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            $city_id=$request->city_id;
            $arr_city_id=json_decode($request->arr_city_id);
            $id=$request->id;
            if (!empty($arr_city_id) &&  $search) {
                $data=Pincode::whereIn('city_id',$arr_city_id)
                    ->where('pincode','like', '%' . $search . '%')
                    ->get();
            }elseif ($search) {
                $data=Pincode::where('pincode','like', '%' . $search . '%')->get();      
            }elseif ($city_id) {
                $data=Pincode::where('city_id',$city_id)->get();
            }elseif ($id) {
                $data=Pincode::where('id',$id)->get();
            } else{
                $data=Pincode::get();   
            }   
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function createUpdate(Request $request)
    {
        $validator = Validator::make(request()->all(),[
            'pincode'=>'required',
            'country_id'=>'required',
            'state_id'=>'required',
            'district_id'=>'required',
            'city_id'=>'required',
            // 'city_type_id'=>'required',
        ]);
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            $id=$request->id;
            if ($id > 0) {
                $data=Pincode::find($id);
                $data->country_id=$request->country_id;
                $data->state_id=$request->state_id;
                $data->district_id=$request->district_id;
                $data->city_id=$request->city_id;
                // $data->city_type_id=$request->city_type_id;
                $data->pincode=$request->pincode;
                // $data->updated_by=Helper::modifyUser($request->user());
                $data->save();
            }else {
                $is_has=Pincode::where([
                        'country_id'=>$request->country_id,
                        'state_id'=>$request->state_id,
                        'district_id'=>$request->district_id,
                        'city_id'=>$request->city_id,
                        'pincode'=>$request->pincode,
                    ])
                    ->get();
                if (count($is_has)>0) {
                    return Helper::WarningResponse(parent::ALREADY_EXIST);
                }else {
                    $data=Pincode::create(array(
                        'country_id'=>$request->country_id,
                        'state_id'=>$request->state_id,
                        'district_id'=>$request->district_id,
                        'city_id'=>$request->city_id,
                        // 'city_type_id'=>$request->city_type_id,
                        'pincode'=>$request->pincode,
                        // 'created_by'=>Helper::modifyUser($request->user()),
                    ));
                }
            }
        } catch (\Throwable $th) {
            // throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function import(Request $request)
    {
        try {
            // return $request;
            $datas = Excel::toArray([], $request->file('file'));
            // return $datas;
            $data=$datas[0];
            // return $data;
            foreach ($data as $key => $value) {
                if ($key==0) {
                    if ($value[0]!="Pincode" && str_replace(" ","_",$value[1])!="City_Type") {
                        return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
                    }
                    // return $value;
                }else {
                    $is_has=Pincode::where('pincode',$value[0])->get();
                    $city_type_id=CityType::where('name',$value[1])->value('id');
                    // return $city_type_id;
                    if (count($is_has) > 0) {
                        Pincode::whereId($is_has[0]->id)->update(array(
                            'country_id'=>$request->country_id,
                            'state_id'=>$request->state_id,
                            'district_id'=>$request->district_id,
                            'city_id'=>$request->city_id,
                            'pincode'=>$value[0],
                            'city_type_id'=>$city_type_id,
                            // 'delete_flag'=>'N',
                        ));
                    }else {
                        Pincode::create(array(
                            'country_id'=>$request->country_id,
                            'state_id'=>$request->state_id,
                            'district_id'=>$request->district_id,
                            'city_id'=>$request->city_id,
                            'pincode'=>$value[0],
                            'city_type_id'=>$city_type_id,
                            // 'delete_flag'=>'N',
                        ));
                        
                    }
                }
            }

            $data1=[];

        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
        }
        return Helper::SuccessResponse($data1);
    }

    public function searchDetails(Request $request)
    {
        try {
            $country_id=json_decode($request->arr_country_id);
            $state_id=json_decode($request->arr_state_id);
            $district_id=json_decode($request->arr_district_id);
            $city_id=json_decode($request->arr_city_id);
            $city_type_id=json_decode($request->arr_city_type_id);
            $pincode=$request->pincode;

            $field=$request->field;
            $order=$request->order;
            if ($order > 0 ) {
                $order='ASC';
            }else {
                $order='DESC';
            }
            $paginate=$request->paginate;
            if ($paginate=='A') {
                $paginate=999999999;
            }
            $raw=$field.' '.$order;

            if ($order && $field) {
                $raw=$field.' '.$order;
                $rawQuery='';
                if (!empty($country_id)) {
                    $rawQuery='country_id '.$country_id;
                }elseif (!empty($state_id)) {
                    $rawQuery='state_id '.$state_id;
                }

                $data=Pincode::leftJoin('md_country','md_country.id','=','md_pincode.country_id')
                    ->leftJoin('md_states','md_states.id','=','md_pincode.state_id')
                    ->leftJoin('md_district','md_district.id','=','md_pincode.district_id')
                    ->leftJoin('md_city','md_city.id','=','md_pincode.city_id')
                    ->leftJoin('md_city_type','md_city_type.id','=','md_pincode.city_type_id')
                    ->select('md_pincode.*','md_country.name as country_name','md_states.name as states_name','md_district.name as district_name','md_city.name as city_name','md_city_type.name as city_type_name')
                    // ->whereIn('md_pincode.country_id', $country_id)
                    // ->whereIn('md_pincode.state_id', $state_id)
                    // ->whereIn('md_pincode.district_id', $district_id)
                    // ->whereIn('md_pincode.city_id', $city_id)
                    ->whereInRaw($rawQuery)
                    ->orderByRaw($raw)
                    ->paginate($paginate);
            }elseif (!empty($country_id) && !empty($state_id) && !empty($district_id) && !empty($city_id) && !empty($city_type_id)) {
                $data=Pincode::leftJoin('md_country','md_country.id','=','md_pincode.country_id')
                    ->leftJoin('md_states','md_states.id','=','md_pincode.state_id')
                    ->leftJoin('md_district','md_district.id','=','md_pincode.district_id')
                    ->leftJoin('md_city','md_city.id','=','md_pincode.city_id')
                    ->leftJoin('md_city_type','md_city_type.id','=','md_pincode.city_type_id')
                    ->select('md_pincode.*','md_country.name as country_name','md_states.name as states_name','md_district.name as district_name','md_city.name as city_name','md_city_type.name as city_type_name')
                    ->whereIn('md_pincode.country_id', $country_id)
                    ->whereIn('md_pincode.state_id', $state_id)
                    ->whereIn('md_pincode.district_id', $district_id)
                    ->whereIn('md_pincode.city_id', $city_id)
                    ->whereIn('md_pincode.city_type_id', $city_type_id)
                    ->paginate($paginate);
            }elseif (!empty($country_id) && !empty($state_id) && !empty($district_id) && !empty($city_id) && $pincode) {
                $data=Pincode::leftJoin('md_country','md_country.id','=','md_pincode.country_id')
                    ->leftJoin('md_states','md_states.id','=','md_pincode.state_id')
                    ->leftJoin('md_district','md_district.id','=','md_pincode.district_id')
                    ->leftJoin('md_city','md_city.id','=','md_pincode.city_id')
                    ->leftJoin('md_city_type','md_city_type.id','=','md_pincode.city_type_id')
                    ->select('md_pincode.*','md_country.name as country_name','md_states.name as states_name','md_district.name as district_name','md_city.name as city_name','md_city_type.name as city_type_name')
                    ->whereIn('md_pincode.country_id', $country_id)
                    ->whereIn('md_pincode.state_id', $state_id)
                    ->whereIn('md_pincode.district_id', $district_id)
                    ->whereIn('md_pincode.city_id', $city_id)
                    ->where('md_pincode.pincode','like', '%' . $pincode . '%')
                    ->paginate($paginate);
            }elseif (!empty($country_id) && !empty($state_id) && !empty($district_id) && !empty($city_id)) {
                $data=Pincode::leftJoin('md_country','md_country.id','=','md_pincode.country_id')
                    ->leftJoin('md_states','md_states.id','=','md_pincode.state_id')
                    ->leftJoin('md_district','md_district.id','=','md_pincode.district_id')
                    ->leftJoin('md_city','md_city.id','=','md_pincode.city_id')
                    ->leftJoin('md_city_type','md_city_type.id','=','md_pincode.city_type_id')
                    ->select('md_pincode.*','md_country.name as country_name','md_states.name as states_name','md_district.name as district_name','md_city.name as city_name','md_city_type.name as city_type_name')
                    ->whereIn('md_pincode.country_id', $country_id)
                    ->whereIn('md_pincode.state_id', $state_id)
                    ->whereIn('md_pincode.district_id', $district_id)
                    ->whereIn('md_pincode.city_id', $city_id)
                    ->paginate($paginate);
            }elseif (!empty($country_id) && !empty($state_id) && !empty($district_id)) {
                $data=Pincode::leftJoin('md_country','md_country.id','=','md_pincode.country_id')
                    ->leftJoin('md_states','md_states.id','=','md_pincode.state_id')
                    ->leftJoin('md_district','md_district.id','=','md_pincode.district_id')
                    ->leftJoin('md_city','md_city.id','=','md_pincode.city_id')
                    ->leftJoin('md_city_type','md_city_type.id','=','md_pincode.city_type_id')
                    ->select('md_pincode.*','md_country.name as country_name','md_states.name as states_name','md_district.name as district_name','md_city.name as city_name','md_city_type.name as city_type_name')
                    ->whereIn('md_pincode.country_id', $country_id)
                    ->whereIn('md_pincode.state_id', $state_id)
                    ->whereIn('md_pincode.district_id', $district_id)
                    ->paginate($paginate);
            }elseif (!empty($country_id) && !empty($state_id)) {
                // return $request;
                $data=Pincode::leftJoin('md_country','md_country.id','=','md_pincode.country_id')
                    ->leftJoin('md_states','md_states.id','=','md_pincode.state_id')
                    ->leftJoin('md_district','md_district.id','=','md_pincode.district_id')
                    ->leftJoin('md_city','md_city.id','=','md_pincode.city_id')
                    ->leftJoin('md_city_type','md_city_type.id','=','md_pincode.city_type_id')
                    ->select('md_pincode.*','md_country.name as country_name','md_states.name as states_name','md_district.name as district_name','md_city.name as city_name','md_city_type.name as city_type_name')
                    ->whereIn('md_pincode.country_id', $country_id)
                    ->whereIn('md_pincode.state_id', $state_id)
                    ->paginate($paginate);
            }elseif (!empty($country_id)) {
                $data=Pincode::leftJoin('md_country','md_country.id','=','md_pincode.country_id')
                    ->leftJoin('md_states','md_states.id','=','md_pincode.state_id')
                    ->leftJoin('md_district','md_district.id','=','md_pincode.district_id')
                    ->leftJoin('md_city','md_city.id','=','md_pincode.city_id')
                    ->leftJoin('md_city_type','md_city_type.id','=','md_pincode.city_type_id')
                    ->select('md_pincode.*','md_country.name as country_name','md_states.name as states_name','md_district.name as district_name','md_city.name as city_name','md_city_type.name as city_type_name')
                    ->whereIn('md_pincode.country_id', $country_id)
                    ->paginate($paginate);
            }elseif (!empty($state_id)) {
                $data=Pincode::leftJoin('md_country','md_country.id','=','md_pincode.country_id')
                    ->leftJoin('md_states','md_states.id','=','md_pincode.state_id')
                    ->leftJoin('md_district','md_district.id','=','md_pincode.district_id')
                    ->leftJoin('md_city','md_city.id','=','md_pincode.city_id')
                    ->leftJoin('md_city_type','md_city_type.id','=','md_pincode.city_type_id')
                    ->select('md_pincode.*','md_country.name as country_name','md_states.name as states_name','md_district.name as district_name','md_city.name as city_name','md_city_type.name as city_type_name')
                    ->whereIn('md_pincode.state_id', $state_id)
                    ->paginate($paginate);
            }elseif (!empty($district_id)) {
                $data=Pincode::leftJoin('md_country','md_country.id','=','md_pincode.country_id')
                    ->leftJoin('md_states','md_states.id','=','md_pincode.state_id')
                    ->leftJoin('md_district','md_district.id','=','md_pincode.district_id')
                    ->leftJoin('md_city','md_city.id','=','md_pincode.city_id')
                    ->leftJoin('md_city_type','md_city_type.id','=','md_pincode.city_type_id')
                    ->select('md_pincode.*','md_country.name as country_name','md_states.name as states_name','md_district.name as district_name','md_city.name as city_name','md_city_type.name as city_type_name')
                    ->whereIn('md_pincode.district_id', $district_id)
                    ->paginate($paginate);
            }elseif (!empty($city_id)) {
                $data=Pincode::leftJoin('md_country','md_country.id','=','md_pincode.country_id')
                    ->leftJoin('md_states','md_states.id','=','md_pincode.state_id')
                    ->leftJoin('md_district','md_district.id','=','md_pincode.district_id')
                    ->leftJoin('md_city','md_city.id','=','md_pincode.city_id')
                    ->leftJoin('md_city_type','md_city_type.id','=','md_pincode.city_type_id')
                    ->select('md_pincode.*','md_country.name as country_name','md_states.name as states_name','md_district.name as district_name','md_city.name as city_name','md_city_type.name as city_type_name')
                    ->whereIn('md_pincode.city_id', $city_id)
                    ->paginate($paginate);
            }elseif (!empty($city_type_id)) {
                $data=Pincode::leftJoin('md_country','md_country.id','=','md_pincode.country_id')
                    ->leftJoin('md_states','md_states.id','=','md_pincode.state_id')
                    ->leftJoin('md_district','md_district.id','=','md_pincode.district_id')
                    ->leftJoin('md_city','md_city.id','=','md_pincode.city_id')
                    ->leftJoin('md_city_type','md_city_type.id','=','md_pincode.city_type_id')
                    ->select('md_pincode.*','md_country.name as country_name','md_states.name as states_name','md_district.name as district_name','md_city.name as city_name','md_city_type.name as city_type_name')
                    ->whereIn('md_pincode.city_type_id', $city_type_id)
                    ->paginate($paginate);
            }elseif ($pincode) {
                $data=Pincode::leftJoin('md_country','md_country.id','=','md_pincode.country_id')
                    ->leftJoin('md_states','md_states.id','=','md_pincode.state_id')
                    ->leftJoin('md_district','md_district.id','=','md_pincode.district_id')
                    ->leftJoin('md_city','md_city.id','=','md_pincode.city_id')
                    ->leftJoin('md_city_type','md_city_type.id','=','md_pincode.city_type_id')
                    ->select('md_pincode.*','md_country.name as country_name','md_states.name as states_name','md_district.name as district_name','md_city.name as city_name','md_city_type.name as city_type_name')
                    ->where('md_pincode.pincode','like', '%' . $pincode . '%')
                    ->paginate($paginate);
            } else {
                $data=Pincode::leftJoin('md_country','md_country.id','=','md_pincode.country_id')
                    ->leftJoin('md_states','md_states.id','=','md_pincode.state_id')
                    ->leftJoin('md_district','md_district.id','=','md_pincode.district_id')
                    ->leftJoin('md_city','md_city.id','=','md_pincode.city_id')
                    ->leftJoin('md_city_type','md_city_type.id','=','md_pincode.city_type_id')
                    ->select('md_pincode.*','md_country.name as country_name','md_states.name as states_name','md_district.name as district_name','md_city.name as city_name','md_city_type.name as city_type_name')
                    ->paginate($paginate);
            }
            // return $data;
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function geographyExport(Request $request)
    {
        try {
            $country_id=json_decode($request->arr_country_id);
            $state_id=json_decode($request->arr_state_id);
            $district_id=json_decode($request->arr_district_id);
            $city_id=json_decode($request->arr_city_id);
            $city_type_id=json_decode($request->arr_city_type_id);
            $pincode=$request->pincode;

            $field=$request->field;
            $order=$request->order;
            if ($order > 0 ) {
                $order='ASC';
            }else {
                $order='DESC';
            }
           
            // $raw=$field.' '.$order;

            if ($order && $field) {
                $raw=$field.' '.$order;
                $rawQuery='';
                if (!empty($country_id)) {
                    $rawQuery='country_id '.$country_id;
                }elseif (!empty($state_id)) {
                    $rawQuery='state_id '.$state_id;
                }

                $data=Pincode::leftJoin('md_country','md_country.id','=','md_pincode.country_id')
                    ->leftJoin('md_states','md_states.id','=','md_pincode.state_id')
                    ->leftJoin('md_district','md_district.id','=','md_pincode.district_id')
                    ->leftJoin('md_city','md_city.id','=','md_pincode.city_id')
                    ->leftJoin('md_city_type','md_city_type.id','=','md_pincode.city_type_id')
                    ->select('md_pincode.*','md_country.name as country_name','md_states.name as states_name','md_district.name as district_name','md_city.name as city_name','md_city_type.name as city_type_name')
                    // ->whereIn('md_pincode.country_id', $country_id)
                    // ->whereIn('md_pincode.state_id', $state_id)
                    // ->whereIn('md_pincode.district_id', $district_id)
                    // ->whereIn('md_pincode.city_id', $city_id)
                    // ->whereInRaw($rawQuery)
                    ->orderByRaw($raw)
                    ->get();
            }elseif (!empty($country_id) && !empty($state_id) && !empty($district_id) && !empty($city_id) && !empty($city_type_id)) {
                $data=Pincode::leftJoin('md_country','md_country.id','=','md_pincode.country_id')
                    ->leftJoin('md_states','md_states.id','=','md_pincode.state_id')
                    ->leftJoin('md_district','md_district.id','=','md_pincode.district_id')
                    ->leftJoin('md_city','md_city.id','=','md_pincode.city_id')
                    ->leftJoin('md_city_type','md_city_type.id','=','md_pincode.city_type_id')
                    ->select('md_pincode.*','md_country.name as country_name','md_states.name as states_name','md_district.name as district_name','md_city.name as city_name','md_city_type.name as city_type_name')
                    ->whereIn('md_pincode.country_id', $country_id)
                    ->whereIn('md_pincode.state_id', $state_id)
                    ->whereIn('md_pincode.district_id', $district_id)
                    ->whereIn('md_pincode.city_id', $city_id)
                    ->whereIn('md_pincode.city_type_id', $city_type_id)
                    ->get();
            }elseif (!empty($country_id) && !empty($state_id) && !empty($district_id) && !empty($city_id) && $pincode) {
                $data=Pincode::leftJoin('md_country','md_country.id','=','md_pincode.country_id')
                    ->leftJoin('md_states','md_states.id','=','md_pincode.state_id')
                    ->leftJoin('md_district','md_district.id','=','md_pincode.district_id')
                    ->leftJoin('md_city','md_city.id','=','md_pincode.city_id')
                    ->leftJoin('md_city_type','md_city_type.id','=','md_pincode.city_type_id')
                    ->select('md_pincode.*','md_country.name as country_name','md_states.name as states_name','md_district.name as district_name','md_city.name as city_name','md_city_type.name as city_type_name')
                    ->whereIn('md_pincode.country_id', $country_id)
                    ->whereIn('md_pincode.state_id', $state_id)
                    ->whereIn('md_pincode.district_id', $district_id)
                    ->whereIn('md_pincode.city_id', $city_id)
                    ->where('md_pincode.pincode','like', '%' . $pincode . '%')
                    ->get();
            }elseif (!empty($country_id) && !empty($state_id) && !empty($district_id) && !empty($city_id)) {
                $data=Pincode::leftJoin('md_country','md_country.id','=','md_pincode.country_id')
                    ->leftJoin('md_states','md_states.id','=','md_pincode.state_id')
                    ->leftJoin('md_district','md_district.id','=','md_pincode.district_id')
                    ->leftJoin('md_city','md_city.id','=','md_pincode.city_id')
                    ->leftJoin('md_city_type','md_city_type.id','=','md_pincode.city_type_id')
                    ->select('md_pincode.*','md_country.name as country_name','md_states.name as states_name','md_district.name as district_name','md_city.name as city_name','md_city_type.name as city_type_name')
                    ->whereIn('md_pincode.country_id', $country_id)
                    ->whereIn('md_pincode.state_id', $state_id)
                    ->whereIn('md_pincode.district_id', $district_id)
                    ->whereIn('md_pincode.city_id', $city_id)
                    ->get();
            }elseif (!empty($country_id) && !empty($state_id) && !empty($district_id)) {
                $data=Pincode::leftJoin('md_country','md_country.id','=','md_pincode.country_id')
                    ->leftJoin('md_states','md_states.id','=','md_pincode.state_id')
                    ->leftJoin('md_district','md_district.id','=','md_pincode.district_id')
                    ->leftJoin('md_city','md_city.id','=','md_pincode.city_id')
                    ->leftJoin('md_city_type','md_city_type.id','=','md_pincode.city_type_id')
                    ->select('md_pincode.*','md_country.name as country_name','md_states.name as states_name','md_district.name as district_name','md_city.name as city_name','md_city_type.name as city_type_name')
                    ->whereIn('md_pincode.country_id', $country_id)
                    ->whereIn('md_pincode.state_id', $state_id)
                    ->whereIn('md_pincode.district_id', $district_id)
                    ->get();
            }elseif (!empty($country_id) && !empty($state_id)) {
                // return $request;
                $data=Pincode::leftJoin('md_country','md_country.id','=','md_pincode.country_id')
                    ->leftJoin('md_states','md_states.id','=','md_pincode.state_id')
                    ->leftJoin('md_district','md_district.id','=','md_pincode.district_id')
                    ->leftJoin('md_city','md_city.id','=','md_pincode.city_id')
                    ->leftJoin('md_city_type','md_city_type.id','=','md_pincode.city_type_id')
                    ->select('md_pincode.*','md_country.name as country_name','md_states.name as states_name','md_district.name as district_name','md_city.name as city_name','md_city_type.name as city_type_name')
                    ->whereIn('md_pincode.country_id', $country_id)
                    ->whereIn('md_pincode.state_id', $state_id)
                    ->get();
            }elseif (!empty($country_id)) {
                $data=Pincode::leftJoin('md_country','md_country.id','=','md_pincode.country_id')
                    ->leftJoin('md_states','md_states.id','=','md_pincode.state_id')
                    ->leftJoin('md_district','md_district.id','=','md_pincode.district_id')
                    ->leftJoin('md_city','md_city.id','=','md_pincode.city_id')
                    ->leftJoin('md_city_type','md_city_type.id','=','md_pincode.city_type_id')
                    ->select('md_pincode.*','md_country.name as country_name','md_states.name as states_name','md_district.name as district_name','md_city.name as city_name','md_city_type.name as city_type_name')
                    ->whereIn('md_pincode.country_id', $country_id)
                    ->get();
            }elseif (!empty($state_id)) {
                $data=Pincode::leftJoin('md_country','md_country.id','=','md_pincode.country_id')
                    ->leftJoin('md_states','md_states.id','=','md_pincode.state_id')
                    ->leftJoin('md_district','md_district.id','=','md_pincode.district_id')
                    ->leftJoin('md_city','md_city.id','=','md_pincode.city_id')
                    ->leftJoin('md_city_type','md_city_type.id','=','md_pincode.city_type_id')
                    ->select('md_pincode.*','md_country.name as country_name','md_states.name as states_name','md_district.name as district_name','md_city.name as city_name','md_city_type.name as city_type_name')
                    ->whereIn('md_pincode.state_id', $state_id)
                    ->get();
            }elseif (!empty($district_id)) {
                $data=Pincode::leftJoin('md_country','md_country.id','=','md_pincode.country_id')
                    ->leftJoin('md_states','md_states.id','=','md_pincode.state_id')
                    ->leftJoin('md_district','md_district.id','=','md_pincode.district_id')
                    ->leftJoin('md_city','md_city.id','=','md_pincode.city_id')
                    ->leftJoin('md_city_type','md_city_type.id','=','md_pincode.city_type_id')
                    ->select('md_pincode.*','md_country.name as country_name','md_states.name as states_name','md_district.name as district_name','md_city.name as city_name','md_city_type.name as city_type_name')
                    ->whereIn('md_pincode.district_id', $district_id)
                    ->get();
            }elseif (!empty($city_id)) {
                $data=Pincode::leftJoin('md_country','md_country.id','=','md_pincode.country_id')
                    ->leftJoin('md_states','md_states.id','=','md_pincode.state_id')
                    ->leftJoin('md_district','md_district.id','=','md_pincode.district_id')
                    ->leftJoin('md_city','md_city.id','=','md_pincode.city_id')
                    ->leftJoin('md_city_type','md_city_type.id','=','md_pincode.city_type_id')
                    ->select('md_pincode.*','md_country.name as country_name','md_states.name as states_name','md_district.name as district_name','md_city.name as city_name','md_city_type.name as city_type_name')
                    ->whereIn('md_pincode.city_id', $city_id)
                    ->get();
            }elseif (!empty($city_type_id)) {
                $data=Pincode::leftJoin('md_country','md_country.id','=','md_pincode.country_id')
                    ->leftJoin('md_states','md_states.id','=','md_pincode.state_id')
                    ->leftJoin('md_district','md_district.id','=','md_pincode.district_id')
                    ->leftJoin('md_city','md_city.id','=','md_pincode.city_id')
                    ->leftJoin('md_city_type','md_city_type.id','=','md_pincode.city_type_id')
                    ->select('md_pincode.*','md_country.name as country_name','md_states.name as states_name','md_district.name as district_name','md_city.name as city_name','md_city_type.name as city_type_name')
                    ->whereIn('md_pincode.city_type_id', $city_type_id)
                    ->get();
            }elseif ($pincode) {
                $data=Pincode::leftJoin('md_country','md_country.id','=','md_pincode.country_id')
                    ->leftJoin('md_states','md_states.id','=','md_pincode.state_id')
                    ->leftJoin('md_district','md_district.id','=','md_pincode.district_id')
                    ->leftJoin('md_city','md_city.id','=','md_pincode.city_id')
                    ->leftJoin('md_city_type','md_city_type.id','=','md_pincode.city_type_id')
                    ->select('md_pincode.*','md_country.name as country_name','md_states.name as states_name','md_district.name as district_name','md_city.name as city_name','md_city_type.name as city_type_name')
                    ->where('md_pincode.pincode','like', '%' . $pincode . '%')
                    ->get();
            } else {
                $data=Pincode::leftJoin('md_country','md_country.id','=','md_pincode.country_id')
                    ->leftJoin('md_states','md_states.id','=','md_pincode.state_id')
                    ->leftJoin('md_district','md_district.id','=','md_pincode.district_id')
                    ->leftJoin('md_city','md_city.id','=','md_pincode.city_id')
                    ->leftJoin('md_city_type','md_city_type.id','=','md_pincode.city_type_id')
                    ->select('md_pincode.*','md_country.name as country_name','md_states.name as states_name','md_district.name as district_name','md_city.name as city_name','md_city_type.name as city_type_name')
                    ->get();
            }
            // return $data;
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}
