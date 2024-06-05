<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scheme extends Model
{
    use HasFactory;
    protected $table="md_scheme";
    protected $fillable = [
        'product_id',
        'amc_id',
        'category_id',
        'subcategory_id',
        'scheme_name',
        'scheme_type',
        'nfo_start_dt',
        'nfo_end_dt',
        'nfo_reopen_dt',
        'nfo_entry_date',
        'pip_fresh_min_amt',
        'pip_add_min_amt',
        'sip_freq_wise_amt',
        'sip_date',
        'swp_freq_wise_amt',
        'swp_date',
        'stp_freq_wise_amt',
        'stp_date',
        'ava_special_sip',
        'special_sip_name',

        'ava_special_swp',
        'special_swp_name',
        'ava_special_stp',
        'special_stp_name',
        'step_up_min_amt',
        'step_up_min_per',
        'nfo_one_pager',
        'nfo_kim',
        'nfo_ppt',
        'nfo_common_app',
        'sip_registration',
        'swp_registration',
        'stp_registration',
        // 'growth_isin',
        // 'idcw_payout_isin',
        // 'idcw_reinvestment_isin',
        'benchmark_id',
        'merge_flag',
        'merge_id',
        'effective_date',

        'purchase_allowed',
        'pip_multiple_amount',
        'sip_allowed',
        'swp_allowed',
        'stp_allowed',
        'switch_allowed',
        'switch_min_amt',
        'switch_mul_amt',
        'exit_load',
        'tax_implication_id',

        'delete_flag',
        'deleted_date',
        'deleted_by',
        'created_by',
        'updated_by',
    ];
}