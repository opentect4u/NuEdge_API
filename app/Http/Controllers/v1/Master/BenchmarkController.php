<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{Benchmark,MutualFund};
use Validator;
use Excel;
use App\Imports\BenchmarkImport;

class BenchmarkController extends Controller
{
    public function searchDetails(Request $request)
    {
        try {
            $paginate=$request->paginate;
            $Benchmark_name=$request->Benchmark_name;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            if ($paginate=='A') {
                $paginate=999999999;
            }
            if ($sort_by && $column_name) {
                $data=Benchmark::where('Benchmark_name','like', '%' . $Benchmark_name . '%')
                    ->orderBy($column_name,$sort_by)
                    ->paginate($paginate); 
            }elseif ($Benchmark_name) {
                $data=Benchmark::where('Benchmark_name','like', '%' . $Benchmark_name . '%')
                    ->orderBy('updated_at','DESC')
                    ->paginate($paginate);  
            } else {
                $data=Benchmark::orderBy('updated_at','DESC')->paginate($paginate);  
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
            $Benchmark_name=$request->Benchmark_name;
            if ($Benchmark_name) {
                $data=Benchmark::where('benchmark','like', '%' . $Benchmark_name . '%')
                    ->orderBy('updated_at','DESC')
                    ->get();  
            } else {
                $data=Benchmark::orderBy('updated_at','DESC')->get();  
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
                $data=Benchmark::where('benchmark','like', '%' . $search . '%')->get();      
            }else if ($id!='') {
                $data=Benchmark::where('id',$id)->get();      
            }elseif ($paginate!='') {
                $data=Benchmark::paginate($paginate);      
            } else {
                $data=Benchmark::get();      
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
            'ex_id' =>'required',
            'benchmark' =>'required',
            'category_id' =>'required',
            'subcat_id' =>'required',
            'launch_date' =>'required',
            'launch_price' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            $data='';
            if ($request->id > 0) {
                $data=Benchmark::find($request->id);
                $data->ex_id=$request->ex_id;
                $data->benchmark=$request->benchmark;
                $data->category_id=$request->category_id;
                $data->subcat_id=$request->subcat_id;
                $data->launch_date=$request->launch_date;
                $data->launch_price=$request->launch_price;
                $data->save();
            }else{
                // $is_has=Benchmark::where('benchmark',$request->benchmark)->where('delete_flag','N')->get();
                // if (count($is_has) > 0) {
                //     return Helper::WarningResponse(parent::ALREADY_EXIST);
                // }else {
                //     $data=Benchmark::create(array(
                //         'ex_id'=>$request->ex_id,
                //         'benchmark'=>$request->benchmark,
                //         'category_id'=>$request->category_id,
                //         'subcat_id'=>$request->subcat_id,
                //         'launch_date'=>$request->launch_date,
                //         'launch_price'=>$request->launch_price,
                //         // 'created_by'=>'',
                //     ));    
                // }
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
            $is_has=MutualFund::where('Benchmark_id',$id)->orWhere('Benchmark_id_to',$id)->get();
            if (count($is_has)>0) {
                return Helper::WarningResponse(parent::DELETE_NOT_ALLOW_ERROR);
            }else {
                $data=Benchmark::find($id);
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
                    if ($value[0]=="Benchmark") {
                        return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
                    }
                    // return $value;
                }else {
                    // return $value;
                    // return $value[0];
                    Benchmark::create(array(
                        'Benchmark_name'=>$value[0],
                        // 'created_by'=>'',
                    ));    
                }
               
            }

            // return gettype($data[0][0]) ;
            // if (in_array("rnt_id", $data)) {
            // if ($data[0][0] == "Benchmark_name") {
            //     return "hii";
                // Excel::import(new BenchmarkImport,$request->file);
                // Excel::import(new BenchmarkImport,request()->file('file'));
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
