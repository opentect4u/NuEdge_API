<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AumReport extends Model
{
    use HasFactory;
    protected $table="td_mutual_fund_trans_aum";
    // protected $primaryKey = 'tin_no';
    protected $fillable = [
        'rnt_id',
        'first_client_name',
        'first_client_pan',
        'amc_code',
        'folio_no',
        'product_code',
        'trans_date',
        'transaction_type',
        'transaction_subtype',
        'amc_name',
        'scheme_name',
        'cat_name',
        'subcat_name',
        'plan_name',
        'option_name',
        'divident_paid',
        'divident_reinvest',
        'total_unit',
        'total_inv_cost',
        'all_amount_arr',
        'all_date_arr',
    ];
}