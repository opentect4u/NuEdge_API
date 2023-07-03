<?php

namespace App\Http\Controllers\v1\INSOperation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{InsProduct,Insurance,InsFormReceived,InsBuOpportunity};
use Validator;

class BuOpportunityController extends Controller
{
    public function searchDetails(Request $request)
    {
        try {
            $data=[];
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function export(Request $request)
    {
        try {
            $data=[];
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function index(Request $request)
    {
        try {
            $data=[];
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function createUpdate(Request $request)
    {
        try {
            return $request;
            $tin_no=$request->tin_no;
            if ($tin_no) {
                # code...
            }else {

                $is_has=InsBuOpportunity::get();
                // return $is_has; 
                if (count($is_has)>0) {
                    $tin_no='INSR00'.(count($is_has)+1);
                } else {
                    $tin_no='INSR001';
                }
                // $data=InsBuOpportunity::create(array(
                //     'rec_datetime'=>date('Y-m-d H:i:s'),
                //     'temp_tin_no'=>$tin_no,
                //     'bu_type'=>,
                //     'arn_no',
                //     'sub_arn_no',
                //     'euin_no',
                //     'sub_brk_cd',
                //     'ins_type_id',
                //     'proposer_id',
                //     'same_as_above',
                //     'insured_person_id',
                //     'comp_id',
                //     'product_type_id',
                //     'product_id',
                //     'sum_insured',
                //     'renewal_dt',
                //     'upload_file',
                //     'remarks',
                //     'delete_flag',
                //     'deleted_date',
                //     'deleted_by',
                //     'created_by',
                //     'updated_by',
                // ));
            }
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}
