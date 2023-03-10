<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\City;
use Validator;

class CityController extends Controller
{
    public function index(Request $request)
    {
        try {  
            $search=$request->search;
            $district_id=$request->district_id;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            if ($search!='') {
                $data=City::where('name','like', '%' . $search . '%')->get();      
            }elseif ($district_id!='') {
                $data=City::where('district_id',$district_id)->get();   
            } else{
                $data=City::get();   
            }   
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}
