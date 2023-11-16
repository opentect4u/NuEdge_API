<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\Country;
use Validator;
use Excel;

class CountryController extends Controller
{
    public function searchDetails(Request $request)
    {
        try {
            $paginate=$request->paginate;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            if ($sort_by && $column_name) {
                $data=Country::paginate($paginate);      
            }else{
                $data=Country::paginate($paginate);   
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
                $data=Country::get();      
            }else{
                $data=Country::get();   
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
            $id=$request->id;
            if ($search!='') {
                $data=Country::where('name','like', '%' . $search . '%')->get();      
            }elseif ($id) {
                $data=Country::where('id',$id)->get();   
            } else{
                $data=Country::get();   
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
                $data=Country::find($id);
                $data->name=$request->name;
                $data->updated_by=Helper::modifyUser($request->user());
                $data->save();
            }else {
                
                $data=Country::create(array(
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
            $datas = Excel::toArray([],  $request->file('file'));
            // return $datas[0];
            $data=$datas[0];
            foreach ($data as $key => $value) {
                if ($key==0) {
                    // return $value;
                    if ($value[0] != "Country") {
                        // return $value;
                        return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
                    }
                }else {
                    $is_has=Country::where('name',$value[0])->get();
                    if (count($is_has) > 0) {
                        Country::whereId($is_has[0]->id)->update(array(
                            'name'=>$value[0],
                            // 'delete_flag'=>'N',
                        ));
                    }else {
                        Country::create(array(
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