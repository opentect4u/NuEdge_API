<?php

namespace App\Http\Controllers\v1\Operation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{Benchmark,MutualFund,BenchmarkScheme};
use Validator;
use Excel;
use App\Imports\BenchmarkImport;

class BenchmarkSchemeController extends Controller
{
    public function searchDetails(Request $request)
    {
        try {
            $paginate=$request->paginate;
            $Benchmark_name=$request->Benchmark_name;
            $order=$request->order;
            $field=$request->field;
            if ($paginate=='A') {
                $paginate=999999999;
            }
            if ($order && $field) {
                $rawOrderBy='';
                if ($order > 0) {
                    $rawOrderBy=$field.' ASC';
                } else {
                    $rawOrderBy=$field.' DESC';
                }
                
                $data=BenchmarkScheme::leftJoin('md_exchange','md_exchange.id','=','td_benchmark_scheme.ex_id')
                    ->leftJoin('md_benchmark','md_benchmark.id','=','td_benchmark_scheme.benchmark')
                    ->select('td_benchmark_scheme.*','md_exchange.ex_name as exchange_name','md_benchmark.benchmark as benchmark')
                    ->orderByRaw($rawOrderBy)
                    ->paginate($paginate);
            }else {
                $data=BenchmarkScheme::leftJoin('md_exchange','md_exchange.id','=','td_benchmark_scheme.ex_id')
                    ->leftJoin('md_benchmark','md_benchmark.id','=','td_benchmark_scheme.benchmark')
                    ->select('td_benchmark_scheme.*','md_exchange.ex_name as exchange_name','md_benchmark.benchmark as benchmark')
                    ->orderBy('td_benchmark_scheme.created_at','desc')
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
            $data=[];
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
            $category_id=$request->category_id;
            $subcategory_id=$request->subcat_id;

            if ($paginate=='A') {
                $paginate=999999999;
            }
            if ($search) {
                $data=BenchmarkScheme::where('benchmark','like', '%' . $search . '%')->get();      
            }else if ($category_id && $subcategory_id) {
                $data=BenchmarkScheme::where('category_id',$category_id)->where('subcat_id',$subcategory_id)->get();      
            }else if ($id) {
                $data=BenchmarkScheme::where('id',$id)->get();      
            }else {
                $data=BenchmarkScheme::get();      
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
            'date'=>'required',
            'open'=>'required',
            'high'=>'required',
            'low'=>'required',
            'close'=>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            if ($request->id > 0) {
                $data=BenchmarkScheme::find($request->id);
                $data->ex_id=$request->ex_id;
                $data->benchmark=$request->benchmark;
                $data->category_id=$request->category_id;
                $data->subcat_id=$request->subcat_id;
                $data->launch_date=$request->launch_date;
                $data->launch_price=$request->launch_price;
                $data->save();
            }else{
                $is_has=BenchmarkScheme::where('benchmark',$request->benchmark)
                    ->where('ex_id',$request->ex_id)
                    ->where('date',$request->date)
                    ->where('delete_flag','N')
                    ->get();
                // return $is_has;
                if (count($is_has) > 0) {
                    return Helper::WarningResponse(parent::ALREADY_EXIST);
                }else {
                    $data=BenchmarkScheme::create(array(
                        'ex_id'=>$request->ex_id,
                        'benchmark'=>$request->benchmark,
                        'date'=>$request->date,
                        'open'=>$request->open,
                        'high'=>$request->high,
                        'low'=>$request->low,
                        'close'=>$request->close,
                        // 'created_by'=>'',
                    ));    
                }
            } 
            $mydata=[];
            $mydata=BenchmarkScheme::leftJoin('md_exchange','md_exchange.id','=','td_benchmark_scheme.ex_id')
                ->leftJoin('md_benchmark','md_benchmark.id','=','td_benchmark_scheme.benchmark')
                ->select('td_benchmark_scheme.*','md_exchange.ex_name as exchange_name','md_benchmark.benchmark as benchmark')
                ->where('td_benchmark_scheme.id',$data->id)
                ->first();
        } catch (\Throwable $th) {
            // throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($mydata);
    }

    public function delete(Request $request)
    {
        try {
            $id=$request->id;
            $is_has=MutualFund::where('Benchmark_id',$id)->orWhere('Benchmark_id_to',$id)->get();
            if (count($is_has)>0) {
                return Helper::WarningResponse(parent::DELETE_NOT_ALLOW_ERROR);
            }else {
                $data=BenchmarkScheme::find($id);
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
                    BenchmarkScheme::create(array(
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

