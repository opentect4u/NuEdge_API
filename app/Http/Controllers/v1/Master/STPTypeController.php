<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\STPType;
use Validator;

class STPTypeController extends Controller
{
    public function searchDetails(Request $request)
    {
        try {
            $paginate=$request->paginate;
            $stp_type_name=$request->stp_type_name;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            if ($paginate=='A') {
                $paginate=999999999;
            }
            // if ($sort_by && $column_name) {
            //     $data=STPType::where('stp_type_name','like', '%' . $stp_type_name . '%')
            //         ->orderBy($column_name,$sort_by)
            //         ->paginate($paginate); 
            // }else
            if ($stp_type_name) {
                $data=STPType::where('stp_type_name','like', '%' . $stp_type_name . '%')
                    ->get(); 
            }else {
                $data=STPType::get(); 
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
            $stp_type_name=$request->stp_type_name;
            if ($stp_type_name) {
                $data=STPType::where('stp_type_name','like', '%' . $stp_type_name . '%')->get(); 
            } else {
                $data=STPType::get(); 
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
                $data=STPType::orWhere('stp_type_name','like', '%' . $search . '%')
                ->get();      
            }elseif ($paginate!='') {
                $data=STPType::paginate($paginate);      
            } else {
                $data=STPType::get();      
            }
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function createUpdate(Request $request)
    {
        $validator = Validator::make(request()->all(),[
            'stp_type_name' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            if ($request->id > 0) {
                $data=STPType::find($request->id);
                $data->stp_type_name=$request->stp_type_name;
                $data->updated_by=Helper::modifyUser($request->user());
                $data->save();
            }else{
                $is_has=STPType::where('stp_type_name',$request->stp_type_name)->count();
                if ($is_has>0) {
                    $msg='Already exist';
                    return Helper::ErrorResponse($msg);
                }else {
                    $data=STPType::create(array(
                        'stp_type_name'=>$request->stp_type_name,
                        'created_by'=>Helper::modifyUser($request->user()),
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