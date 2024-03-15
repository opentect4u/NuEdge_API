<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MutualFundTransactionReport extends Model
{
    use HasFactory;
    protected $table="tt_mutual_fund_trans_report";
    // protected $primaryKey = 'tin_no';
    protected $fillable = [
        'mailback_process_id',
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
        'trxn_code',
        'trxn_nature_code',
        'trans_desc',
        'kf_trans_type',
        'trans_flag',

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