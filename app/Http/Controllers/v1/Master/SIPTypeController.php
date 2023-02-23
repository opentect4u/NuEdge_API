<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\SIPType;
use Validator;

class SIPTypeController extends Controller
{
    public function searchDetails(Request $request)
    {
        try {
            $paginate=$request->paginate;
            $sip_type_name=$request->sip_type_name;
            if ($paginate=='A') {
                $paginate=999999999;
            }
            if ($sip_type_name) {
                $data=SIPType::where('sip_type_name','like', '%' . $sip_type_name . '%')
                    ->paginate($paginate); 
            }else {
                $data=SIPType::paginate($paginate); 
            }
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
    public function export(Request $request)
    {
        try {
            $paginate=$request->paginate;
            $sip_type_name=$request->sip_type_name;
            if ($sip_type_name) {
                $data=SIPType::where('sip_type_name','like', '%' . $sip_type_name . '%')->get(); 
            } else {
                $data=SIPType::get(); 
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
            $paginate=$request->paginate;
            $product_id=$request->product_id;
            if ($search!='') {
                $data=SIPType::orWhere('sip_type_name','like', '%' . $search . '%')
                ->get();      
            }elseif ($paginate!='') {
                $data=SIPType::paginate($paginate);      
            } else {
                $data=SIPType::get();      
            }
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function createUpdate(Request $request)
    {
        // $validator = Validator::make(request()->all(),[
        //     'trans_type_id' =>'required',
        //     'trns_name' =>'required',
        // ]);
    
        // if($validator->fails()) {
        //     $errors = $validator->errors();
        //     return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        // }
        try {
            if ($request->id > 0) {
                $data=SIPType::find($request->id);
                $data->sip_type_name=$request->sip_type_name;
                $data->save();
            }else{
                $is_has=SIPType::where('sip_type_name',$request->sip_type_name)->count();
                if ($is_has>0) {
                    $msg='Already exist';
                    return Helper::ErrorResponse($msg);
                }else {
                    $data=SIPType::create(array(
                        'sip_type_name'=>$request->sip_type_name,
                        // 'created_by'=>'',
                    ));      
                }
            }    
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

  
}
