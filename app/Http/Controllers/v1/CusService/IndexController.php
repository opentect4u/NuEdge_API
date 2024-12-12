<?php

namespace App\Http\Controllers\v1\CusService;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{
    MutualFund,
    Client,
    FormReceived,
    MutualFundTransaction,
    MFTransTypeSubType,
    Disclaimer,
    QueryNature,
    QueryGivenBy,
    QueryRecGivenThrogh,
    Query,
    QueryScheme,
    QueryEntryAttach,
    QuerySolveAttach
};
use Validator;
use Illuminate\Support\Carbon;
use Excel;
use App\Helpers\TransHelper;
use DB;
use App\Helpers\SMSHelper;
use App\Http\Controllers\V1\Client\LiveMFPLController;

class IndexController extends Controller
{
    public function index(Request $request)
    {
        try {
            // return $request;
            $data=Query::leftjoin('md_products','md_products.id','=','td_query.product_id')
                ->leftJoin('md_query_status','md_query_status.id','=','td_query.query_status_id')
                ->leftJoin('md_query_sub_type','md_query_sub_type.id','=','td_query.query_subtype_id')
                ->leftJoin('md_query_type','md_query_type.id','=','td_query.query_type_id')
                ->select('td_query.*','md_products.product_name as product_name','md_query_status.status_name','md_query_status.status_name',
                'md_query_sub_type.query_subtype','md_query_type.query_type')
                ->selectRaw('IF(td_query.query_tat IS NULL || td_query.query_tat="",md_query_sub_type.query_tat,td_query.query_tat)as query_tat')
                ->get();
            $group_product = array();
            foreach($data as $key => $item)
            {
                $group_product[$item['product_id']][] = $item;
            }
            // return $group_product["MUTUAL FUND"];
            foreach((array)$group_product as $key => $value) {
                $group_status = array();
                foreach ($value as $key1 => $value1) {
                    $group_status[$value1['query_status_id']][] = $value1;
                }
                $group_product[$key]=$group_status;
            }
            // return $group_product;
            // return count((array)$group_product);
                                                                    
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($group_product);
    }
}