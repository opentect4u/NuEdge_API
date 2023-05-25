<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\City;
use Validator;
use Excel;

class CityController extends Controller
{
    public function index(Request $request)
    {
        try {  
            $search=$request->search;
            $district_id=$request->district_id;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            $id=$request->id;
            if ($search!='') {
                $data=City::where('name','like', '%' . $search . '%')->get();      
            }elseif ($district_id!='') {
                $data=City::where('district_id',$district_id)->get();   
            }elseif ($id!='') {
                $data=City::where('id',$id)->get();   
            } else{
                $data=City::get();   
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
            'name'=>'required',
            'country_id'=>'required',
            'state_id'=>'required',
            'district_id'=>'required',
        ]);
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            $id=$request->id;
            if ($id > 0) {
                $data=City::find($id);
                $data->country_id=$request->country_id;
                $data->state_id=$request->state_id;
                $data->district_id=$request->district_id;
                $data->name=$request->name;
                $data->save();
            }else {
                $data=City::create(array(
                    'country_id'=>$request->country_id,
                    'state_id'=>$request->state_id,
                    'district_id'=>$request->district_id,
                    'name'=>$request->name,
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
            $datas = Excel::toArray([], $request->file('file'));
            // return $datas;
            $data=$datas[0];
            // return $data;
            foreach ($data as $key => $value) {
                if ($key==0) {
                    if ($value[0]!="City") {
                        // return $value;
                        return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
                    }
                    // return $value;
                }else {
                    $is_has=City::where('name',$value[0])->get();
                    if (count($is_has) > 0) {
                        City::whereId($is_has[0]->id)->update(array(
                            'country_id'=>$request->country_id,
                            'state_id'=>$request->state_id,
                            'district_id'=>$request->district_id,
                            'name'=>$value[0],
                            // 'delete_flag'=>'N',
                        ));
                    }else {
                        City::create(array(
                            'country_id'=>$request->country_id,
                            'state_id'=>$request->state_id,
                            'district_id'=>$request->district_id,
                            'name'=>$value[0],
                            // 'delete_flag'=>'N',
                        ));
                        
                    }
                }
            }

            $data1=[];

        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
        }
        return Helper::SuccessResponse($data1);
    }
}
