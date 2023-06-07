<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\State;
use Validator;
use Excel;

class StateController extends Controller
{
    public function searchDetails(Request $request)
    {
        try {
            $paginate=$request->paginate;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            if ($sort_by && $column_name) {
                $data=State::paginate($paginate);      
            }else{
                $data=State::paginate($paginate);   
            }   
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
    }

    public function export(Request $request)
    {
        try {
            $paginate=$request->paginate;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            if ($sort_by && $column_name) {
                $data=State::get();      
            }else{
                $data=State::get();   
            }   
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
    }

    public function index(Request $request)
    {
        try {  
            $search=$request->search;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            $country_id=$request->country_id;
            $arr_country_id=json_decode($request->arr_country_id);
            $id=$request->id;
            if ($search) {
                $data=State::where('name','like', '%' . $search . '%')->get();      
            }elseif ($country_id) {
                $data=State::where('country_id',$country_id)->orderBy('name','desc')->get();
            }elseif (!empty($arr_country_id)) {
                // return $arr_country_id;
                $data=State::whereIn('country_id',$arr_country_id)->orderBy('name','desc')->get();
            }elseif ($id) {
                $data=State::where('id',$id)->orderBy('name','desc')->get();
            } else{
                $data=State::get();   
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
            'name'=>'required',
            'country_id'=>'required',
        ]);
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            $id=$request->id;
            if ($id > 0) {
                $data=State::find($id);
                $data->country_id=$request->country_id;
                $data->name=$request->name;
                $data->save();
            }else {
                $data=State::create(array(
                    'country_id'=>$request->country_id,
                    'name'=>$request->name,
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
            $datas = Excel::toArray([],  $request->file('file'));
            // return $data[0];
            $data=$datas[0];
            foreach ($data as $key => $value) {
                if ($key==0) {
                    if ($value[0]!="State") {
                        return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
                    }
                    // return $value;
                }else {
                    $is_has=State::where('name',$value[0])->get();
                    if (count($is_has) > 0) {
                        State::whereId($is_has[0]->id)->update(array(
                            'country_id'=>$request->country_id,
                            'name'=>$value[0],
                            // 'delete_flag'=>'N',
                        ));
                    }else {
                        State::create(array(
                            'country_id'=>$request->country_id,
                            'name'=>$value[0],
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