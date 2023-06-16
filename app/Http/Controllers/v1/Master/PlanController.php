<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{Plan,MutualFund};
use Validator;
use Excel;
use App\Imports\PlanImport;

class PlanController extends Controller
{
    public function searchDetails(Request $request)
    {
        try {
            $paginate=$request->paginate;
            $plan_name=$request->plan_name;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            if ($paginate=='A') {
                $paginate=999999999;
            }
            if ($sort_by && $column_name) {
                $data=Plan::where('plan_name','like', '%' . $plan_name . '%')
                    ->orderBy($column_name,$sort_by)
                    ->paginate($paginate); 
            }elseif ($plan_name) {
                $data=Plan::where('plan_name','like', '%' . $plan_name . '%')
                    ->orderBy('updated_at','DESC')
                    ->paginate($paginate);  
            } else {
                $data=Plan::orderBy('updated_at','DESC')->paginate($paginate);  
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
            $plan_name=$request->plan_name;
            if ($plan_name) {
                $data=Plan::where('plan_name','like', '%' . $plan_name . '%')
                    ->orderBy('updated_at','DESC')
                    ->get();  
            } else {
                $data=Plan::orderBy('updated_at','DESC')->get();  
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
                $data=Plan::where('plan_name','like', '%' . $search . '%')->get();      
            }else if ($id!='') {
                $data=Plan::where('id',$id)->get();      
            }elseif ($paginate!='') {
                $data=Plan::paginate($paginate);      
            } else {
                $data=Plan::get();      
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
            'plan_name' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            if ($request->id > 0) {
                $data=Plan::find($request->id);
                $data->plan_name=$request->plan_name;
                $data->save();
            }else{
                $is_has=Plan::where('plan_name',$request->plan_name)->where('delete_flag','N')->get();
                if (count($is_has) > 0) {
                    return Helper::WarningResponse(parent::ALREADY_EXIST);
                }else {
                    $data=Plan::create(array(
                        'plan_name'=>$request->plan_name,
                        // 'created_by'=>'',
                    ));    
                }
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
            $is_has=MutualFund::where('plan_id',$id)->orWhere('plan_id_to',$id)->get();
            if (count($is_has)>0) {
                return Helper::WarningResponse(parent::DELETE_NOT_ALLOW_ERROR);
            }else {
                $data=Plan::find($id);
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
                    if ($value[0]=="Plan") {
                        return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
                    }
                    // return $value;
                }else {
                    // return $value;
                    // return $value[0];
                    Plan::create(array(
                        'plan_name'=>$value[0],
                        // 'created_by'=>'',
                    ));    
                }
               
            }

            // return gettype($data[0][0]) ;
            // if (in_array("rnt_id", $data)) {
            // if ($data[0][0] == "plan_name") {
            //     return "hii";
                // Excel::import(new PlanImport,$request->file);
                // Excel::import(new PlanImport,request()->file('file'));
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
