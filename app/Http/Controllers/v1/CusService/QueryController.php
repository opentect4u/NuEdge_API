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
    QuerySolveAttach,
    QueryAttachment,
    Holiday,
};
use Validator;
use Illuminate\Support\Carbon;
use Excel;
use App\Helpers\TransHelper;
use DB;
use App\Helpers\SMSHelper;
use App\Http\Controllers\V1\Client\LiveMFPLController;
use Mail;
use App\Mail\CusService\QueryStatusEmail;
use Illuminate\Support\Facades\Crypt;
use File;

class QueryController extends Controller
{
    public function searchClient(Request $request)
    {
        try {
            $search=$request->search;
            // DB::enableQueryLog();
            // $data=MutualFundTransaction::select('td_mutual_fund_trans.*','td_mutual_fund_trans.first_client_name as client_name','td_mutual_fund_trans.first_client_pan as pan')
            //         ->selectRaw('IF(td_mutual_fund_trans.first_client_pan!="",
            //         (select client_code from md_client where pan=td_mutual_fund_trans.first_client_pan limit 1),
            //         (select client_code from md_client where client_name=td_mutual_fund_trans.first_client_name limit 1)
            //         )as client_code')
            //         ->selectRaw('IF(td_mutual_fund_trans.first_client_pan!="",
            //         (select email from md_client where pan=td_mutual_fund_trans.first_client_pan limit 1),
            //         (select email from md_client where client_name=td_mutual_fund_trans.first_client_name limit 1)
            //         )as email')
            //         ->selectRaw('IF(td_mutual_fund_trans.first_client_pan!="",
            //         (select mobile from md_client where pan=td_mutual_fund_trans.first_client_pan limit 1),
            //         (select mobile from md_client where client_name=td_mutual_fund_trans.first_client_name limit 1)
            //         )as mobile')
            //         ->where('td_mutual_fund_trans.first_client_pan','like','%' . $search . '%')
            //         ->orWhere('td_mutual_fund_trans.first_client_name','like', '%' . $search . '%')
            //         ->orWhere('td_mutual_fund_trans.folio_no','like', '%' . $search . '%')
            //         // ->orWhereRaw('(IF(td_mutual_fund_trans.first_client_pan!="",
            //         // (select mobile from md_client where pan=td_mutual_fund_trans.first_client_pan limit 1),
            //         // (select mobile from md_client where client_name=td_mutual_fund_trans.first_client_name limit 1)
            //         // ) LIKE "%' . $search . '%")')
            //         ->groupBy('td_mutual_fund_trans.first_client_pan')
            //         ->get();
            
            $data=Client::leftJoin('td_mutual_fund_trans','td_mutual_fund_trans.first_client_name','=','md_client.client_name')
                    ->select('md_client.*','td_mutual_fund_trans.folio_no','td_mutual_fund_trans.first_client_name')
                    // ->where('client_type','!=','E')
                    ->where('md_client.client_name','like', '%' . $search . '%')
                    ->orWhere('md_client.client_code','like', '%' . $search . '%')
                    ->orWhere('md_client.pan','like', '%' . $search . '%')
                    ->orWhere('md_client.mobile','like', '%' . $search . '%')
                    ->orWhere('md_client.email','like', '%' . $search . '%')
                    ->orWhere('td_mutual_fund_trans.folio_no','like', '%' . $search . '%')
                    ->groupBy('td_mutual_fund_trans.first_client_name')
                    ->get();
            // dd(DB::getQueryLog());
        } catch (\Throwable $th) {
            // throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function search(Request $request)
    {
        try {
            $search=$request->search;
            $product_id=$request->product_id;
            $data=Query::
                // with('allscheme')->with('entryattach')->with('solveattach')->
                leftJoin('md_query_status','md_query_status.id','=','td_query.query_status_id')
                ->leftJoin('md_client','md_client.id','=','td_query.invester_id')
                ->leftJoin('md_query_sub_type','md_query_sub_type.id','=','td_query.query_subtype_id')
                ->select('td_query.*','md_query_status.status_name','md_query_status.color_code','md_query_sub_type.query_subtype',
                'md_client.client_name as investor_name','md_client.pan as investor_pan','md_client.id as investor_id','md_client.email as investor_email','md_client.mobile as investor_mobile')
                ->selectRaw('IF(td_query.query_tat IS NULL || td_query.query_tat="",md_query_sub_type.query_tat,td_query.query_tat)as query_tat')
                ->where('td_query.product_id',$product_id)
                ->where('td_query.query_id','like','%' . $search . '%')
                ->get();
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function showDetails(Request $request)
    {
        try {
            $query_id=Crypt::decrypt($request->query_id);
            $data=Query::with('allscheme','allscheme.schemename')
                // ->with('entryattach')->with('solveattach')
                ->with('allattach')
                ->leftJoin('md_query_status','md_query_status.id','=','td_query.query_status_id')
                ->leftJoin('md_query_type','md_query_type.id','=','td_query.query_type_id')
                ->leftJoin('md_query_sub_type','md_query_sub_type.id','=','td_query.query_subtype_id')
                ->leftJoin('md_query_given_by','md_query_given_by.id','=','td_query.query_given_by_id')
                ->leftJoin('md_query_rec_given_through','md_query_rec_given_through.id','=','td_query.query_rec_through_id')
                ->leftJoin('md_query_nature','md_query_nature.id','=','td_query.query_nature_id')
                ->leftJoin('md_query_rec_given_through as md_query_given_through','md_query_given_through.id','=','td_query.query_given_through_id')
                ->leftJoin('md_client','md_client.id','=','td_query.invester_id')
                ->leftJoin('users','users.id','=','td_query.query_rec_by_id')
                ->leftJoin('users as solve_users','solve_users.id','=','td_query.query_solve_by_id')
                ->select('td_query.*','md_query_status.status_name','md_query_status.color_code','md_query_type.query_type','md_query_sub_type.query_subtype','md_query_sub_type.query_tat',
                'md_query_given_by.name as query_given_by','md_query_rec_given_through.name as query_receive_through','md_query_nature.query_nature','md_query_given_through.name as query_given_through',
                'md_client.client_name as investor_name','md_client.pan as investor_pan','md_client.id as investor_id','md_client.email as investor_email','md_client.mobile as investor_mobile',
                // 'md_scheme.scheme_name as scheme_name','md_plan.plan_name as plan_name','md_option.opt_name as option_name','md_amc.amc_name'
                'users.name as entry_name','td_query.rating as query_feedback_received','solve_users.name as query_solve_by'
                )
                ->where('td_query.query_id',$query_id)
                ->first();
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }
    
    public function index(Request $request)
    {
        try {
            $product_id=$request->product_id;
            $query_status_id=$request->query_status_id;
            $query_mode_id=$request->query_mode_id;
            $id=$request->id;
            
            $query_id=$request->query_id;
            $client_id=$request->client_id;
            $query_rec_by_id=$request->query_rec_by_id;
            $query_solve_by_id=$request->query_solve_by_id;
            $query_given_by_id=$request->query_given_by_id;
            $date_range=$request->date_range;
            $query_given_through_id=$request->query_given_thrugh_id;
            
            if ($id) {
                $data=Query::with('allscheme')
                    // ->with('entryattach')->with('solveattach')
                    ->with('allattach')
                    ->leftJoin('md_query_status','md_query_status.id','=','td_query.query_status_id')
                    ->leftJoin('md_client','md_client.id','=','td_query.invester_id')
                    ->leftJoin('md_query_sub_type','md_query_sub_type.id','=','td_query.query_subtype_id')
                    ->leftJoin('users','users.id','=','td_query.query_rec_by_id')
                    ->select('td_query.*','md_query_status.status_name','md_query_status.color_code','md_query_sub_type.query_subtype',
                    'md_client.client_name as investor_name','md_client.pan as investor_pan','md_client.id as investor_id','md_client.email as investor_email','md_client.mobile as investor_mobile','users.name as entry_name')
                    ->selectRaw('IF(td_query.query_tat IS NULL || td_query.query_tat="",md_query_sub_type.query_tat,td_query.query_tat)as query_tat')
                    ->where('td_query.id',$id)
                    ->first();
            }else {
                // return $request;
                $rawQuery='';
                if ($query_id || $client_id || $query_rec_by_id || $query_solve_by_id || $query_given_by_id || $query_status_id || $date_range 
                || $query_given_through_id) {
                    
                    $queryString='td_query.query_id';
                    $rawQuery.=Helper::WhereRawQuery($query_id,$rawQuery,$queryString);
                    $queryString='td_query.invester_id';
                    $rawQuery.=Helper::WhereRawQuery($client_id,$rawQuery,$queryString);
                    $queryString='td_query.query_rec_by_id';
                    $rawQuery.=Helper::WhereRawQuery($query_rec_by_id,$rawQuery,$queryString);
                    $queryString='td_query.query_solve_by_id';
                    $rawQuery.=Helper::WhereRawQuery($query_solve_by_id,$rawQuery,$queryString);
                    $queryString='td_query.query_given_by_id';
                    $rawQuery.=Helper::WhereRawQuery($query_given_by_id,$rawQuery,$queryString);
                    $queryString='td_query.query_status_id';
                    $rawQuery.=Helper::WhereRawQuery($query_status_id,$rawQuery,$queryString);
                    $queryString='td_query.query_given_through_id';
                    $rawQuery.=Helper::WhereRawQuery($query_given_through_id,$rawQuery,$queryString);
                    if ($date_range) {
                        $from_date=Carbon::parse(str_replace('/','-',explode("-",$date_range)[0]))->format('Y-m-d') ;
                        $to_date=Carbon::parse(str_replace('/','-',explode("-",$date_range)[1]))->format('Y-m-d') ;
                        // return $to_date;
                        $queryString='td_query.date_time';
                        $rawQuery.=Helper::FrmToDateRawQuery($from_date,$to_date,$rawQuery,$queryString);
                    }
                    $queryString='td_query.query_given_through_id';
                    $rawQuery.=Helper::WhereRawQuery($query_given_through_id,$rawQuery,$queryString);
                    // $queryString2='td_query.query_mode_id';
                    // $rawQuery.=Helper::WhereRawQuery($query_mode_id,$rawQuery,$queryString2);


                    $queryString1='td_query.product_id';
                    $rawQuery.=Helper::WhereRawQuery($product_id,$rawQuery,$queryString1);
                } else {
                    $queryString='td_query.product_id';
                    $rawQuery.=Helper::WhereRawQuery($product_id,$rawQuery,$queryString);
                }
                // return $rawQuery;
                if ($product_id==1) {
                    $data=Query::with('allscheme','allscheme.schemename')
                        // ->with('entryattach')->with('solveattach')
                        ->with('allattach')
                        ->leftJoin('md_query_status','md_query_status.id','=','td_query.query_status_id')
                        ->leftJoin('md_query_type','md_query_type.id','=','td_query.query_type_id')
                        ->leftJoin('md_query_sub_type','md_query_sub_type.id','=','td_query.query_subtype_id')
                        ->leftJoin('md_query_given_by','md_query_given_by.id','=','td_query.query_given_by_id')
                        ->leftJoin('md_query_rec_given_through','md_query_rec_given_through.id','=','td_query.query_rec_through_id')
                        ->leftJoin('md_query_nature','md_query_nature.id','=','td_query.query_nature_id')
                        ->leftJoin('md_query_rec_given_through as md_query_given_through','md_query_given_through.id','=','td_query.query_given_through_id')
                        
                        
                        ->leftJoin('md_client','md_client.id','=','td_query.invester_id')
                        ->leftJoin('users','users.id','=','td_query.query_rec_by_id')
                        ->leftJoin('users as solve_users','solve_users.id','=','td_query.query_solve_by_id')
                        // ->leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_query.product_code')
                        // ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                        // ->leftJoin('md_amc','md_amc.id','=','md_scheme.amc_id')
                        // ->leftJoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
                        // ->leftJoin('md_option','md_option.id','=','md_scheme_isin.option_id')
                        ->select('td_query.*','md_query_status.status_name','md_query_status.color_code','md_query_type.query_type','md_query_sub_type.query_subtype','md_query_sub_type.query_tat',
                        'md_query_given_by.name as query_given_by','md_query_rec_given_through.name as query_receive_through','md_query_nature.query_nature','md_query_given_through.name as query_given_through',
                        'md_client.client_name as investor_name','md_client.pan as investor_pan','md_client.id as investor_id','md_client.email as investor_email','md_client.mobile as investor_mobile',
                        // 'md_scheme.scheme_name as scheme_name','md_plan.plan_name as plan_name','md_option.opt_name as option_name','md_amc.amc_name'
                        'users.name as entry_name','td_query.rating as query_feedback_received','solve_users.name as query_solve_by'
                        )
                        ->whereRaw($rawQuery)
                        ->orderBy('td_query.date_time','desc')
                        ->get();
                } else if ($product_id==2) {
                    $data=Query::with('entryattach')->with('solveattach')
                        ->leftJoin('md_query_status','md_query_status.id','=','td_query.query_status_id')
                        ->leftJoin('md_client','md_client.id','=','td_query.invester_id')
                        ->select('td_query.*','md_query_status.status_name','md_query_status.color_code',
                        'md_client.client_name as investor_name','md_client.pan as investor_pan','md_client.id as investor_id','md_client.email as investor_email','md_client.mobile as investor_mobile')
                        ->whereRaw($rawQuery)
                        ->get();
                } else if ($product_id==3) {
                    $data=Query::with('entryattach')->with('solveattach')
                        ->leftJoin('md_query_status','md_query_status.id','=','td_query.query_status_id')
                        ->leftJoin('md_client','md_client.id','=','td_query.invester_id')
                        ->leftJoin('md_ins_products','md_ins_products.id','=','td_query.ins_product_id')
                        ->select('td_query.*','md_query_status.status_name','md_query_status.color_code',
                        'md_client.client_name as investor_name','md_client.pan as investor_pan','md_client.id as investor_id','md_client.email as investor_email','md_client.mobile as investor_mobile',
                        'md_ins_products.product_name')
                        ->whereRaw($rawQuery)
                        ->get();
                } else if ($product_id==4) {
                    $data=Query::with('entryattach')->with('solveattach')
                        ->leftJoin('md_query_status','md_query_status.id','=','td_query.query_status_id')
                        ->leftJoin('md_client','md_client.id','=','td_query.invester_id')
                        ->leftJoin('md_fd_scheme','md_fd_scheme.id','=','td_query.fd_scheme_id')
                        ->select('td_query.*','md_query_status.status_name','md_query_status.color_code',
                        'md_client.client_name as investor_name','md_client.pan as investor_pan','md_client.id as investor_id','md_client.email as investor_email','md_client.mobile as investor_mobile',
                        'md_fd_scheme.scheme_name')
                        ->whereRaw($rawQuery)
                        ->get();
                }else {
                    $data=Query::with('entryattach')->with('solveattach')
                        ->leftJoin('md_query_status','md_query_status.id','=','td_query.query_status_id')
                        ->leftJoin('md_client','md_client.id','=','td_query.invester_id')
                        ->select('td_query.*','md_query_status.status_name','md_query_status.color_code',
                        'md_client.client_name as investor_name','md_client.pan as investor_pan','md_client.id as investor_id','md_client.email as investor_email','md_client.mobile as investor_mobile')
                        ->whereRaw($rawQuery)
                        ->get();
                }
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
            $total_client_count=Client::whereIn('client_type',['M','N','P'])->count();
            $total_amu_balance=300;
            if ($request->id > 0) {
                // return $request;
                $update_data=Query::find($request->id);
                // return $update_data;
                if ($request->query_nature_id==4) { //  external
                    $update_data->query_given_to_id=$request->query_given_to_id;
                    $update_data->query_rec_through_id=$request->query_rec_through_id;
                    $update_data->query_given_through_id=$request->query_given_through_id;
                    $update_data->concern_person_name=$request->concern_person_name;
                    $update_data->contact_no=$request->contact_no;
                    $update_data->email_id=$request->email_id;
                    $update_data->query_mode_id=$request->query_mode_id;
                    $update_data->query_tat=$request->query_tat;
                }
                $update_data->query_nature_id=$request->query_nature_id;
                $update_data->query_status_id=$request->query_status_id;
                $update_data->expected_close_date=$request->expected_close_date;
                if ($request->query_status_id==5 || $request->query_status_id==7) {
                    $update_data->actual_close_date=date('Y-m-d H:i:s');
                    $update_data->query_solve_by_id=Helper::modifyUser($request->user());
                    $update_data->query_solve_date=date('Y-m-d H:i:s');
                }
                if ($request->query_status_id==6) { //reopen
                    $update_data->actual_close_date=NULL;
                }

                $update_data->whats_app_flag='Y';
                $update_data->whats_app_date=date('Y-m-d H:i:s');
                $update_data->email_flag='Y';
                $update_data->email_date=date('Y-m-d H:i:s');
                $update_data->sms_flag='Y';
                $update_data->sms_date=date('Y-m-d H:i:s');

                $update_data->query_feedback=$request->query_feedback;
                $update_data->suggestion=$request->suggestion;
                $update_data->remarks=$request->remarks;
                $update_data->updated_by=Helper::modifyUser($request->user());
                $update_data->save();

                $solve_attachment=$request->solve_attachment;
                // return $entry_attachment;
                $doc_name='';
                if (!empty($solve_attachment)) {
                    foreach ($solve_attachment as $key => $file) {
                        // return $file;
                        if ($file) {
                            $doc_path_extension=$file->getClientOriginalExtension();
                            $doc_name=(microtime(true)*10000).".".$doc_path_extension;
                            $file->move(public_path('query-attachment/'),$doc_name);
                        }
                        QueryAttachment::create([
                            'query_id'=>$update_data->id,
                            'query_status_id'=>$update_data->query_status_id,
                            'name'=>$doc_name,
                            'created_by',
                            'updated_by',
                        ]);
                    }
                }

                $query_id=$update_data->query_id;
                /*********************start update feedback url****************************/
                if ($update_data->query_status_id==5 || $update_data->query_status_id==7) {  // Completed and Re-Completed
                    $url=env('QUERY_FEEDBACK').Crypt::encrypt($query_id);
                    $short_url_json=SMSHelper::createShortUrl($url);
                    // $short_url_json=json_decode($short_url_json);
                    // return $short_url_json;
                    $feedback_url="";
                    if ($short_url_json->status=='success') {
                        $feedback_url=$short_url_json->shorturl;
                    }
                    // return $short_url;
                    $update=Query::find($update_data->id);
                    $update->feedback_url=$feedback_url;
                    $update->save();
                }
                /*********************end update feedback url****************************/
                $data=Query::with('allscheme','allscheme.schemename')
                    // ->with('entryattach')->with('solveattach')
                    ->with('allattach')
                    ->leftJoin('md_query_status','md_query_status.id','=','td_query.query_status_id')
                    ->leftJoin('md_query_type','md_query_type.id','=','td_query.query_type_id')
                    ->leftJoin('md_query_sub_type','md_query_sub_type.id','=','td_query.query_subtype_id')
                    ->leftJoin('md_query_given_by','md_query_given_by.id','=','td_query.query_given_by_id')
                    ->leftJoin('md_query_rec_given_through','md_query_rec_given_through.id','=','td_query.query_rec_through_id')
                    ->leftJoin('md_query_nature','md_query_nature.id','=','td_query.query_nature_id')
                    ->leftJoin('md_query_rec_given_through as md_query_given_through','md_query_given_through.id','=','td_query.query_given_through_id')
                    ->leftJoin('md_client','md_client.id','=','td_query.invester_id')
                    ->leftJoin('users','users.id','=','td_query.query_rec_by_id')
                    ->leftJoin('users as solve_users','solve_users.id','=','td_query.query_solve_by_id')
                    ->select('td_query.*','md_query_status.status_name','md_query_status.color_code','md_query_type.query_type','md_query_sub_type.query_subtype','md_query_sub_type.query_tat',
                    'md_query_given_by.name as query_given_by','md_query_rec_given_through.name as query_receive_through','md_query_nature.query_nature','md_query_given_through.name as query_given_through',
                    'md_client.client_name as investor_name','md_client.pan as investor_pan','md_client.id as investor_id','md_client.email as investor_email','md_client.mobile as investor_mobile',
                    'users.name as entry_name','solve_users.name as query_solve_by')
                    ->where('td_query.id',$update_data->id)
                    ->first();
                // return $data;
                /***************************TAT EXPIRE LOGIC******************************* */
                // if ($data->actual_close_date) {
                //     $actual_close_date = strtotime(date('y-m-d',strtotime($data->actual_close_date)));
                //     $expected_close_date = strtotime($data->expected_close_date);
                //     $isExpired =($expected_close_date < $actual_close_date)?'Yes':'No';
                //     // return $isExpired;
                //  }else{
                //     if($data->expected_close_date){
                //           $expected_close_date = strtotime($data->expected_close_date);
                //           $today=strtotime(date('Y-m-d'));
                //           $isExpired =($expected_close_date < $today)?'Yes':'No';
                //     } else{
                //           $cal_date=date('Y-m-d', strtotime($data->date_time. ' + '.$data->query_tat.' day'));
                //           $new_date = \Carbon\Carbon::create($cal_date);
                //           $isAfter = \Carbon\Carbon::create(date('Y-m-d'))->diffInDays($new_date);
                //           $isExpired = ($isAfter <= 0) ?'Yes':'No';
                //     }
                // }
                /***************************TAT EXPIRE LOGIC******************************* */ 
                // email and sms 
                $query_status_id=$data->query_status_id;
                $expected_close_date=date('d-m-Y',strtotime($data->expected_close_date));
                $close_date=date('d-m-Y',strtotime($data->actual_close_date));
                $investor_name=$data->investor_name;
                $investor_email=$data->investor_email;
                $mobile_no=$data->investor_mobile;
                $query_status=$data->status_name;
                $subject="Query status changed to ".$query_status."- QueryId : ".$query_id;
                /**********************start sending email and sms and whatsapp******************************/
                $short_url=$update_data->short_url;
                if ($update_data->query_status_id==2 || $update_data->query_status_id==6) {  // register and reopen
                    $res=SMSHelper::registerReOpen($mobile_no,$short_url,$query_status,$investor_name,$query_id);
                } else if ($update_data->query_status_id==3 || $update_data->query_status_id==4) {  // in process and re in process 
                    $res=SMSHelper::inReinProcess($mobile_no,$short_url,$query_status,$investor_name,$query_id,$expected_close_date);
                } else if ($update_data->query_status_id==5 || $update_data->query_status_id==7) {  // Completed and Re-Completed
                    $res=SMSHelper::completedReCompleted($mobile_no,$short_url,$query_status,$investor_name,$query_id,$close_date,$feedback_url);
                }
                // Mail::to($investor_email)->send(new QueryStatusEmail($subject,$investor_name,$query_status,$query_status_id,$data));
                // $allAtched=QuerySolveAttach::where('query_id',$data->id)->get();
                $files=[];
                // return $data;
                $entry_attachment=[];
                foreach ($data->allattach as $key => $value) {
                    if ($value->query_status_id==2) {
                        array_push($entry_attachment,$value);
                    }
                }
                $data->entry_attachment=$entry_attachment;
                $status_change_attachment=[];
                foreach ($data->allattach as $key => $value) {
                    if ($value->query_status_id==$data->query_status_id) {
                        array_push($status_change_attachment,$value);
                    }
                }
                $data->status_change_attachment=$status_change_attachment;
                // return $data->status_change_attachment;
                $files=[];
                if (count($data->status_change_attachment) > 0) {
                    foreach ($data->status_change_attachment as $key => $value1) {
                        // $filePath=public_path('query-entry/'.$value1->name);
                        $final_file_name=public_path('query-attachment/'.$value1->name);
                        array_push($files,$final_file_name);
                    }
                    // if (count($data->status_change_attachment)==1) {
                    //     $final_file_name=public_path('query-attachment/'.$allAtched[0]->name);
                    //     array_push($files,$final_file_name);
                    // }else {
                    //     $all_files=[];
                    //     foreach ($data->status_change_attachment as $key => $value1) {
                    //         // $filePath=public_path('query-entry/'.$value1->name);
                    //         $filePath=$value1->name;
                    //         array_push($all_files,$filePath);
                    //     }
                    //     // return $all_files;
                    //     $zip = new \ZipArchive();
                    //     // $fileName = 'query-attachment-zip/zipFile_'. (string)$data->id.'.zip';
                    //     $fileName = 'query-attachment-zip/zipFile_'. (string)$data->id.'.zip';
                    //     if (file_exists(public_path($fileName))) {
                    //         unlink(public_path($fileName));
                    //     }
                    //     if ($zip->open(public_path($fileName), \ZipArchive::CREATE)== TRUE)
                    //     {
                    //         // return public_path($fileName);
                    //         $query_attachment_files = File::files(public_path('query-attachment'));
                    //         // return $query_attachment_files;
                    //         foreach ($query_attachment_files as $key => $file){
                    //             // return $file->getFilename();
                    //             $relativeName = basename($file);
                    //             if (in_array($relativeName, $all_files)) {
                    //                 $zip->addFile($file, $relativeName);
                    //             }
                    //         }
                    //         $zip->close();
                    //     }
                    //     $final_file_name=public_path($fileName);
                    //     // return $final_file_name;
                    //     array_push($files,$final_file_name);
                    // }
                }

                // return $files;
                // if (count($data->entry_attachment)>0) {
                //     // foreach ($data->solveattach as $key => $value1) {
                //     //     $filePath=public_path('query-solve/'.$value1->name);
                //     //     array_push($files,$filePath);
                //     // }

                //     Mail::to($investor_email)->send(new QueryStatusEmail($subject,$investor_name,$query_status,$query_status_id,$data,$files,$total_client_count));
                // }else {
                //     Mail::to($investor_email)->send(new QueryStatusEmail($subject,$investor_name,$query_status,$query_status_id,$data,$files,$total_client_count));
                // }
                // return view('emails.customer_service.query_desk_email',compact('data','investor_name','query_status','query_status_id','files','total_client_count'));
                Mail::to($investor_email)->send(new QueryStatusEmail($subject,$investor_name,$query_status,$query_status_id,$data,$files,$total_client_count,$total_amu_balance));

                /**********************end sending email and sms and whatsapp******************************/
            }else{
                // return $request;
                $investor_pan=$request->investor_pan;
                $investor_name=$request->investor_name;
                if ($investor_pan) {
                    $invester_id=DB::table('md_client')->where('pan',$investor_pan)->value('id');
                }else {
                    if (str_contains(strtolower($investor_name), 'rep by')) { 
                        $newStr = explode("rep by", strtolower($investor_name));
                        $investor_name=$newStr[0];
                    }
                    $invester_id=DB::table('md_client')->where('client_name',trim($investor_name))->value('id');
                }
                // return $invester_id;
                $count=Query::where('product_id',$request->product_id)->count();
                if ($request->product_id==1) {
                    $query_id=($count > 0)?"QRY-MF-".(1000+$count):"QRY-MF-1000";
                } elseif ($request->product_id==2) {
                    $query_id=($count > 0)?"QRY-BND-".(1000+$count):"QRY-BND-1000";
                } elseif ($request->product_id==3) {
                    $query_id=($count > 0)?"QRY-INS-".(1000+$count):"QRY-INS-1000";
                } elseif ($request->product_id==4) {
                    $query_id=($count > 0)?"QRY-FD-".(1000+$count):"QRY-FD-1000";
                } elseif ($request->product_id==11) {
                    $query_id=($count > 0)?"QRY-PMS-".(1000+$count):"QRY-PMS-1000";
                } else {
                    $query_id='QRY-'.(microtime(true)*1000);
                }
                $query_status_id=2;
                $data_arr=[
                    'product_id'=>$request->product_id,
                    'emp_name'=>"",
                    'query_id'=>$query_id,
                    'date_time'=>date('Y-m-d H:i:s'),
                    'invester_id'=>$invester_id,
                    'folio_no'=>isset($request->folio_no)?$request->folio_no:NULL,
                    'policy_no'=>isset($request->policy_no)?$request->policy_no:NULL,
                    'ins_product_id'=>isset($request->ins_product_id)?$request->ins_product_id:NULL,
                    'fd_no'=>isset($request->fd_no)?$request->fd_no:NULL,
                    'fd_scheme_id'=>isset($request->fd_scheme_id)?$request->fd_scheme_id:NULL,
                    'application_no'=>$request->application_no,
                    'query_given_by_id'=>$request->query_given_by_id,
                    'query_rec_by_id'=>$request->query_rec_by_id,
                    'query_type_id'=>$request->query_type_id,
                    'query_subtype_id'=>$request->query_subtype_id,
                    'query_details'=>$request->query_details,
                    'query_rec_through_id'=>$request->query_rec_through_id,
                    'query_status_id'=>$query_status_id, //Registered
                    'created_by'=>Helper::modifyUser($request->user()),
                    'updated_by'=>Helper::modifyUser($request->user()),
                ];
                $data=Query::create($data_arr); 
                
                $scheme_details=json_decode($request->scheme_dtls);
                if (!empty($scheme_details)) {
                    foreach ($scheme_details as $key => $product) {
                        QueryScheme::create([
                            'query_id'=>$data->id,
                            'product_code'=>$product->product_code,
                            'isin_no'=>$product->isin_no,
                            'created_by'=>Helper::modifyUser($request->user()),
                            'updated_by'=>Helper::modifyUser($request->user())
                        ]);
                    }
                }
                $entry_attachment=$request->entry_attachment;
                $doc_name='';
                if (!empty($entry_attachment)) {
                    foreach ($entry_attachment as $key => $file) {
                        if ($file) {
                            $doc_path_extension=$file->getClientOriginalExtension();
                            $doc_name=(microtime(true)*10000).".".$doc_path_extension;
                            $file->move(public_path('query-attachment/'),$doc_name);
                        }
                        QueryAttachment::create([
                            'query_id'=>$data->id,
                            'query_status_id'=>$data->query_status_id,
                            'name'=>$doc_name,
                            'created_by',
                            'updated_by',
                        ]);
                    }
                }

                $url=env('QUERY_DETAILS').Crypt::encrypt($query_id);
                // return $url;
                $short_url_json=SMSHelper::createShortUrl($url);
                // $short_url_json=json_decode($short_url_json);
                // return $short_url_json;
                $short_url="";
                if ($short_url_json->status=='success') {
                    $short_url=$short_url_json->shorturl;
                }
                // return $short_url;
                $update=Query::find($data->id);
                $update->short_url=$short_url;
                $update->save();
                /**********************************************************/
                $data=Query::with('allscheme','allscheme.schemename')
                    ->with('allattach')
                    // ->with('entryattach')->with('solveattach')
                    ->leftJoin('md_query_status','md_query_status.id','=','td_query.query_status_id')
                    ->leftJoin('md_query_type','md_query_type.id','=','td_query.query_type_id')
                    ->leftJoin('md_query_sub_type','md_query_sub_type.id','=','td_query.query_subtype_id')
                    ->leftJoin('md_query_given_by','md_query_given_by.id','=','td_query.query_given_by_id')
                    ->leftJoin('md_query_rec_given_through','md_query_rec_given_through.id','=','td_query.query_rec_through_id')
                    ->leftJoin('md_query_nature','md_query_nature.id','=','td_query.query_nature_id')
                    ->leftJoin('md_query_rec_given_through as md_query_given_through','md_query_given_through.id','=','td_query.query_given_through_id')
                    
                    ->leftJoin('md_client','md_client.id','=','td_query.invester_id')
                    ->leftJoin('users','users.id','=','td_query.query_rec_by_id')
                    ->select('td_query.*','md_query_status.status_name','md_query_status.color_code','md_query_type.query_type','md_query_sub_type.query_subtype','md_query_sub_type.query_tat',
                    'md_query_given_by.name as query_given_by','md_query_rec_given_through.name as query_receive_through','md_query_nature.query_nature','md_query_given_through.name as query_given_through',
                    'md_client.client_name as investor_name','md_client.pan as investor_pan','md_client.id as investor_id','md_client.email as investor_email','md_client.mobile as investor_mobile',
                    // 'md_scheme.scheme_name as scheme_name','md_plan.plan_name as plan_name','md_option.opt_name as option_name','md_amc.amc_name'
                    'users.name as entry_name'
                    )
                    ->where('td_query.id',$data->id)
                    ->first();
                // email and sms 
                /**add working day */
                // return $data;
                $startDate=date('Y-m-d',strtotime($data->date_time));
                $daysToAdd=$data->query_tat;
                $holidays=Holiday::where('occ_date','>',date('Y-m-d'))->orderBy('occ_date','ASC')->pluck('occ_date')->all();
                // return $holidays;
                $newDate = Helper::addWorkingDays($startDate, $daysToAdd, $holidays);
                // return $newDate;
                $data->expected_close_date=$newDate;
                // return $data;
                /**end working day */
                
                // $invester_email=DB::table('md_client')->where('id',$invester_id)->first();
                $investor_name=$data->investor_name;
                $investor_email=$data->investor_email;
                $mobile_no=$data->investor_mobile;
                $query_status=$data->status_name;
                // $query_status=DB::table('md_query_status')->where('id',2)->value('status_name');
                $subject="Query status changed to ".$query_status."- QueryId : ".$query_id;
                $res=SMSHelper::registerReOpen($mobile_no,$short_url,$query_status,$investor_name,$query_id);
                // return $res;
                // Mail::to($investor_email)->send(new QueryStatusEmail($subject,$investor_name,$query_status,$query_status_id,$data));
                $files=[];
                // return $data->allattach;
                $entry_attachment=[];
                foreach ($data->allattach as $key => $value) {
                    if ($value->query_status_id==2) {
                        // array_push($entry_attachment,$value->name);
                        array_push($entry_attachment,$value);
                    }
                }
                $data->entry_attachment=$entry_attachment;
                if (count($data->entry_attachment)>0) {
                    // foreach ($allAtched as $key => $value1) {
                    //     $filePath=public_path('query-entry/'.$value1->name);
                    //     array_push($files,$filePath);
                    // }
                    Mail::to($investor_email)->send(new QueryStatusEmail($subject,$investor_name,$query_status,$query_status_id,$data,$files,$total_client_count,$total_amu_balance));
                }else {
                    Mail::to($investor_email)->send(new QueryStatusEmail($subject,$investor_name,$query_status,$query_status_id,$data,$files,$total_client_count,$total_amu_balance));
                }
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
            $valuation_as_on=date('Y-m-d');
            session()->forget('valuation_as_on');
            session(['valuation_as_on' => $valuation_as_on]);
            $rawQuery='';
            if ($folio_no) {
                $queryString='td_mutual_fund_trans.folio_no';
                $rawQuery.=Helper::WhereRawQuery($folio_no,$rawQuery,$queryString);
            }
            $all_data=MutualFundTransaction::with('profitloss')->leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_mutual_fund_trans.product_code')
                ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                ->leftJoin('md_category','md_category.id','=','md_scheme.category_id')
                ->leftJoin('md_subcategory','md_subcategory.id','=','md_scheme.subcategory_id')
                ->leftJoin('md_amc','md_amc.amc_code','=','td_mutual_fund_trans.amc_code')
                ->leftJoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
                ->leftJoin('md_option','md_option.id','=','md_scheme_isin.option_id')
                ->select('td_mutual_fund_trans.rnt_id','td_mutual_fund_trans.folio_no','td_mutual_fund_trans.product_code','td_mutual_fund_trans.pur_price','td_mutual_fund_trans.trans_date',
                'md_scheme.scheme_name as scheme_name','md_category.cat_name as cat_name','md_subcategory.subcategory_name as subcat_name','md_amc.amc_short_name as amc_name','md_amc.amc_code as amc_code','md_amc.id as amc_id',
                'md_plan.plan_name as plan_name','md_option.opt_name as option_name')
                ->selectRaw('IF(td_mutual_fund_trans.rnt_id=1,md_scheme_isin.isin_no,td_mutual_fund_trans.isin_no) as isin_no')
                ->selectRaw('sum(td_mutual_fund_trans.units) as tot_units')
                ->selectRaw('sum(td_mutual_fund_trans.amount) as tot_amount')
                ->selectRaw('sum(td_mutual_fund_trans.stamp_duty) as tot_stamp_duty')
                ->selectRaw('sum(td_mutual_fund_trans.tds) as tot_tds')
                ->selectRaw('count(*) as tot_rows')
                ->where('td_mutual_fund_trans.delete_flag','N')
                ->where('td_mutual_fund_trans.amc_flag','N')
                ->where('td_mutual_fund_trans.scheme_flag','N')
                ->where('td_mutual_fund_trans.plan_option_flag','N')
                ->where('td_mutual_fund_trans.bu_type_flag','N')
                ->where('td_mutual_fund_trans.divi_mismatch_flag','N')
                ->whereRaw($rawQuery)
                ->groupBy('td_mutual_fund_trans.folio_no')
                ->groupBy('td_mutual_fund_trans.product_code')
                ->groupBy('td_mutual_fund_trans.isin_no')
                ->orderBy('td_mutual_fund_trans.trans_date','ASC')
                ->get();
            // dd(DB::getQueryLog());
            $all_trans_product=[];
            $data=[];
            foreach ($all_data as $key => $value) {
                $value->inv_since=date('Y-m-d',strtotime($value->trans_date));
                $value->pur_nav=$value->pur_price;
                $f_trans_product="(nav_date=(SELECT MAX(nav_date) FROM td_nav_details WHERE product_code='".$value->product_code."' AND nav_date <='".$valuation_as_on."') AND product_code='".$value->product_code."')";
                array_push($all_trans_product,$f_trans_product);
                array_push($data,$value);
            }
            usort($data, function($a, $b) {
                return $a['scheme_name'] <=> $b['scheme_name'];
            });
            // return $data;
            $string_version_product_code = implode(',', $all_trans_product);
            // return $string_version_product_code;
            $res_array =DB::connection('mysql_nav')
                ->select('SELECT product_code,isin_no,DATE_FORMAT(nav_date, "%Y-%m-%d") as nav_date,nav FROM td_nav_details where '.str_replace(",","  OR  ",$string_version_product_code));
            // return $res_array;
            $filter_data=[];
            foreach ($data as $data_key => $value1) {
                $isin_no=$value1->isin_no;
                $product_code=$value1->product_code;
                $new='';
                if (count($res_array) > 0) {
                    foreach($res_array as $val_nav){
                        if($val_nav->product_code==$product_code){
                            $new=$val_nav;
                        }
                    }
                }
                // return $new;
                $value1->new=$new;
                $value1->curr_nav=isset($new->nav)?$new->nav:0;
                $value1->nav_date=isset($new->nav_date)?$new->nav_date:0;
                //calculation
                $profitloss=$value1->profitloss;
                $final_profitloss=[];
                foreach ($profitloss as $key => $profitloss_value) {
                    if ($profitloss_value->transaction_type=="Transfer In" && $profitloss_value->transaction_subtype=="Transfer In") {
                        $broker_data=TransHelper::getBrokerData($profitloss_value);
                        foreach ($broker_data as $broker_key => $broker_value) {
                            array_push($final_profitloss,$broker_value);
                        }
                    }else {
                        array_push($final_profitloss,$profitloss_value);
                    }
                }
                $profitloss=$final_profitloss;
                $value1->final_profitloss=$profitloss;
                $purchase=0;
                $switch_in=0;
                $tot_inflow=0;
                $redemption=0;
                $switch_out=0;
                $idcw_reinv=0;
                $idcwp=0;
                $tot_outflow=0;
                if ($value1->tot_amount > 0) {
                    foreach ($profitloss as $key_1 => $profitloss_value_1) {
                        if ($profitloss_value_1->lmf_pl=='PL_P') {
                            $purchase +=$profitloss_value_1->tot_amount;
                        }elseif ($profitloss_value_1->lmf_pl=='PL_R') {
                            $redemption +=$profitloss_value_1->tot_amount;
                        }elseif ($profitloss_value_1->lmf_pl=='PL_SI') {
                            $switch_in +=$profitloss_value_1->tot_amount;
                        }elseif ($profitloss_value_1->lmf_pl=='PL_SO') {
                            $switch_out +=$profitloss_value_1->tot_amount;
                        }elseif ($profitloss_value_1->lmf_pl=='PL_IR') {
                            $idcw_reinv +=$profitloss_value_1->tot_amount;
                        }elseif ($profitloss_value_1->lmf_pl=='PL_IP') {
                            $idcwp +=$profitloss_value_1->tot_amount;
                        }
                    }
                }
                
                $value1->purchase=$purchase;
                $value1->redemption=$redemption;
                $value1->switch_in=$switch_in;
                $value1->switch_out=$switch_out;
                $value1->idcw_reinv=$idcw_reinv;
                $value1->idcwp=$idcwp;
                $value1->tot_inflow=($purchase + $switch_in + $idcw_reinv);
                $value1->tot_outflow=($redemption + $switch_out + $idcwp);

                $my_profitloss=$value1->profitloss;
                $mydata='';
                $json  = json_encode($my_profitloss);
                $array = json_decode($json, true);
                if (array_search('Consolidation In',array_column($array,'transaction_subtype'))) {
                    $my_profitloss=TransHelper::ConsolidationInQuery($value1->rnt_id,$value1->folio_no,$value1->isin_no,$value1->product_code,$valuation_as_on);
                }
                
                $mydata=TransHelper::calculate($my_profitloss,$value1->curr_nav,$valuation_as_on);
                
                $value1->mydata=$mydata;
                $value1->idcw_reinv=isset($mydata['idcw_reinv'])? number_format((float)$mydata['idcw_reinv'], 2, '.', ''):0;
                $value1->idcwr=number_format((float)($value1->idcwp + $value1->idcw_reinv), 2, '.', '');
                $value1->inv_since=isset($mydata['inv_since'])? $mydata['inv_since']:$value1->inv_since;
                $value1->pur_nav=isset($mydata['pur_nav'])?$mydata['pur_nav']:$value1->pur_nav;
                $value1->transaction_type=isset($mydata['transaction_type'])?$mydata['transaction_type']:$value1->transaction_type;
                $value1->transaction_subtype=isset($mydata['transaction_subtype'])?$mydata['transaction_subtype']:$value1->transaction_subtype;
                $value1->inv_cost=isset($mydata['inv_cost'])?number_format((float)$mydata['inv_cost'], 2, '.', ''):0;
                $value1->tot_units=isset($mydata['tot_units'])?number_format((float)$mydata['tot_units'], 2, '.', ''):0;
                $value1->curr_val= number_format((float)($value1->curr_nav * $value1->tot_units), 2, '.', '');
                // $value1->gain_loss=number_format((float)(($value1->curr_val - $value1->inv_cost) + $value1->idcwr), 2, '.', '');
                // if ($value1->gain_loss==0 || $value1->inv_cost==0) {
                //     $value1->ret_abs=0;
                // }else {
                //     $value1->ret_abs=number_format((float)(($value1->gain_loss / $value1->inv_cost) * 100), 2, '.', '');
                // }

                $value1->gain_loss=number_format((float)(($value1->tot_outflow + $value1->curr_val) - $value1->tot_inflow), 2, '.', '');
                if ($value1->gain_loss==0 || $value1->tot_inflow==0) {
                    $value1->ret_abs=0;
                }else {
                    $value1->ret_abs=number_format((float)(($value1->gain_loss / $value1->tot_inflow) * 100), 2, '.', '');
                }
                array_push($filter_data,$value1);
            }
            
        } catch (\Throwable $th) {
            //throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($filter_data);
    }


    public function queryInform(Request $request)
    {
        try {
            $query_id=$request->query_id;
            $inform_flag=$request->inform_flag;
            $update=Query::find($query_id);
            if ($inform_flag=='C') {
                $update->call_flag='Y';
                $update->call_date=date('Y-m-d H:i:s');
            }elseif ($inform_flag=='W') {
                $update->whats_app_flag='Y';
                $update->whats_app_date=date('Y-m-d H:i:s');
            }elseif ($inform_flag=='E') {
                $update->email_flag='Y';
                $update->email_date=date('Y-m-d H:i:s');
            }elseif ($inform_flag=='S') {
                $update->sms_flag='Y';
                $update->sms_date=date('Y-m-d H:i:s');
            }
            $update->save();
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($update);
    }

    public function feedback(Request $request)
    {
        try {
            // return $request;
            $query_id=Crypt::decrypt($request->enc_query_id);
            $id=Query::where('query_id',$query_id)->value('id');
            $data=Query::find($id);
            $data->rating=$request->rating;
            $data->query_feedback=$request->query_feedback;
            $data->suggestion=$request->suggestion;
            $data->save();
        } catch (\Throwable $th) {
            // throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function addTATRemarks(Request $request)
    {
        try {
            // return $request;
            $id=$request->id;
            $data=Query::find($id);
            $data->tat_remarks=$request->tat_remarks;
            $data->save();

            // $investor_name=$data->investor_name;
            //     $investor_email=$data->investor_email;
            //     $mobile_no=$data->investor_mobile;
            //     $query_status=$data->status_name;
            //     // $query_status=DB::table('md_query_status')->where('id',2)->value('status_name');
            //     $subject="Query status changed to ".$query_status."- QueryId : ".$query_id;
            //     // $res=SMSHelper::registerReOpen($mobile_no,$short_url,$query_status,$investor_name,$query_id);
            //     // return $res;
            //     // Mail::to($investor_email)->send(new QueryStatusEmail($subject,$investor_name,$query_status,$query_status_id,$data));

        } catch (\Throwable $th) {
            // throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($data);
    }

    public function downloadFile(Request $request)
    {
        try {
            $query_id=Crypt::decrypt($request->query_id);
            // return $query_id;
            // $allAtched=QueryAttachment::where('query_id',$query_id)->where('query_status_id',2)->get();
            $allAtched=QueryAttachment::where('id',$query_id)
                // ->where('query_status_id',2)
                ->get();
            // return $allAtched;
            if (count($allAtched)>0) {
                if (count($allAtched)==1) {
                    // $fileName=public_path('query-entry/'.$allAtched[0]->name);
                    $fileName=$allAtched[0]->name;
                    // return $fileName;
                    $final_file_name=env('APP_URL_IP')."/public/query-attachment/".$fileName;
                }
                // else {
                //     $all_files=[];
                //     foreach ($allAtched as $key => $value1) {
                //         // $filePath=public_path('query-entry/'.$value1->name);
                //         $filePath=$value1->name;
                //         array_push($all_files,$filePath);
                //     }
                //     // return $files;
                //     $zip = new \ZipArchive();
                //     $fileName = 'query-attachment-zip/zipFile_'. (string)$query_id.'.zip';
                //     if (file_exists(public_path($fileName))) {
                //         public_path(public_path($fileName));
                //     }
                //     if ($zip->open(public_path($fileName), \ZipArchive::CREATE)== TRUE)
                //     {
                //         $files = File::files(public_path('query-attachment'));
                //         // return $files;
                //         foreach ($files as $key => $file){
                //             // return $file->getFilename();
                //             $relativeName = basename($file);
                //             if (in_array($relativeName, $all_files)) {
                //                 $zip->addFile($file, $relativeName);
                //             }
                //         }
                //         $zip->close();
                //     }
                //     $final_file_name=env('APP_URL_IP')."/public/".$fileName;
                // }
            }
            // return $final_file_name;
            // return response()->download(public_path($fileName));
        } catch (\Throwable $th) {
            throw $th;
            return Helper::ErrorResponse(parent::DATA_FETCH_ERROR);
        }
        return Helper::SuccessResponse($final_file_name);
    }
    


    public function sendSMS111()
    {
        $apiKey = urlencode(env('SMS_API_KEY'));
 
        // Prepare data for POST request
        $data = array('apikey' => $apiKey);
    
        // Send the POST request with cURL
        $ch = curl_init('https://api.textlocal.in/get_templates/');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $response = curl_exec($ch);
        curl_close($ch);
        // Process your response here
        // echo $response;
        return json_decode($response);
    }

    public function sendSMS()
    {
        // display_errors = On display_startup_errors = On
        // return 'hii';
        // $url="https://nuedgecorporate.co.in/main/home";
        // // return $url;
        // $data=SMSHelper::createShortUrl($url);
        // return $data;

        // $message1 ='Dear %%|name^{\"inputtype\" : \"text\", \"maxlength\" : \"30\"}%%,\r\n\r\nGreetings from NuEdge Corporate Private Limited\r\n\r\nYour below query is %%|text^{\"inputtype\" : \"text\", \"maxlength\" : \"300\"}%%.\r\n\r\nQuery Id- %%|number^{\"inputtype\" : \"text\", \"maxlength\" : \"10\"}%%.\r\n\r\nQuery Details- %%|text^{\"inputtype\" : \"text\", \"maxlength\" : \"300\"}%%.\r\n\r\n\r\nNow you can post your Query directly to NuEdge Customer Care. Call or Whatsapp- 9830939393. Timing Monday to Friday from 10 A.M to 6 P.M.\r\n\r\n\r\nRegards,\r\nNuEdge Corporate Private Limited.\r\nAMFI- Registered Mutual Fund Distributor\r\n\r\nMutual Fund investments are subject to market risks, read all scheme related documents carefully.';
        $message1 ='Dear Chitta,

Greetings from NuEdge Corporate Private Limited

Your below query is Complete.

Query Id- 122.

Query Details- werewrw.

Actual Close Date- 07-11-2024.

Now you can post your Query directly to NuEdge Customer Care. Call or Whatsapp- 9830939393. Timing Monday to Friday from 10 A.M to 6 P.M.

Customer satisfaction is always a number one priority for us. Your feedback will help us make our services better. Please share your feedback click here- sddddddd


Regards,
NuEdge Corporate Private Limited.
AMFI- Registered Mutual Fund Distributor

Mutual Fund investments are subject to market risks, read all scheme related documents carefully.';
        
        // 'Dear chitta,\r\n\r\nGreetings from NuEdge Corporate Private Limited\r\n\r\nYour below query is dgdg.\r\n\r\nQuery Id- d444.\r\n\r\nQuery Details- dgdfhtdh.\r\n\r\n\r\nNow you can post your Query directly to NuEdge Customer Care. Call or Whatsapp- 9830939393. Timing Monday to Friday from 10 A.M to 6 P.M.\r\n\r\n\r\nRegards,\r\nNuEdge Corporate Private Limited.\r\nAMFI- Registered Mutual Fund Distributor\r\n\r\nMutual Fund investments are subject to market risks, read all scheme related documents carefully.';
        
        // return $message1;
        // return nl2br($message1);
        
        $apiKey = urlencode(env('SMS_API_KEY'));
	
        // Message details
        // $numbers = array(11111111, 918987654321);
        $numbers = array(env('SMS_MOBILE_NO'));
        $sender = urlencode(env('SMS_SENDER_NAME'));
        // $message = rawurlencode(nl2br($message1));
        $message = rawurlencode($message1);
        // return $message;
        // $message='Dear%20Name%2C%0A%0A/nGreetings%20from%20NuEdge%20Corporate%20Private%20Limited%0A%0A/nYour%20below%20query%20is%20text.%0A%0A/nQuery%20Id-%202e314e3e.%0A%0A/nQuery%20Details-%20teqwedqwe3q2ext.%0A%0A/nExpected%20Close%20Date-%20er233232ed.%0A%0A/nNow%20you%20can%20post%20your%20Query%20directly%20to%20NuEdge%20Customer%20Care.%20Call%20or%20Whatsapp-%209830939393.%20Timing%20Monday%20to%20Friday%20from%2010%20A.M%20to%206%20P.M.%0A%0A/n/nWe%20sincerely%20regret%20the%20inconvenience%20caused%20by%20the%20delay.%0A%0A%0A/n/n/nRegards%2C%0A/nNuEdge%20Corporate%20Private%20Limited.%0A/nAMFI-%20Registered%20Mutual%20Fund%20Distributor%0A%0A/n/nMutual%20Fund%20investments%20are%20subject%20to%20market%20risks%2C%20read%20all%20scheme%20related%20documents%20carefully.';
        $numbers = implode(',', $numbers);
        // return $numbers;
        // Prepare data for POST request
        $data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);
        $ch = curl_init('https://api.textlocal.in/send/');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $response = curl_exec($ch);
        curl_close($ch);
        // Process your response here
        // echo $response;
        return json_decode($response) ;
    }

    public function whatsapp(Request $request)
    {
        // return $request;
        $messages = array(
            // Put parameters here such as force or test
            'send_channel' => 'whatsapp',
            'messages' => array(
                array(
                    'number' => '',
                    'template' => array(
                        'id' => '',
                        'merge_fields' => array(
                            'Name' => ' ',
                            'Status' => ' ',
                            'Number' => ' ',
                            'Details' => ' ',
                            'Date' => ' ',
                            'Link' => ' ',
                        )
                    )
                )
            )
        );
         
        // Prepare data for POST request
        $data = array(
            'apikey' => env('SMS_API_KEY'),
            'data' => json_encode($messages),
            'test'=>true
        );
         
        // Send the POST request with cURL
        $ch = curl_init('https://api.textlocal.in/bulk_json/');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        //     'Content-Type: application/json',
        //     'Connection: Keep-Alive'
        //     ));
        $response = curl_exec($ch);
        curl_close($ch);
         
        echo $response;
        
    }
}