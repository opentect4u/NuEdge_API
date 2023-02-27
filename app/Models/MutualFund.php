<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MutualFund extends Model
{
    use HasFactory;
    protected $table="td_mutual_fund";
    // protected $primaryKey = 'tin_no';
    protected $fillable = [
        'temp_tin_no',
        'tin_no',
        'entry_date',
        'first_client_id',
        'first_kyc',
        'mode_of_holding',
        'sip_duration',
        'sip_frequency',
        'second_client_id',
        'second_kyc',
        'third_client_id',
        'third_kyc',
        'trans_scheme_from',
        'trans_scheme_to',
        'option_id',
        'plan_id',
        'option_id_to',
        'plan_id_to',
        'folio_no',
        'switch_by',
        'first_inv_amount',
        'amount',
        'unit',
        'trans_id',
        'sip_type',
        'sip_start_date',
        'sip_end_date',
        'chq_no',
        'chq_bank',
        'app_form_scan',
        'email_id_outlook',
        'rnt_login_dt',
        'rnt_login_at',
        'rnt_login_cutt_off',
        'ack_copy_scan',
        'report_scan',
        'logged_in',
        'trans_date',
        'soa_sent',
        'soa_date',
        'form_scan_status',
        'sip_status',
        'report_send',
        'remarks',
        'ack_remarks',
        'form_status',
        'delete_flag',
        'deleted_date',
        'deleted_by',
        'created_by',
        'updated_by',
    ];
}
