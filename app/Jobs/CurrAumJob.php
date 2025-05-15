<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\AumReport;
use App\Models\CurrAumReport;
use Illuminate\Support\Facades\DB;

class CurrAumJob implements ShouldQueue
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
        \Log::info('Current Aum Job Run Successfully');
        $date= date("Y-m-d", strtotime("- 1 day"));
        // $date= "2024-02-28";
        // return $date;
        $rawQuery='';
        if ($date) {
            $condition_v = (strlen($rawQuery) > 0) ? " AND " : " ";
            $queryString = 'td_mutual_fund_trans_aum.trans_date';
            $rawQuery .= $condition_v . $queryString . "=(SELECT MAX(trans_date) FROM td_mutual_fund_trans_aum WHERE trans_date <='" . $date . "')";
        }
        // DB::enableQueryLog();
        $all_data = AumReport::leftJoin('md_rnt', 'md_rnt.id', '=', 'td_mutual_fund_trans_aum.rnt_id')
            ->selectRaw('td_mutual_fund_trans_aum.*,SUM(td_mutual_fund_trans_aum.total_unit) AS tot_units, SUM(td_mutual_fund_trans_aum.total_inv_cost) as inv_cost,md_rnt.rnt_name')
            ->whereRaw($rawQuery)
            ->groupBy('td_mutual_fund_trans_aum.amc_code', 'td_mutual_fund_trans_aum.product_code')
            ->get();
        // dd(DB::getQueryLog());
        $all_trans_product = [];
        foreach ($all_data as $value_product_code) {
            $f_trans_product = "(nav_date=(SELECT MAX(nav_date) FROM td_nav_details WHERE product_code='" . $value_product_code->product_code . "' AND nav_date <='" . $date . "') AND product_code='" . $value_product_code->product_code . "')";
            array_push($all_trans_product, $f_trans_product);
        }
        // return $all_trans_product;
        $res_array = [];
        if (count($all_data) > 0) {
            $string_version_product_code = implode(',', $all_trans_product);
            $res_array                   = DB::connection('mysql_nav')
                ->select('SELECT product_code,isin_no,DATE_FORMAT(nav_date, "%Y-%m-%d") as nav_date,nav FROM td_nav_details where ' . str_replace(",", "  OR  ", $string_version_product_code));
        }
        // return $res_array;
        $final_data = [];
        foreach ($all_data as $key_group_amc_data => $value_group_amc_data) { // amc loop
            // return $value_group_amc_data;
            if ($value_group_amc_data->inv_cost > 0) {
                $product_code = $value_group_amc_data->product_code;
                $new          = '';
                if (count($res_array) > 0) {
                    foreach ($res_array as $val_nav) {
                        if ($val_nav->product_code == $product_code) {
                            $new = $val_nav;
                        }
                    }
                }
                $value_group_amc_data->new        = $new;
                $value_group_amc_data->curr_nav   = isset($new->nav) ? $new->nav : 0;
                $value_group_amc_data->nav_date   = isset($new->nav_date) ? $new->nav_date : 0;
                $value_group_amc_data->idcw_reinv = 0;
                $value_group_amc_data->idcw_paid  = 0;
                $value_group_amc_data->idcwr      = 0;
                $value_group_amc_data->curr_aum   = number_format((float) ($value_group_amc_data->curr_nav * $value_group_amc_data->tot_units), 2, '.', '');
                array_push($final_data, $value_group_amc_data);
            }
        }
        // return $final_data;
        $total_aum=0;
        foreach ($final_data as $key_final_data => $value_final_data) {
            $total_aum  +=$value_final_data->curr_aum;
        }
        // return $total_aum;
        CurrAumReport::where('trans_date', $date)->delete();
        CurrAumReport::insert([
            'trans_date' => $date,
            'curr_aum' => $total_aum,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        \Log::info('Current Aum Job Run Completed Successfully');
    }
}