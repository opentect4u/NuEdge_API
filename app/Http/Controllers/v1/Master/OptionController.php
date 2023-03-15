<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{Option,MutualFund};
use Validator;
use Excel;
use App\Imports\OptionImport;

class OptionController extends Controller
{
    public function searchDetails(Request $request)
    {
        try {
            $paginate=$request->paginate;
            $opt_name=$request->opt_name;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            if ($paginate=='A') {
                $paginate=999999999;
            }
            if ($sort_by && $column_name) {
                $data=Option::where('opt_name','like', '%' . $opt_name . '%')
                    ->orderBy($column_name,$sort_by)
                    ->paginate($paginate); 
            }elseif ($opt_name) {
                $data=Option::where('opt_name','like', '%' . $opt_name . '%')
                    ->orderBy('updated_at','DESC')
                    ->paginate($paginate); 
            } else {
                $data=Option::orderBy('updated_at','DESC')
                    ->paginate($paginate); 
            }
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
    public function export(Request $request)
    {
        try {
            $opt_name=$request->opt_name;
            if ($opt_name) {
                $data=Option::where('opt_name','like', '%' . $opt_name . '%')
                    ->orderBy('updated_at','DESC')
                    ->get(); 
            } else {
                $data=Option::orderBy('updated_at','DESC')
                    ->get(); 
            }    
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
    public function index(Request $request)
    {
        try {  
            $search=$request->search;
            $id=$request->id;
            $paginate=$request->paginate;
            if ($paginate=='A') {
                $paginate=999999999;
            }
            if ($search!='') {
                $data=Option::where('opt_name','like', '%' . $search . '%')->get();      
            }else if ($id!='') {
                $data=Option::where('id',$id)->get();      
            }elseif ($paginate!='') {
                $data=Option::paginate($paginate);      
            }else {
                $data=Option::get();      
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
            'opt_name' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            if ($request->id > 0) {
                $data=Option::find($request->id);
                $data->opt_name=$request->opt_name;
                $data->save();
            }else{
                $data=Option::create(array(
                    'opt_name'=>$request->opt_name,
                    // 'created_by'=>'',
                ));    
            }  
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function delete(Request $request)
    {
        try {
            $id=$request->id;
            $is_has=MutualFund::where('option_id',$id)->orWhere('option_id_to',$id)->get();
            if (count($is_has)>0) {
                return Helper::WarningResponse(parent::DELETE_NOT_ALLOW_ERROR);
            }else {
                $data=Option::find($id);
                $data->delete_flag='Y';
                $data->deleted_date=date('Y-m-d H:i:s');
                $data->deleted_by=1;
                $data->save();
            }
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DELETE_FAIL_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function import(Request $request)
    {
        try {
            // return $request;
            $path = $request->file('file')->getRealPath();
            $data = array_map('str_getcsv', file($path));
            // return $data[0][0];


            foreach ($data as $key => $value) {
                if ($key==0) {
                    if ($value[0]=="Option") {
                        return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
                    }
                    // return $value;
                }else {
                    // return $value;
                    // return $value[0];
                    Option::create(array(
                        'opt_name'=>$value[0],
                        // 'created_by'=>'',
                    ));       
                }
               
            }
            // return gettype($data[0][0]) ;
            // if (in_array("rnt_id", $data)) {
            // if ($data[0][0] == "opt_name") {
            //     return "hii";
                // Excel::import(new OptionImport,$request->file);
                // Excel::import(new OptionImport,request()->file('file'));
                $data1=[];
            // }else {
            //     return "else";
            //     return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
            // }
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
        }
        return Helper::SuccessResponse($data1);
    }
    
}

