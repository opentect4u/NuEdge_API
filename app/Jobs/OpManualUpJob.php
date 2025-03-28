<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\MutualFund;
use App\Models\MutualFundTransaction;
use Illuminate\Support\Facades\DB;

class OpManualUpJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Log::info('Operation (FIN + NFO) Manual Update Job Run Successfully');
        // for final 
        $fin_array=array(1, 4);
            // DB::enableQueryLog();
        $all_data=MutualFund::join('md_client','md_client.id','=','td_mutual_fund.first_client_id')
            ->leftJoin('md_trans','md_trans.id','=','td_mutual_fund.trans_id')
            ->select('td_mutual_fund.id as id','td_mutual_fund.rnt_login_dt','td_mutual_fund.trans_scheme_from','td_mutual_fund.option_id','td_mutual_fund.plan_id','td_mutual_fund.trans_scheme_to','td_mutual_fund.first_client_id',
                'md_client.client_name as first_client_name','md_client.pan as first_client_pan','td_mutual_fund.amount as amount',
                'md_trans.trans_type_id')
            ->selectRaw('(SELECT product_code FROM md_scheme_isin WHERE scheme_id=td_mutual_fund.trans_scheme_from AND plan_id=td_mutual_fund.plan_id AND option_id=td_mutual_fund.option_id LIMIT 1)as product_code')
            ->where('td_mutual_fund.form_status','A')
            ->whereIn('md_trans.trans_type_id',$fin_array)
            ->orderBy('td_mutual_fund.rnt_login_dt','DESC')
            ->get();
            // dd(DB::getQueryLog());
        // \Log::info(DB::getQueryLog());

        foreach ($all_data as $key => $value) {
            // \Log::info($value->id);
            // \Log::info($value->rnt_login_dt);
            // \Log::info($value->first_client_id);
            // \Log::info($value->product_code);
            // \Log::info($value->first_client_name);
            // \Log::info($value->first_client_pan);
            // \Log::info($value->amount);
            // \Log::info('---------------------------');
            if ($value->first_client_pan) {
                // \Log::info('if');
                $mydata=MutualFundTransaction::leftJoin('md_scheme_isin','md_scheme_isin.product_code','=','td_mutual_fund_trans.product_code')
                    // ->leftjoin('md_scheme','md_scheme.scheme_id','=','md_scheme_isin.scheme_id')
                    ->select('td_mutual_fund_trans.product_code','td_mutual_fund_trans.folio_no','td_mutual_fund_trans.trans_date','td_mutual_fund_trans.amount','td_mutual_fund_trans.stamp_duty',
                    'md_scheme_isin.scheme_id','md_scheme_isin.plan_id','md_scheme_isin.option_id',
                    'td_mutual_fund_trans.remarks')
                    ->selectRaw('(SELECT id FROM md_client WHERE pan=td_mutual_fund_trans.first_client_pan LIMIT 1)as first_client_id')
                    ->where('td_mutual_fund_trans.product_code',$value->product_code)
                    ->where('td_mutual_fund_trans.first_client_pan',$value->first_client_pan)
                    ->whereDate('td_mutual_fund_trans.trans_date','>=',date('Y-m-d',strtotime($value->rnt_login_dt)))
                    ->orderBy('td_mutual_fund_trans.trans_date','ASC')
                    ->get()
                    ->take(1);
                if (count($mydata)>0) {
                    // \Log::info($mydata[0]->first_client_id);
                    // \Log::info($mydata[0]->scheme_id);
                    $amount=$mydata[0]->amount + $mydata[0]->stamp_duty;
                    // \Log::info($amount);
                    // \Log::info($mydata[0]->folio_no);
                    // \Log::info($mydata[0]->trans_date);
                    $up_data=MutualFund::where('first_client_id',$mydata[0]->first_client_id)
                        ->where('trans_scheme_from',$mydata[0]->scheme_id)
                        ->where('amount',$amount)
                        ->whereDate('rnt_login_dt','=',date('Y-m-d',strtotime($value->rnt_login_dt)))
                        // ->count();
                        ->update(['manual_trans_status'=>'P','process_date'=>$mydata[0]->trans_date,'form_status'=>'M','folio_no'=>$mydata[0]->folio_no]);
                    // \Log::info("count: ".$up_data);
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
                    ->whereDate('td_mutual_fund_trans.trans_date','>=',date('Y-m-d',strtotime($value->rnt_login_dt)))
                    ->orderBy('td_mutual_fund_trans.trans_date','ASC')
                    ->get()
                    ->take(1);
                if (count($mydata)>0) {
                    // \Log::info($mydata[0]->first_client_id);
                    // \Log::info($mydata[0]->scheme_id);
                    $amount=$mydata[0]->amount + $mydata[0]->stamp_duty;
                    // \Log::info($amount);
                    // \Log::info($mydata[0]->folio_no);
                    // \Log::info($mydata[0]->trans_date);
                    // DB::enableQueryLog();
                    $up_data=MutualFund::where('first_client_id',$mydata[0]->first_client_id)
                        ->where('trans_scheme_from',$mydata[0]->scheme_id)
                        ->where('amount',$amount)
                        ->whereDate('rnt_login_dt','=',date('Y-m-d',strtotime($value->rnt_login_dt)))
                        // ->count();
                        ->update([
                            'manual_trans_status'=>'P',
                            'process_date'=>$mydata[0]->trans_date,
                            'form_status'=>'M',
                            'folio_no'=>$mydata[0]->folio_no,
                            'manual_update_remarks'=>$mydata[0]->remarks,
                        ]);
                    // \Log::info(DB::getQueryLog());
                    // \Log::info("count: ".$up_data);
                }
            }
            // \Log::info(json_encode($mydata));
            // \Log::info($mydata[0]->product_code);
            // \Log::info($mydata[0]->amount);
            // \Log::info($mydata[0]->stamp_duty);
            // \Log::info('***************************');
        }
    }
}