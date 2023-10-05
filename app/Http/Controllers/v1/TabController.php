<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use Illuminate\Support\Carbon;

class TabController extends Controller
{
    public function Tab1()
    {
        try {
            $data=[];

        // $data=[
        //     [
        //         'id'=>1,
        //         'tab_name'=>'',
        //         'img_src'=>'',
        //         'flag'=>'',
        //         'sub_menu'=>[
        //             [
        //                 'id'=>1,
        //                 'tab_name'=>'',
        //                 'img_src'=>'',
        //                 'flag'=>''
        //             ]
        //         ]
        //     ]
        // ];
        $myarray=[
            [
                'tab_name'=>'aaa',
                'flag'=>'A',
                'sub_menu'=>[
                    ['tab_name'=>'aaa','flag'=>'A'],
                    ['tab_name'=>'bbb','flag'=>'B'],
                    [
                        'tab_name'=>'ccc',
                        'flag'=>'C',
                        'sub_manu'=>[
                            ['tab_name'=>'aaa','flag'=>'A'],
                            ['tab_name'=>'bbb','flag'=>'B'],
                            [
                                'tab_name'=>'ccc',
                                'flag'=>'C',
                            ]
                        ]
                    ]
                ]
            ],[
                'tab_name'=>'bbb','flag'=>'B','sub_menu'=>[]
            ],
            ['tab_name'=>'ccc','flag'=>'C','sub_menu'=>[]]
        ];
        // return count($myarray);
        return $myarray;
        

        return $this->commonTab($myarray);
        } catch (\Throwable $th) {
            // throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function commonTab($value)
    {
        $myarray=[];
        $mysubarray=[];
        for ($i=0; $i < count($value); $i++) { 
            // return $value[$i]['sub_menu'];
            if (count($value[$i]['sub_menu']) > 0) {
                // return count($value[$i]['sub_menu']);
                $mysubarray=$this->subTab($value[$i]['sub_menu']);
                
            }else {
                $mysubarray=$value[$i]['sub_menu'];
            }
            $data=[
                    'id'=> $i+1,
                    'tab_name'=>$value[$i]['tab_name'],
                    'img_src'=>'',
                    'flag'=>$value[$i]['flag'],
                    'sub_menu'=>$mysubarray
            ];
            array_push($myarray,$data);
        }
        return $myarray;
    }

    public function subTab($value)
    {
        $myarray=[];
        $mysubarray=[];
        for ($i=0; $i < count($value); $i++) { 
            // return $value[$i]['sub_menu'];
            $data=[
                    'id'=> $i+1,
                    'tab_name'=>$value[$i]['tab_name'],
                    'img_src'=>'',
                    'flag'=>$value[$i]['flag'],
                    'sub_menu'=>$value[$i]['sub_menu']
            ];
            array_push($myarray,$data);
        }
        return $myarray;
    }
}
