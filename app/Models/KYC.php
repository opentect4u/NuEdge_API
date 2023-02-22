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
        'kyc_login_at',
        'amc_id',
        'form_scan_status',
        'final_kyc_status',
        'scaned_form',
        'remarks',
        'branch_code',
        'deleted_at',
        'deleted_by',
        'deleted_flag',
        'created_by',
        'updated_by',
    ];
}
