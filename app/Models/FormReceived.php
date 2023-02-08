<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormReceived extends Model
{
    use HasFactory;
    protected $table="td_form_received";
    protected $fillable = [
        'rec_datetime',
        'temp_tin_no',
        'bu_type',
        'arn_no',
        'sub_arn_no',
        'euin_no',
        'sub_brk_cd',
        'client_id',
        'product_id',
        'trans_id',
        'scheme_id',
        'recv_from',
        'inv_type',
        'application_no',
        'kyc_status',
        'branch_code',
        'created_by',
        'updated_by',
    ];
}
