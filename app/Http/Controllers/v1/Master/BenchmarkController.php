<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{Benchmark,MutualFund,Exchange,BenchmarkScheme};
use Validator;
use Excel;
use App\Imports\BenchmarkImport;
use Illuminate\Support\Carbon;

class BenchmarkController extends Controller
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
                $data=Benchmark::leftJoin('md_exchange','md_exchange.id','=','md_benchmark.ex_id')
                    ->leftJoin('md_category','md_category.id','=','md_benchmark.category_id')
                    ->leftJoin('md_subcategory','md_subcategory.id','=','md_benchmark.subcat_id')
                    ->select('md_benchmark.*','md_exchange.ex_name as exchange_name','md_category.cat_name as category_name','md_subcategory.subcategory_name as subcategory_name')
                    ->where('md_benchmark.delete_flag','N')
                    ->orderByRaw($rawOrderBy)
                    ->paginate($paginate);
               
            }else {
                $data=Benchmark::leftJoin('md_exchange','md_exchange.id','=','md_benchmark.ex_id')
                    ->leftJoin('md_category','md_category.id','=','md_benchmark.category_id')
                    ->leftJoin('md_subcategory','md_subcategory.id','=','md_benchmark.subcat_id')
                    ->select('md_benchmark.*','md_exchange.ex_name as exchange_name','md_category.cat_name as category_name','md_subcategory.subcategory_name as subcategory_name')
                    ->where('md_benchmark.delete_flag','N')
                    ->orderBy('md_benchmark.created_at','desc')
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
            $ex_id=$request->ex_id;

            if ($paginate=='A') {
                $paginate=999999999;
            }
            if ($search) {
                $data=Benchmark::where('benchmark','like', '%' . $search . '%')->get();      
            }else if ($category_id && $subcategory_id) {
                $data=Benchmark::where('category_id',$category_id)->where('subcat_id',$subcategory_id)->get();      
            }else if ($id) {
                $data=Benchmark::where('id',$id)->get();      
            }else if ($ex_id) {
                $data=Benchmark::leftjoin('md_exchange','md_exchange.id','=','md_benchmark.ex_id')
                    ->select('md_benchmark.*','md_exchange.ex_name as ex_name')
                    ->where('md_benchmark.ex_id',$ex_id)
                    ->groupBy('md_benchmark.benchmark')
                    ->get();      
            }else {
                $data=Benchmark::groupBy('md_benchmark.benchmark')->get();      
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
            'base_date' =>'required',
            'base_value' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            // return $request;
            $subcat_id=json_decode($request->subcat_id);
            if ($request->id > 0) {
                $data=Benchmark::find($request->id);
                $data->ex_id=$request->ex_id;
                $data->benchmark=$request->benchmark;
                $data->category_id=$request->category_id;
                $data->subcat_id=$subcat_id[0];
                $data->launch_date=$request->launch_date;
                $data->base_date=$request->base_date;
                $data->base_value=$request->base_value;
                $data->save();
            }else{
                // $is_has=Benchmark::where('benchmark',$request->benchmark)
                //     ->where('ex_id',$request->ex_id)
                //     ->where('category_id',$request->category_id)
                //     ->where('subcat_id',$request->subcat_id)
                //     ->where('delete_flag','N')
                //     ->get();
                // // return $is_has;
                // if (count($is_has) > 0) {
                //     return Helper::WarningResponse(parent::ALREADY_EXIST);
                // }else {
                
                foreach ($subcat_id as $key => $value) {
                    // return $value;
                    $data=Benchmark::create(array(
                        'ex_id'=>$request->ex_id,
                        'benchmark'=>$request->benchmark,
                        'category_id'=>$request->category_id,
                        'subcat_id'=>$value,
                        'launch_date'=>$request->launch_date,
                        'base_date'=>$request->base_date,
                        'base_value'=>$request->base_value,
                        // 'created_by'=>'',
                    )); 
                }
                    
                // }
            } 
            
            $mydata=Benchmark::leftJoin('md_exchange','md_exchange.id','=','md_benchmark.ex_id')
                ->leftJoin('md_category','md_category.id','=','md_benchmark.category_id')
                ->leftJoin('md_subcategory','md_subcategory.id','=','md_benchmark.subcat_id')
                ->select('md_benchmark.*','md_exchange.ex_name as exchange_name','md_category.cat_name as category_name','md_subcategory.subcategory_name as subcategory_name')
                ->where('md_benchmark.delete_flag','N')
                ->where('md_benchmark.id',$data->id)
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
            // return $id;
            $is_has=BenchmarkScheme::where('Benchmark',$id)->get();
            if (count($is_has)>0) {
                return Helper::WarningResponse(parent::DELETE_NOT_ALLOW_ERROR);
            }else {
                $data=Benchmark::find($id);
                $data->delete_flag='Y';
                $data->delete_date=date('Y-m-d H:i:s');
                $data->delete_by=1;
                $data->save();
            }
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DELETE_FAIL_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function import(Request $request)
    {
        try {
            // return $request;
            // $path = $request->file('file')->getRealPath();
            // $data = array_map('str_getcsv', file($path));
            // $path = $request->file('file');
            // $data = array_map(function($v){return str_getcsv($v, ";");}, file($path));

            // return $data;

            $datas = Excel::toArray([],  $request->file('file'));
            // return $datas;
            $data=$datas[0];

            foreach ($data as $key => $value) {
                if ($key==0) {
                    if ($value[0]=="Exchange" && $value[1]=="Benchmark" && str_replace(" ","_",$value[2])!="Launch_Date" && str_replace(" ","_",$value[3])!="Base_Value" && str_replace(" ","_",$value[4])=="Category" && str_replace(" ","_",$value[5])!="Sub_Category") {
                        return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
                    }
                    // return $value;
                }else {
                    return $value;
                    // return $value[0];

                    return Carbon::parse($value[2])->format('Y-m-d');;
                    
                    $ex_id=Exchange::where('ex_name',$value[0])->value('id');
                    $category_id=Exchange::where('',$value[0])->value('id');
                    $data=Benchmark::create(array(
                        'ex_id'=>$ex_id,
                        'benchmark'=>$request->benchmark,
                        'category_id'=>$category_id,
                        'subcat_id'=>$value,
                        'launch_date'=>$request->launch_date,
                        'base_value'=>$request->base_value,
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
            throw $th;
            return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
        }
        return Helper::SuccessResponse($data1);
    }
}
