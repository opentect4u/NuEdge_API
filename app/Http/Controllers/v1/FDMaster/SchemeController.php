<?php

namespace App\Http\Controllers\v1\FDMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\FDScheme;
use Validator;
use Excel;

class SchemeController extends Controller
{
    public function searchDetails(Request $request)
    {
        try {
            $paginate=$request->paginate;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;

            $comp_type_id=json_decode($request->comp_type_id);
            $comp_id=json_decode($request->company_id);
            $scheme_name=$request->scheme_name;

            if ($paginate=='A') {
                $paginate=999999999;
            }
            if ($sort_by && $column_name) {
                $data=FDScheme::leftJoin('md_fd_company','md_fd_company.id','=','md_fd_scheme.comp_id')
                    ->leftJoin('md_fd_type_of_company','md_fd_type_of_company.id','=','md_fd_scheme.comp_type_id')
                    ->select('md_fd_scheme.*','md_fd_company.comp_short_name as comp_short_name','md_fd_company.comp_full_name as comp_full_name','md_fd_type_of_company.comp_type as comp_type')
                    ->where('md_fd_scheme.delete_flag','N')
                    ->orderBy('md_fd_scheme.updated_at','DESC')
                    ->paginate($paginate);  
            }elseif (!empty($comp_id)) {
                $setarray=[];
                foreach ($comp_id as $key => $comp) {
                    array_push($setarray,$comp->id);
                }
                $data=FDScheme::leftJoin('md_fd_company','md_fd_company.id','=','md_fd_scheme.comp_id')
                    ->leftJoin('md_fd_type_of_company','md_fd_type_of_company.id','=','md_fd_scheme.comp_type_id')
                    ->select('md_fd_scheme.*','md_fd_company.comp_short_name as comp_short_name','md_fd_company.comp_full_name as comp_full_name','md_fd_type_of_company.comp_type as comp_type')
                    ->where('md_fd_scheme.delete_flag','N')
                    ->whereIn('md_fd_scheme.comp_id', $setarray)
                    ->orderBy('md_fd_scheme.updated_at','DESC')
                    ->paginate($paginate);  
            }elseif (!empty($comp_type_id)) {
                $arr_comp_type=[];
                foreach ($comp_type_id as $key => $comp_types) {
                    array_push($arr_comp_type,$comp_types->id);
                }
                $data=FDScheme::leftJoin('md_fd_company','md_fd_company.id','=','md_fd_scheme.comp_id')
                    ->leftJoin('md_fd_type_of_company','md_fd_type_of_company.id','=','md_fd_scheme.comp_type_id')
                    ->select('md_fd_scheme.*','md_fd_company.comp_short_name as comp_short_name','md_fd_company.comp_full_name as comp_full_name','md_fd_type_of_company.comp_type as comp_type')
                    ->whereIn('md_fd_scheme.comp_type_id', $arr_comp_type)
                    ->where('md_fd_scheme.delete_flag','N')
                    ->orderBy('md_fd_scheme.updated_at','DESC')
                    ->paginate($paginate);  
            }elseif ($scheme_name) {
                $data=FDScheme::leftJoin('md_fd_company','md_fd_company.id','=','md_fd_scheme.comp_id')
                    ->leftJoin('md_fd_type_of_company','md_fd_type_of_company.id','=','md_fd_scheme.comp_type_id')
                    ->select('md_fd_scheme.*','md_fd_company.comp_short_name as comp_short_name','md_fd_company.comp_full_name as comp_full_name','md_fd_type_of_company.comp_type as comp_type')
                    ->where('md_fd_scheme.scheme_name','like', '%' . $scheme_name . '%')
                    ->where('md_fd_scheme.delete_flag','N')
                    ->orderBy('md_fd_scheme.updated_at','DESC')
                    ->paginate($paginate);  
            } else {
                $data=FDScheme::leftJoin('md_fd_company','md_fd_company.id','=','md_fd_scheme.comp_id')
                    ->leftJoin('md_fd_type_of_company','md_fd_type_of_company.id','=','md_fd_scheme.comp_type_id')
                    ->select('md_fd_scheme.*','md_fd_company.comp_short_name as comp_short_name','md_fd_company.comp_full_name as comp_full_name','md_fd_type_of_company.comp_type as comp_type')
                    ->where('md_fd_scheme.delete_flag','N')
                    ->orderBy('md_fd_scheme.updated_at','DESC')
                    ->paginate($paginate);  
            }
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
    public function export(Request $request)
    {
        try {
            $paginate=$request->paginate;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;

            $comp_type_id=json_decode($request->comp_type_id);
            $comp_id=json_decode($request->company_id);
            $scheme_name=$request->scheme_name;

            if ($paginate=='A') {
                $paginate=999999999;
            }
            if ($sort_by && $column_name) {
                $data=FDScheme::leftJoin('md_fd_company','md_fd_company.id','=','md_fd_scheme.comp_id')
                    ->leftJoin('md_fd_type_of_company','md_fd_type_of_company.id','=','md_fd_scheme.comp_type_id')
                    ->select('md_fd_scheme.*','md_fd_company.comp_short_name as comp_short_name','md_fd_company.comp_full_name as comp_full_name','md_fd_type_of_company.comp_type as comp_type')
                    ->where('md_fd_scheme.delete_flag','N')
                    ->orderBy('md_fd_scheme.updated_at','DESC')
                    ->get();  
            }elseif (!empty($comp_id)) {
                $setarray=[];
                foreach ($comp_id as $key => $comp) {
                    array_push($setarray,$comp->id);
                }
                $data=FDScheme::leftJoin('md_fd_company','md_fd_company.id','=','md_fd_scheme.comp_id')
                    ->leftJoin('md_fd_type_of_company','md_fd_type_of_company.id','=','md_fd_scheme.comp_type_id')
                    ->select('md_fd_scheme.*','md_fd_company.comp_short_name as comp_short_name','md_fd_company.comp_full_name as comp_full_name','md_fd_type_of_company.comp_type as comp_type')
                    ->where('md_fd_scheme.delete_flag','N')
                    ->whereIn('md_fd_scheme.comp_id', $setarray)
                    ->orderBy('md_fd_scheme.updated_at','DESC')
                    ->get();  
            }elseif (!empty($comp_type_id)) {
                $arr_comp_type=[];
                foreach ($comp_type_id as $key => $comp_types) {
                    array_push($arr_comp_type,$comp_types->id);
                }
                $data=FDScheme::leftJoin('md_fd_company','md_fd_company.id','=','md_fd_scheme.comp_id')
                    ->leftJoin('md_fd_type_of_company','md_fd_type_of_company.id','=','md_fd_scheme.comp_type_id')
                    ->select('md_fd_scheme.*','md_fd_company.comp_short_name as comp_short_name','md_fd_company.comp_full_name as comp_full_name','md_fd_type_of_company.comp_type as comp_type')
                    ->whereIn('md_fd_scheme.comp_type_id', $arr_comp_type)
                    ->where('md_fd_scheme.delete_flag','N')
                    ->orderBy('md_fd_scheme.updated_at','DESC')
                    ->get();  
            }elseif ($scheme_name) {
                $data=FDScheme::leftJoin('md_fd_company','md_fd_company.id','=','md_fd_scheme.comp_id')
                    ->leftJoin('md_fd_type_of_company','md_fd_type_of_company.id','=','md_fd_scheme.comp_type_id')
                    ->select('md_fd_scheme.*','md_fd_company.comp_short_name as comp_short_name','md_fd_company.comp_full_name as comp_full_name','md_fd_type_of_company.comp_type as comp_type')
                    ->where('md_fd_scheme.scheme_name','like', '%' . $scheme_name . '%')
                    ->where('md_fd_scheme.delete_flag','N')
                    ->orderBy('md_fd_scheme.updated_at','DESC')
                    ->get();  
            } else {
                $data=FDScheme::leftJoin('md_fd_company','md_fd_company.id','=','md_fd_scheme.comp_id')
                    ->leftJoin('md_fd_type_of_company','md_fd_type_of_company.id','=','md_fd_scheme.comp_type_id')
                    ->select('md_fd_scheme.*','md_fd_company.comp_short_name as comp_short_name','md_fd_company.comp_full_name as comp_full_name','md_fd_type_of_company.comp_type as comp_type')
                    ->where('md_fd_scheme.delete_flag','N')
                    ->orderBy('md_fd_scheme.updated_at','DESC')
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
            $comp_id=$request->comp_id;
            $paginate=$request->paginate;
            if ($paginate=='A') {
                $paginate=999999999;
            }
            if ($search!='') {
                $data=FDScheme::where('delete_flag','N')->where('scheme_name','like', '%' . $search . '%')->get();      
            }else if ($id!='') {
                $data=FDScheme::where('delete_flag','N')->where('id',$id)->get();      
            }elseif ($paginate!='') {
                $data=FDScheme::where('delete_flag','N')->paginate($paginate);      
            }else if ($comp_id!='') {
                $data=FDScheme::where('delete_flag','N')->where('comp_id',$comp_id)->get();      
            } else {
                $data=FDScheme::where('delete_flag','N')->get();      
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
            'comp_type_id' =>'required',
            'comp_id' =>'required',
            'scheme_name' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            if ($request->id > 0) {
                $data=FDScheme::find($request->id);
                $data->comp_type_id=$request->comp_type_id;
                $data->comp_id=$request->comp_id;
                $data->scheme_name=$request->scheme_name;
                $data->save();
            }else{
                $data=FDScheme::create(array(
                    'comp_type_id'=>$request->comp_type_id,
                    'comp_id'=>$request->comp_id,
                    'scheme_name'=>$request->scheme_name,
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
                $data=FDScheme::find($id);
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
            $datas = Excel::toArray([],  $request->file('file'));
            return $data[0];
            $data=$datas[0];

            foreach ($data as $key => $value) {
                if ($key==0) {
                    if ($value[0]=="Plan") {
                        return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
                    }
                    // return $value;
                }else {
                    // return $value;
                    // return $value[0];
                    FDScheme::create(array(
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
