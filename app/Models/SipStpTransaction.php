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
        'cease_terminate_date',
        'periodicity',
        'period_day',
        'inv_iin',
        'payment_mode',
        'reg_date',
        'sub_brk_cd',
        'euin_no',
        'remarks',
        'top_up_req',
        'top_up_amount',
        'ac_type',
        'bank',
        'bank_branch',
        'instrm_no',
        'chq_micr_no',
        'first_client_pan',
        'amc_code',
        'sub_trans_desc',
        'pause_from_date',
        'pause_to_date',
        'req_ref_no',
        'frequency',
        'f_status',
        'no_of_installment',
        'to_product_code',
        'to_scheme_code',
        'amc_flag',
        'scheme_flag',
        'bu_type_flag',
        'plan_option_flag',
        'idcw_mismatch_flag',
        'freq_mismatch_flag',
        'delete_flag',
        'deleted_at',
        'deleted_date',
    ];
}
