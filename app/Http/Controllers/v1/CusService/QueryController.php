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
    QueryEntryAttach,
    QuerySolveAttach
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
                $data=Query::with('entryattach')->with('solveattach')
                    ->leftJoin('md_query_status','md_query_status.id','=','td_query.query_status_id')
                    ->leftJoin('md_client','md_client.id','=','td_query.invester_id')
                    ->select('td_query.*','md_query_status.status_name','md_query_status.color_code',
                    'md_client.client_name as investor_name','md_client.pan as investor_pan','md_client.id as investor_id','md_client.email as investor_email','md_client.mobile as investor_mobile')
                    ->where('td_query.id',$id)
                    ->first();
            } else {
                $rawQuery='';
                if ($query_status_id || $query_mode_id) {
                    $queryString='td_query.query_status_id';
                    $rawQuery.=Helper::WhereRawQuery($query_status_id,$rawQuery,$queryString);
                    $queryString1='td_query.product_id';
                    $rawQuery.=Helper::WhereRawQuery($product_id,$rawQuery,$queryString1);
                    $queryString2='td_query.query_mode_id';
                    $rawQuery.=Helper::WhereRawQuery($query_mode_id,$rawQuery,$queryString2);
                }else {
                    $queryString='td_query.product_id';
                    $rawQuery.=Helper::WhereRawQuery($product_id,$rawQuery,$queryString);
                }
                if ($product_id==1) {
                    $data=Query::leftJoin('md_query_status','md_query_status.id','=','td_query.query_status_id')
                        ->leftJoin('md_query_type','md_query_type.id','=','td_query.query_type_id')
                        ->leftJoin('md_query_sub_type','md_query_sub_type.id','=','td_query.query_subtype_id')
                        ->leftJoin('md_query_given_by','md_query_given_by.id','=','td_query.query_given_by_id')
                        ->leftJoin('md_query_rec_given_through','md_query_rec_given_through.id','=','td_query.query_rec_through_id')
                        ->leftJoin('md_query_nature','md_query_nature.id','=','td_query.query_nature_id')
                        ->leftJoin('md_query_rec_given_through as md_query_given_through','md_query_given_through.id','=','td_query.query_given_through_id')
                        
                        
                        ->leftJoin('md_client','md_client.id','=','td_query.invester_id')
                        ->leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_query.product_code')
                        ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
                        ->leftJoin('md_amc','md_amc.id','=','md_scheme.amc_id')
                        ->leftJoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
                        ->leftJoin('md_option','md_option.id','=','md_scheme_isin.option_id')
                        ->select('td_query.*','md_query_status.status_name','md_query_status.color_code','md_query_type.query_type','md_query_sub_type.query_subtype','md_query_sub_type.query_tat',
                        'md_query_given_by.name as query_given_by','md_query_rec_given_through.name as query_receive_through','md_query_nature.query_nature','md_query_given_through.name as query_given_through',
                        'md_client.client_name as investor_name','md_client.pan as investor_pan','md_client.id as investor_id','md_client.email as investor_email','md_client.mobile as investor_mobile',
                        'md_scheme.scheme_name as scheme_name','md_plan.plan_name as plan_name','md_option.opt_name as option_name','md_amc.amc_name')
                        ->whereRaw($rawQuery)
                        ->get();
                } else if ($product_id==2) {
                    $data=Query::leftJoin('md_query_status','md_query_status.id','=','td_query.query_status_id')
                        ->leftJoin('md_client','md_client.id','=','td_query.invester_id')
                        ->select('td_query.*','md_query_status.status_name','md_query_status.color_code',
                        'md_client.client_name as investor_name','md_client.pan as investor_pan','md_client.id as investor_id','md_client.email as investor_email','md_client.mobile as investor_mobile')
                        ->whereRaw($rawQuery)
                        ->get();
                } else if ($product_id==3) {
                    $data=Query::leftJoin('md_query_status','md_query_status.id','=','td_query.query_status_id')
                        ->leftJoin('md_client','md_client.id','=','td_query.invester_id')
                        ->leftJoin('md_ins_products','md_ins_products.id','=','td_query.ins_product_id')
                        ->select('td_query.*','md_query_status.status_name','md_query_status.color_code',
                        'md_client.client_name as investor_name','md_client.pan as investor_pan','md_client.id as investor_id','md_client.email as investor_email','md_client.mobile as investor_mobile',
                        'md_ins_products.product_name')
                        ->whereRaw($rawQuery)
                        ->get();
                } else if ($product_id==4) {
                    $data=Query::leftJoin('md_query_status','md_query_status.id','=','td_query.query_status_id')
                        ->leftJoin('md_client','md_client.id','=','td_query.invester_id')
                        ->leftJoin('md_fd_scheme','md_fd_scheme.id','=','td_query.fd_scheme_id')
                        ->select('td_query.*','md_query_status.status_name','md_query_status.color_code',
                        'md_client.client_name as investor_name','md_client.pan as investor_pan','md_client.id as investor_id','md_client.email as investor_email','md_client.mobile as investor_mobile',
                        'md_fd_scheme.scheme_name')
                        ->whereRaw($rawQuery)
                        ->get();
                }else {
                    $data=Query::leftJoin('md_query_status','md_query_status.id','=','td_query.query_status_id')
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
            if ($request->id > 0) {
                // return $request;
                $update_data=Query::find($request->id);

                if ($request->query_nature_id==4) { //  external
                    $update_data->query_given_to_id=$request->query_given_to_id;
                    $update_data->query_rec_through_id=$request->query_rec_through_id;
                    $update_data->query_given_through_id=$request->query_given_through_id;
                    $update_data->concern_person_name=$request->concern_person_name;
                    $update_data->contact_no=$request->contact_no;
                    $update_data->email_id=$request->email_id;
                    $update_data->expected_close_date=$request->expected_close_date;
                    $update_data->query_mode_id=$request->query_mode_id;
                }
                $update_data->query_nature_id=$request->query_nature_id;
                $update_data->query_status_id=$request->query_status_id;
                if ($request->query_status_id==5 || $request->query_status_id==5) {
                    $update_data->actual_close_date=date('Y-m-d H:i:s');
                }
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
                            $doc_name=(microtime(true)*1000).".".$doc_path_extension;
                            $file->move(public_path('query-solve/'),$doc_name);
                        }
                        QuerySolveAttach::create([
                            'query_id'=>$update_data->id,
                            'name'=>$doc_name,
                            'created_by',
                            'updated_by',
                        ]);
                    }
                }
                $data=Query::leftJoin('md_query_status','md_query_status.id','=','td_query.query_status_id')
                    ->select('td_query.*','md_query_status.status_name','md_query_status.color_code')
                    ->where('td_query.id',$update_data->id)
                    ->first();
            }else{
                // return $request;
                // QueryEntryAttach
                // QuerySolveAttach
                $investor_pan=$request->investor_pan;
                $investor_name=$request->investor_name;
                if ($investor_pan) {
                    $invester_id=DB::table('md_client')->where('pan',$investor_pan)->value('id');
                }else {
                    $invester_id=DB::table('md_client')->where('client_name',$investor_name)->value('id');
                }
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
                    'entry_name'=>$request->entry_name,
                    'product_code'=>$request->product_code,
                    'isin_no'=>$request->isin_no,
                    'query_type_id'=>$request->query_type_id,
                    'query_subtype_id'=>$request->query_subtype_id,
                    'query_details'=>$request->query_details,
                    // 'query_nature_id'=>$request->query_nature_id,
                    // 'query_given_to_id'=>$request->query_given_to_id,
                    // 'query_rec_through_id'=>$request->query_receive_through_id,
                    // 'query_given_through_id'=>$request->query_given_through_id,
                    // 'concern_per_name'=>$request->concern_person_name,
                    // 'contact_no'=>$request->contact_no,
                    // 'email_id'=>$request->email_id,
                    // 'expected_close_date'=>$request->expected_close_date,
                    // 'actual_close_date'=>$request->actual_close_date,
                    'query_status_id'=>2, //Registered
                    // 'remarks'=>$request->remarks,
                    // 'query_mode_id'=>$request->query_mode_id,
                    // 'query_feedback',
                    // 'overall_feedback',
                    'created_by'=>Helper::modifyUser($request->user()),
                    'updated_by'=>Helper::modifyUser($request->user()),
                ];
                $data=Query::create($data_arr); 
                
                // $entry_attachment=json_decode($request->entry_attachment);
                $entry_attachment=$request->entry_attachment;
                // return $entry_attachment;
                $doc_name='';
                foreach ($entry_attachment as $key => $file) {
                    // return $file;
                    if ($file) {
                        $doc_path_extension=$file->getClientOriginalExtension();
                        $doc_name=(microtime(true)*1000).".".$doc_path_extension;
                        $file->move(public_path('query-entry/'),$doc_name);
                    }
                    QueryEntryAttach::create([
                        'query_id'=>$data->id,
                        'name'=>$doc_name,
                        'created_by',
                        'updated_by',
                    ]);
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

    public function sendSMS()
    {
        // return 'hii';
        $apiKey = urlencode(env('SMS_API_KEY'));
	
        // Message details
        // $numbers = array(11111111, 918987654321);
        $numbers = array(1111111111);
        $sender = urlencode(env('SMS_SENDER_NAME'));
        $message = rawurlencode("Dear Chittaranjan Maity,

Greetings from NuEdge Corporate Private Limited.

Please find below the link of Valuation Report.

 https://www.google.com/
Please feel free to write or call back to us with any queries. We would be delighted to assist you.
NuEdge Customer Service No-9830939393 (Monday to Friday 10 a.m to 6 p.m)

Your PAN number is password!!!.

Regards,
NuEdge Corporate Pvt. Ltd.
AMFI Registered Mutual Fund Distributor");
        // return $message;
        // $message="Dear%20Chittaranjan%20Maity%2C%0A%0AGreetings%20from%20NuEdge%20Corporate%20Private%20Limited.%0A%0APlease%20find%20below%20the%20link%20of%20Valuation%20Report.%0A%0A%20https%3A%2F%2Fwww.google.com%2F%0APlease%20feel%20free%20to%20write%20or%20call%20back%20to%20us%20with%20any%20queries.%20We%20would%20be%20delighted%20to%20assist%20you.%0ANuEdge%20Customer%20Service%20No-9830939393%20%28Monday%20to%20Friday%2010%20a.m%20to%206%20p.m%29%0A%0AYour%20PAN%20number%20is%20password%21%21%21.%0A%0ARegards%2C%0ANuEdge%20Corporate%20Pvt.%20Ltd.%0AAMFI%20Registered%20Mutual%20Fund%20Distributor";
        $numbers = implode(',', $numbers);
     
        // Prepare data for POST request
        $data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);
     
        // Send the POST request with cURL
        $ch = curl_init('https://api.textlocal.in/send/');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        
        // Process your response here
        echo $response;
    }
}