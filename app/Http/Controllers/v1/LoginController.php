<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Helpers\Helper;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user(); 

            // Auth::user()->tokens->each(function($token, $key) {
            //     $token->delete();
            // });
            $success['token'] =  $user->createToken('MyNuedgeApp')->accessToken; 
            $success['user'] =  $user;
            // return $success;
        } else { 
            return Helper::loginErrorResponse('Username and password don`t match');
        } 
        return Helper::SuccessResponse($success);
    }

    public function logout(Request $request)
    {
        try {
            // $user = Auth::user()->token();
            // $user->revoke();
            $request->user()->token()->revoke();
            $success='Successfully logged out';
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($success);
    }
}
