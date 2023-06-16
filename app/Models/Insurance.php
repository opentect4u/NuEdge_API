<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insurance extends Model
{
    use HasFactory;
    protected $table="td_insurance";
    protected $fillable = [
        'temp_tin_no',
        'tin_no',
        'entry_tin_status',
        'entry_date',
        'proposer_id',
        'insured_person_id',
        'company_id',
        'product_type_id',
        'product_id',
        'proposal_no',
        'sum_assured',
        'sum_insured',
        'mode_of_premium',
        'premium_paying_date',
        'gross_premium',
        'net_premium',
        'third_party_premium',
        'od_premium',
        'mode_of_payment',
        'chq_bank',
        'acc_no',
        'payment_ref_no',
        'chq_no',
        'policy_term',
        'policy_pre_pay_term',
        'app_form_scan',
        'comp_login_at',
        'comp_login_dt',
        'comp_login_cutt_off',
        'ack_copy_scan',
        'remarks',
        'ack_remarks',
        'form_status',
        'medical_trigger',
        'medical_status',
        'policy_status',
        'policy_issue_dt',
        'risk_dt',
        'maturity_dt',
        'next_renewal_dt',
        'policy_no',
        'policy_copy_scan',
        'reject_remarks',
        'delete_flag',
        'deleted_date',
        'deleted_by',
        'created_by',
        'updated_by',
    ];
}
