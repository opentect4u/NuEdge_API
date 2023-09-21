<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SipStpTransaction extends Model
{
    use HasFactory;
    protected $table="td_sip_stp_trans";
    protected $fillable = [
        'rnt_id',
        'arn_no',
        'product_code',
        'folio_no',
        'first_client_name',
        'auto_trans_type',
        'auto_trans_no',
        'auto_amount',
        'from_date',
        'to_date',
        'period_day',
        'reg_date',
        'sub_brk_cd',
        'euin_no',
        'remarks',
        'bank',
        'branch',
        'instrm_no',
        'chq_micr_no',
        'first_client_pan',
        'amc_code',
        'sub_trans_desc',
        'pause_from_date',
        'pause_to_date',
        'req_ref_no',
        'frequency',
        'terminate_date',
        'status',
        'amc_flag',
        'scheme_flag',
        'delete_flag',
        'deleted_at',
        'deleted_date',
    ];
}
