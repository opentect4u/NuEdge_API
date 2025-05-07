<?php

namespace App\Http\Controllers\V1\Operation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\{MutualFund,Client,FormReceived,FolioDetailsReport,
    MutualFundTransaction,FolioDetails,Query,QueryAttachment,QuerySolveAttach,QueryEntryAttach,QueryScheme};

use Validator;
use Illuminate\Support\Carbon;

class TestOperationController extends Controller
{
    public function index(Request $request)
    {
        // $non_fin_array=array(29,23);
        $non_fin_array=array(23);
            // DB::enableQueryLog();
        $all_data=MutualFund::join('md_client','md_client.id','=','td_mutual_fund.first_client_id')
            ->join('md_trans','md_trans.id','=','td_mutual_fund.trans_id')
            ->join('md_scheme','md_scheme.id','=','td_mutual_fund.trans_scheme_from')
            ->join('md_amc','md_amc.id','=','md_scheme.amc_id')
            ->select('td_mutual_fund.id as id','td_mutual_fund.trans_id','td_mutual_fund.rnt_login_dt','td_mutual_fund.rnt_login_cutt_off','td_mutual_fund.trans_scheme_from',
                'td_mutual_fund.option_id','td_mutual_fund.plan_id','td_mutual_fund.trans_scheme_to','td_mutual_fund.first_client_id',
                'md_client.client_name as first_client_name','md_client.pan as first_client_pan','td_mutual_fund.amount as amount',
                'td_mutual_fund.folio_no','md_trans.trans_type_id','md_amc.rnt_id')
            ->selectRaw('(SELECT product_code FROM md_scheme_isin WHERE scheme_id=td_mutual_fund.trans_scheme_from AND plan_id=td_mutual_fund.plan_id AND option_id=td_mutual_fund.option_id LIMIT 1)as product_code')
            ->where('td_mutual_fund.form_status','A')
            ->whereIn('td_mutual_fund.trans_id',$non_fin_array)
            ->orderBy('td_mutual_fund.rnt_login_dt','DESC')
            ->get();
        // dd(DB::getQueryLog());
        // \Log::info(DB::getQueryLog());
        return $all_data;
        foreach ($all_data as $key => $value) {
            // \Log::info($value->id);
            // \Log::info($value->rnt_login_dt);
            // \Log::info($value->first_client_id);
            // \Log::info($value->product_code);
            // \Log::info($value->first_client_name);
            // \Log::info($value->first_client_pan);
            // \Log::info($value->rnt_login_cutt_off);
            // \Log::info($value->folio_no);
            // \Log::info($value->amount);
            // \Log::info('---------------------------');
            // \Log::info($value->amount);
            // return $value;
            $trans_id=$value->trans_id;
            switch ($trans_id) {
                case '29':
                    if ($value->first_client_pan) {
                        // \Log::info('if');
                        $mydata=MutualFundTransaction::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_mutual_fund_trans.product_code')
                            // ->leftjoin('md_scheme','md_scheme.scheme_id','=','md_scheme_isin.scheme_id')
                            ->select('td_mutual_fund_trans.rnt_id','td_mutual_fund_trans.product_code','td_mutual_fund_trans.folio_no','td_mutual_fund_trans.trans_date','td_mutual_fund_trans.amount','td_mutual_fund_trans.tds',
                            'md_scheme_isin.scheme_id','md_scheme_isin.plan_id','md_scheme_isin.option_id',
                            'td_mutual_fund_trans.remarks')
                            ->selectRaw('(SELECT id FROM md_client WHERE pan=td_mutual_fund_trans.first_client_pan LIMIT 1)as first_client_id')
                            ->where('td_mutual_fund_trans.product_code',$value->product_code)
                            ->where('td_mutual_fund_trans.first_client_pan',$value->first_client_pan)
                            ->where('td_mutual_fund_trans.folio_no','=',$value->folio_no)
                            ->whereDate('td_mutual_fund_trans.trans_date','=',$value->rnt_login_cutt_off)
                            ->where('td_mutual_fund_trans.amount','=',$value->amount)
                            ->orderBy('td_mutual_fund_trans.trans_date','ASC')
                            ->get()
                            ->take(1);
                        
                        // \Log::info(json_encode($mydata));
                        if (count($mydata)>0) {
                            $up_data=MutualFund::where('first_client_id',$mydata[0]->first_client_id)
                                ->where('folio_no',$mydata[0]->folio_no)
                                ->where('trans_scheme_from',$mydata[0]->scheme_id)
                                ->where('amount',$mydata[0]->amount)
                                ->whereDate('rnt_login_cutt_off','=',$mydata[0]->trans_date)
                                // ->count();
                                ->update([
                                    'manual_trans_status'=>'P',
                                    'process_date'=>$mydata[0]->trans_date,
                                    'form_status'=>'M',
                                    'folio_no'=>$mydata[0]->folio_no,
                                    'manual_update_remarks'=>$mydata[0]->remarks,
                                ]);
                        }
                    }else {
                        // \Log::info('else');
                        $mydata=MutualFundTransaction::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_mutual_fund_trans.product_code')
                            // ->leftjoin('md_scheme','md_scheme.scheme_id','=','md_scheme_isin.scheme_id')
                            ->select('td_mutual_fund_trans.product_code','td_mutual_fund_trans.folio_no','td_mutual_fund_trans.trans_date','td_mutual_fund_trans.amount','td_mutual_fund_trans.stamp_duty',
                            'md_scheme_isin.scheme_id','md_scheme_isin.plan_id','md_scheme_isin.option_id',
                            'td_mutual_fund_trans.remarks')
                            ->selectRaw('(SELECT id FROM md_client WHERE client_name=td_mutual_fund_trans.first_client_name LIMIT 1)as first_client_id')
                            ->where('td_mutual_fund_trans.product_code',$value->product_code)
                            ->where('td_mutual_fund_trans.first_client_name',$value->first_client_name)
                            ->where('td_mutual_fund_trans.folio_no','=',$value->folio_no)
                            ->whereDate('td_mutual_fund_trans.trans_date','=',$value->rnt_login_cutt_off)
                            ->where('td_mutual_fund_trans.amount','=',$value->amount)
                            // ->whereDate('td_mutual_fund_trans.trans_date','>=',date('Y-m-d',strtotime($value->rnt_login_dt)))
                            ->orderBy('td_mutual_fund_trans.trans_date','ASC')
                            ->get()
                            ->take(1);
                            
                        // \Log::info(json_encode($mydata));
                        if (count($mydata)>0) {
                         
                            $up_data=MutualFund::where('first_client_id',$mydata[0]->first_client_id)
                                ->where('folio_no',$mydata[0]->folio_no)
                                ->where('trans_scheme_from',$mydata[0]->scheme_id)
                                ->where('amount',$mydata[0]->amount)
                                ->whereDate('rnt_login_cutt_off','=',$mydata[0]->trans_date)
                                // ->count();
                                ->update([
                                    'manual_trans_status'=>'P',
                                    'process_date'=>$mydata[0]->trans_date,
                                    'form_status'=>'M',
                                    'folio_no'=>$mydata[0]->folio_no,
                                    'manual_update_remarks'=>$mydata[0]->remarks,
                                ]);
                        }
                    }
                    break;
                case '23':
                    $folio_details=FolioDetails::where('rnt_id',$value->rnt_id)
                        ->where('product_code',$value->product_code)
                        ->where('folio_no',$value->folio_no)
                        ->orderBy('folio_date','ASC')
                        ->get();
                    // return $folio_details;
                    if (count($folio_details)>0) {
                        // return $folio_details;
                        // return request()->ip();
                        $pan=$folio_details[0]->pan;
                        $new_name=$folio_details[0]->first_client_name;
                        $product_code=$folio_details[0]->product_code;
                        $folio_no=$folio_details[0]->folio_no;
                        if($pan){
                            $first_client_id=Client::where('pan',$pan)->pluck('id')->first();
                            $up_data=Client::find($first_client_id);
                            if ($up_data->client_name!=$new_name) {
                                $client_name=ucwords($new_name);
                                $words = explode(" ",$client_name);
                                $client_code="";
                                $client_code_1 = mb_substr($words[0], 0, 1).mb_substr($words[(count($words)-1)], 0, 1);;
                                $is_has=Client::where('client_code',$client_code_1)->get();
                                if (count($is_has)>0) {
                                    $client_code=$client_code_1.date('dmy',strtotime($up_data->dob)).count($is_has);
                                }else {
                                    $client_code=$client_code_1.date('dmy',strtotime($up_data->dob));
                                }
                                $up_data->client_code=$client_code;
                                $up_data->client_name=$client_name;
                                $up_data->updated_by=1;
                                $up_data->save();
                                // return $up_data;
                                $description='Client Name Update';
                                Helper::createLog($first_client_id,$description);
                            }
                            // return $up_data;
                        }
                        MutualFundTransaction::where('product_code',$product_code)
                            ->where('folio_no',$folio_no)
                            ->update([
                                'first_client_name'=>$new_name,
                                'updated_at'=>date('Y-m-d H:i:s'),
                            ]);
                        MutualFund::where('id',$value->id)
                            ->update([
                                'manual_trans_status'=>'P',
                                'process_date'=>date('Y-m-d'),
                                'form_status'=>'M',
                                // 'folio_no'=>$mydata[0]->folio_no,
                                // 'manual_update_remarks'=>$mydata[0]->remarks,
                            ]);
                    }
                    break;
                default:
                    # code...
                    break;
            }
            
            // \Log::info(json_encode($mydata));
            // \Log::info($mydata[0]->product_code);
            // \Log::info($mydata[0]->amount);
            // \Log::info($mydata[0]->stamp_duty);
            // \Log::info('***************************');
        }
    }


    public function test(Request $request)
    {
        try {
            // return 'Test Operation';


            $data=Query::with('allscheme','allattach')->whereIn('created_by',[1,5,8,11])->delete();
            return $data;
            foreach ($data as $key => $value) {
                // return $value->id;
                QueryScheme::where('query_id',$value->id)
                    ->delete();
                // return $value->allscheme;
                // return $value->allattach;
                foreach ($value->allattach as $key => $allattach) {
                    // return $allattach;
                    $del_id=QueryAttachment::find($allattach->id);
                    $final_file_name=public_path('query-attachment/'.$del_id->name);
                    if (file_exists($final_file_name)) {
                        unlink($final_file_name);
                    }
                    $del_id->delete();
                    // QueryScheme::where('id',$scheme->id)
                    //     ->delete();
                }
            }
            return $data;
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}