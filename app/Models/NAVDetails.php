<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NAVDetails extends Model
{
    use HasFactory;
    protected $table="td_nav_details";
    protected $fillable = [
        'rnt_id',
        'amc_code',
        'product_code',
        'nav_date',
        'nav',
        'isin_no',
        'amc_flag',
        'scheme_flag',
        'created_by',
        'updated_by',
    ];
}
