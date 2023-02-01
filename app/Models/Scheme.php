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
        'isin_no',
        'scheme_type',
        'nfo_start_dt',
        'nfo_end_dt',
        'nfo_reopen_dt',
        'pip_fresh_min_amt',
        'sip_fresh_min_amt',
        'pip_add_min_amt',
        'sip_add_min_amt',
        'created_by',
        'updated_by',
    ];
}
