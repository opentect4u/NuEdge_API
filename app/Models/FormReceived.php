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
        'temp_tin_id',
        'bu_type',
        'arn_no',
        'euin_from',
        'euin_to',
        'sub_arn_no',
        'sub_brk_cd',
        'product_id',
        'trans_id',
        'application_no',
        'pan_no',
        'mobile',
        'email',
        'branch_code',
        'created_by',
        'updated_by',
    ];
}
