<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{
    Holiday,
    Client
};
use Illuminate\Support\Facades\Crypt;

class HolidayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $data = Holiday::where('occ_date','>',date('Y-m-d'))->orderBy('occ_date','ASC')->get();
            // $data = Holiday::where('occ_date','>',date('Y-m-d'))->orderBy('occ_date','ASC')->pluck('occ_date')->all();
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}