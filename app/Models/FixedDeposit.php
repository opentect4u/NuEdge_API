<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FixedDeposit extends Model
{
    use HasFactory;
    protected $table="td_fixed_deposit";
    protected $fillable = [
        'temp_tin_no',
        'tin_no',
        'entry_tin_status',
        'entry_date',
        'mode_of_holding',
        'kyc_status',
        'first_client_id',
        'first_kyc',
        'second_client_id',
        'second_kyc',
        'third_client_id',
        'third_kyc',
        'scheme_id',
        'investment_type',
        'application_no',
        'fdr_no',
        'option',
        'sub_option',
        'tenure_type',
        'tenure',
        'interest_rate',
        'maturity_instruction',
        'amount',
        'mode_of_payment',
        'chq_bank',
        'acc_no',
        'payment_ref_no',
        'chq_no',
        'certificate_delivery_opt',
        'tds_info_id',
        'app_form_scan',
        'comp_login_at',
        'comp_login_dt',
        'comp_login_cutt_off',
        'ack_copy_scan',
        'manual_trans_status',
        'logged_in',
        'fdr_scan',
        'contact_to_comp',
        'contact_via',
        'contact_per_name',
        'contact_per_phone',
        'contact_per_email',
        'reject_reason_id',
        'reject_memo',
        'pending_reason',
        'manual_update_remarks',
        'form_scan_status',
        'report_send',
        'remarks',
        'ack_remarks',
        'form_status',
        'cert_collect_from_comp',
        'cert_pending_remarks',
        'cert_collect_by_dt',
        'cert_collect_by',
        'cert_delivery_by',
        'cert_delivery_dt',
        'cert_delivery_name',
        'cert_delivery_contact_no',
        'cert_delivery_cu_dt',
        'cert_delivery_cu_comp_name',
        'cert_delivery_cu_pod',
        'cert_delivery_cu_pod_scan',
        'cert_rec_by_dt',
        'cert_rec_by_name',
        'cert_rec_by_scan',
        'cert_delivery_flag',
        'deleted_at',
        'deleted_by',
        'deleted_flag',
        'created_by',
        'updated_by',
    ];
}
