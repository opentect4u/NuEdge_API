<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\ClientType;
use Validator;

class ClientTypeController extends Controller
{
    public function searchDetails(Request $request)
    {
        try {
            $paginate=$request->paginate;
            $type_name=$request->type_name;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            if ($paginate=='A') {
                $paginate=999999999;
            }
            if ($type_name) {
                $data=ClientType::where('type_name','like', '%' . $type_name . '%')
                    ->paginate($paginate); 
            }else {
                $data=ClientType::paginate($paginate); 
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
            $type_name=$request->type_name;
            if ($type_name) {
                $data=ClientType::where('type_name','like', '%' . $type_name . '%')->get(); 
            } else {
                $data=ClientType::get(); 
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
            $flag=$request->flag;
            if ($search!='') {
                $data=ClientType::orWhere('type_name','like', '%' . $search . '%')
                ->get();      
            }else if ($flag) {
                $data=ClientType::where('flag',$flag)->orderBy('type_name','ASC')->get();      
            }elseif ($paginate!='') {
                $data=ClientType::paginate($paginate);      
            } else {
                $data=ClientType::get();      
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
            'type_name' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            if ($request->id > 0) {
                $data=ClientType::find($request->id);
                $data->type_name=$request->type_name;
                $data->save();
            }else{
                $is_has=ClientType::where('type_name',$request->type_name)->count();
                if ($is_has>0) {
                    $msg='Already exist';
                    return Helper::ErrorResponse($msg);
                }else {
                    $data=ClientType::create(array(
                        'type_name'=>$request->type_name,
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
