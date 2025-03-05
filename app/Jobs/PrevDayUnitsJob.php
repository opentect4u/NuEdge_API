<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\AumReport;
use Illuminate\Support\Facades\DB;

class PrevDayUnitsJob implements ShouldQueue
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
        \Log::info('Previous Day Units Job Run Successfully');
        $old_2_day= date("Y-m-d", strtotime("- 2 day"));
        $old_1_day= date("Y-m-d", strtotime("- 1 day"));
        $all_datas=DB::select('SELECT * FROM td_mutual_fund_trans_aum WHERE (trans_date=(SELECT MAX(trans_date) FROM td_mutual_fund_trans_aum WHERE trans_date <="'.$old_2_day.'"))');
        $insert_arr=[];
        foreach ($all_datas as $key => $single) {
            // \Log::info($single->id);
            // \Log::info($single->rnt_id);
            // \Log::info('******************PrevDayUnitsJob******************');
            $insert=[
                'rnt_id'=>$single->rnt_id,
                'first_client_name'=>$single->first_client_name,
                'first_client_pan'=>$single->first_client_pan,
                'amc_code'=>$single->amc_code,
                'folio_no'=>$single->folio_no,
                'product_code'=>$single->product_code,
                'trans_date'=>$old_1_day,
                'transaction_type'=>$single->transaction_type,
                'transaction_subtype'=>$single->transaction_subtype,
                'amc_name'=>$single->amc_name,
                'scheme_name'=>$single->scheme_name,
                'cat_name'=>$single->cat_name,
                'subcat_name'=>$single->subcat_name,
                'plan_name'=>$single->plan_name,
                'option_name'=>$single->option_name,
                'divident_paid'=>$single->divident_paid,
                'divident_reinvest'=>$single->divident_reinvest,
                'total_unit'=>$single->total_unit,
                'total_inv_cost'=>$single->total_inv_cost,
                'created_at'=>date('Y-m-d H:i:s'),
                'updated_at'=>date('Y-m-d H:i:s')
            ];
            AumReport::insert($insert);
            // \Log::info('******************'.$key.'******************');
        }
    }
}