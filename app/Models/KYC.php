<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KYC extends Model
{
    use HasFactory;
    protected $table="td_kyc";
    protected $fillable = [
        'bu_type',
        'arn_no',
        'sub_arn_no',
        'euin_no',
        'sub_brk_cd',
        'tin_no',
        'entry_dt',
        'client_id',
        'present_kyc_status',
        'kyc_type',
        'kyc_login_type',
        'kyc_login_at',
        'form_scan_status',
        'final_kyc_status',
        'scaned_form',
        'remarks',

        'kyc_login_dt',
        'kyc_login_cutt_off',
        'ack_copy_scan',
        'ack_remarks',
        'manual_trans_status',
        'process_date',
        'ckyc_no',
        'upload_scan',
        'manual_update_remarks',
        'reject_reason_id',
        'contact_to_amc',
        'contact_via',
        'contact_per_name',
        'contact_per_email',
        'reject_memo',
        'pending_reason',
        'form_status',

        'branch_code',
        'deleted_at',
        'deleted_by',
        'deleted_flag',
        'created_by',
        'updated_by',
    ];
}
