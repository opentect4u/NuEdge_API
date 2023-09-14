<?php

namespace App\Http\Controllers\v1\Operation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{Benchmark,MutualFund,BenchmarkScheme,Exchange};
use Validator;
use Excel;
use App\Imports\BenchmarkImport;
use Illuminate\Support\Carbon;
use DB;

class BenchmarkSchemeController extends Controller
{
    public function searchDetails(Request $request)
    {
        try {
            $paginate=$request->paginate;
            $benchmark=json_decode($request->benchmark);
            $order=$request->order;
            $field=$request->field;
            $date_range=$request->date_range;
            $periods=$request->periods;

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
                
                $my_data=BenchmarkScheme::leftJoin('md_exchange','md_exchange.id','=','td_benchmark_scheme.ex_id')
                    ->leftJoin('md_benchmark','md_benchmark.id','=','td_benchmark_scheme.benchmark')
                    ->select('td_benchmark_scheme.*','md_exchange.ex_name as exchange_name','md_benchmark.benchmark as benchmark')
                    ->orderByRaw($rawOrderBy)
                    ->paginate($paginate);
            }else {
                $rawQuery='';  
                if ($periods=='D') {
                    $from_date=Carbon::parse(str_replace('/','-',explode("-",$date_range)[0]))->format('Y-m-d') ;
                    $to_date=Carbon::parse(str_replace('/','-',explode("-",$date_range)[1]))->format('Y-m-d') ;  
                    $queryString='td_benchmark_scheme.date';
                    $rawQuery.=Helper::FrmToDateRawQuery($from_date,$to_date,$rawQuery,$queryString);
                }elseif ($periods=='M') {
                    $f_date="01-".str_replace('/','-',explode("-",$date_range)[0]);
                    $t_date=date('d')."-".str_replace('/','-',explode("-",$date_range)[1]);
                    $from_date=Carbon::parse(str_replace(' ','',$f_date))->format('Y-m-d');
                    $to_date=Carbon::parse(str_replace(' ','',$t_date))->format('Y-m-d');
                    // return  $t_date;
                    $queryString='r.date';
                    $rawQuery.=Helper::FrmToDateRawQuery($from_date,$to_date,$rawQuery,$queryString);
                }
                
                // return $rawQuery;
                $row_name_string=  "'" .implode("','", $benchmark). "'";

                $my_data=DB::select('SELECT r.*,e.ex_name,b.benchmark
                FROM td_benchmark_scheme AS r
                    LEFT JOIN md_exchange AS e ON r.ex_id=e.id
                    LEFT JOIN md_benchmark AS b ON r.benchmark=b.id
                    JOIN (
                        SELECT MAX(t.date) AS mydate
                        FROM td_benchmark_scheme AS t
                        GROUP BY YEAR(t.date), MONTH(t.date)
                    ) AS x ON r.date=x.mydate
                    where r.benchmark IN ('.$row_name_string.') AND '.$rawQuery.' order BY r.date DESC');
                return $my_data;
                $my_data=BenchmarkScheme::leftJoin('md_exchange','md_exchange.id','=','td_benchmark_scheme.ex_id')
                    ->leftJoin('md_benchmark','md_benchmark.id','=','td_benchmark_scheme.benchmark')
                    ->select('td_benchmark_scheme.*','md_exchange.ex_name as exchange_name','md_benchmark.benchmark as benchmark')
                    // ->selectRaw('DATE_FORMAT(td_benchmark_scheme.date, "%b %e") AS Week ')
                    // ->selectRaw('WEEK(td_benchmark_scheme.date) AS Week')
                    // ->orderBy('td_benchmark_scheme.date','desc')
                    ->selectRaw('YEAR(td_benchmark_scheme.date) AS Year')
                    ->selectRaw('MONTH(td_benchmark_scheme.date) AS Month')
                    // ->selectRaw('MAX(td_benchmark_scheme.date) AS MAX_date')
                    ->whereIn('td_benchmark_scheme.benchmark',$benchmark)
                    // ->whereRaw('td_benchmark_scheme.date=MAX_date')
                    ->whereRaw($rawQuery)
                    ->orderBy('td_benchmark_scheme.date','desc')
                    // ->groupByRaw('Week')
                    // ->groupByRaw('Year')
                    // ->groupByRaw('Month')
                    // ->orderBy('td_benchmark_scheme.date','desc')
                    // ->orderByRaw('MAX_date desc')
                    // ->paginate($paginate);
                    // ->take(100)
                    ->get();

                // $my_data=BenchmarkScheme::leftJoin('md_exchange','md_exchange.id','=','td_benchmark_scheme.ex_id')
                //     ->leftJoin('md_benchmark','md_benchmark.id','=','td_benchmark_scheme.benchmark')
                //     ->select('td_benchmark_scheme.*','md_exchange.ex_name as exchange_name','md_benchmark.benchmark as benchmark')
                //     ->selectRaw('YEAR(td_benchmark_scheme.date) AS Year')
                //     ->selectRaw('MONTH(td_benchmark_scheme.date) AS Month')
                //     ->whereIn('td_benchmark_scheme.benchmark',$benchmark)
                //     ->whereRaw($rawQuery)
                //     ->orderBy('td_benchmark_scheme.date','desc')
                //     ->get();
            }
            // return $my_data;
            $data=[];
            foreach ($my_data as $key => $value) {
                // return $value;
                // return $key;
                $close_price=$value->close;
                $old_close_price=0;
                $change_price=0;
                $change_percentage_format=0.00;
                if (isset($my_data[$key+1]['close']) && $my_data[$key+1]['close']) {
                    $old_close_price=$my_data[$key+1]['close'];
                    $change_price=$close_price-$old_close_price;
                    $change_percentage=(($change_price/$old_close_price)*100);
                    $change_percentage_format=number_format((float)round($change_percentage, 0, PHP_ROUND_HALF_UP), 2, '.', '');
                }
                $value->change_price=number_format((float)$change_price, 2, '.', '');
                $value->change_percentage=$change_percentage_format;
                array_push($data,$value);
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
            // $path = $request->file('file')->getRealPath();
            // $data = array_map('str_getcsv', file($path));
            // return $data[0][0];

            $datas = Excel::toArray([],  $request->file('file'));
            // return $datas;
            $data=$datas[0];
            // return count($data);
            // return $data[0];


            $start_count=$request->start_count;
            $end_count=$request->end_count;
            if ($end_count==count($data) || $end_count >= count($data)) {
                $end_count=count($data)-1;
            }

            if ($data[0][0]!="Benchmark" && $data[0][1]!="Date" && $data[0][2]!="Open" && $data[0][3]!="High") {
                return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
            }else {
                // return $data[1];
                for ($i=$start_count; $i <= $end_count; $i++) { 
                    // return $data[$i];
                    $benchmark=Benchmark::where('benchmark',$data[$i][0])->first();
                    // return $benchmark;
                    $benchmark_id=$benchmark->id;
                    $ex_id=$benchmark->ex_id;
                    $date=date('Y-m-d',strtotime(str_replace('/','-',$data[$i][1])));
                    // return $date;
                    $is_has=BenchmarkScheme::where('benchmark',$benchmark_id)
                        ->where('ex_id',$ex_id)
                        ->where('date',$date)
                        ->where('delete_flag','N')
                        ->get();

                    if (count($is_has) > 0) {
                        // return $is_has[0]->id;
                        $up_data=BenchmarkScheme::find($is_has[0]->id);
                        $up_data->date=$date;
                        $up_data->open=isset($data[$i][2])?$data[$i][2]:0;
                        $up_data->high=isset($data[$i][3])?$data[$i][3]:0;
                        $up_data->low=isset($data[$i][4])?$data[$i][4]:0;
                        $up_data->close=isset($data[$i][5])?$data[$i][5]:0;
                        $up_data->save();
                    } else {
                        BenchmarkScheme::create(array(
                            'ex_id'=>$ex_id,
                            'benchmark'=>$benchmark_id,
                            'date'=>$date,
                            'open'=>isset($data[$i][2])?$data[$i][2]:0,
                            'high'=>isset($data[$i][3])?$data[$i][3]:0,
                            'low'=>isset($data[$i][4])?$data[$i][4]:0,
                            'close'=>isset($data[$i][5])?$data[$i][5]:0,
                            // 'created_by'=>'',
                        ));   
                    } 
                }
            }
            
            $scc_res=[
                'start_count'=>$start_count,
                'end_count'=>$end_count,
                'total_count'=>count($data),
            ];
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
        }
        return Helper::SuccessResponse($scc_res);
    }

    public function import_old(Request $request)
    {
        try {
            // return $request;
            // $path = $request->file('file')->getRealPath();
            // $data = array_map('str_getcsv', file($path));
            // return $data[0][0];

            $datas = Excel::toArray([],  $request->file('file'));
            // return $datas;
            $data=$datas[0];
            return count($data);
            foreach ($data as $key => $value) {
                if ($key==0) {
                    // return $value;
                    if ($value[0]!="Benchmark" && $value[1]!="Date") {
                        return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
                    }
                    // return $value;
                }else {
                    // return $value;
                    // return $value[0];
                    // $ex_id=Exchange::where('ex_name',$value[0])->value('id');
                    $benchmark=Benchmark::where('benchmark',$value[0])->first();
                    // return $benchmark;
                    $benchmark_id=$benchmark->id;
                    $ex_id=$benchmark->ex_id;
                    $date=date('Y-m-d',strtotime(str_replace('/','-',$value[1])));
                    // return $date;
                    $is_has=BenchmarkScheme::where('benchmark',$benchmark_id)
                        ->where('ex_id',$ex_id)
                        ->where('date',$date)
                        ->where('delete_flag','N')
                        ->get();

                    if (count($is_has) > 0) {
                        // return $is_has[0]->id;
                        $up_data=BenchmarkScheme::find($is_has[0]->id);
                        $up_data->date=$date;
                        $up_data->open=isset($value[2])?$value[2]:0;
                        $up_data->high=isset($value[3])?$value[3]:0;
                        $up_data->low=isset($value[4])?$value[4]:0;
                        $up_data->close=isset($value[5])?$value[5]:0;
                        $up_data->save();
                    } else {
                        BenchmarkScheme::create(array(
                            'ex_id'=>$ex_id,
                            'benchmark'=>$benchmark_id,
                            'date'=>$date,
                            'open'=>isset($value[2])?$value[2]:0,
                            'high'=>isset($value[3])?$value[3]:0,
                            'low'=>isset($value[4])?$value[4]:0,
                            'close'=>isset($value[5])?$value[5]:0,
                            // 'created_by'=>'',
                        ));   
                    } 
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

