<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index()
    {
        // return 'hii';

        // $string_n='';
        // $val=substr( $string_n, 0, 4 ) === "http"
        if (str_starts_with('http://www.google.com', 'httppp')) {
            $val='if';
        }else {
            $val='else';
        }

        return $val;
    }
}
