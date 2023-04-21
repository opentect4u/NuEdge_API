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

        'nfo_one_pager',
        'nfo_kim',
        'nfo_ppt',

        'delete_flag',
        'deleted_date',
        'deleted_by',
        'created_by',
        'updated_by',
    ];
}
