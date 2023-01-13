<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KYC extends Model
{
    use HasFactory;
    protected $table="td_kyc";
    protected $fillable = [
        'temp_tin_id',
        'tin_no',
        'entry_dt',
        'client_code',
        'pan_no',
        'present_kyc_status',
        'kyc_type',
        'kyc_login_type',
        'kyc_login_at',
        'form_scan_status',
        'final_kyc_status',
        'scaned_form',
        'remarks',
        'branch_code',
        'created_by',
        'updated_by',
    ];
}
