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
        return $request;
        try {
            $date=$request->date;
            $data=MutualFund::whereDate('entry_date',date('Y-m-d',strtotome($date)))->get();
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}
