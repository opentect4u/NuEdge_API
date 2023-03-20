<?php

namespace App\Http\Controllers\v1\INSOperation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Insurance,Client,InsFormReceived};
use Validator;
use Illuminate\Support\Carbon;
use Mail;
use App\Mail\Master\SendAckEmail;
use App\Models\Email;

class ManualUpdateController extends Controller
{
    public function searchDetails(Request $request)
    {
        try {
            
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function update(Request $request)
    {
        try {
            $tin_no=$request->tin_no;
            
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}
