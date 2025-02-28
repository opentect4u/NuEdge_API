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
        \Log::info('hii');
        $all_data=MutualFund::join('md_client','md_client.id','=','td_mutual_fund.first_client_id')
                    ->select('td_mutual_fund.id as id','td_mutual_fund.trans_scheme_from','td_mutual_fund.option_id','td_mutual_fund.plan_id','td_mutual_fund.trans_scheme_to','td_mutual_fund.first_client_id',
                    'md_client.client_name as first_client_name','md_client.pan as first_client_pan','td_mutual_fund.amount as amount')
                    ->selectRaw('(SELECT product_code FROM md_scheme_isin WHERE scheme_id=td_mutual_fund.trans_scheme_from AND plan_id=td_mutual_fund.plan_id AND option_id=td_mutual_fund.option_id LIMIT 1)as product_code')
                    ->where('td_mutual_fund.form_status','A')
                    ->get();
        foreach ($all_data as $key => $value) {
            \Log::info($value->id);
            \Log::info($value->product_code);
            \Log::info($value->first_client_pan);
            \Log::info($value->amount);
            \Log::info('---------------------------');
            $mydata=MutualFundTransaction::where('product_code',$value->product_code)
                ->where('first_client_pan',$value->first_client_pan)
                ->orderBy('trans_date','ASC')
                ->get()
                ->take(1);
            // foreach ($mydata as $key => $value__) {
            //     \Log::info($value__->product_code);
            //     \Log::info($value__->amount);
            //     \Log::info($value__->stamp_duty);
            // }
            if (count($mydata)>0) {
                \Log::info($mydata[0]->trans_date);
                $amount=$mydata[0]->amount + $mydata[0]->stamp_duty;
                \Log::info($amount);
            }
            // \Log::info(json_encode($mydata));
            // \Log::info($mydata[0]->product_code);
            // \Log::info($mydata[0]->amount);
            // \Log::info($mydata[0]->stamp_duty);
            \Log::info('***************************');
        }
    }
}