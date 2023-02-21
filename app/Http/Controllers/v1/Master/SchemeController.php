<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\Scheme;
use Validator;
use Excel;
use App\Imports\SchemeImport;

class SchemeController extends Controller
{
    public function searchDetails(Request $request)
    {
        try {
            $paginate=$request->paginate;
            $cat_name=$request->cat_name;
            $data=Scheme::join('md_amc','md_amc.id','=','md_scheme.amc_id')
                    ->join('md_category','md_category.id','=','md_scheme.category_id')
                    ->join('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                    ->join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                    ->select('md_scheme.*','md_amc.amc_name as amc_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcate_name','md_rnt.rnt_name as rnt_name')
                    // ->where('md_scheme.id',$scheme_id)
                    ->paginate($paginate);      
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
    public function export(Request $request)
    {
        try {
            $data=Scheme::join('md_amc','md_amc.id','=','md_scheme.amc_id')
                    ->join('md_category','md_category.id','=','md_scheme.category_id')
                    ->join('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                    ->join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                    ->select('md_scheme.*','md_amc.amc_name as amc_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcate_name','md_rnt.rnt_name as rnt_name')
                    // ->where('md_scheme.id',$scheme_id)
                    ->get();         
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
            $product_id=$request->product_id;
            $category_id=$request->category_id;
            $subcategory_id=$request->subcategory_id;
            $id=$request->id;
            $scheme_id=$request->scheme_id;
            $scheme_type=$request->scheme_type;
            $paginate=$request->paginate;
            $amc_id=$request->amc_id;
            if ($paginate=='A') {
                $paginate=999999999;
            }
            if ($search!='' && $amc_id!='' && $scheme_type!='') {
                $data=Scheme::where('scheme_type',$scheme_type)
                    ->where('amc_id',$amc_id)
                    ->where('scheme_name','like', '%' . $search . '%')
                    ->get();      
            } else if ($search!='' && $amc_id!='') {
                $data=Scheme::where('amc_id',$amc_id)
                    ->orWhere('scheme_name','like', '%' . $search . '%')
                    ->get();      
            }else if ($search!='' && $scheme_type!='') {
                // return "hii";
                $data=Scheme::where('scheme_type',$scheme_type)
                    ->where('scheme_name','like', '%' . $search . '%')
                    ->get();      
            }else if ($search!='') {
                $data=Scheme::where('scheme_name','like', '%' . $search . '%')->get();      
            }else if ($product_id!='' && $category_id!='' && $subcategory_id!='') {
                $data=Scheme::where('product_id',$product_id)
                    ->where('category_id',$category_id)
                    ->where('subcategory_id',$subcategory_id)
                    ->get();      
            }elseif ($scheme_id!='') {
                $data=Scheme::join('md_amc','md_amc.id','=','md_scheme.amc_id')
                    ->join('md_category','md_category.id','=','md_scheme.category_id')
                    ->join('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                    ->join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                    ->select('md_scheme.*','md_amc.amc_name as amc_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcate_name','md_rnt.rnt_name as rnt_name')
                    ->where('md_scheme.id',$scheme_id)
                    ->get();      
                // $data=Scheme::where('id',$scheme_id)->get();      
            }elseif ($id!='') {
                $data=Scheme::where('id',$id)->get();      
            }elseif ($scheme_type!='') {
                $data=Scheme::where('scheme_type',$scheme_type)->whereDate('updated_at',date('Y-m-d'))->get();      
            } elseif ($paginate!='') {
                $data=Scheme::paginate($paginate);      
            } else {
                $data=Scheme::whereDate('updated_at',date('Y-m-d'))->get();      
            }
            // $data=Scheme::whereDate('updated_at',date('Y-m-d'))->get();      
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function createUpdate(Request $request)
    {
        $validator = Validator::make(request()->all(),[
            'product_id' =>'required',
            'amc_id' =>'required',
            'category_id' =>'required',
            'subcategory_id' =>'required',
            'scheme_name' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        // try {
            // return $request->sip_date;
            // return json_decode($request->sip_date);
            // 'sip_date'=>json_encode($request->sip_date),
            if ($request->id > 0) {
                $data=Scheme::find($request->id);
                if ($request->scheme_type=='N') {
                    $data->nfo_start_dt=date('Y-m-d',strtotime($request->nfo_start_dt));
                    $data->nfo_end_dt=date('Y-m-d',strtotime($request->nfo_end_dt));
                    $data->nfo_reopen_dt=date('Y-m-d',strtotime($request->nfo_reopen_dt));
                    $data->nfo_entry_date=date('Y-m-d',strtotime($request->nfo_entry_date));
                }  
                $data->product_id=$request->product_id;
                $data->amc_id=$request->amc_id;
                $data->category_id=$request->category_id;
                $data->subcategory_id=$request->subcategory_id;
                $data->scheme_name=$request->scheme_name;
                $data->pip_fresh_min_amt=$request->pip_fresh_min_amt;
                $data->sip_fresh_min_amt=$request->sip_fresh_min_amt;
                $data->pip_add_min_amt=$request->pip_add_min_amt;
                $data->sip_add_min_amt=$request->sip_add_min_amt;
                $data->sip_date=$request->sip_date;
                $data->save();
            }else{
                if ($request->scheme_type=='O') {
                    $data=Scheme::create(array(
                        'product_id'=>$request->product_id,
                        'amc_id'=>$request->amc_id,
                        'category_id'=>$request->category_id,
                        'subcategory_id'=>$request->subcategory_id,
                        'scheme_name'=>$request->scheme_name,
                        'scheme_type'=>$request->scheme_type,
                        // 'nfo_start_dt'=>$request->nfo_start_dt,
                        // 'nfo_end_dt'=>$request->nfo_end_dt,
                        // 'nfo_reopen_dt'=>$request->nfo_end_dt,
                        'pip_fresh_min_amt'=>$request->pip_fresh_min_amt,
                        'sip_fresh_min_amt'=>$request->sip_fresh_min_amt,
                        'pip_add_min_amt'=>$request->pip_add_min_amt,
                        'sip_add_min_amt'=>$request->sip_add_min_amt,
                        'sip_date'=>$request->sip_date,
                        // 'created_by'=>'',
                    ));    
                }elseif ($request->scheme_type=='N') {
                    $data=Scheme::create(array(
                        'product_id'=>$request->product_id,
                        'amc_id'=>$request->amc_id,
                        'category_id'=>$request->category_id,
                        'subcategory_id'=>$request->subcategory_id,
                        'scheme_name'=>$request->scheme_name,
                        'scheme_type'=>$request->scheme_type,
                        'nfo_start_dt'=>date('Y-m-d',strtotime($request->nfo_start_dt)),
                        'nfo_end_dt'=>date('Y-m-d',strtotime($request->nfo_end_dt)),
                        'nfo_reopen_dt'=>date('Y-m-d',strtotime($request->nfo_reopen_dt)),
                        'nfo_entry_date'=>date('Y-m-d',strtotime($request->nfo_entry_date)),
                        'pip_fresh_min_amt'=>$request->pip_fresh_min_amt,
                        'sip_fresh_min_amt'=>$request->sip_fresh_min_amt,
                        'pip_add_min_amt'=>$request->pip_add_min_amt,
                        'sip_add_min_amt'=>$request->sip_add_min_amt,
                        'sip_date'=>$request->sip_date,
                        // 'created_by'=>'',
                    ));  
                }  
            }    
        // } catch (\Throwable $th) {
        //     //throw $th;
        //     return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        // }
        return Helper::SuccessResponse($data);
    }

    public function import(Request $request)
    {
        // try {
            // return $request;
            $path = $request->file('file')->getRealPath();
            $data = array_map('str_getcsv', file($path));
            // return $data[0][0];
            // return gettype($data[0][0]) ;
            // if (in_array("rnt_id", $data)) {
            // if ($data[0][0] == "rnt_id" && $data[0][1] == "product_id" && $data[0][2] == "amc_name" && $data[0][3] == "website" && $data[0][4] == "ofc_addr") {
            //     return "hii";
                Excel::import(new SchemeImport,$request->file);
                // Excel::import(new SchemeImport,request()->file('file'));
                $data1=[];
            // }else {
            //     return "else";
            //     return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
            // }
        // } catch (\Throwable $th) {
        //     //throw $th;
        //     return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
        // }
        return Helper::SuccessResponse($data1);
    }
}
