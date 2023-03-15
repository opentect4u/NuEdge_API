<?php

namespace App\Http\Controllers\v1\INSMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\InsuranceType;
use Validator;

class InsuranceTypeController extends Controller
{
    public function searchDetails(Request $request)
    {
        try {
            $paginate=$request->paginate;
            $type=$request->type;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            if ($paginate=='A') {
                $paginate=999999999;
            }
            if ($sort_by && $column_name) {
                $data=InsuranceType::where('type','like', '%' . $type . '%')
                    ->where('delete_flag','N')
                    ->orderBy($column_name,$sort_by)
                    ->paginate($paginate); 
            }elseif ($type) {
                $data=InsuranceType::where('type','like', '%' . $type . '%')
                    ->where('delete_flag','N')
                    ->orderBy('updated_at','DESC')
                    ->paginate($paginate);  
            } else {
                $data=InsuranceType::where('delete_flag','N')->orderBy('updated_at','DESC')->paginate($paginate);  
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
            $type=$request->type;
            if ($type) {
                $data=InsuranceType::where('type','like', '%' . $type . '%')
                    ->where('delete_flag','N')
                    ->orderBy('updated_at','DESC')
                    ->get();  
            } else {
                $data=InsuranceType::where('delete_flag','N')->orderBy('updated_at','DESC')->get();  
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
                $data=InsuranceType::where('delete_flag','N')->where('type','like', '%' . $search . '%')->get();      
            }else if ($id!='') {
                $data=InsuranceType::where('delete_flag','N')->where('id',$id)->get();      
            }elseif ($paginate!='') {
                $data=InsuranceType::where('delete_flag','N')->paginate($paginate);      
            } else {
                $data=InsuranceType::where('delete_flag','N')->get();      
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
            'type' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            if ($request->id > 0) {
                $data=InsuranceType::find($request->id);
                $data->type=$request->type;
                $data->save();
            }else{
                $data=InsuranceType::create(array(
                    'type'=>$request->type,
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
            $is_has=InsCompany::where('ins_type_id',$id)->get();
            if (count($is_has)>0) {
                return Helper::WarningResponse(parent::DELETE_NOT_ALLOW_ERROR);
            }else {
                $data=InsuranceType::find($id);
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
                    InsuranceType::create(array(
                        'type'=>$value[0],
                        // 'created_by'=>'',
                    ));    
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
