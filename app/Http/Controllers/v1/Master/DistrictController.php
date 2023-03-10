<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\District;
use Validator;

class DistrictController extends Controller
{
    public function index(Request $request)
    {
        try {  
            $search=$request->search;
            $state_id=$request->state_id;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            if ($search!='') {
                $data=District::where('name','like', '%' . $search . '%')->get();      
            }elseif ($state_id!='') {
                $data=District::where('state_id',$state_id)->get();   
            } else{
                $data=District::get();   
            }   
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}
