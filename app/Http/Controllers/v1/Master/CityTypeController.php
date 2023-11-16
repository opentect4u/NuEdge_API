<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\CityType;
use App\Models\Pincode;
use Validator;
use Excel;

class CityTypeController extends Controller
{
    public function index(Request $request)
    {
        try {  
            $search=$request->search;
            $district_id=$request->district_id;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            if ($search!='') {
                $data=CityType::where('name','like', '%' . $search . '%')->get();      
            }else{
                $data=CityType::get();   
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
        ]);
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            $id=$request->id;
            if ($id > 0) {
                $data=CityType::find($id);
                $data->name=$request->name;
                $data->updated_by=Helper::modifyUser($request->user());
                $data->save();
            }else {
                $data=CityType::create(array(
                    'name'=>$request->name,
                    'created_by'=>Helper::modifyUser($request->user()),
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
                    if (str_replace(" ","_",$value[0])!="City_Type") {
                        // return $value;
                        return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
                    }
                    // return $value;
                }else {
                    $is_has=CityType::where('name',$value[0])->get();
                    if (count($is_has) > 0) {
                        CityType::whereId($is_has[0]->id)->update(array(
                            'name'=>$value[0],
                            // 'delete_flag'=>'N',
                        ));
                    }else {
                        CityType::create(array(
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



    public function map(Request $request)
    {
        try {
            // return $request;
            $datas = Excel::toArray([], $request->file('file'));
            $data=$datas[0];
            // $datas = array_map(function($v){return str_getcsv($v, ";");}, file($file_name));  //for csv file

            // return $data;
            // return count($data);
            $data1=array_slice($data,1,(count($data)-1));
            $manku = array_map(function ($el){
                    // print_r($el);
                    return $el[0];
            },$data1);
            // return $manku;

            $update_wherein=Pincode::whereIn('pincode',$manku)
            ->update([
                'city_type_id'=>$request->city_type_id,
                'updated_by'=>Helper::modifyUser($request->user()),
            ]);
            if ($request->city_type_id==1) {
                $update_notwherein=Pincode::whereNotIn('pincode',$manku)
                    ->update([
                        'city_type_id'=>2,
                        'updated_by'=>Helper::modifyUser($request->user()),
                    ]);
            }elseif ($request->city_type_id==2) {
                $update_notwherein=Pincode::whereNotIn('pincode',$manku)
                    ->update([
                        'city_type_id'=>1,
                        'updated_by'=>Helper::modifyUser($request->user()),
                    ]);
            }
            // return $update_notwherein;
            $data1=[];

        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
        }
        return Helper::SuccessResponse($data1);
    }
}
