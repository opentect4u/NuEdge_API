<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\SWPType;
use Validator;

class SWPTypeController extends Controller
{
    public function searchDetails(Request $request)
    {
        try {
            $paginate=$request->paginate;
            $swp_type_name=$request->swp_type_name;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            if ($paginate=='A') {
                $paginate=999999999;
            }
            if ($swp_type_name) {
                $data=SWPType::where('swp_type_name','like', '%' . $swp_type_name . '%')
                    ->paginate($paginate); 
            }else {
                $data=SWPType::paginate($paginate); 
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
            $swp_type_name=$request->swp_type_name;
            if ($swp_type_name) {
                $data=SWPType::where('swp_type_name','like', '%' . $swp_type_name . '%')->get(); 
            } else {
                $data=SWPType::get(); 
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
                $data=SWPType::orWhere('swp_type_name','like', '%' . $search . '%')
                ->get();      
            }elseif ($paginate!='') {
                $data=SWPType::paginate($paginate);      
            } else {
                $data=SWPType::get();      
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
            'swp_type_name' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            if ($request->id > 0) {
                $data=SWPType::find($request->id);
                $data->swp_type_name=$request->swp_type_name;
                $data->updated_by=Helper::modifyUser($request->user());
                $data->save();
            }else{
                $is_has=SWPType::where('swp_type_name',$request->swp_type_name)->count();
                if ($is_has>0) {
                    $msg='Already exist';
                    return Helper::ErrorResponse($msg);
                }else {
                    $data=SWPType::create(array(
                        'swp_type_name'=>$request->swp_type_name,
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