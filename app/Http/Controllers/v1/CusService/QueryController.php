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
    Query
};
use Validator;
use Illuminate\Support\Carbon;
use Excel;
use App\Helpers\TransHelper;
use DB;

class QueryController extends Controller
{
    public function index(Request $request)
    {
        try {
            $product_id=$request->product_id;
            $query_status_id=$request->query_status_id;
            $query_mode_id=$request->query_mode_id;
            $id=$request->id;
            if ($id) {
                $data=Query::leftJoin('md_query_status','md_query_status.id','=','td_query.query_status_id')
                    ->leftJoin('md_client','md_client.id','=','td_query.invester_id')
                    ->select('td_query.*','md_query_status.status_name','md_query_status.color_code',
                    'md_client.client_name as investor_name','md_client.pan as investor_pan','md_client.id as investor_id','md_client.email as investor_email','md_client.mobile as investor_mobile')
                    ->where('td_query.id',$id)
                    ->first();
            }elseif ($query_status_id || $query_mode_id) {
                $data=Query::leftJoin('md_query_status','md_query_status.id','=','td_query.query_status_id')
                    ->leftJoin('md_client','md_client.id','=','td_query.invester_id')
                    ->select('td_query.*','md_query_status.status_name','md_query_status.color_code',
                    'md_client.client_name as investor_name','md_client.pan as investor_pan','md_client.id as investor_id','md_client.email as investor_email','md_client.mobile as investor_mobile')
                    ->where('td_query.product_id',$product_id)
                    ->get();
            } else {
                $data=Query::leftJoin('md_query_status','md_query_status.id','=','td_query.query_status_id')
                    ->leftJoin('md_client','md_client.id','=','td_query.invester_id')
                    ->select('td_query.*','md_query_status.status_name','md_query_status.color_code',
                    'md_client.client_name as investor_name','md_client.pan as investor_pan','md_client.id as investor_id','md_client.email as investor_email','md_client.mobile as investor_mobile')
                    ->where('td_query.product_id',$product_id)
                    ->get();
            }
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function createUpdate(Request $request)
    {
        // return $request;
        // $validator = Validator::make(request()->all(),[
        //     'emp_name' =>'required',
        // ]);
    
        // if($validator->fails()) {
        //     $errors = $validator->errors();
        //     return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        // }
        try {
            if ($request->id > 0) {
                // return $request;
                $update_data=Query::find($request->id);
                $update_data->query_status_id=$request->query_status_id;
                $update_data->query_feedback=$request->query_feedback;
                $update_data->overall_feedback=$request->overall_feedback;
                $update_data->updated_by=Helper::modifyUser($request->user());
                $update_data->save();

                $data=Query::leftJoin('md_query_status','md_query_status.id','=','td_query.query_status_id')
                    ->select('td_query.*','md_query_status.status_name','md_query_status.color_code')
                    ->where('td_query.id',$update_data->id)
                    ->first();
            }else{
                // return $request;
                $investor_pan=$request->investor_pan;
                $investor_name=$request->investor_name;
                if ($investor_pan) {
                    $invester_id=DB::table('md_client')->where('pan',$investor_pan)->value('id');
                }else {
                    $invester_id=DB::table('md_client')->where('client_name',$investor_name)->value('id');
                }
                $data=Query::create(array(
                    'product_id'=>$request->product_id,
                    'emp_name'=>"",
                    'query_id'=>"QRY_".(microtime(true)*10000),
                    'date_time'=>date('Y-m-d H:i:s'),
                    'invester_id'=>$invester_id,
                    'folio_no'=>$request->folio_no,
                    'application_no'=>$request->application_no,
                    'query_given_by_id'=>$request->query_given_by_id,
                    'entry_name'=>$request->entry_name,
                    'product_code'=>$request->product_code,
                    'isin_no'=>$request->isin_no,
                    'query_type_subtype_id'=>$request->query_subtype_id,
                    'query_details'=>$request->query_details,
                    'query_nature_id'=>$request->query_nature_id,
                    'query_given_to_id'=>$request->query_given_to_id,
                    'query_rec_through_id'=>$request->query_receive_through_id,
                    'query_given_through_id'=>$request->query_given_through_id,
                    'concern_per_name'=>$request->concern_person_name,
                    'contact_no'=>$request->contact_no,
                    'email_id'=>$request->email_id,
                    'expected_close_date'=>$request->expected_close_date,
                    // 'actual_close_date'=>$request->actual_close_date,
                    'query_status_id'=>2, //Registered
                    'remarks'=>$request->remarks,
                    // 'query_feedback',
                    // 'overall_feedback',
                    'created_by'=>Helper::modifyUser($request->user()),
                    'updated_by'=>Helper::modifyUser($request->user()),
                ));      
            }    
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_SAVE_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function getFolio(Request $request)
    {
        try {
            $pan_no=$request->pan_no;
            $client_name=$request->client_name;
            $rawQuery='';
            if ($pan_no) {
                $queryString='td_mutual_fund_trans.first_client_pan';
                $rawQuery.=Helper::WhereRawQuery($pan_no,$rawQuery,$queryString);
            }else {
                $queryString='td_mutual_fund_trans.first_client_name';
                $rawQuery.=Helper::WhereRawQuery($client_name,$rawQuery,$queryString);
            }
            $data=MutualFundTransaction::whereRaw($rawQuery)->groupBy('folio_no')->get();
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function getFoliowiseProduct(Request $request)
    {
        try {
            $folio_no=$request->folio_no;
            // $client_name=$request->client_name;
            $rawQuery='';
            if ($folio_no) {
                $queryString='td_mutual_fund_trans.folio_no';
                $rawQuery.=Helper::WhereRawQuery($folio_no,$rawQuery,$queryString);
            }
            $data=MutualFundTransaction::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_mutual_fund_trans.product_code')
                ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                ->leftJoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
                ->leftJoin('md_option','md_option.id','=','md_scheme_isin.option_id')
                ->select('td_mutual_fund_trans.*','md_scheme.scheme_name as scheme_name','md_plan.plan_name as plan_name','md_option.opt_name as option_name')
                ->whereRaw($rawQuery)->groupBy('product_code')
                ->get();
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
}