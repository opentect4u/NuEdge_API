<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    const DATA_FETCH_ERROR = 'Currently we are facing some data fetching problem. Please try again.';
    const DATA_FETCH_SUCCESS = 'Successfull';
    const DATA_SAVE_ERROR = 'Currently we are facing some data saving problem. Please try again.';
    const ALREADY_EXIST = 'Already exist.';
    const IP_WHITELIST_ERROR = 'You can not access ';
    const POST_METHOD_ACCESS_ERROR='Method is not allowed for the requested route.';
    const CONTROLLER_NOT_FOUND='Controller does not exist.';
    const METHOD_NOT_FOUND='Method does not exist.';
    const VALIDATION_ERROR='Some validation related error.';
    const IMPORT_CSV_ERROR='Error in importing csv.';
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
