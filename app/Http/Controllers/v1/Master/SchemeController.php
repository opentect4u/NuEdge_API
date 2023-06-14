<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{Scheme,MutualFund,FormReceived,AMC,Category,SubCategory,SchemeOtherForm};
use Validator;
use Excel;
use App\Imports\SchemeImport;

class SchemeController extends Controller
{
    public function searchDetails(Request $request)
    {
        try {
            $paginate=$request->paginate;
            $scheme_name=$request->scheme_name;
            $amc_name=$request->amc_name;
            $cat_id=$request->cat_id;
            $subcat_id=$request->subcat_id;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;

            $scheme_type=$request->scheme_type;
            // return $request;
            if ($paginate=='A') {
                $paginate=999999999;
            }
            if ($sort_by && $column_name) {
                // return $request;

                // if ($column_name=='scheme_name' || $column_name=='scheme_type') {
                //     $data=Scheme::join('md_amc','md_amc.id','=','md_scheme.amc_id')
                //     ->join('md_category','md_category.id','=','md_scheme.category_id')
                //     ->join('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                //     ->join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                //     ->select('md_scheme.*','md_amc.amc_name as amc_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcate_name','md_rnt.rnt_name as rnt_name')
                //     ->where('md_scheme.delete_flag','N')
                //     ->where('md_scheme.scheme_type',$scheme_type)
                //     // ->orWhere('md_scheme.scheme_name','like', '%' . $scheme_name . '%')
                //     // ->orWhere('md_amc.amc_name','like', '%' . $amc_name . '%')
                //     ->orderBy('md_scheme.'.$column_name,$sort_by)
                //     ->paginate($paginate);
                // }elseif ($column_name=='cat_name') {
                //     $data=Scheme::join('md_amc','md_amc.id','=','md_scheme.amc_id')
                //     ->join('md_category','md_category.id','=','md_scheme.category_id')
                //     ->join('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                //     ->join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                //     ->select('md_scheme.*','md_amc.amc_name as amc_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcate_name','md_rnt.rnt_name as rnt_name')
                //     ->where('md_scheme.delete_flag','N')
                //     ->where('md_scheme.scheme_type',$scheme_type)
                //     // ->orWhere('md_scheme.scheme_name','like', '%' . $scheme_name . '%')
                //     // ->orWhere('md_amc.amc_name','like', '%' . $amc_name . '%')
                //     ->orderBy('md_category.'.$column_name,$sort_by)
                //     ->paginate($paginate);
                // }elseif ($column_name=='subcate_name') {
                //     $data=Scheme::join('md_amc','md_amc.id','=','md_scheme.amc_id')
                //     ->join('md_category','md_category.id','=','md_scheme.category_id')
                //     ->join('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                //     ->join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                //     ->select('md_scheme.*','md_amc.amc_name as amc_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcate_name','md_rnt.rnt_name as rnt_name')
                //     ->where('md_scheme.delete_flag','N')
                //     ->where('md_scheme.scheme_type',$scheme_type)
                //     // ->orWhere('md_scheme.scheme_name','like', '%' . $scheme_name . '%')
                //     // ->orWhere('md_amc.amc_name','like', '%' . $amc_name . '%')
                //     ->orderBy('md_subcategory.'.$column_name,$sort_by)
                //     ->paginate($paginate);
                // }elseif ($column_name=='amc_name') {
                //     $data=Scheme::join('md_amc','md_amc.id','=','md_scheme.amc_id')
                //     ->join('md_category','md_category.id','=','md_scheme.category_id')
                //     ->join('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                //     ->join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                //     ->select('md_scheme.*','md_amc.amc_name as amc_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcate_name','md_rnt.rnt_name as rnt_name')
                //     ->where('md_scheme.delete_flag','N')
                //     ->where('md_scheme.scheme_type',$scheme_type)
                //     // ->orWhere('md_scheme.scheme_name','like', '%' . $scheme_name . '%')
                //     // ->orWhere('md_amc.amc_name','like', '%' . $amc_name . '%')
                //     ->orderBy('md_amc.'.$column_name,$sort_by)
                //     ->paginate($paginate);
                // }
                if ($scheme_name || $amc_name) {
                    $rawQuery='';
                    if ($scheme_name) {
                        if (strlen($rawQuery) > 0) {
                            $rawQuery.=" AND scheme_name LIKE '%".$scheme_name."%'";
                        }else {
                            $rawQuery.=" scheme_name LIKE '%".$scheme_name."%'";
                        }
                    }
                    if ($amc_name) {
                        if (strlen($rawQuery) > 0) {
                            $rawQuery.=" AND amc_name LIKE '%".$amc_name."%'";
                        }else {
                            $rawQuery.=" amc_name LIKE '%".$amc_name."%'";
                        }
                    }

                    $data=Scheme::join('md_amc','md_amc.id','=','md_scheme.amc_id')
                        ->join('md_category','md_category.id','=','md_scheme.category_id')
                        ->join('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                        ->join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                        ->select('md_scheme.*','md_amc.amc_name as amc_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcate_name','md_rnt.rnt_name as rnt_name')
                        ->where('md_scheme.delete_flag','N')
                        ->where('md_scheme.scheme_type',$scheme_type)
                        ->whereRaw($rawQuery)
                        ->orderByRaw($column_name,$sort_by)
                        ->paginate($paginate);
                }else {
                    $data=Scheme::join('md_amc','md_amc.id','=','md_scheme.amc_id')
                        ->join('md_category','md_category.id','=','md_scheme.category_id')
                        ->join('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                        ->join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                        ->select('md_scheme.*','md_amc.amc_name as amc_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcate_name','md_rnt.rnt_name as rnt_name')
                        ->where('md_scheme.delete_flag','N')
                        ->where('md_scheme.scheme_type',$scheme_type)
                        ->orderByRaw($column_name,$sort_by)
                        ->paginate($paginate);
                }
            }elseif($scheme_name && $amc_name){
                $data=Scheme::join('md_amc','md_amc.id','=','md_scheme.amc_id')
                    ->join('md_category','md_category.id','=','md_scheme.category_id')
                    ->join('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                    ->join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                    ->select('md_scheme.*','md_amc.amc_name as amc_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcate_name','md_rnt.rnt_name as rnt_name')
                    ->where('md_scheme.delete_flag','N')
                    ->where('md_scheme.scheme_type',$scheme_type)
                    ->where('md_scheme.scheme_name','like', '%' . $scheme_name . '%')
                    ->where('md_amc.amc_name','like', '%' . $amc_name . '%')
                    ->paginate($paginate);
            }elseif ($scheme_name) {
                $data=Scheme::join('md_amc','md_amc.id','=','md_scheme.amc_id')
                    ->join('md_category','md_category.id','=','md_scheme.category_id')
                    ->join('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                    ->join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                    ->select('md_scheme.*','md_amc.amc_name as amc_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcate_name','md_rnt.rnt_name as rnt_name')
                    ->where('md_scheme.delete_flag','N')
                    ->where('md_scheme.scheme_type',$scheme_type)
                    ->where('md_scheme.scheme_name','like', '%' . $scheme_name . '%')
                    ->paginate($paginate);  
            }elseif ($amc_name) {
                $data=Scheme::join('md_amc','md_amc.id','=','md_scheme.amc_id')
                    ->join('md_category','md_category.id','=','md_scheme.category_id')
                    ->join('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                    ->join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                    ->select('md_scheme.*','md_amc.amc_name as amc_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcate_name','md_rnt.rnt_name as rnt_name')
                    ->where('md_scheme.delete_flag','N')
                    ->where('md_scheme.scheme_type',$scheme_type)
                    ->where('md_amc.amc_name','like', '%' . $amc_name . '%')
                    ->paginate($paginate);  
            } else {
                $data=Scheme::join('md_amc','md_amc.id','=','md_scheme.amc_id')
                    ->join('md_category','md_category.id','=','md_scheme.category_id')
                    ->join('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                    ->join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                    ->select('md_scheme.*','md_amc.amc_name as amc_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcate_name','md_rnt.rnt_name as rnt_name')
                    ->where('md_scheme.delete_flag','N')
                    ->where('md_scheme.scheme_type',$scheme_type)
                    ->paginate($paginate);  
            }
            // $mydata=$data;
            // // return $mydata;
            // $alldatas=[];
            // foreach($data as $mydata){
            //     $sip_freq_wise_amt=json_decode($mydata->sip_freq_wise_amt);
            //     if ($sip_freq_wise_amt[0]->id=='D' && $sip_freq_wise_amt[0]->is_checked==true) {
            //         array_push($alldatas,$mydata);
            //     }
            // }
            // return $alldatas;
            

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
            $scheme_name=$request->scheme_name;
            $amc_name=$request->amc_name;
            $cat_id=$request->cat_id;
            $subcat_id=$request->subcat_id;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;

            $scheme_type=$request->scheme_type;
            // return $request;
            
            if ($sort_by && $column_name) {
                // return $request;
                // if ($column_name=='scheme_name' || $column_name=='scheme_type') {
                //     $data=Scheme::join('md_amc','md_amc.id','=','md_scheme.amc_id')
                //     ->join('md_category','md_category.id','=','md_scheme.category_id')
                //     ->join('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                //     ->join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                //     ->select('md_scheme.*','md_amc.amc_name as amc_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcate_name','md_rnt.rnt_name as rnt_name')
                //     ->where('md_scheme.delete_flag','N')
                //     ->where('md_scheme.scheme_type',$scheme_type)
                //     // ->orWhere('md_scheme.scheme_name','like', '%' . $scheme_name . '%')
                //     // ->orWhere('md_amc.amc_name','like', '%' . $amc_name . '%')
                //     ->orderBy('md_scheme.'.$column_name,$sort_by)
                //     ->get();
                // }elseif ($column_name=='cat_name') {
                //     $data=Scheme::join('md_amc','md_amc.id','=','md_scheme.amc_id')
                //     ->join('md_category','md_category.id','=','md_scheme.category_id')
                //     ->join('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                //     ->join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                //     ->select('md_scheme.*','md_amc.amc_name as amc_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcate_name','md_rnt.rnt_name as rnt_name')
                //     ->where('md_scheme.delete_flag','N')
                //     ->where('md_scheme.scheme_type',$scheme_type)
                //     // ->orWhere('md_scheme.scheme_name','like', '%' . $scheme_name . '%')
                //     // ->orWhere('md_amc.amc_name','like', '%' . $amc_name . '%')
                //     ->orderBy('md_category.'.$column_name,$sort_by)
                //     ->get();
                // }elseif ($column_name=='subcate_name') {
                //     $data=Scheme::join('md_amc','md_amc.id','=','md_scheme.amc_id')
                //     ->join('md_category','md_category.id','=','md_scheme.category_id')
                //     ->join('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                //     ->join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                //     ->select('md_scheme.*','md_amc.amc_name as amc_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcate_name','md_rnt.rnt_name as rnt_name')
                //     ->where('md_scheme.delete_flag','N')
                //     ->where('md_scheme.scheme_type',$scheme_type)
                //     // ->orWhere('md_scheme.scheme_name','like', '%' . $scheme_name . '%')
                //     // ->orWhere('md_amc.amc_name','like', '%' . $amc_name . '%')
                //     ->orderBy('md_subcategory.'.$column_name,$sort_by)
                //     ->get();
                // }elseif ($column_name=='amc_name') {
                //     $data=Scheme::join('md_amc','md_amc.id','=','md_scheme.amc_id')
                //     ->join('md_category','md_category.id','=','md_scheme.category_id')
                //     ->join('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                //     ->join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                //     ->select('md_scheme.*','md_amc.amc_name as amc_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcate_name','md_rnt.rnt_name as rnt_name')
                //     ->where('md_scheme.delete_flag','N')
                //     ->where('md_scheme.scheme_type',$scheme_type)
                //     // ->orWhere('md_scheme.scheme_name','like', '%' . $scheme_name . '%')
                //     // ->orWhere('md_amc.amc_name','like', '%' . $amc_name . '%')
                //     ->orderBy('md_amc.'.$column_name,$sort_by)
                //     ->get();
                // }

                if ($scheme_name || $amc_name) {
                    $rawQuery='';
                    if ($scheme_name) {
                        if (strlen($rawQuery) > 0) {
                            $rawQuery.=" AND scheme_name LIKE '%".$scheme_name."%'";
                        }else {
                            $rawQuery.=" scheme_name LIKE '%".$scheme_name."%'";
                        }
                    }
                    if ($amc_name) {
                        if (strlen($rawQuery) > 0) {
                            $rawQuery.=" AND amc_name LIKE '%".$amc_name."%'";
                        }else {
                            $rawQuery.=" amc_name LIKE '%".$amc_name."%'";
                        }
                    }

                    $data=Scheme::join('md_amc','md_amc.id','=','md_scheme.amc_id')
                        ->join('md_category','md_category.id','=','md_scheme.category_id')
                        ->join('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                        ->join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                        ->select('md_scheme.*','md_amc.amc_name as amc_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcate_name','md_rnt.rnt_name as rnt_name')
                        ->where('md_scheme.delete_flag','N')
                        ->where('md_scheme.scheme_type',$scheme_type)
                        ->whereRaw($rawQuery)
                        ->orderByRaw($column_name,$sort_by)
                        ->paginate($paginate);
                }else {
                    $data=Scheme::join('md_amc','md_amc.id','=','md_scheme.amc_id')
                        ->join('md_category','md_category.id','=','md_scheme.category_id')
                        ->join('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                        ->join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                        ->select('md_scheme.*','md_amc.amc_name as amc_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcate_name','md_rnt.rnt_name as rnt_name')
                        ->where('md_scheme.delete_flag','N')
                        ->where('md_scheme.scheme_type',$scheme_type)
                        ->orderByRaw($column_name,$sort_by)
                        ->paginate($paginate);
                }
                
            }elseif($scheme_name && $amc_name){
                $data=Scheme::join('md_amc','md_amc.id','=','md_scheme.amc_id')
                    ->join('md_category','md_category.id','=','md_scheme.category_id')
                    ->join('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                    ->join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                    ->select('md_scheme.*','md_amc.amc_name as amc_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcate_name','md_rnt.rnt_name as rnt_name')
                    ->where('md_scheme.delete_flag','N')
                    ->where('md_scheme.scheme_type',$scheme_type)
                    ->where('md_scheme.scheme_name','like', '%' . $scheme_name . '%')
                    ->where('md_amc.amc_name','like', '%' . $amc_name . '%')
                    ->get();
            }elseif ($scheme_name) {
                $data=Scheme::join('md_amc','md_amc.id','=','md_scheme.amc_id')
                    ->join('md_category','md_category.id','=','md_scheme.category_id')
                    ->join('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                    ->join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                    ->select('md_scheme.*','md_amc.amc_name as amc_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcate_name','md_rnt.rnt_name as rnt_name')
                    ->where('md_scheme.delete_flag','N')
                    ->where('md_scheme.scheme_type',$scheme_type)
                    ->where('md_scheme.scheme_name','like', '%' . $scheme_name . '%')
                    ->get();  
            }elseif ($amc_name) {
                $data=Scheme::join('md_amc','md_amc.id','=','md_scheme.amc_id')
                    ->join('md_category','md_category.id','=','md_scheme.category_id')
                    ->join('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                    ->join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                    ->select('md_scheme.*','md_amc.amc_name as amc_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcate_name','md_rnt.rnt_name as rnt_name')
                    ->where('md_scheme.delete_flag','N')
                    ->where('md_scheme.scheme_type',$scheme_type)
                    ->where('md_amc.amc_name','like', '%' . $amc_name . '%')
                    ->get();  
            } else {
                $data=Scheme::join('md_amc','md_amc.id','=','md_scheme.amc_id')
                    ->join('md_category','md_category.id','=','md_scheme.category_id')
                    ->join('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                    ->join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                    ->select('md_scheme.*','md_amc.amc_name as amc_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcate_name','md_rnt.rnt_name as rnt_name')
                    ->where('md_scheme.delete_flag','N')
                    ->where('md_scheme.scheme_type',$scheme_type)
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
            $product_id=$request->product_id;
            $category_id=$request->category_id;
            $subcategory_id=$request->subcategory_id;
            $id=$request->id;
            $scheme_id=$request->scheme_id;
            $scheme_type=$request->scheme_type;
            $paginate=$request->paginate;
            $amc_id=$request->amc_id;
            $arr_amc_id=json_decode($request->arr_amc_id);
            if ($paginate=='A') {
                $paginate=999999999;
            }
            if ($search!='' && $amc_id!='' && $scheme_type!='') {
                $data=Scheme::where('scheme_type',$scheme_type)
                    ->where('delete_flag','N')
                    ->where('amc_id',$amc_id)
                    ->where('scheme_name','like', '%' . $search . '%')
                    ->get();      
            } else if ($search!='' && $amc_id!='') {
                $data=Scheme::where('amc_id',$amc_id)
                    ->where('delete_flag','N')
                    ->orWhere('scheme_name','like', '%' . $search . '%')
                    ->get();      
            }else if ($search!='' && $scheme_type!='') {
                // return "hii";
                $data=Scheme::where('scheme_type',$scheme_type)
                    ->where('delete_flag','N')
                    ->where('scheme_name','like', '%' . $search . '%')
                    ->get();      
            }else if ($search!='') {
                    $data=Scheme::where('delete_flag','N')
                        ->where('scheme_name','like', '%' . $search . '%')->get();      
            }else if ($product_id!='' && $category_id!='' && $subcategory_id!='') {
                $data=Scheme::where('product_id',$product_id)
                    ->where('delete_flag','N')
                    ->where('category_id',$category_id)
                    ->where('subcategory_id',$subcategory_id)
                    ->get();      
            }elseif ($scheme_id!='') {
                $data=Scheme::join('md_amc','md_amc.id','=','md_scheme.amc_id')
                    ->join('md_category','md_category.id','=','md_scheme.category_id')
                    ->join('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                    ->join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                    ->select('md_scheme.*','md_amc.amc_name as amc_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcate_name','md_rnt.rnt_name as rnt_name')
                    ->where('md_scheme.delete_flag','N')
                    ->where('md_scheme.id',$scheme_id)
                    ->get();      
                // $data=Scheme::where('id',$scheme_id)->get();      
            }elseif (!empty($arr_amc_id)) {
                $data=Scheme::where('delete_flag','N')->whereIn('amc_id',$arr_amc_id)->get();  
            }elseif ($amc_id!='') {
                $data=Scheme::where('delete_flag','N')->where('amc_id',$amc_id)->get();  
            }elseif ($id!='') {
                $data=Scheme::where('delete_flag','N')->where('id',$id)->get();      
            }elseif ($scheme_type!='') {
                $data=Scheme::where('delete_flag','N')->where('scheme_type',$scheme_type)->whereDate('updated_at',date('Y-m-d'))->get();      
            } elseif ($paginate!='') {
                $data=Scheme::where('delete_flag','N')->paginate($paginate);      
            } else {
                $data=Scheme::where('delete_flag','N')->get();      
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
        try {
            // return $request;
            // $request->swp_date
            // $request->stp_date
            if ($request->sip_date!='') {
                $sip_date=json_decode($request->sip_date);
                sort($sip_date);
                $sip_date=json_encode($sip_date);
            }
            if ($request->swp_date!='') {
                $swp_date=json_decode($request->swp_date);
                sort($swp_date);
                $swp_date=json_encode($swp_date);
            }
            if ($request->stp_date!='') {
                $stp_date=json_decode($request->stp_date);
                sort($stp_date);
                $stp_date=json_encode($stp_date);
            }
            // return $request->sip_date;
            // return json_decode($request->sip_date);
            // 'sip_date'=>json_encode($request->sip_date),
            if ($request->id > 0) {
                $data=Scheme::find($request->id);
                if ($request->scheme_type=='N') {
                    // return $request;
                        $doc_name_1='';
                        $nfo_one_pager=$request->nfo_one_pager;
                        if ($nfo_one_pager) {
                            $cv_path_extension=$nfo_one_pager->getClientOriginalExtension();
                            $doc_name_1=microtime(true).".".$cv_path_extension;
                            $nfo_one_pager->move(public_path('application-forms/'),$doc_name_1);

                            if($data->nfo_one_pager!=null){
                                $filenfo_one_pager = public_path('application-forms/') . $data->nfo_one_pager;
                                if (file_exists($filenfo_one_pager) != null) {
                                    unlink($filenfo_one_pager);
                                }
                            } 
                        }else {
                            $doc_name_1=$data->nfo_one_pager;
                        }
                        $doc_name_2='';
                        $nfo_kim=$request->nfo_kim;
                        if ($nfo_kim) {
                            $cv_path_extension=$nfo_kim->getClientOriginalExtension();
                            $doc_name_2=microtime(true).".".$cv_path_extension;
                            $nfo_kim->move(public_path('application-forms/'),$doc_name_2);

                            if($data->nfo_kim!=null){
                                $filenfo_kim = public_path('application-forms/') . $data->nfo_kim;
                                if (file_exists($filenfo_kim) != null) {
                                    unlink($filenfo_kim);
                                }
                            } 
                        }else {
                            $doc_name_2=$data->nfo_kim;
                        }
                        $doc_name_3='';
                        $nfo_ppt=$request->nfo_ppt;
                        if ($nfo_ppt) {
                            $cv_path_extension=$nfo_ppt->getClientOriginalExtension();
                            $doc_name_3=microtime(true).".".$cv_path_extension;
                            $nfo_ppt->move(public_path('application-forms/'),$doc_name_3);

                            if($data->nfo_ppt!=null){
                                $filecv = public_path('application-forms/') . $data->nfo_ppt;
                                if (file_exists($filecv) != null) {
                                    unlink($filecv);
                                }
                            } 
                        }else {
                            $doc_name_3=$data->nfo_ppt;
                        }
                        $doc_name_4='';
                        $nfo_common_app=$request->nfo_common_app;
                        if ($nfo_common_app) {
                            $cv_path_extension=$nfo_common_app->getClientOriginalExtension();
                            $doc_name_4=microtime(true).".".$cv_path_extension;
                            $nfo_common_app->move(public_path('application-forms/'),$doc_name_4);

                            if($data->nfo_common_app!=null){
                                $filecv = public_path('application-forms/') . $data->nfo_common_app;
                                if (file_exists($filecv) != null) {
                                    unlink($filecv);
                                }
                            } 
                        }else {
                            $doc_name_4=$data->nfo_common_app;
                        }
                        $doc_name_5='';
                        $sip_registration=$request->sip_registration;
                        if ($sip_registration) {
                            $cv_path_extension=$sip_registration->getClientOriginalExtension();
                            $doc_name_5=microtime(true).".".$cv_path_extension;
                            $sip_registration->move(public_path('application-forms/'),$doc_name_5);

                            if($data->sip_registration!=null){
                                $filecv = public_path('application-forms/') . $data->sip_registration;
                                if (file_exists($filecv) != null) {
                                    unlink($filecv);
                                }
                            } 
                        }else {
                            $doc_name_5=$data->sip_registration;
                        }
                        $doc_name_6='';
                        $swp_registration=$request->swp_registration;
                        if ($swp_registration) {
                            $cv_path_extension=$swp_registration->getClientOriginalExtension();
                            $doc_name_6=microtime(true).".".$cv_path_extension;
                            $swp_registration->move(public_path('application-forms/'),$doc_name_6);

                            if($data->swp_registration!=null){
                                $filecv = public_path('application-forms/') . $data->swp_registration;
                                if (file_exists($filecv) != null) {
                                    unlink($filecv);
                                }
                            } 
                        }else {
                            $doc_name_6=$data->swp_registration;
                        }
                        $doc_name_7='';
                        $stp_registration=$request->stp_registration;
                        if ($stp_registration) {
                            $cv_path_extension=$stp_registration->getClientOriginalExtension();
                            $doc_name_7=microtime(true).".".$cv_path_extension;
                            $stp_registration->move(public_path('application-forms/'),$doc_name_7);

                            if($data->stp_registration!=null){
                                $filecv = public_path('application-forms/') . $data->stp_registration;
                                if (file_exists($filecv) != null) {
                                    unlink($filecv);
                                }
                            } 
                        }else {
                            $doc_name_7=$data->stp_registration;
                        }
                    
                    $data->nfo_start_dt=date('Y-m-d',strtotime($request->nfo_start_dt));
                    $data->nfo_end_dt=date('Y-m-d',strtotime($request->nfo_end_dt));
                    $data->nfo_reopen_dt=date('Y-m-d',strtotime($request->nfo_reopen_dt));
                    $data->nfo_entry_date=date('Y-m-d',strtotime($request->nfo_entry_date));

                    $data->nfo_one_pager=$doc_name_1;
                    $data->nfo_kim=$doc_name_2;
                    $data->nfo_ppt=$doc_name_3;
                    $data->nfo_common_app=$doc_name_4;
                    $data->sip_registration=$doc_name_5;
                    $data->swp_registration=$doc_name_6;
                    $data->stp_registration=$doc_name_7;
                }  

                $data->ava_special_swp=$request->ava_special_swp;
                $data->special_swp_name=$request->special_swp_name;
                $data->ava_special_stp=$request->ava_special_stp;
                $data->special_stp_name=$request->special_stp_name;
                $data->step_up_min_amt=$request->step_up_min_amt;
                $data->step_up_min_per=$request->step_up_min_per;

                // $data->growth_isin=$request->growth_isin;
                // $data->idcw_payout_isin=$request->idcw_payout_isin;
                // $data->idcw_reinvestment_isin=$request->idcw_reinvestment_isin;

                $data->product_id=$request->product_id;
                $data->amc_id=$request->amc_id;
                $data->category_id=$request->category_id;
                $data->subcategory_id=$request->subcategory_id;
                $data->scheme_name=$request->scheme_name;
                $data->pip_fresh_min_amt=$request->pip_fresh_min_amt;
                // $data->sip_fresh_min_amt=$request->sip_fresh_min_amt;
                $data->pip_add_min_amt=$request->pip_add_min_amt;
                // $data->sip_add_min_amt=$request->sip_add_min_amt;
                $data->sip_freq_wise_amt=$request->frequency;
                $data->sip_date=$sip_date;
                $data->swp_freq_wise_amt=$request->swp_freq_wise_amt;
                $data->swp_date=$swp_date;
                $data->stp_freq_wise_amt=$request->stp_freq_wise_amt;
                $data->stp_date=$stp_date;
                $data->ava_special_sip=$request->ava_special_sip;
                $data->special_sip_name=$request->special_sip_name;
                $data->save();


                $doc_names='';
                $file=$request->form_upload;
                if (count($request->row_id)>0) {
                    foreach ($request->row_id as $key => $row_id) {
                        if ($row_id!=null) {
                            if ($row_id==0) {
                                if ($file[$key]) {
                                    $cv_path_extension=$file[$key]->getClientOriginalExtension();
                                    $doc_names=microtime(true).".".$cv_path_extension;
                                    $file[$key]->move(public_path('application-forms/'),$doc_names);
                                }
                                SchemeOtherForm::create(array(
                                    'scheme_id'=>$data->id,
                                    'form_name'=>$request->form_name[$key],
                                    'form_upload'=>$doc_names,
                                    // 'created_by'=>'',
                                ));      
                            } else {
                                if ($file[$key]) {
                                    $cv_path_extension=$file[$key]->getClientOriginalExtension();
                                    $doc_names=microtime(true).".".$cv_path_extension;
                                    $file[$key]->move(public_path('application-forms/'),$doc_names);
                                }
                                $data=SchemeOtherForm::find($row_id);
                                if($data->doc_names!=null){
                                    $filecv = public_path('application-forms/') . $data->doc_names;
                                    if (file_exists($filecv) != null) {
                                        unlink($filecv);
                                    }
                                } 
                                $data->form_name=$request->form_name[$key];
                                $data->form_upload=$doc_names;
                                $data->save();
                            }
                        }
                    }
                }
            }else{
                $is_has=Scheme::where('scheme_name',$request->scheme_name)->where('delete_flag','N')->get();
                if (count($is_has) > 0) {
                    return Helper::WarningResponse(parent::ALREADY_EXIST);
                }else {
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
                            'pip_add_min_amt'=>$request->pip_add_min_amt,
                            'sip_freq_wise_amt'=>$request->frequency,
                            'sip_date'=>$sip_date,
                            'swp_freq_wise_amt'=>$request->swp_freq_wise_amt,
                            'swp_date'=>$swp_date,
                            'stp_freq_wise_amt'=>$request->stp_freq_wise_amt,
                            'stp_date'=>$stp_date,
                            'ava_special_sip'=>$request->ava_special_sip,
                            'special_sip_name'=>$request->special_sip_name,
                            // 'created_by'=>'',
                        ));    
                    }elseif ($request->scheme_type=='N') {
                        $doc_name_1='';
                        $nfo_one_pager=$request->nfo_one_pager;
                        if ($nfo_one_pager) {
                            $cv_path_extension=$nfo_one_pager->getClientOriginalExtension();
                            $doc_name_1=microtime(true).".".$cv_path_extension;
                            $nfo_one_pager->move(public_path('application-forms/'),$doc_name_1);
                        }
                        $doc_name_2='';
                        $nfo_kim=$request->nfo_kim;
                        if ($nfo_kim) {
                            $cv_path_extension=$nfo_kim->getClientOriginalExtension();
                            $doc_name_2=microtime(true).".".$cv_path_extension;
                            $nfo_kim->move(public_path('application-forms/'),$doc_name_2);
                        }
                        $doc_name_3='';
                        $nfo_ppt=$request->nfo_ppt;
                        if ($nfo_ppt) {
                            $cv_path_extension=$nfo_ppt->getClientOriginalExtension();
                            $doc_name_3=microtime(true).".".$cv_path_extension;
                            $nfo_ppt->move(public_path('application-forms/'),$doc_name_3);
                        }
                        $doc_name_4='';
                        $nfo_common_app=$request->nfo_common_app;
                        if ($nfo_common_app) {
                            $cv_path_extension=$nfo_common_app->getClientOriginalExtension();
                            $doc_name_4=microtime(true).".".$cv_path_extension;
                            $nfo_common_app->move(public_path('application-forms/'),$doc_name_4);
                        }
                        $doc_name_5='';
                        $sip_registration=$request->sip_registration;
                        if ($sip_registration) {
                            $cv_path_extension=$sip_registration->getClientOriginalExtension();
                            $doc_name_5=microtime(true).".".$cv_path_extension;
                            $sip_registration->move(public_path('application-forms/'),$doc_name_5);
                        }
                        $doc_name_6='';
                        $swp_registration=$request->swp_registration;
                        if ($swp_registration) {
                            $cv_path_extension=$swp_registration->getClientOriginalExtension();
                            $doc_name_6=microtime(true).".".$cv_path_extension;
                            $swp_registration->move(public_path('application-forms/'),$doc_name_6);
                        }
                        $doc_name_7='';
                        $stp_registration=$request->stp_registration;
                        if ($stp_registration) {
                            $cv_path_extension=$stp_registration->getClientOriginalExtension();
                            $doc_name_7=microtime(true).".".$cv_path_extension;
                            $stp_registration->move(public_path('application-forms/'),$doc_name_7);
                        }
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
                            'pip_add_min_amt'=>$request->pip_add_min_amt,
                            'sip_freq_wise_amt'=>$request->frequency,
                            'sip_date'=>$sip_date,
                            'swp_freq_wise_amt'=>$request->swp_freq_wise_amt,
                            'swp_date'=>$swp_date,
                            'stp_freq_wise_amt'=>$request->stp_freq_wise_amt,
                            'stp_date'=>$stp_date,
                            'ava_special_sip'=>$request->ava_special_sip,
                            'special_sip_name'=>$request->special_sip_name,

                            'ava_special_swp'=>$request->ava_special_swp,
                            'special_swp_name'=>$request->special_swp_name,
                            'ava_special_stp'=>$request->ava_special_stp,
                            'special_stp_name'=>$request->special_stp_name,
                            'step_up_min_amt'=>$request->step_up_min_amt,
                            'step_up_min_per'=>$request->step_up_min_per,
                            'nfo_one_pager'=>$doc_name_1,
                            'nfo_kim'=>$doc_name_2,
                            'nfo_ppt'=>$doc_name_3,
                            'nfo_common_app'=>$doc_name_4,
                            'sip_registration'=>$doc_name_5,
                            'swp_registration'=>$doc_name_6,
                            'stp_registration'=>$doc_name_7,
                            // 'growth_isin'=>$request->growth_isin,
                            // 'idcw_payout_isin'=>$request->idcw_payout_isin,
                            // 'idcw_reinvestment_isin'=>$request->idcw_reinvestment_isin,
                            // 'created_by'=>'',
                        ));  

                        $doc_names='';
                        $files=$request->form_upload;
                        if ($files!='') {
                            foreach ($files as $key => $file) {
                                // return $file;
                                if ($file) {
                                    $cv_path_extension=$file->getClientOriginalExtension();
                                    $doc_names=microtime(true).$cv_path_extension;
                                    $file->move(public_path('application-forms/'),$doc_names);
                                }
                                SchemeOtherForm::create(array(
                                    'scheme_id'=>$data->id,
                                    'form_name'=>$request->form_name[$key],
                                    'form_upload'=>$doc_names,
                                    // 'created_by'=>'',
                                ));      
                            }
                        }
                    }  
                }
            }    
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function delete(Request $request)
    {
        try {
            $id=$request->id;
            $is_has=FormReceived::where('scheme_id',$id)->orWhere('scheme_id_to',$id)->get();
            if (count($is_has)>0) {
                return Helper::WarningResponse(parent::DELETE_NOT_ALLOW_ERROR);
            }else {
                $data=Scheme::find($id);
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
            $scheme_type=$request->scheme_type;
            $amc_id=$request->amc_id;
            $category_id=$request->category_id;
            $subcategory_id=$request->subcategory_id;
            $product_id=$request->product_id;
            // $path = $request->file('file')->getRealPath();
            // $data = array_map('str_getcsv', file($path));
            // return $data;
            $datas = Excel::toArray([],  $request->file('file'));
            // return $datas[0];
            $data=$datas[0];

            if ($scheme_type=='O') {
                foreach ($data as $key => $value) {
                    if ($key==0) {
                        if (str_replace(" ","_",$value[0])!="AMC_Short_Name"  && str_replace(" ","_",$value[1])!="Category_Name"  && str_replace(" ","_",$value[2])!="Sub_Category_Name"  && $value[3]!="Scheme" && str_replace(" ","_",$value[4])!="PIP_Fresh_Minimum_Amount" && str_replace(" ","_",$value[5])!="PIP_Additional_Minimum_Amount" && str_replace(" ","_",$value[6])!="SIP_Date") {
                            return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
                        }
                        // return $value;
                    }else {
                        // return $value;
                        
                        //==========================================SIP=========================
                        if ($value[21]) {
                            $ava_special_sip='true';
                        }else {
                            $ava_special_sip='false';
                        }
                        // return $ava_special_sip;
                        $sip_date=explode(',',$value[6]);
                        $sip_date_array=[];
                        foreach ($sip_date as $key => $value1) {
                            $sip_dd['id']=$value1;
                            $sip_dd['date']=$value1;
                            array_push($sip_date_array,$sip_dd);
                        }
                        // return $sip_date_array;
                        //==========================================SWP=========================
                        if ($value[30]) {
                            $ava_special_swp='true';
                        }else {
                            $ava_special_swp='false';
                        }
                        $swp_date=explode(',',$value[22]);
                        $swp_date_array=[];
                        foreach ($swp_date as $key => $value2) {
                            $swp_dd['id']=$value2;
                            $swp_dd['date']=$value2;
                            array_push($swp_date_array,$swp_dd);
                        }
                        // return $swp_date_array;
                        //==========================================STP=========================
                        if ($value[39]) {
                            $ava_special_stp='true';
                        }else {
                            $ava_special_stp='false';
                        }
                        $stp_date=explode(',',$value[31]);
                        $stp_date_array=[];
                        foreach ($stp_date as $key => $value3) {
                            $stp_dd['id']=$value3;
                            $stp_dd['date']=$value3;
                            array_push($stp_date_array,$stp_dd);
                        }
                        // return $stp_date_array;
                        // 7 - 20  SIP
                        // 21 - 27 SWP
                        // 28 - 34 STP
                        
                        $sip_freq_wise_amt=[];
                        $dd['id']="D";
                        $dd['freq_name']="Daily";
                        $dd['is_checked']=$this->freqWiseAmt($value[7], $value[8]);
                        $dd['sip_fresh_min_amt']=isset($value[7])?$value[7]:"";
                        $dd['sip_add_min_amt']=isset($value[8])?$value[8]:"";
                        array_push($sip_freq_wise_amt,$dd);
                        $ww['id']="W";
                        $ww['freq_name']="Weekly";
                        $ww['is_checked']=$this->freqWiseAmt($value[9], $value[10]);
                        $ww['sip_fresh_min_amt']=isset($value[9])?$value[9]:"";
                        $ww['sip_add_min_amt']=isset($value[10])?$value[10]:"";
                        array_push($sip_freq_wise_amt,$ww);
                        $ff['id']="F";
                        $ff['freq_name']="Fortnightly";
                        $ff['is_checked']=$this->freqWiseAmt($value[11], $value[12]);
                        $ff['sip_fresh_min_amt']=isset($value[11])?$value[11]:"";
                        $ff['sip_add_min_amt']=isset($value[12])?$value[12]:"";
                        array_push($sip_freq_wise_amt,$ff);
                        $mm['id']="M";
                        $mm['freq_name']="Monthly";
                        $mm['is_checked']=$this->freqWiseAmt($value[13], $value[14]);
                        $mm['sip_fresh_min_amt']=isset($value[13])?$value[13]:"";
                        $mm['sip_add_min_amt']=isset($value[14])?$value[14]:"";
                        array_push($sip_freq_wise_amt,$mm);
                        $qq['id']="Q";
                        $qq['freq_name']="Quarterly";
                        $qq['is_checked']=$this->freqWiseAmt($value[15], $value[16]);
                        $qq['sip_fresh_min_amt']=isset($value[15])?$value[15]:"";
                        $qq['sip_add_min_amt']=isset($value[16])?$value[16]:"";
                        array_push($sip_freq_wise_amt,$qq);
                        $ss['id']="S";
                        $ss['freq_name']="Semi Anually";
                        $ss['is_checked']=$this->freqWiseAmt($value[17], $value[18]);
                        $ss['sip_fresh_min_amt']=isset($value[17])?$value[17]:"";
                        $ss['sip_add_min_amt']=isset($value[18])?$value[18]:"";
                        array_push($sip_freq_wise_amt,$ss);
                        $aa['id']="A";
                        $aa['freq_name']="Anually";
                        $aa['is_checked']=$this->freqWiseAmt($value[19], $value[20]);
                        $aa['sip_fresh_min_amt']=isset($value[19])?$value[19]:"";
                        $aa['sip_add_min_amt']=isset($value[20])?$value[20]:"";
                        array_push($sip_freq_wise_amt,$aa);
                        // return $sip_freq_wise_amt;

                        // return $value[25];
                        $swp_freq_wise_amt=[];
                        $swp_dd['id']="D";
                        $swp_dd['freq_name']="Daily";
                        $swp_dd['is_checked']=$this->freqWiseAmt1($value[23]);
                        $swp_dd['sip_add_min_amt']=isset($value[23])?$value[23]:"";
                        array_push($swp_freq_wise_amt,$swp_dd);
                        $swp_ww['id']="W";
                        $swp_ww['freq_name']="Weekly";
                        $swp_ww['is_checked']=$this->freqWiseAmt1($value[24]);
                        $swp_ww['sip_add_min_amt']=isset($value[24])?$value[24]:"";
                        array_push($swp_freq_wise_amt,$swp_ww);
                        $swp_ff['id']="F";
                        $swp_ff['freq_name']="Fortnightly";
                        $swp_ff['is_checked']=$this->freqWiseAmt1($value[25]);
                        $swp_ff['sip_add_min_amt']=isset($value[25])?$value[25]:"";
                        array_push($swp_freq_wise_amt,$swp_ff);
                        $swp_mm['id']="M";
                        $swp_mm['freq_name']="Monthly";
                        $swp_mm['is_checked']=$this->freqWiseAmt1($value[26]);
                        $swp_mm['sip_add_min_amt']=isset($value[26])?$value[26]:"";
                        array_push($swp_freq_wise_amt,$swp_mm);
                        $swp_qq['id']="Q";
                        $swp_qq['freq_name']="Quarterly";
                        $swp_qq['is_checked']=$this->freqWiseAmt1($value[27]);
                        $swp_qq['sip_add_min_amt']=isset($value[27])?$value[27]:"";
                        array_push($swp_freq_wise_amt,$swp_qq);
                        $swp_ss['id']="S";
                        $swp_ss['freq_name']="Semi Anually";
                        $swp_ss['is_checked']=$this->freqWiseAmt1($value[28]);
                        $swp_ss['sip_add_min_amt']=isset($value[28])?$value[28]:"";
                        array_push($swp_freq_wise_amt,$swp_ss);
                        $swp_aa['id']="A";
                        $swp_aa['freq_name']="Anually";
                        $swp_aa['is_checked']=$this->freqWiseAmt1($value[29]);
                        $swp_aa['sip_add_min_amt']=isset($value[29])?$value[29]:"";
                        array_push($swp_freq_wise_amt,$swp_aa);
                        // return $swp_freq_wise_amt;

                        $stp_freq_wise_amt=[];
                        $stp_dd['id']="D";
                        $stp_dd['freq_name']="Daily";
                        $stp_dd['is_checked']=$this->freqWiseAmt1($value[32]);
                        $stp_dd['sip_add_min_amt']=isset($value[32])?$value[32]:"";
                        array_push($stp_freq_wise_amt,$stp_dd);
                        $stp_ww['id']="W";
                        $stp_ww['freq_name']="Weekly";
                        $stp_ww['is_checked']=$this->freqWiseAmt1($value[33]);
                        $stp_ww['sip_add_min_amt']=isset($value[33])?$value[33]:"";
                        array_push($stp_freq_wise_amt,$stp_ww);
                        $stp_ff['id']="F";
                        $stp_ff['freq_name']="Fortnightly";
                        $stp_ff['is_checked']=$this->freqWiseAmt1($value[34]);
                        $stp_ff['sip_add_min_amt']=isset($value[34])?$value[34]:"";
                        array_push($stp_freq_wise_amt,$stp_ff);
                        $stp_mm['id']="M";
                        $stp_mm['freq_name']="Monthly";
                        $stp_mm['is_checked']=$this->freqWiseAmt1($value[35]);
                        $stp_mm['sip_add_min_amt']=isset($value[35])?$value[35]:"";
                        array_push($stp_freq_wise_amt,$stp_mm);
                        $stp_qq['id']="Q";
                        $stp_qq['freq_name']="Quarterly";
                        $stp_qq['is_checked']=$this->freqWiseAmt1($value[36]);
                        $stp_qq['sip_add_min_amt']=isset($value[36])?$value[36]:"";
                        array_push($stp_freq_wise_amt,$stp_qq);
                        $stp_ss['id']="S";
                        $stp_ss['freq_name']="Semi Anually";
                        $stp_ss['is_checked']=$this->freqWiseAmt1($value[37]);
                        $stp_ss['sip_add_min_amt']=isset($value[37])?$value[37]:"";
                        array_push($stp_freq_wise_amt,$stp_ss);
                        $stp_aa['id']="A";
                        $stp_aa['freq_name']="Anually";
                        $stp_aa['is_checked']=$this->freqWiseAmt1($value[38]);
                        $stp_aa['sip_add_min_amt']=isset($value[38])?$value[38]:"";
                        array_push($stp_freq_wise_amt,$stp_aa);
                        // return $stp_freq_wise_amt;
                        $is_has=Scheme::where('scheme_name',$value[3])->get();

                        $amc_id=AMC::where('amc_short_name',$value[0])->value('id');
                        $category_id=Category::where('cat_name',$value[1])->value('id');
                        $subcategory_id=SubCategory::where('subcategory_name',$value[2])->value('id');
                        if (count($is_has) <= 0) {
                            
                            Scheme::create(array(
                                'product_id'=>base64_decode($product_id),
                                'amc_id'=>$amc_id,
                                'category_id'=>$category_id,
                                'subcategory_id'=>$subcategory_id,
                                'scheme_type'=>$scheme_type,
                                'scheme_name'=>$value[3],
                                'pip_fresh_min_amt'=>$value[4],
                                'pip_add_min_amt'=>$value[5],
                                'sip_date'=>json_encode($sip_date_array),
                                'sip_freq_wise_amt'=>json_encode($sip_freq_wise_amt),
                                'ava_special_sip'=>$ava_special_sip,
                                'special_sip_name'=>isset($value[21])?$value[21]:NULL,

                                'swp_date'=>json_encode($swp_date_array),
                                'swp_freq_wise_amt'=>json_encode($swp_freq_wise_amt),
                                'ava_special_swp'=>$ava_special_swp,
                                'special_swp_name'=>isset($value[30])?$value[30]:NULL,

                                'stp_date'=>json_encode($stp_date_array),
                                'stp_freq_wise_amt'=>json_encode($stp_freq_wise_amt),
                                'ava_special_stp'=>$ava_special_stp,
                                'special_stp_name'=>isset($value[39])?$value[39]:NULL,
                                'step_up_min_amt'=>isset($value[40])?$value[40]:NULL,
                                'step_up_min_per'=>isset($value[41])?$value[41]:NULL,
                                'delete_flag'=>'N',
                            ));
                        }else {
                            // return Helper::WarningResponse(parent::ALREADY_EXIST);
                            Scheme::whereId($is_has[0]->id)->update(array(
                                'product_id'=>base64_decode($product_id),
                                'amc_id'=>$amc_id,
                                'category_id'=>$category_id,
                                'subcategory_id'=>$subcategory_id,
                                'scheme_type'=>$scheme_type,
                                'scheme_name'=>$value[3],
                                'pip_fresh_min_amt'=>$value[4],
                                'pip_add_min_amt'=>$value[5],
                                'sip_date'=>json_encode($sip_date_array),
                                'sip_freq_wise_amt'=>json_encode($sip_freq_wise_amt),
                                'ava_special_sip'=>$ava_special_sip,
                                'special_sip_name'=>isset($value[21])?$value[21]:NULL,

                                'swp_date'=>json_encode($swp_date_array),
                                'swp_freq_wise_amt'=>json_encode($swp_freq_wise_amt),
                                'ava_special_swp'=>$ava_special_swp,
                                'special_swp_name'=>isset($value[30])?$value[30]:NULL,

                                'stp_date'=>json_encode($stp_date_array),
                                'stp_freq_wise_amt'=>json_encode($stp_freq_wise_amt),
                                'ava_special_stp'=>$ava_special_stp,
                                'special_stp_name'=>isset($value[39])?$value[39]:NULL,
                                'step_up_min_amt'=>isset($value[40])?$value[40]:NULL,
                                'step_up_min_per'=>isset($value[41])?$value[41]:NULL,
                                'delete_flag'=>'N',
                            ));
                        }
                        
                    }
                }
            }else {
                // return 'hii';
                foreach ($data as $key => $value) {
                    if ($key==0) {
                        // return 'hii';
                        if (str_replace(" ","_",$value[0])!="AMC_Short_Name"  && str_replace(" ","_",$value[1])!="Category_Name"  && str_replace(" ","_",$value[2])!="Sub_Category_Name"  && $value[3]!="Scheme" && str_replace(" ","_",$value[4])!="NFO_Start_Date" && str_replace(" ","_",$value[5])!="NFO_End_Date" && str_replace(" ","_",$value[6])!="NFO_Reopen_Date") {
                            // return 'hii';
                            return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
                        }
                        // return $value;
                    }else {
                        // return $value;
                        
                        //=======================================SIP=======================
                        if ($value[25]) {
                            $ava_special_sip='true';
                        }else {
                            $ava_special_sip='false';
                        }
                        // return $ava_special_sip;
                        $sip_date=explode(',',$value[10]);
                        $sip_date_array=[];
                        foreach ($sip_date as $key => $value1) {
                            $sip_dd['id']=$value1;
                            $sip_dd['date']=$value1;
                            array_push($sip_date_array,$sip_dd);
                        }
                        // return $sip_date_array;

                        //=======================================SWP=======================
                        if ($value[34]) {
                            $ava_special_swp='true';
                        }else {
                            $ava_special_swp='false';
                        }

                        $swp_date=explode(',',$value[26]);
                        $swp_date_array=[];
                        foreach ($swp_date as $key => $value2) {
                            $swp_dd['id']=$value2;
                            $swp_dd['date']=$value2;
                            array_push($swp_date_array,$swp_dd);
                        }
                        // return $swp_date_array;
                        //=======================================STP=======================

                        if ($value[43]) {
                            $ava_special_stp='true';
                        }else {
                            $ava_special_stp='false';
                        }

                        $stp_date=explode(',',$value[35]);
                        $stp_date_array=[];
                        foreach ($stp_date as $key => $value3) {
                            $stp_dd['id']=$value3;
                            $stp_dd['date']=$value3;
                            array_push($stp_date_array,$stp_dd);
                        }
                        // return $stp_date_array;
                        // 11 - 24  SIP
                        // 25 - 31 SWP
                        // 32 - 38 STP
                        
                        $sip_freq_wise_amt=[];
                        $dd['id']="D";
                        $dd['freq_name']="Daily";
                        $dd['is_checked']=$this->freqWiseAmt($value[11], $value[12]);
                        $dd['sip_fresh_min_amt']=isset($value[11])?$value[11]:"";
                        $dd['sip_add_min_amt']=isset($value[12])?$value[12]:"";
                        array_push($sip_freq_wise_amt,$dd);
                        $ww['id']="W";
                        $ww['freq_name']="Weekly";
                        $ww['is_checked']=$this->freqWiseAmt($value[13], $value[14]);
                        $ww['sip_fresh_min_amt']=isset($value[13])?$value[13]:"";
                        $ww['sip_add_min_amt']=isset($value[14])?$value[14]:"";
                        array_push($sip_freq_wise_amt,$ww);
                        $ff['id']="F";
                        $ff['freq_name']="Fortnightly";
                        $ff['is_checked']=$this->freqWiseAmt($value[15], $value[16]);
                        $ff['sip_fresh_min_amt']=isset($value[15])?$value[15]:"";
                        $ff['sip_add_min_amt']=isset($value[16])?$value[16]:"";
                        array_push($sip_freq_wise_amt,$ff);
                        $mm['id']="M";
                        $mm['freq_name']="Monthly";
                        $mm['is_checked']=$this->freqWiseAmt($value[17], $value[18]);
                        $mm['sip_fresh_min_amt']=isset($value[17])?$value[17]:"";
                        $mm['sip_add_min_amt']=isset($value[18])?$value[18]:"";
                        array_push($sip_freq_wise_amt,$mm);
                        $qq['id']="Q";
                        $qq['freq_name']="Quarterly";
                        $qq['is_checked']=$this->freqWiseAmt($value[19], $value[20]);
                        $qq['sip_fresh_min_amt']=isset($value[19])?$value[19]:"";
                        $qq['sip_add_min_amt']=isset($value[20])?$value[20]:"";
                        array_push($sip_freq_wise_amt,$qq);
                        $ss['id']="S";
                        $ss['freq_name']="Semi Anually";
                        $ss['is_checked']=$this->freqWiseAmt($value[21], $value[22]);
                        $ss['sip_fresh_min_amt']=isset($value[21])?$value[21]:"";
                        $ss['sip_add_min_amt']=isset($value[22])?$value[22]:"";
                        array_push($sip_freq_wise_amt,$ss);
                        $aa['id']="A";
                        $aa['freq_name']="Anually";
                        $aa['is_checked']=$this->freqWiseAmt($value[23], $value[24]);
                        $aa['sip_fresh_min_amt']=isset($value[23])?$value[23]:"";
                        $aa['sip_add_min_amt']=isset($value[24])?$value[24]:"";
                        array_push($sip_freq_wise_amt,$aa);
                        // return $sip_freq_wise_amt;

                        // return $value[25];
                        $swp_freq_wise_amt=[];
                        $swp_dd['id']="D";
                        $swp_dd['freq_name']="Daily";
                        $swp_dd['is_checked']=$this->freqWiseAmt1($value[27]);
                        $swp_dd['sip_add_min_amt']=isset($value[27])?$value[27]:"";
                        array_push($swp_freq_wise_amt,$swp_dd);
                        $swp_ww['id']="W";
                        $swp_ww['freq_name']="Weekly";
                        $swp_ww['is_checked']=$this->freqWiseAmt1($value[28]);
                        $swp_ww['sip_add_min_amt']=isset($value[28])?$value[28]:"";
                        array_push($swp_freq_wise_amt,$swp_ww);
                        $swp_ff['id']="F";
                        $swp_ff['freq_name']="Fortnightly";
                        $swp_ff['is_checked']=$this->freqWiseAmt1($value[29]);
                        $swp_ff['sip_add_min_amt']=isset($value[29])?$value[29]:"";
                        array_push($swp_freq_wise_amt,$swp_ff);
                        $swp_mm['id']="M";
                        $swp_mm['freq_name']="Monthly";
                        $swp_mm['is_checked']=$this->freqWiseAmt1($value[30]);
                        $swp_mm['sip_add_min_amt']=isset($value[30])?$value[30]:"";
                        array_push($swp_freq_wise_amt,$swp_mm);
                        $swp_qq['id']="Q";
                        $swp_qq['freq_name']="Quarterly";
                        $swp_qq['is_checked']=$this->freqWiseAmt1($value[31]);
                        $swp_qq['sip_add_min_amt']=isset($value[31])?$value[31]:"";
                        array_push($swp_freq_wise_amt,$swp_qq);
                        $swp_ss['id']="S";
                        $swp_ss['freq_name']="Semi Anually";
                        $swp_ss['is_checked']=$this->freqWiseAmt1($value[32]);
                        $swp_ss['sip_add_min_amt']=isset($value[32])?$value[32]:"";
                        array_push($swp_freq_wise_amt,$swp_ss);
                        $swp_aa['id']="A";
                        $swp_aa['freq_name']="Anually";
                        $swp_aa['is_checked']=$this->freqWiseAmt1($value[33]);
                        $swp_aa['sip_add_min_amt']=isset($value[33])?$value[33]:"";
                        array_push($swp_freq_wise_amt,$swp_aa);
                        // return $swp_freq_wise_amt;

                        $stp_freq_wise_amt=[];
                        $stp_dd['id']="D";
                        $stp_dd['freq_name']="Daily";
                        $stp_dd['is_checked']=$this->freqWiseAmt1($value[36]);
                        $stp_dd['sip_add_min_amt']=isset($value[36])?$value[36]:"";
                        array_push($stp_freq_wise_amt,$stp_dd);
                        $stp_ww['id']="W";
                        $stp_ww['freq_name']="Weekly";
                        $stp_ww['is_checked']=$this->freqWiseAmt1($value[37]);
                        $stp_ww['sip_add_min_amt']=isset($value[37])?$value[37]:"";
                        array_push($stp_freq_wise_amt,$stp_ww);
                        $stp_ff['id']="F";
                        $stp_ff['freq_name']="Fortnightly";
                        $stp_ff['is_checked']=$this->freqWiseAmt1($value[38]);
                        $stp_ff['sip_add_min_amt']=isset($value[38])?$value[38]:"";
                        array_push($stp_freq_wise_amt,$stp_ff);
                        $stp_mm['id']="M";
                        $stp_mm['freq_name']="Monthly";
                        $stp_mm['is_checked']=$this->freqWiseAmt1($value[39]);
                        $stp_mm['sip_add_min_amt']=isset($value[39])?$value[39]:"";
                        array_push($stp_freq_wise_amt,$stp_mm);
                        $stp_qq['id']="Q";
                        $stp_qq['freq_name']="Quarterly";
                        $stp_qq['is_checked']=$this->freqWiseAmt1($value[40]);
                        $stp_qq['sip_add_min_amt']=isset($value[40])?$value[40]:"";
                        array_push($stp_freq_wise_amt,$stp_qq);
                        $stp_ss['id']="S";
                        $stp_ss['freq_name']="Semi Anually";
                        $stp_ss['is_checked']=$this->freqWiseAmt1($value[41]);
                        $stp_ss['sip_add_min_amt']=isset($value[41])?$value[41]:"";
                        array_push($stp_freq_wise_amt,$stp_ss);
                        $stp_aa['id']="A";
                        $stp_aa['freq_name']="Anually";
                        $stp_aa['is_checked']=$this->freqWiseAmt1($value[42]);
                        $stp_aa['sip_add_min_amt']=isset($value[42])?$value[42]:"";
                        array_push($stp_freq_wise_amt,$stp_aa);
                        // return $stp_freq_wise_amt;
                        $is_has=Scheme::where('scheme_name',$value[3])->get();
                        // return count($is_has);
                        $amc_id=AMC::where('amc_short_name',$value[0])->value('id');
                        $category_id=Category::where('cat_name',$value[1])->value('id');
                        $subcategory_id=SubCategory::where('subcategory_name',$value[2])->value('id');
                        if (count($is_has) <= 0) {
                            
                            // return $amc_id;
                            Scheme::create(array(
                                'product_id'=>base64_decode($product_id),
                                'amc_id'=>$amc_id,
                                'category_id'=>$category_id,
                                'subcategory_id'=>$subcategory_id,
                                'scheme_type'=>$scheme_type,
                                'scheme_name'=>$value[3],
                                'nfo_start_dt'=>$value[4],
                                'nfo_end_dt'=>$value[5],
                                'nfo_reopen_dt'=>$value[6],
                                'nfo_entry_date'=>$value[7],
                                'pip_fresh_min_amt'=>$value[8],
                                'pip_add_min_amt'=>$value[9],
                                'sip_date'=>json_encode($sip_date_array),
                                'sip_freq_wise_amt'=>json_encode($sip_freq_wise_amt),
                                'ava_special_sip'=>$ava_special_sip,
                                'special_sip_name'=>isset($value[25])?$value[25]:NULL,

                                'swp_date'=>json_encode($swp_date_array),
                                'swp_freq_wise_amt'=>json_encode($swp_freq_wise_amt),
                                'ava_special_swp'=>$ava_special_swp,
                                'special_swp_name'=>isset($value[34])?$value[34]:NULL,

                                'stp_date'=>json_encode($stp_date_array),
                                'stp_freq_wise_amt'=>json_encode($stp_freq_wise_amt),
                                'ava_special_stp'=>$ava_special_stp,
                                'special_stp_name'=>isset($value[43])?$value[43]:NULL,

                                'step_up_min_amt'=>isset($value[44])?$value[44]:NULL,
                                'step_up_min_per'=>isset($value[45])?$value[45]:NULL,
                                'delete_flag'=>'N',
                            ));
                        }else {
                            // return 'else';
                            // return Helper::WarningResponse(parent::ALREADY_EXIST);
                            Scheme::whereId($is_has[0]->id)->update(array(
                                'product_id'=>base64_decode($product_id),
                                'amc_id'=>$amc_id,
                                'category_id'=>$category_id,
                                'subcategory_id'=>$subcategory_id,
                                'scheme_type'=>$scheme_type,
                                'scheme_name'=>$value[3],
                                'nfo_start_dt'=>$value[4],
                                'nfo_end_dt'=>$value[5],
                                'nfo_reopen_dt'=>$value[6],
                                'nfo_entry_date'=>$value[7],
                                'pip_fresh_min_amt'=>$value[8],
                                'pip_add_min_amt'=>$value[9],
                                'sip_date'=>json_encode($sip_date_array),
                                'sip_freq_wise_amt'=>json_encode($sip_freq_wise_amt),
                                'ava_special_sip'=>$ava_special_sip,
                                'special_sip_name'=>isset($value[25])?$value[25]:NULL,

                                'swp_date'=>json_encode($swp_date_array),
                                'swp_freq_wise_amt'=>json_encode($swp_freq_wise_amt),
                                'ava_special_swp'=>$ava_special_swp,
                                'special_swp_name'=>isset($value[34])?$value[34]:NULL,

                                'stp_date'=>json_encode($stp_date_array),
                                'stp_freq_wise_amt'=>json_encode($stp_freq_wise_amt),
                                'ava_special_stp'=>$ava_special_stp,
                                'special_stp_name'=>isset($value[43])?$value[43]:NULL,

                                'step_up_min_amt'=>isset($value[44])?$value[44]:NULL,
                                'step_up_min_per'=>isset($value[45])?$value[45]:NULL,
                                'delete_flag'=>'N',
                            ));
                        }
                    }
                
                }
            }





            // return gettype($data[0][0]) ;
            // if (in_array("rnt_id", $data)) {
            // if ($data[0][0] == "rnt_id" && $data[0][1] == "product_id" && $data[0][2] == "amc_name" && $data[0][3] == "website" && $data[0][4] == "ofc_addr") {
            //     return "hii";
                // Excel::import(new SchemeImport,$request->file);
                // Excel::import(new SchemeImport,request()->file('file'));
                $data1=[];
            // }else {
            //     return "else";
            //     return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
            // }
        } catch (\Throwable $th) {
            // throw $th;
            //return $value;
            return Helper::ErrorResponse(parent::IMPORT_CSV_ERROR);
        }
        return Helper::SuccessResponse($data1);
    }

    public function freqWiseAmt($val1, $val2)
    {
        if ($val1 && $val2) {
            $is_checked='true';
        }else{
            $is_checked='false';
        }
        return $is_checked;
    }

    public function freqWiseAmt1($val1)
    {
        if ($val1) {
            $is_checked='true';
        }else{
            $is_checked='false';
        }
        return $is_checked;
    }


}
