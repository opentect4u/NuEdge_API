<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MutualFundTransaction extends Model
{
    use HasFactory;
    protected $table="td_mutual_fund_trans";
    // protected $primaryKey = 'tin_no';
    protected $fillable = [
        'arn_no',
        'sub_brk_cd',
        'euin_no',
        'first_client_name',
        'first_client_pan',
        'amc_code',
        'folio_no',
        'product_code',
        'trans_no',
        'trans_mode',
        'trans_status',
        'user_trans_no',
        'trans_date',
        'post_date',
        'pur_price',
        'units',
        'amount',
        'rec_date',
        'trans_desc',
        'trans_type',
        'trans_sub_type',
        'trans_flag',
        'trans_nature',
        'te_15h',
        'micr_code',
        'sw_flag',
        'old_folio',
        'seq_no',
        'reinvest_flag',
        'stt',
        'stamp_duty',
        'tds',
        'acc_no',
        'bank_name',
        'remarks',
    ];
}
