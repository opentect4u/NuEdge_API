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
        // $aArray = file('C:\Users\Chitta\Downloads\28072023151755_152516610R49_new.txt',FILE_IGNORE_NEW_LINES);
        $aArray = file('C:\Users\Chitta\Documents\Nuedge-Online\31_07_2023_transaction\6093033632027015AH8FDJOJ3JDQKKHM6JPO5LIIHF2P17098625196BMB152683051R2\31072023151059_152683051R2.txt',FILE_IGNORE_NEW_LINES);
        
        
        // return $aArray[0];
        // return count($aArray);
        $start=0;
        $end=100;
        for ($i=$start; $i < $end ; $i++) { 
            // return $aArray[$i];
            $exp_data=explode("\t",$aArray[$i]);
            // return count($exp_data);
            return $exp_data;
            
            // return $exp_data[0];
        }
        // foreach($aArray as $key =>$line) {
        //     return $line;
        //     // if ($key > 0) {
        //     //     return $line;
        //     // }
        //     // $exp_data=explode("\t",$line);
        //     // return $exp_data;
        //     // return $exp_data[0];
            
        //     array_push($data,$line);
        // }
        // return $data;

        // ===========================================================================
        // if (str_starts_with('http://www.google.com', 'httppp')) {
        //     $val='if';
        // }else {
        //     $val='else';
        // }
        // return $val;
    }
}
