<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Controllers\v1\Client\AumCalculationController;
use App\Models\AumReport;
use App\Models\MutualFundTransaction;

class PrevDateUnitsJob implements ShouldQueue
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
        \Log::info('Previous Data Units Job Run Successfully');
        $clients=MutualFundTransaction::select('id','first_client_name','first_client_pan')
            ->groupBy('first_client_name')
            ->groupBy('first_client_pan')
            ->get();
        // return $clients;
        for ($i=8; $i <= 14; $i++) { 
            // $year=(date('Y') - $i).'-03-01';
            // $valuation_as_on = date("Y-m-t", strtotime($year));
            $time = strtotime(date('Y-m-d'));
            $valuation_as_on = date("Y-m-t", strtotime("-".$i." month", $time));
            // return $valuation_as_on;
            foreach ($clients as $key => $client) {
                $client_name=$client['first_client_name'];
                $pan_no=$client['first_client_pan'];
                // $valuation_as_on= date("Y-m-d", strtotime("- 1 day"));
                // \Log::info($client_name);
                // \Log::info($pan_no);
                // \Log::info($valuation_as_on);
                $portfolio=AumCalculationController::calucationTotUnitsAndInvCost($client_name,$pan_no,$valuation_as_on);
                // \Log::info('portfolio count : '.count($portfolio));
                $insert_arr=[];
                foreach ($portfolio as $key => $single) {
                    // return $single;
                    // \Log::info($single['portfolio_show_flag']);
                    // \Log::info(json_encode($single['mydata']['all_date_arr']));
                    // \Log::info(json_encode($single['mydata']['all_date_arr']));
    
                    AumReport::where('folio_no',$single['folio_no'])
                        ->where('product_code',$single['product_code'])
                        ->where('trans_date',$valuation_as_on)
                        ->delete();
                    $insert_arr[]=[
                        'rnt_id'=>$single['rnt_id'],
                        'first_client_name'=>$single['first_client_name'],
                        'first_client_pan'=>$single['first_client_pan'],
                        'amc_code'=>$single['amc_code'],
                        'folio_no'=>$single['folio_no'],
                        'product_code'=>$single['product_code'],
                        'trans_date'=>$valuation_as_on,
                        'transaction_type'=>$single['transaction_type'],
                        'transaction_subtype'=>$single['transaction_subtype'],
                        'amc_name'=>$single['amc_name'],
                        'scheme_name'=>$single['scheme_name'],
                        'cat_name'=>$single['cat_name'],
                        'subcat_name'=>$single['subcat_name'],
                        'plan_name'=>$single['plan_name'],
                        'option_name'=>$single['option_name'],
                        'divident_paid'=>$single['idcwp'],
                        'divident_reinvest'=>$single['idcw_reinv'],
                        'total_unit'=>($single['tot_units']>0)?$single['tot_units']:0,
                        'total_inv_cost'=>($single['inv_cost']>0)?$single['inv_cost']:0,
                        'all_amount_arr'=>json_encode($single['mydata']['all_amt_arr']),
                        'all_date_arr'=>json_encode($single['mydata']['all_date_arr']),
                        'portfolio_show_flag'=>$single['portfolio_show_flag'],
                        'amc_id'=>$single['amc_id'],
                        'category_id'=>$single['cat_id'],
                        'subcategory_id'=>$single['subcat_id'],
                        'scheme_id'=>$single['scheme_id'],
                        'created_at'=>date('Y-m-d H:i:s'),
                        'updated_at'=>date('Y-m-d H:i:s')
                    ];
                }
                AumReport::insert($insert_arr);
                // \Log::info('***********************************');
            }
        }
        
    }
}