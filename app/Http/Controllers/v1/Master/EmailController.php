<?php

namespace App\Http\Controllers\v1\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\Email;
use Validator;

class EmailController extends Controller
{
    public function searchDetails(Request $request)
    {
        try {
            $paginate=$request->paginate;
            $event=$request->event;
            $sort_by=$request->sort_by;
            $column_name=$request->column_name;
            if ($paginate=='A') {
                $paginate=999999999;
            }
            if ($event) {
                $data=Email::where('event','like', '%' . $event . '%')
                    ->paginate($paginate); 
            }else {
                $data=Email::paginate($paginate); 
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
            $event=$request->event;
            if ($event) {
                $data=Email::where('event','like', '%' . $event . '%')->get(); 
            } else {
                $data=Email::get(); 
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
                $data=Email::orWhere('event','like', '%' . $search . '%')
                ->get();      
            }elseif ($paginate!='') {
                $data=Email::paginate($paginate);      
            } else {
                $data=Email::get();      
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
            'event' =>'required',
            'subject' =>'required',
        ]);
    
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        try {
            if ($request->id > 0) {
                $data=Email::find($request->id);
                $data->event=$request->event;
                $data->subject=$request->subject;
                $data->body=$request->body;
                $data->save();
            }else{
                $is_has=Email::where('event',$request->event)->count();
                if ($is_has>0) {
                    $msg='Already exist';
                    return Helper::ErrorResponse($msg);
                }else {
                    $data=Email::create(array(
                        'event'=>$request->event,
                        'subject'=>$request->subject,
                        'body'=>$request->body,
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
