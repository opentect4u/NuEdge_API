<?php

namespace App\Http\Controllers\v1\Cron;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\Scheme;
use Validator;
use Excel;
use App\Imports\SchemeImport;

class SchemeController extends Controller
{
    public function nfoTOongoing(Request $request)
    {
        try {
            // return $request;
            $scheme_type='N';
            $datas=Scheme::where('scheme_type',$scheme_type)->get();   
            // return $datas;
            foreach ($datas as $key => $data) {
                $id=$data->id;
                if ($data->nfo_reopen_dt < date('Y-m-d')) {
                    // return $data->nfo_reopen_dt;
                    $ud=Scheme::find($id);
                    $ud->scheme_type='O';
                    $ud->save();
                }
            }
            return "Success";
        } catch (\Throwable $th) {
            //throw $th;
            return 'error';
        }
         
    }
}
