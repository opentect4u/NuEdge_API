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
            $amc_id=json_decode($request->amc_id);
            $cat_id=json_decode($request->cat_id);
            $subcat_id=json_decode($request->subcat_id);
            $scheme_id=json_decode($request->scheme_id);
            $order=$request->order;
            $field=$request->field;
            $scheme_type=$request->scheme_type;
            $search_scheme_id=$request->search_scheme_id;
            
            // return $request;
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
                if (!empty($amc_id) || !empty($cat_id) || !empty($subcat_id) || !empty($scheme_id) || $search_scheme_id) {
                    $rawQuery='';
                    $rawQuery=$this->filterCriteria($rawQuery,$amc_id,$cat_id,$subcat_id,$scheme_id,$search_scheme_id);
                    
                    $data=Scheme::join('md_amc','md_amc.id','=','md_scheme.amc_id')
                        ->join('md_category','md_category.id','=','md_scheme.category_id')
                        ->join('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                        ->join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                        ->leftJoin('md_benchmark','md_benchmark.id','=','md_scheme.benchmark_id')
                        ->select('md_scheme.*','md_amc.amc_name as amc_name','md_amc.amc_short_name as amc_short_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcate_name','md_rnt.rnt_name as rnt_name','md_benchmark.benchmark as benchmark')
                        ->selectRaw('SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",16) as sip_amount_filter_1')
                        ->selectRaw('SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",20) as sip_amount_filter_2')
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",16), "\"", -1) as D_sip_min_F_amount') // D sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",20), "\"", -1) as D_sip_min_A_amount') // D sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",36), "\"", -1) as W_sip_min_F_amount') // W sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",40), "\"", -1) as W_sip_min_A_amount') // W sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",56), "\"", -1) as F_sip_min_F_amount') // F sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",60), "\"", -1) as F_sip_min_A_amount') // F sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",76), "\"", -1) as M_sip_min_F_amount') // M sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",80), "\"", -1) as M_sip_min_A_amount') // M sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",96), "\"", -1) as Q_sip_min_F_amount') // Q sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",100), "\"", -1) as Q_sip_min_A_amount') // Q sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",116), "\"", -1) as S_sip_min_F_amount') // S sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",120), "\"", -1) as S_sip_min_A_amount') // S sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",136), "\"", -1) as A_sip_min_F_amount') // A sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",140), "\"", -1) as A_sip_min_A_amount') // A sip min
                        ->selectRaw('SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",16) as swp_amount_filter_1')
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",16), "\"", -1) as D_swp_min_amount') // D swp min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",32), "\"", -1) as W_swp_min_amount') // W swp min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",48), "\"", -1) as F_swp_min_amount') // F swp min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",64), "\"", -1) as M_swp_min_amount') // M swp min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",80), "\"", -1) as Q_swp_min_amount') // Q swp min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",96), "\"", -1) as S_swp_min_amount') // S swp min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",112), "\"", -1) as A_swp_min_amount') // A swp min
                        ->selectRaw('SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",16) as stp_amount_filter_2')
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",16), "\"", -1) as D_stp_min_amount') // D stp min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",32), "\"", -1) as W_stp_min_amount') // W stp min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",48), "\"", -1) as F_stp_min_amount') // F stp min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",64), "\"", -1) as M_stp_min_amount') // M stp min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",80), "\"", -1) as Q_stp_min_amount') // Q stp min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",96), "\"", -1) as S_stp_min_amount') // S stp min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",112), "\"", -1) as A_stp_min_amount') // A stp min
                        ->where('md_scheme.delete_flag','N')
                        ->where('md_scheme.scheme_type',$scheme_type)
                        ->whereRaw($rawQuery)
                        ->orderByRaw($rawOrderBy)
                        ->paginate($paginate);  
                } else {
                    $data=Scheme::join('md_amc','md_amc.id','=','md_scheme.amc_id')
                        ->join('md_category','md_category.id','=','md_scheme.category_id')
                        ->join('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                        ->join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                        ->leftJoin('md_benchmark','md_benchmark.id','=','md_scheme.benchmark_id')
                        ->select('md_scheme.*','md_amc.amc_name as amc_name','md_amc.amc_short_name as amc_short_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcate_name','md_rnt.rnt_name as rnt_name','md_benchmark.benchmark as benchmark')
                        ->selectRaw('SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",16) as sip_amount_filter_1')
                        ->selectRaw('SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",20) as sip_amount_filter_2')
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",16), "\"", -1) as D_sip_min_F_amount') // D sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",20), "\"", -1) as D_sip_min_A_amount') // D sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",36), "\"", -1) as W_sip_min_F_amount') // W sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",40), "\"", -1) as W_sip_min_A_amount') // W sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",56), "\"", -1) as F_sip_min_F_amount') // F sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",60), "\"", -1) as F_sip_min_A_amount') // F sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",76), "\"", -1) as M_sip_min_F_amount') // M sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",80), "\"", -1) as M_sip_min_A_amount') // M sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",96), "\"", -1) as Q_sip_min_F_amount') // Q sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",100), "\"", -1) as Q_sip_min_A_amount') // Q sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",116), "\"", -1) as S_sip_min_F_amount') // S sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",120), "\"", -1) as S_sip_min_A_amount') // S sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",136), "\"", -1) as A_sip_min_F_amount') // A sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",140), "\"", -1) as A_sip_min_A_amount') // A sip min
                        ->selectRaw('SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",16) as swp_amount_filter_1')
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",16), "\"", -1) as D_swp_min_amount') // D swp min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",32), "\"", -1) as W_swp_min_amount') // W swp min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",48), "\"", -1) as F_swp_min_amount') // F swp min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",64), "\"", -1) as M_swp_min_amount') // M swp min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",80), "\"", -1) as Q_swp_min_amount') // Q swp min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",96), "\"", -1) as S_swp_min_amount') // S swp min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",112), "\"", -1) as A_swp_min_amount') // A swp min
                        ->selectRaw('SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",16) as stp_amount_filter_2')
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",16), "\"", -1) as D_stp_min_amount') // D stp min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",32), "\"", -1) as W_stp_min_amount') // W stp min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",48), "\"", -1) as F_stp_min_amount') // F stp min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",64), "\"", -1) as M_stp_min_amount') // M stp min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",80), "\"", -1) as Q_stp_min_amount') // Q stp min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",96), "\"", -1) as S_stp_min_amount') // S stp min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",112), "\"", -1) as A_stp_min_amount') // A stp min
                        ->where('md_scheme.delete_flag','N')
                        ->where('md_scheme.scheme_type',$scheme_type)
                        ->orderByRaw($rawOrderBy)
                        ->paginate($paginate);  
                }
            }elseif (!empty($amc_id) || !empty($cat_id) || !empty($subcat_id) || !empty($scheme_id) || $search_scheme_id) {
                $rawQuery='';
                $rawQuery=$this->filterCriteria($rawQuery,$amc_id,$cat_id,$subcat_id,$scheme_id,$search_scheme_id);

                $data=Scheme::join('md_amc','md_amc.id','=','md_scheme.amc_id')
                    ->join('md_category','md_category.id','=','md_scheme.category_id')
                    ->join('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                    ->join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                    ->leftJoin('md_benchmark','md_benchmark.id','=','md_scheme.benchmark_id')
                    ->select('md_scheme.*','md_amc.amc_name as amc_name','md_amc.amc_short_name as amc_short_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcate_name','md_rnt.rnt_name as rnt_name','md_benchmark.benchmark as benchmark')
                    ->selectRaw('SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",16) as sip_amount_filter_1')
                    ->selectRaw('SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",20) as sip_amount_filter_2')
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",16), "\"", -1) as D_sip_min_F_amount') // D sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",20), "\"", -1) as D_sip_min_A_amount') // D sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",36), "\"", -1) as W_sip_min_F_amount') // W sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",40), "\"", -1) as W_sip_min_A_amount') // W sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",56), "\"", -1) as F_sip_min_F_amount') // F sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",60), "\"", -1) as F_sip_min_A_amount') // F sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",76), "\"", -1) as M_sip_min_F_amount') // M sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",80), "\"", -1) as M_sip_min_A_amount') // M sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",96), "\"", -1) as Q_sip_min_F_amount') // Q sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",100), "\"", -1) as Q_sip_min_A_amount') // Q sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",116), "\"", -1) as S_sip_min_F_amount') // S sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",120), "\"", -1) as S_sip_min_A_amount') // S sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",136), "\"", -1) as A_sip_min_F_amount') // A sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",140), "\"", -1) as A_sip_min_A_amount') // A sip min
                    ->selectRaw('SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",16) as swp_amount_filter_1')
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",16), "\"", -1) as D_swp_min_amount') // D swp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",32), "\"", -1) as W_swp_min_amount') // W swp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",48), "\"", -1) as F_swp_min_amount') // F swp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",64), "\"", -1) as M_swp_min_amount') // M swp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",80), "\"", -1) as Q_swp_min_amount') // Q swp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",96), "\"", -1) as S_swp_min_amount') // S swp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",112), "\"", -1) as A_swp_min_amount') // A swp min
                    ->selectRaw('SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",16) as stp_amount_filter_2')
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",16), "\"", -1) as D_stp_min_amount') // D stp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",32), "\"", -1) as W_stp_min_amount') // W stp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",48), "\"", -1) as F_stp_min_amount') // F stp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",64), "\"", -1) as M_stp_min_amount') // M stp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",80), "\"", -1) as Q_stp_min_amount') // Q stp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",96), "\"", -1) as S_stp_min_amount') // S stp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",112), "\"", -1) as A_stp_min_amount') // A stp min
                    ->where('md_scheme.delete_flag','N')
                    ->where('md_scheme.scheme_type',$scheme_type)
                    ->whereRaw($rawQuery)
                    ->paginate($paginate);  
            } else {
                $data=Scheme::join('md_amc','md_amc.id','=','md_scheme.amc_id')
                    ->join('md_category','md_category.id','=','md_scheme.category_id')
                    ->join('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                    ->join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                    ->leftJoin('md_benchmark','md_benchmark.id','=','md_scheme.benchmark_id')
                    ->select('md_scheme.*','md_amc.amc_name as amc_name','md_amc.amc_short_name as amc_short_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcate_name','md_rnt.rnt_name as rnt_name','md_benchmark.benchmark as benchmark')
                    ->selectRaw('SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",16) as sip_amount_filter_1')
                    ->selectRaw('SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",20) as sip_amount_filter_2')
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",16), "\"", -1) as D_sip_min_F_amount') // D sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",20), "\"", -1) as D_sip_min_A_amount') // D sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",36), "\"", -1) as W_sip_min_F_amount') // W sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",40), "\"", -1) as W_sip_min_A_amount') // W sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",56), "\"", -1) as F_sip_min_F_amount') // F sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",60), "\"", -1) as F_sip_min_A_amount') // F sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",76), "\"", -1) as M_sip_min_F_amount') // M sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",80), "\"", -1) as M_sip_min_A_amount') // M sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",96), "\"", -1) as Q_sip_min_F_amount') // Q sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",100), "\"", -1) as Q_sip_min_A_amount') // Q sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",116), "\"", -1) as S_sip_min_F_amount') // S sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",120), "\"", -1) as S_sip_min_A_amount') // S sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",136), "\"", -1) as A_sip_min_F_amount') // A sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",140), "\"", -1) as A_sip_min_A_amount') // A sip min
                    ->selectRaw('SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",16) as swp_amount_filter_1')
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",16), "\"", -1) as D_swp_min_amount') // D swp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",32), "\"", -1) as W_swp_min_amount') // W swp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",48), "\"", -1) as F_swp_min_amount') // F swp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",64), "\"", -1) as M_swp_min_amount') // M swp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",80), "\"", -1) as Q_swp_min_amount') // Q swp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",96), "\"", -1) as S_swp_min_amount') // S swp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",112), "\"", -1) as A_swp_min_amount') // A swp min
                    ->selectRaw('SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",16) as stp_amount_filter_2')
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",16), "\"", -1) as D_stp_min_amount') // D stp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",32), "\"", -1) as W_stp_min_amount') // W stp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",48), "\"", -1) as F_stp_min_amount') // F stp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",64), "\"", -1) as M_stp_min_amount') // M stp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",80), "\"", -1) as Q_stp_min_amount') // Q stp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",96), "\"", -1) as S_stp_min_amount') // S stp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",112), "\"", -1) as A_stp_min_amount') // A stp min
                    ->where('md_scheme.delete_flag','N')
                    ->where('md_scheme.scheme_type',$scheme_type)
                    ->orderBy('md_scheme.updated_at','desc')
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
            $amc_id=json_decode($request->amc_id);
            $cat_id=json_decode($request->cat_id);
            $subcat_id=json_decode($request->subcat_id);
            $scheme_id=json_decode($request->scheme_id);
            $order=$request->order;
            $field=$request->field;
            $scheme_type=$request->scheme_type;
            $search_scheme_id=$request->search_scheme_id;
            
            // return $request;
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
                if (!empty($amc_id) || !empty($cat_id) || !empty($subcat_id) || !empty($scheme_id) || $search_scheme_id) {
                    $rawQuery='';
                    $rawQuery=$this->filterCriteria($rawQuery,$amc_id,$cat_id,$subcat_id,$scheme_id,$search_scheme_id);
                    
                    $data=Scheme::join('md_amc','md_amc.id','=','md_scheme.amc_id')
                        ->join('md_category','md_category.id','=','md_scheme.category_id')
                        ->join('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                        ->join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                        ->leftJoin('md_benchmark','md_benchmark.id','=','md_scheme.benchmark_id')
                        ->select('md_scheme.*','md_amc.amc_name as amc_name','md_amc.amc_short_name as amc_short_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcate_name','md_rnt.rnt_name as rnt_name','md_benchmark.benchmark as benchmark')
                        ->selectRaw('SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",16) as sip_amount_filter_1')
                        ->selectRaw('SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",20) as sip_amount_filter_2')
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",16), "\"", -1) as D_sip_min_F_amount') // D sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",20), "\"", -1) as D_sip_min_A_amount') // D sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",36), "\"", -1) as W_sip_min_F_amount') // W sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",40), "\"", -1) as W_sip_min_A_amount') // W sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",56), "\"", -1) as F_sip_min_F_amount') // F sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",60), "\"", -1) as F_sip_min_A_amount') // F sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",76), "\"", -1) as M_sip_min_F_amount') // M sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",80), "\"", -1) as M_sip_min_A_amount') // M sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",96), "\"", -1) as Q_sip_min_F_amount') // Q sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",100), "\"", -1) as Q_sip_min_A_amount') // Q sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",116), "\"", -1) as S_sip_min_F_amount') // S sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",120), "\"", -1) as S_sip_min_A_amount') // S sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",136), "\"", -1) as A_sip_min_F_amount') // A sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",140), "\"", -1) as A_sip_min_A_amount') // A sip min
                        ->selectRaw('SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",16) as swp_amount_filter_1')
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",16), "\"", -1) as D_swp_min_amount') // D swp min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",32), "\"", -1) as W_swp_min_amount') // W swp min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",48), "\"", -1) as F_swp_min_amount') // F swp min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",64), "\"", -1) as M_swp_min_amount') // M swp min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",80), "\"", -1) as Q_swp_min_amount') // Q swp min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",96), "\"", -1) as S_swp_min_amount') // S swp min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",112), "\"", -1) as A_swp_min_amount') // A swp min
                        ->selectRaw('SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",16) as stp_amount_filter_2')
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",16), "\"", -1) as D_stp_min_amount') // D stp min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",32), "\"", -1) as W_stp_min_amount') // W stp min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",48), "\"", -1) as F_stp_min_amount') // F stp min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",64), "\"", -1) as M_stp_min_amount') // M stp min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",80), "\"", -1) as Q_stp_min_amount') // Q stp min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",96), "\"", -1) as S_stp_min_amount') // S stp min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",112), "\"", -1) as A_stp_min_amount') // A stp min
                        ->where('md_scheme.delete_flag','N')
                        ->where('md_scheme.scheme_type',$scheme_type)
                        ->whereRaw($rawQuery)
                        ->orderByRaw($rawOrderBy)
                        ->get();  
                } else {
                    $data=Scheme::join('md_amc','md_amc.id','=','md_scheme.amc_id')
                        ->join('md_category','md_category.id','=','md_scheme.category_id')
                        ->join('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                        ->join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                        ->leftJoin('md_benchmark','md_benchmark.id','=','md_scheme.benchmark_id')
                        ->select('md_scheme.*','md_amc.amc_name as amc_name','md_amc.amc_short_name as amc_short_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcate_name','md_rnt.rnt_name as rnt_name','md_benchmark.benchmark as benchmark')
                        ->selectRaw('SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",16) as sip_amount_filter_1')
                        ->selectRaw('SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",20) as sip_amount_filter_2')
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",16), "\"", -1) as D_sip_min_F_amount') // D sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",20), "\"", -1) as D_sip_min_A_amount') // D sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",36), "\"", -1) as W_sip_min_F_amount') // W sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",40), "\"", -1) as W_sip_min_A_amount') // W sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",56), "\"", -1) as F_sip_min_F_amount') // F sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",60), "\"", -1) as F_sip_min_A_amount') // F sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",76), "\"", -1) as M_sip_min_F_amount') // M sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",80), "\"", -1) as M_sip_min_A_amount') // M sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",96), "\"", -1) as Q_sip_min_F_amount') // Q sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",100), "\"", -1) as Q_sip_min_A_amount') // Q sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",116), "\"", -1) as S_sip_min_F_amount') // S sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",120), "\"", -1) as S_sip_min_A_amount') // S sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",136), "\"", -1) as A_sip_min_F_amount') // A sip min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",140), "\"", -1) as A_sip_min_A_amount') // A sip min
                        ->selectRaw('SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",16) as swp_amount_filter_1')
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",16), "\"", -1) as D_swp_min_amount') // D swp min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",32), "\"", -1) as W_swp_min_amount') // W swp min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",48), "\"", -1) as F_swp_min_amount') // F swp min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",64), "\"", -1) as M_swp_min_amount') // M swp min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",80), "\"", -1) as Q_swp_min_amount') // Q swp min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",96), "\"", -1) as S_swp_min_amount') // S swp min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",112), "\"", -1) as A_swp_min_amount') // A swp min
                        ->selectRaw('SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",16) as stp_amount_filter_2')
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",16), "\"", -1) as D_stp_min_amount') // D stp min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",32), "\"", -1) as W_stp_min_amount') // W stp min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",48), "\"", -1) as F_stp_min_amount') // F stp min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",64), "\"", -1) as M_stp_min_amount') // M stp min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",80), "\"", -1) as Q_stp_min_amount') // Q stp min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",96), "\"", -1) as S_stp_min_amount') // S stp min
                        ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",112), "\"", -1) as A_stp_min_amount') // A stp min
                        ->where('md_scheme.delete_flag','N')
                        ->where('md_scheme.scheme_type',$scheme_type)
                        ->orderByRaw($rawOrderBy)
                        ->get();  
                }
            }elseif (!empty($amc_id) || !empty($cat_id) || !empty($subcat_id) || !empty($scheme_id) || $search_scheme_id) {
                $rawQuery='';
                $rawQuery=$this->filterCriteria($rawQuery,$amc_id,$cat_id,$subcat_id,$scheme_id,$search_scheme_id);

                $data=Scheme::join('md_amc','md_amc.id','=','md_scheme.amc_id')
                    ->join('md_category','md_category.id','=','md_scheme.category_id')
                    ->join('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                    ->join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                    ->leftJoin('md_benchmark','md_benchmark.id','=','md_scheme.benchmark_id')
                    ->select('md_scheme.*','md_amc.amc_name as amc_name','md_amc.amc_short_name as amc_short_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcate_name','md_rnt.rnt_name as rnt_name','md_benchmark.benchmark as benchmark')
                    ->selectRaw('SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",16) as sip_amount_filter_1')
                    ->selectRaw('SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",20) as sip_amount_filter_2')
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",16), "\"", -1) as D_sip_min_F_amount') // D sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",20), "\"", -1) as D_sip_min_A_amount') // D sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",36), "\"", -1) as W_sip_min_F_amount') // W sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",40), "\"", -1) as W_sip_min_A_amount') // W sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",56), "\"", -1) as F_sip_min_F_amount') // F sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",60), "\"", -1) as F_sip_min_A_amount') // F sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",76), "\"", -1) as M_sip_min_F_amount') // M sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",80), "\"", -1) as M_sip_min_A_amount') // M sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",96), "\"", -1) as Q_sip_min_F_amount') // Q sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",100), "\"", -1) as Q_sip_min_A_amount') // Q sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",116), "\"", -1) as S_sip_min_F_amount') // S sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",120), "\"", -1) as S_sip_min_A_amount') // S sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",136), "\"", -1) as A_sip_min_F_amount') // A sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",140), "\"", -1) as A_sip_min_A_amount') // A sip min
                    ->selectRaw('SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",16) as swp_amount_filter_1')
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",16), "\"", -1) as D_swp_min_amount') // D swp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",32), "\"", -1) as W_swp_min_amount') // W swp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",48), "\"", -1) as F_swp_min_amount') // F swp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",64), "\"", -1) as M_swp_min_amount') // M swp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",80), "\"", -1) as Q_swp_min_amount') // Q swp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",96), "\"", -1) as S_swp_min_amount') // S swp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",112), "\"", -1) as A_swp_min_amount') // A swp min
                    ->selectRaw('SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",16) as stp_amount_filter_2')
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",16), "\"", -1) as D_stp_min_amount') // D stp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",32), "\"", -1) as W_stp_min_amount') // W stp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",48), "\"", -1) as F_stp_min_amount') // F stp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",64), "\"", -1) as M_stp_min_amount') // M stp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",80), "\"", -1) as Q_stp_min_amount') // Q stp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",96), "\"", -1) as S_stp_min_amount') // S stp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",112), "\"", -1) as A_stp_min_amount') // A stp min
                    ->where('md_scheme.delete_flag','N')
                    ->where('md_scheme.scheme_type',$scheme_type)
                    ->whereRaw($rawQuery)
                    ->get();  
            } else {
                $data=Scheme::join('md_amc','md_amc.id','=','md_scheme.amc_id')
                    ->join('md_category','md_category.id','=','md_scheme.category_id')
                    ->join('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                    ->join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                    ->leftJoin('md_benchmark','md_benchmark.id','=','md_scheme.benchmark_id')
                    ->select('md_scheme.*','md_amc.amc_name as amc_name','md_amc.amc_short_name as amc_short_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcate_name','md_rnt.rnt_name as rnt_name','md_benchmark.benchmark as benchmark')
                    ->selectRaw('SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",16) as sip_amount_filter_1')
                    ->selectRaw('SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",20) as sip_amount_filter_2')
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",16), "\"", -1) as D_sip_min_F_amount') // D sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",20), "\"", -1) as D_sip_min_A_amount') // D sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",36), "\"", -1) as W_sip_min_F_amount') // W sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",40), "\"", -1) as W_sip_min_A_amount') // W sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",56), "\"", -1) as F_sip_min_F_amount') // F sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",60), "\"", -1) as F_sip_min_A_amount') // F sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",76), "\"", -1) as M_sip_min_F_amount') // M sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",80), "\"", -1) as M_sip_min_A_amount') // M sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",96), "\"", -1) as Q_sip_min_F_amount') // Q sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",100), "\"", -1) as Q_sip_min_A_amount') // Q sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",116), "\"", -1) as S_sip_min_F_amount') // S sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",120), "\"", -1) as S_sip_min_A_amount') // S sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",136), "\"", -1) as A_sip_min_F_amount') // A sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",140), "\"", -1) as A_sip_min_A_amount') // A sip min
                    ->selectRaw('SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",16) as swp_amount_filter_1')
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",16), "\"", -1) as D_swp_min_amount') // D swp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",32), "\"", -1) as W_swp_min_amount') // W swp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",48), "\"", -1) as F_swp_min_amount') // F swp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",64), "\"", -1) as M_swp_min_amount') // M swp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",80), "\"", -1) as Q_swp_min_amount') // Q swp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",96), "\"", -1) as S_swp_min_amount') // S swp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",112), "\"", -1) as A_swp_min_amount') // A swp min
                    ->selectRaw('SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",16) as stp_amount_filter_2')
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",16), "\"", -1) as D_stp_min_amount') // D stp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",32), "\"", -1) as W_stp_min_amount') // W stp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",48), "\"", -1) as F_stp_min_amount') // F stp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",64), "\"", -1) as M_stp_min_amount') // M stp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",80), "\"", -1) as Q_stp_min_amount') // Q stp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",96), "\"", -1) as S_stp_min_amount') // S stp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",112), "\"", -1) as A_stp_min_amount') // A stp min
                    ->where('md_scheme.delete_flag','N')
                    ->where('md_scheme.scheme_type',$scheme_type)
                    ->orderBy('md_scheme.updated_at','desc')
                    ->get();  
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
            $arr_cat_id=json_decode($request->arr_cat_id);
            $arr_subcat_id=json_decode($request->arr_subcat_id);
            if ($paginate=='A') {
                $paginate=999999999;
            }
            if (!empty($arr_amc_id) && !empty($arr_cat_id) && !empty($arr_subcat_id)) {
                // return 'hii';
                $data=Scheme::join('md_amc','md_amc.id','=','md_scheme.amc_id')
                    ->join('md_category','md_category.id','=','md_scheme.category_id')
                    ->join('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                    ->join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                    ->leftJoin('md_benchmark','md_benchmark.id','=','md_scheme.benchmark_id')
                    ->select('md_scheme.*','md_amc.amc_short_name as amc_short_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcate_name','md_rnt.rnt_name as rnt_name','md_benchmark.benchmark as benchmark')
                    ->where('md_scheme.delete_flag','N')
                    ->whereIn('md_scheme.amc_id',$arr_amc_id)
                    ->whereIn('md_scheme.category_id',$arr_cat_id)
                    ->whereIn('md_scheme.subcategory_id',$arr_subcat_id)
                    ->get();      
            } elseif ($search!='' && $amc_id!='' && $scheme_type!='') {
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
                        ->where('scheme_name','like', '%' . $search . '%')
                        ->get();      
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
                    ->select('md_scheme.*','md_amc.amc_name as amc_name','md_amc.amc_short_name as amc_short_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcate_name','md_rnt.rnt_name as rnt_name')
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
            throw $th;
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
                $data->benchmark_id=isset($request->benchmark_id)?$request->benchmark_id:NULL;

                $data->purchase_allowed=isset($request->purchase_allowed)?$request->purchase_allowed:NULL;
                $data->pip_multiple_amount=isset($request->pip_multiple_amount)?$request->pip_multiple_amount:NULL;
                $data->sip_allowed=isset($request->sip_allowed)?$request->sip_allowed:NULL;
                $data->swp_allowed=isset($request->swp_allowed)?$request->swp_allowed:NULL;
                $data->stp_allowed=isset($request->stp_allowed)?$request->stp_allowed:NULL;
                $data->switch_allowed=isset($request->switch_allowed)?$request->switch_allowed:NULL;
                $data->switch_min_amt=isset($request->switch_min_amt)?$request->switch_min_amt:NULL;
                $data->switch_mul_amt=isset($request->switch_mul_amt)?$request->switch_mul_amt:NULL;
                $data->exit_load=isset($request->exit_load)?$request->exit_load:NULL;

                $data->updated_by=Helper::modifyUser($request->user());
                $data->save();

                if ($request->scheme_type=='N') {
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
                            'benchmark_id'=>isset($request->benchmark_id)?$request->benchmark_id:NULL,

                            'purchase_allowed'=>isset($request->purchase_allowed)?$request->purchase_allowed:NULL,
                            'pip_multiple_amount'=>isset($request->pip_multiple_amount)?$request->pip_multiple_amount:NULL,
                            'sip_allowed'=>isset($request->sip_allowed)?$request->sip_allowed:NULL,
                            'swp_allowed'=>isset($request->swp_allowed)?$request->swp_allowed:NULL,
                            'stp_allowed'=>isset($request->stp_allowed)?$request->stp_allowed:NULL,
                            'switch_allowed'=>isset($request->switch_allowed)?$request->switch_allowed:NULL,
                            'switch_min_amt'=>isset($request->switch_min_amt)?$request->switch_min_amt:NULL,
                            'switch_mul_amt'=>isset($request->switch_mul_amt)?$request->switch_mul_amt:NULL,
                            'exit_load'=>isset($request->exit_load)?$request->exit_load:NULL,

                            'created_by'=>Helper::modifyUser($request->user()),
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
                            'benchmark'=>isset($request->benchmark)?$request->benchmark:NULL,
                            // 'growth_isin'=>$request->growth_isin,
                            // 'idcw_payout_isin'=>$request->idcw_payout_isin,
                            // 'idcw_reinvestment_isin'=>$request->idcw_reinvestment_isin,

                            'purchase_allowed'=>isset($request->purchase_allowed)?$request->purchase_allowed:NULL,
                            'pip_multiple_amount'=>isset($request->pip_multiple_amount)?$request->pip_multiple_amount:NULL,
                            'sip_allowed'=>isset($request->sip_allowed)?$request->sip_allowed:NULL,
                            'swp_allowed'=>isset($request->swp_allowed)?$request->swp_allowed:NULL,
                            'stp_allowed'=>isset($request->stp_allowed)?$request->stp_allowed:NULL,
                            'switch_allowed'=>isset($request->switch_allowed)?$request->switch_allowed:NULL,
                            'switch_min_amt'=>isset($request->switch_min_amt)?$request->switch_min_amt:NULL,
                            'switch_mul_amt'=>isset($request->switch_mul_amt)?$request->switch_mul_amt:NULL,
                            'exit_load'=>isset($request->exit_load)?$request->exit_load:NULL,

                            'created_by'=>Helper::modifyUser($request->user()),
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
                                    // 'created_by'=>Helper::modifyUser($request->user()),
                                ));      
                            }
                        }
                    }  
                }
            }  
            $data=Scheme::join('md_amc','md_amc.id','=','md_scheme.amc_id')
                    ->join('md_category','md_category.id','=','md_scheme.category_id')
                    ->join('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                    ->join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                    ->leftJoin('md_benchmark','md_benchmark.id','=','md_scheme.benchmark_id')
                    ->select('md_scheme.*','md_amc.amc_name as amc_name','md_amc.amc_short_name as amc_short_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcate_name','md_rnt.rnt_name as rnt_name','md_benchmark.benchmark as benchmark')
                    ->selectRaw('SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",16) as sip_amount_filter_1')
                    ->selectRaw('SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",20) as sip_amount_filter_2')
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",16), "\"", -1) as D_sip_min_F_amount') // D sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",20), "\"", -1) as D_sip_min_A_amount') // D sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",36), "\"", -1) as W_sip_min_F_amount') // W sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",40), "\"", -1) as W_sip_min_A_amount') // W sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",56), "\"", -1) as F_sip_min_F_amount') // F sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",60), "\"", -1) as F_sip_min_A_amount') // F sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",76), "\"", -1) as M_sip_min_F_amount') // M sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",80), "\"", -1) as M_sip_min_A_amount') // M sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",96), "\"", -1) as Q_sip_min_F_amount') // Q sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",100), "\"", -1) as Q_sip_min_A_amount') // Q sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",116), "\"", -1) as S_sip_min_F_amount') // S sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",120), "\"", -1) as S_sip_min_A_amount') // S sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",136), "\"", -1) as A_sip_min_F_amount') // A sip min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.sip_freq_wise_amt,"\"",140), "\"", -1) as A_sip_min_A_amount') // A sip min
                    ->selectRaw('SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",16) as swp_amount_filter_1')
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",16), "\"", -1) as D_swp_min_amount') // D swp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",32), "\"", -1) as W_swp_min_amount') // W swp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",48), "\"", -1) as F_swp_min_amount') // F swp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",64), "\"", -1) as M_swp_min_amount') // M swp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",80), "\"", -1) as Q_swp_min_amount') // Q swp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",96), "\"", -1) as S_swp_min_amount') // S swp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.swp_freq_wise_amt,"\"",112), "\"", -1) as A_swp_min_amount') // A swp min
                    ->selectRaw('SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",16) as stp_amount_filter_2')
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",16), "\"", -1) as D_stp_min_amount') // D stp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",32), "\"", -1) as W_stp_min_amount') // W stp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",48), "\"", -1) as F_stp_min_amount') // F stp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",64), "\"", -1) as M_stp_min_amount') // M stp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",80), "\"", -1) as Q_stp_min_amount') // Q stp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",96), "\"", -1) as S_stp_min_amount') // S stp min
                    ->selectRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",112), "\"", -1) as A_stp_min_amount') // A stp min
                    // ->where('md_scheme.delete_flag','N')
                    ->where('md_scheme.id',$data->id)
                    ->first(); 
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
                        $dd['sip_fresh_min_amt']=isset($value[7])?(string)$value[7]:"";
                        $dd['sip_add_min_amt']=isset($value[8])?(string)$value[8]:"";
                        array_push($sip_freq_wise_amt,$dd);
                        $ww['id']="W";
                        $ww['freq_name']="Weekly";
                        $ww['is_checked']=$this->freqWiseAmt($value[9], $value[10]);
                        $ww['sip_fresh_min_amt']=isset($value[9])?(string)$value[9]:"";
                        $ww['sip_add_min_amt']=isset($value[10])?(string)$value[10]:"";
                        array_push($sip_freq_wise_amt,$ww);
                        $ff['id']="F";
                        $ff['freq_name']="Fortnightly";
                        $ff['is_checked']=$this->freqWiseAmt($value[11], $value[12]);
                        $ff['sip_fresh_min_amt']=isset($value[11])?(string)$value[11]:"";
                        $ff['sip_add_min_amt']=isset($value[12])?(string)$value[12]:"";
                        array_push($sip_freq_wise_amt,$ff);
                        $mm['id']="M";
                        $mm['freq_name']="Monthly";
                        $mm['is_checked']=$this->freqWiseAmt($value[13], $value[14]);
                        $mm['sip_fresh_min_amt']=isset($value[13])?(string)$value[13]:"";
                        $mm['sip_add_min_amt']=isset($value[14])?(string)$value[14]:"";
                        array_push($sip_freq_wise_amt,$mm);
                        $qq['id']="Q";
                        $qq['freq_name']="Quarterly";
                        $qq['is_checked']=$this->freqWiseAmt($value[15], $value[16]);
                        $qq['sip_fresh_min_amt']=isset($value[15])?(string)$value[15]:"";
                        $qq['sip_add_min_amt']=isset($value[16])?(string)$value[16]:"";
                        array_push($sip_freq_wise_amt,$qq);
                        $ss['id']="S";
                        $ss['freq_name']="Semi Anually";
                        $ss['is_checked']=$this->freqWiseAmt($value[17], $value[18]);
                        $ss['sip_fresh_min_amt']=isset($value[17])?(string)$value[17]:"";
                        $ss['sip_add_min_amt']=isset($value[18])?(string)$value[18]:"";
                        array_push($sip_freq_wise_amt,$ss);
                        $aa['id']="A";
                        $aa['freq_name']="Anually";
                        $aa['is_checked']=$this->freqWiseAmt($value[19], $value[20]);
                        $aa['sip_fresh_min_amt']=isset($value[19])?(string)$value[19]:"";
                        $aa['sip_add_min_amt']=isset($value[20])?(string)$value[20]:"";
                        array_push($sip_freq_wise_amt,$aa);
                        // return $sip_freq_wise_amt;
                        // return json_encode($sip_freq_wise_amt);

                        // return $value[25];
                        $swp_freq_wise_amt=[];
                        $swp_dd_feq['id']="D";
                        $swp_dd_feq['freq_name']="Daily";
                        $swp_dd_feq['is_checked']=$this->freqWiseAmt1($value[23]);
                        $swp_dd_feq['sip_add_min_amt']=isset($value[23])?(string)$value[23]:"";
                        array_push($swp_freq_wise_amt,$swp_dd_feq);
                        $swp_ww['id']="W";
                        $swp_ww['freq_name']="Weekly";
                        $swp_ww['is_checked']=$this->freqWiseAmt1($value[24]);
                        $swp_ww['sip_add_min_amt']=isset($value[24])?(string)$value[24]:"";
                        array_push($swp_freq_wise_amt,$swp_ww);
                        $swp_ff['id']="F";
                        $swp_ff['freq_name']="Fortnightly";
                        $swp_ff['is_checked']=$this->freqWiseAmt1($value[25]);
                        $swp_ff['sip_add_min_amt']=isset($value[25])?(string)$value[25]:"";
                        array_push($swp_freq_wise_amt,$swp_ff);
                        $swp_mm['id']="M";
                        $swp_mm['freq_name']="Monthly";
                        $swp_mm['is_checked']=$this->freqWiseAmt1($value[26]);
                        $swp_mm['sip_add_min_amt']=isset($value[26])?(string)$value[26]:"";
                        array_push($swp_freq_wise_amt,$swp_mm);
                        $swp_qq['id']="Q";
                        $swp_qq['freq_name']="Quarterly";
                        $swp_qq['is_checked']=$this->freqWiseAmt1($value[27]);
                        $swp_qq['sip_add_min_amt']=isset($value[27])?(string)$value[27]:"";
                        array_push($swp_freq_wise_amt,$swp_qq);
                        $swp_ss['id']="S";
                        $swp_ss['freq_name']="Semi Anually";
                        $swp_ss['is_checked']=$this->freqWiseAmt1($value[28]);
                        $swp_ss['sip_add_min_amt']=isset($value[28])?(string)$value[28]:"";
                        array_push($swp_freq_wise_amt,$swp_ss);
                        $swp_aa['id']="A";
                        $swp_aa['freq_name']="Anually";
                        $swp_aa['is_checked']=$this->freqWiseAmt1($value[29]);
                        $swp_aa['sip_add_min_amt']=isset($value[29])?(string)$value[29]:"";
                        array_push($swp_freq_wise_amt,$swp_aa);
                        // return $swp_freq_wise_amt;

                        $stp_freq_wise_amt=[];
                        $stp_dd_frq['id']="D";
                        $stp_dd_frq['freq_name']="Daily";
                        $stp_dd_frq['is_checked']=$this->freqWiseAmt1($value[32]);
                        $stp_dd_frq['sip_add_min_amt']=isset($value[32])?(string)$value[32]:"";
                        array_push($stp_freq_wise_amt,$stp_dd_frq);
                        $stp_ww['id']="W";
                        $stp_ww['freq_name']="Weekly";
                        $stp_ww['is_checked']=$this->freqWiseAmt1($value[33]);
                        $stp_ww['sip_add_min_amt']=isset($value[33])?(string)$value[33]:"";
                        array_push($stp_freq_wise_amt,$stp_ww);
                        $stp_ff['id']="F";
                        $stp_ff['freq_name']="Fortnightly";
                        $stp_ff['is_checked']=$this->freqWiseAmt1($value[34]);
                        $stp_ff['sip_add_min_amt']=isset($value[34])?(string)$value[34]:"";
                        array_push($stp_freq_wise_amt,$stp_ff);
                        $stp_mm['id']="M";
                        $stp_mm['freq_name']="Monthly";
                        $stp_mm['is_checked']=$this->freqWiseAmt1($value[35]);
                        $stp_mm['sip_add_min_amt']=isset($value[35])?(string)$value[35]:"";
                        array_push($stp_freq_wise_amt,$stp_mm);
                        $stp_qq['id']="Q";
                        $stp_qq['freq_name']="Quarterly";
                        $stp_qq['is_checked']=$this->freqWiseAmt1($value[36]);
                        $stp_qq['sip_add_min_amt']=isset($value[36])?(string)$value[36]:"";
                        array_push($stp_freq_wise_amt,$stp_qq);
                        $stp_ss['id']="S";
                        $stp_ss['freq_name']="Semi Anually";
                        $stp_ss['is_checked']=$this->freqWiseAmt1($value[37]);
                        $stp_ss['sip_add_min_amt']=isset($value[37])?(string)$value[37]:"";
                        array_push($stp_freq_wise_amt,$stp_ss);
                        $stp_aa['id']="A";
                        $stp_aa['freq_name']="Anually";
                        $stp_aa['is_checked']=$this->freqWiseAmt1($value[38]);
                        $stp_aa['sip_add_min_amt']=isset($value[38])?(string)$value[38]:"";
                        array_push($stp_freq_wise_amt,$stp_aa);
                        // return $stp_freq_wise_amt;
                        $is_has=Scheme::where('scheme_name',$value[3])->get();
                        // return $is_has;
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
                        $dd['sip_fresh_min_amt']=isset($value[11])?(string)$value[11]:"";
                        $dd['sip_add_min_amt']=isset($value[12])?(string)$value[12]:"";
                        array_push($sip_freq_wise_amt,$dd);
                        $ww['id']="W";
                        $ww['freq_name']="Weekly";
                        $ww['is_checked']=$this->freqWiseAmt($value[13], $value[14]);
                        $ww['sip_fresh_min_amt']=isset($value[13])?(string)$value[13]:"";
                        $ww['sip_add_min_amt']=isset($value[14])?(string)$value[14]:"";
                        array_push($sip_freq_wise_amt,$ww);
                        $ff['id']="F";
                        $ff['freq_name']="Fortnightly";
                        $ff['is_checked']=$this->freqWiseAmt($value[15], $value[16]);
                        $ff['sip_fresh_min_amt']=isset($value[15])?(string)$value[15]:"";
                        $ff['sip_add_min_amt']=isset($value[16])?(string)$value[16]:"";
                        array_push($sip_freq_wise_amt,$ff);
                        $mm['id']="M";
                        $mm['freq_name']="Monthly";
                        $mm['is_checked']=$this->freqWiseAmt($value[17], $value[18]);
                        $mm['sip_fresh_min_amt']=isset($value[17])?(string)$value[17]:"";
                        $mm['sip_add_min_amt']=isset($value[18])?(string)$value[18]:"";
                        array_push($sip_freq_wise_amt,$mm);
                        $qq['id']="Q";
                        $qq['freq_name']="Quarterly";
                        $qq['is_checked']=$this->freqWiseAmt($value[19], $value[20]);
                        $qq['sip_fresh_min_amt']=isset($value[19])?(string)$value[19]:"";
                        $qq['sip_add_min_amt']=isset($value[20])?(string)$value[20]:"";
                        array_push($sip_freq_wise_amt,$qq);
                        $ss['id']="S";
                        $ss['freq_name']="Semi Anually";
                        $ss['is_checked']=$this->freqWiseAmt($value[21], $value[22]);
                        $ss['sip_fresh_min_amt']=isset($value[21])?(string)$value[21]:"";
                        $ss['sip_add_min_amt']=isset($value[22])?(string)$value[22]:"";
                        array_push($sip_freq_wise_amt,$ss);
                        $aa['id']="A";
                        $aa['freq_name']="Anually";
                        $aa['is_checked']=$this->freqWiseAmt($value[23], $value[24]);
                        $aa['sip_fresh_min_amt']=isset($value[23])?(string)$value[23]:"";
                        $aa['sip_add_min_amt']=isset($value[24])?(string)$value[24]:"";
                        array_push($sip_freq_wise_amt,$aa);
                        // return $sip_freq_wise_amt;

                        // return $value[25];
                        $swp_freq_wise_amt=[];
                        $swp_dd['id']="D";
                        $swp_dd['freq_name']="Daily";
                        $swp_dd['is_checked']=$this->freqWiseAmt1($value[27]);
                        $swp_dd['sip_add_min_amt']=isset($value[27])?(string)$value[27]:"";
                        array_push($swp_freq_wise_amt,$swp_dd);
                        $swp_ww['id']="W";
                        $swp_ww['freq_name']="Weekly";
                        $swp_ww['is_checked']=$this->freqWiseAmt1($value[28]);
                        $swp_ww['sip_add_min_amt']=isset($value[28])?(string)$value[28]:"";
                        array_push($swp_freq_wise_amt,$swp_ww);
                        $swp_ff['id']="F";
                        $swp_ff['freq_name']="Fortnightly";
                        $swp_ff['is_checked']=$this->freqWiseAmt1($value[29]);
                        $swp_ff['sip_add_min_amt']=isset($value[29])?(string)$value[29]:"";
                        array_push($swp_freq_wise_amt,$swp_ff);
                        $swp_mm['id']="M";
                        $swp_mm['freq_name']="Monthly";
                        $swp_mm['is_checked']=$this->freqWiseAmt1($value[30]);
                        $swp_mm['sip_add_min_amt']=isset($value[30])?(string)$value[30]:"";
                        array_push($swp_freq_wise_amt,$swp_mm);
                        $swp_qq['id']="Q";
                        $swp_qq['freq_name']="Quarterly";
                        $swp_qq['is_checked']=$this->freqWiseAmt1($value[31]);
                        $swp_qq['sip_add_min_amt']=isset($value[31])?(string)$value[31]:"";
                        array_push($swp_freq_wise_amt,$swp_qq);
                        $swp_ss['id']="S";
                        $swp_ss['freq_name']="Semi Anually";
                        $swp_ss['is_checked']=$this->freqWiseAmt1($value[32]);
                        $swp_ss['sip_add_min_amt']=isset($value[32])?(string)$value[32]:"";
                        array_push($swp_freq_wise_amt,$swp_ss);
                        $swp_aa['id']="A";
                        $swp_aa['freq_name']="Anually";
                        $swp_aa['is_checked']=$this->freqWiseAmt1($value[33]);
                        $swp_aa['sip_add_min_amt']=isset($value[33])?(string)$value[33]:"";
                        array_push($swp_freq_wise_amt,$swp_aa);
                        // return $swp_freq_wise_amt;

                        $stp_freq_wise_amt=[];
                        $stp_dd['id']="D";
                        $stp_dd['freq_name']="Daily";
                        $stp_dd['is_checked']=$this->freqWiseAmt1($value[36]);
                        $stp_dd['sip_add_min_amt']=isset($value[36])?(string)$value[36]:"";
                        array_push($stp_freq_wise_amt,$stp_dd);
                        $stp_ww['id']="W";
                        $stp_ww['freq_name']="Weekly";
                        $stp_ww['is_checked']=$this->freqWiseAmt1($value[37]);
                        $stp_ww['sip_add_min_amt']=isset($value[37])?(string)$value[37]:"";
                        array_push($stp_freq_wise_amt,$stp_ww);
                        $stp_ff['id']="F";
                        $stp_ff['freq_name']="Fortnightly";
                        $stp_ff['is_checked']=$this->freqWiseAmt1($value[38]);
                        $stp_ff['sip_add_min_amt']=isset($value[38])?(string)$value[38]:"";
                        array_push($stp_freq_wise_amt,$stp_ff);
                        $stp_mm['id']="M";
                        $stp_mm['freq_name']="Monthly";
                        $stp_mm['is_checked']=$this->freqWiseAmt1($value[39]);
                        $stp_mm['sip_add_min_amt']=isset($value[39])?(string)$value[39]:"";
                        array_push($stp_freq_wise_amt,$stp_mm);
                        $stp_qq['id']="Q";
                        $stp_qq['freq_name']="Quarterly";
                        $stp_qq['is_checked']=$this->freqWiseAmt1($value[40]);
                        $stp_qq['sip_add_min_amt']=isset($value[40])?(string)$value[40]:"";
                        array_push($stp_freq_wise_amt,$stp_qq);
                        $stp_ss['id']="S";
                        $stp_ss['freq_name']="Semi Anually";
                        $stp_ss['is_checked']=$this->freqWiseAmt1($value[41]);
                        $stp_ss['sip_add_min_amt']=isset($value[41])?(string)$value[41]:"";
                        array_push($stp_freq_wise_amt,$stp_ss);
                        $stp_aa['id']="A";
                        $stp_aa['freq_name']="Anually";
                        $stp_aa['is_checked']=$this->freqWiseAmt1($value[42]);
                        $stp_aa['sip_add_min_amt']=isset($value[42])?(string)$value[42]:"";
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


    public function filterCriteria($rawQuery,$amc_id,$cat_id,$subcat_id,$scheme_id,$search_scheme_id)
    {
        $queryString1='md_scheme.amc_id';
        $rawQuery.=Helper::WhereRawQuery($amc_id,$rawQuery,$queryString1);
        $queryString1='md_scheme.category_id';
        $rawQuery.=Helper::WhereRawQuery($cat_id,$rawQuery,$queryString1);
        $queryString1='md_scheme.subcategory_id';
        $rawQuery.=Helper::WhereRawQuery($subcat_id,$rawQuery,$queryString1);
        $queryString1='md_scheme.id';
        $rawQuery.=Helper::WhereRawQuery($scheme_id,$rawQuery,$queryString1);
        $queryString1='md_scheme.id';
        $rawQuery.=Helper::WhereRawQuery($search_scheme_id,$rawQuery,$queryString1);
        return $rawQuery;
    }


    public function merge(Request $request)
    {
        try {
            // return $request;
            $data=[];
            $scheme_ids=json_decode($request->scheme_ids);
            $is_has=Scheme::where('scheme_name',$request->scheme_name)->where('delete_flag','N')->get();
            if (count($is_has) > 0) {
                return Helper::WarningResponse(parent::ALREADY_EXIST);
            }else {
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
                $data=Scheme::create(array(
                    'product_id'=>$request->product_id,
                    'amc_id'=>$request->amc_id,
                    'category_id'=>$request->category_id,
                    'subcategory_id'=>$request->subcategory_id,
                    'scheme_name'=>$request->scheme_name,
                    'scheme_type'=>$request->scheme_type,
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
                    'benchmark_id'=>isset($request->benchmark_id)?$request->benchmark_id:NULL,
                    // 'created_by'=>'',
                ));  
                
                foreach ($scheme_ids as $key => $scheme_id) {
                    $data1=Scheme::find($scheme_id);
                    $data1->merge_flag='M';
                    $data1->merge_id=$data->id;
                    $data1->effective_date=$request->effective_date;
                    $data1->save();
                }
            } 
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function replace(Request $request)
    {
        try {
            // return $request;
            $data=[];
            $scheme_id=json_decode($request->scheme_ids)[0];
            $is_has=Scheme::where('scheme_name',$request->scheme_name)->where('delete_flag','N')->get();
            if (count($is_has) > 0) {
                return Helper::WarningResponse(parent::ALREADY_EXIST);
            }else {
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
                $data=Scheme::create(array(
                    'product_id'=>$request->product_id,
                    'amc_id'=>$request->amc_id,
                    'category_id'=>$request->category_id,
                    'subcategory_id'=>$request->subcategory_id,
                    'scheme_name'=>$request->scheme_name,
                    'scheme_type'=>$request->scheme_type,
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
                    'benchmark_id'=>isset($request->benchmark_id)?$request->benchmark_id:NULL,
                    // 'created_by'=>'',
                ));  
                
                $data1=Scheme::find($scheme_id);
                $data1->merge_flag='R';
                $data1->merge_id=$data->id;
                $data1->effective_date=$request->effective_date;
                $data1->save();
            } 
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function acquisition(Request $request)
    {
        try {
            $data=[];

            $amc_id=json_decode($request->scheme_ids)[0];
            $acquisition_to_id=$request->acquisition_to_id;
            $is_has=Scheme::where('scheme_name',$request->scheme_name)->where('delete_flag','N')->get();
            if (count($is_has) > 0) {
                return Helper::WarningResponse(parent::ALREADY_EXIST);
            }else {

                $data1=Scheme::find($amc_id);
                $data1->merge_flag='A';
                $data1->merge_id=$acquisition_to_id;
                $data1->effective_date=$request->effective_date;
                $data1->save();
            }  

        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}