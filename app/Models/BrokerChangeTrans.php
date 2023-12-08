<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrokerChangeTrans extends Model
{
    use HasFactory;
    protected $table="td_broker_change_trans";
    protected $fillable = [
        'rnt_id',
        'arn_no',
        'sub_brk_cd',
        'euin_no',
        'old_euin_no',
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
        'trxn_type',
        'trxn_type_flag',
        'trxn_nature',
        'trans_desc',
        'kf_trans_type',
        'trans_flag',

        // 'trans_type_code_flag',
        // 'trans_type',
        // 'trans_sub_type',

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
        'dividend_option',
        'isin_no',

        'bu_type_flag',
        'bu_type_lock_flag',
        'amc_flag',
        'scheme_flag',
        'plan_option_flag',
        'divi_mismatch_flag',
        'divi_lock_flag',

        'delete_flag',
        'deleted_at',
        'deleted_date',
    ];
}
