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
            $id=$request->id;
            if ($search) {
                $data=Pincode::where('name','like', '%' . $search . '%')->get();      
            }elseif ($city_id) {
                $data=Pincode::where('city_id',$city_id)->get();
            }elseif ($id) {
                $data=Pincode::where('id',$id)->get();
            } else{
                $data=Pincode::get();   
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
            'pincode'=>'required',
            'country_id'=>'required',
            'state_id'=>'required',
            'district_id'=>'required',
            'city_id'=>'required',
            'city_type_id'=>'required',
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
                $data->city_type_id=$request->city_type_id;
                $data->pincode=$request->pincode;
                $data->save();
            }else {
                $data=Pincode::create(array(
                    'country_id'=>$request->country_id,
                    'state_id'=>$request->state_id,
                    'district_id'=>$request->district_id,
                    'city_id'=>$request->city_id,
                    'city_type_id'=>$request->city_type_id,
                    'pincode'=>$request->pincode,
                ));
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
}
