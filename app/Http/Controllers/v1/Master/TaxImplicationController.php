<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\TaxImplication;
use Validator;

class TaxImplicationController extends Controller
{
    public function index(Request $request)
    {
        try {  
            $search=$request->search;
            // $search=$request->search;
            $paginate=$request->paginate;
            $product_id=$request->product_id;
            if ($search!='') {
                $data=TaxImplication::where('product_id',$product_id)
                    ->orWhere('trns_type','like', '%' . $search . '%')->get();      
            }elseif ($paginate!='') {
                $data=TaxImplication::where('product_id',$product_id)
                    ->orderBy('updated_at','DESC')
                    ->paginate($paginate);      
            }else{
                $data=TaxImplication::get();      
            }
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}