<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\District;
use Validator;
use Excel;

class DistrictController extends Controller
{
    public function index(Request $request)
    {
        try {  
            $search=$request->search;
            $state_id=$request->state_id;
            $arr_state_id=json_decode($request->arr_state_id);
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            $id=$request->id;
            if ($search!='') {
                $data=District::where('name','like', '%' . $search . '%')->get();      
            }elseif ($state_id!='') {
                $data=District::where('state_id',$state_id)->orderBy('name','desc')->get();   
            }elseif (!empty($arr_state_id)) {
                $data=District::whereIn('state_id',$arr_state_id)->orderBy('name','desc')->get();   
            }elseif ($id!='') {
                $data=District::where('id',$id)->get();   
            } else{
                $data=District::get();   
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
        ]);
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            $id=$request->id;
            if ($id > 0) {
                $data=District::find($id);
                $data->country_id=$request->country_id;
                $data->state_id=$request->state_id;
                $data->name=$request->name;
                $data->save();
            }else {
                $data=District::create(array(
                    'country_id'=>$request->country_id,
                    'state_id'=>$request->state_id,
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
            $datas = Excel::toArray([],  $request->file('file'));
            // return $data[0];
            $data=$datas[0];
            // return $data;
            foreach ($data as $key => $value) {
                if ($key==0) {
                    if ($value[0]!="District") {
                        // return $value;
                        return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
                    }
                    // return $value;
                }else {
                    $is_has=District::where('name',$value[0])->get();
                    if (count($is_has) > 0) {
                        District::whereId($is_has[0]->id)->update(array(
                            'country_id'=>$request->country_id,
                            'state_id'=>$request->state_id,
                            'name'=>$value[0],
                            // 'delete_flag'=>'N',
                        ));
                    }else {
                        District::create(array(
                            'country_id'=>$request->country_id,
                            'state_id'=>$request->state_id,
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
