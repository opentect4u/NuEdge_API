<?php

namespace App\Http\Controllers\v1\CusService;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{
    Client,
    User
};
use DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     * show also list of users
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $data=User::get();
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

}