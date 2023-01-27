<?php

namespace App\Http\Controllers\v1\Operation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\MutualFund;
use Validator;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // return $request;
        try {
            $date=$request->date;
            // $date=date('Y-m-d');
            $data=MutualFund::join('md_client','md_client.client_code','=','td_mutual_fund.first_client_code')
                ->join('md_amc','md_amc.id','=','td_mutual_fund.amc_id')
                ->join('md_scheme','md_scheme.id','=','td_mutual_fund.trans_scheme_to')
                ->join('md_rnt','md_rnt.id','=','td_mutual_fund.rnt_login_at')
                ->join('md_trans','md_trans.id','=','td_mutual_fund.trans_type')
                ->select('td_mutual_fund.*','md_client.client_name as client_name','md_amc.amc_name as amc_name','md_scheme.scheme_name as scheme_to_name','md_rnt.rnt_name as rnt_name','md_trans.trns_name as trans_name')
                ->whereDate('td_mutual_fund.entry_date',date('Y-m-d',strtotime($date)))
                ->get();
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}
