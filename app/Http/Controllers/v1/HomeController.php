<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        try {
            $data=$request->user();
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function chnagePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if($validator->fails()){
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }

        try {
            $old_password=$request->old_password;
            $password=$request->password;
            $c_password=$request->c_password;
            $email=$request->user()->email;
            $is_has=User::where('email',$email)->first();
            if ($is_has) {
                if (Hash::check($old_password, $is_has->password)) {
                    // The passwords match...
                    $update_data=User::find($is_has->id);
                    $update_data->password=Hash::make($password);
                    $update_data->updated_by=Helper::modifyUser($request->user());
                    $update_data->save();
                    $data='Your password has been changed successfully';
                }else {
                    return Helper::WarningResponse('Old password doesnt match!');
                }
            }else {
                return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
                // return Helper::WarningResponse('');
            }
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        } 
        return Helper::SuccessResponse($data);
    }
}
