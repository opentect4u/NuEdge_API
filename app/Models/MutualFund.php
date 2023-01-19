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
        'temp_tin_id',
        'tin_no',
        'entry_date',
        'first_client_code',
        'first_pan',
        'first_kyc',
        'second_client_code',
        'second_pan',
        'second_kyc',
        'third_client_code',
        'third_pan',
        'third_kyc',
        'amc_id',
        'trans_catg',
        'trans_subcat',
        'trans_scheme_from',
        'trans_scheme_to',
        'folio_no',
        'amount',
        'unit',
        'trans_type',
        'sip_start_date',
        'sip_end_date',
        'chq_no',
        'chq_bank',
        'app_form_scan',
        'email_id_outlook',
        'rnt_login_dt',
        'rnt_login_at',
        'rnt_login_cutt_off',
        'report_scan',
        'logged_in',
        'trans_date',
        'soa_sent',
        'soa_date',
        'form_scan_status',
        'sip_status',
        'report_send',
        'remarks',
        'delete_flag',
        'deleted_date',
        'deleted_by',
        'created_by',
        'updated_by',
    ];
}
