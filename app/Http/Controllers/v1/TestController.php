<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\AMC;

class TestController extends Controller
{
    public function ShowIp(Request $request)
    {
        $data=$request->ip();
        return Helper::SuccessResponse($data);
        return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
    }

    public function index1()
    {
        // return "hii";


        $paginate=10;
        $data=AMC::join('md_rnt','md_rnt.id','=','md_amc.rnt_id')
            ->select('md_amc.*','md_rnt.rnt_name as rnt_name')
            ->orderBy('md_amc.updated_at','DESC')
            ->paginate($paginate);   
        return $data;
        $start="2022-01-04";
        $end=date('Y-m-d');
        // $months=[];
        $monthsarray=CarbonPeriod::create($start, '1 month', $end);
        foreach ($monthsarray as  $key=>$month) {
            // return  $month;
            // $months[$key] = $month->format('F Y');
            // return $month->format('Y-m-d');
            $dt=$month->format('Y-m-d');
            $data['month']= $month->format('F Y');
            if ($key==0) {
                $data['dates']='Start date : '.$start.' - Last day : '. date("Y-m-t", strtotime($dt)); 
            }else if ($key==(count($monthsarray)-1)) {
                $data['dates']='Start date : '.date("Y-m-01", strtotime($dt)).' - Last day : '. $end; 
            } else {
                $data['dates']='Start date : '.date("Y-m-01", strtotime($dt)).' - Last day : '. date("Y-m-t", strtotime($dt)); 
            }
            $months[$month->format('m-Y')] = $data;
            // array_push($months,$month->format('F Y'));
        }
        return $months;
    }

    public function index2(Request $request)
    {
        $words = explode(" ", "Community College company District");
        $acronym =  mb_substr($words[0], 0, 1).mb_substr($words[(count($words)-1)], 0, 1);
        return $acronym;

        foreach ($words as $key=>$w) {
            return $w;
            if ($key==0) {
                $acronym .= mb_substr($w, 0, 1);
            }elseif($key==(count($words)-1)){
                $acronym .= mb_substr($w, 0, 1);
            }
        }
        return $acronym;
        return "hii";
        # code...
    }
}
