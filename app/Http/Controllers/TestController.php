<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\{Scheme,MutualFundTransaction};

class TestController extends Controller
{
    public function index()
    {

        $data=[];
        // $aArray = file('C:\Users\Chitta\Downloads\28072023105405_152496138R49.txt', FILE_IGNORE_NEW_LINES);
        // $aArray = file('C:\Users\Chitta\Downloads\28072023105405_152496138R49.txt');
        // $aArray = file('C:\Users\Chitta\Downloads\28072023151755_152516610R49_new.txt',FILE_IGNORE_NEW_LINES);
        $aArray = file('C:\Users\Chitta\Documents\Nuedge-Online\31_07_2023_transaction\6093033632027015AH8FDJOJ3JDQKKHM6JPO5LIIHF2P17098625196BMB152683051R2\31072023151059_152683051R2.txt',FILE_IGNORE_NEW_LINES);
        
        
        // return $aArray[0];
        // return count($aArray);
        $start=0;
        $end=100;
        for ($i=$start; $i < $end ; $i++) { 
            // return $aArray[$i];
            $exp_data=explode("\t",$aArray[$i]);
            // return count($exp_data);
            return $exp_data;
            
            // return $exp_data[0];
        }
        // foreach($aArray as $key =>$line) {
        //     return $line;
        //     // if ($key > 0) {
        //     //     return $line;
        //     // }
        //     // $exp_data=explode("\t",$line);
        //     // return $exp_data;
        //     // return $exp_data[0];
            
        //     array_push($data,$line);
        // }
        // return $data;

        // ===========================================================================
        // if (str_starts_with('http://www.google.com', 'httppp')) {
        //     $val='if';
        // }else {
        //     $val='else';
        // }
        // return $val;
    }

    public function test111()
    {
        $scheme_type='O';
        DB::enableQueryLog();

        $data=Scheme::join('md_amc','md_amc.id','=','md_scheme.amc_id')
                ->join('md_category','md_category.id','=','md_scheme.category_id')
                ->join('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                ->join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
                ->select('md_scheme.*','md_amc.amc_name as amc_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcate_name','md_rnt.rnt_name as rnt_name')
                
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
                
                ->selectRaw('cast(SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",98), "\"", -1)AS UNSIGNED) as A_stp_min_amount_11') // A stp min
                // order by cast(SUBSTRING_INDEX(replace(book_page_name,'.pdf',''),'_',-1)AS UNSIGNED)");

                ->where('md_scheme.delete_flag','N')
                ->where('md_scheme.scheme_type',$scheme_type)
                ->orderBy('md_scheme.updated_at','desc')
                // ->whereRaw('SUBSTRING_INDEX(SUBSTRING_INDEX(md_scheme.stp_freq_wise_amt,"\"",56), "\"", -1) = "500"')
                ->take(10)
                ->get(); 
        // DB::getQueryLog();
        // dd(DB::getQueryLog());
        // dd($data);

        return $data;
    }

    public function test(){

        // $master_arr = array('CAT','DOG','RABBIT');
        // $log_arr = array('CAT','CAR','RABBIT','DOG','PHONE');
        // $unique=array_unique( array_merge($master_arr, $log_arr) );
        // $master_arr=array_diff($unique, $master_arr);
        // // print_r($master_arr);
        // return $master_arr;

        // $A = array(1,2,3,4,5,6,7,8);
        // $B = array(1,2,3,4);

        // $C = array_intersect($A,$B);  //equals (1,2,3,4)
        // $A = array_diff($A,$B);   
        // return $A;

        DB::enableQueryLog();
        $euin_no=MutualFundTransaction::select('euin_no')->where('folio_no','3101929013')->where('product_code','178ARRG')
        ->orderBy('trans_date','ASC')
            // ->get();
            ->first();
                        // $euin_no=MutualFundTransaction::where('folio_no',6017105704)
                        // ->where('euin_no','!=','')->first(['euin_no'])->value('euin_no');

        dd(DB::getQueryLog());
        // return $euin_no;
    }
}
