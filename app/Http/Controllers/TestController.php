<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index()
    {

        $data=[];
        // $aArray = file('C:\Users\Chitta\Downloads\28072023105405_152496138R49.txt', FILE_IGNORE_NEW_LINES);
        // $aArray = file('C:\Users\Chitta\Downloads\28072023105405_152496138R49.txt');
        $aArray = file('C:\Users\Chitta\Downloads\28072023151755_152516610R49_new.txt',FILE_IGNORE_NEW_LINES);
        
        // return $aArray;
        // return count($aArray);
        // for ($i=0; $i < count($aArray) ; $i++) { 
        //     return $aArray[$i];
        // }
        foreach($aArray as $key =>$line) {
            // return $line;
            // if ($key > 0) {
            //     return $line;
            // }
            // $exp_data=explode("\t",$line);
            // return $exp_data;
            // return $exp_data[0];
            
            array_push($data,$line);
        }
        return $data;

        // ===========================================================================
        // if (str_starts_with('http://www.google.com', 'httppp')) {
        //     $val='if';
        // }else {
        //     $val='else';
        // }
        // return $val;
    }
}
