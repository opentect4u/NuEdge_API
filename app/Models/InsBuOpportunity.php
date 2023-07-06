<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsBuOpportunity extends Model
{
    use HasFactory;
    protected $table="td_ins_bu_opportunity";
    protected $fillable = [
        'rec_datetime',
        'temp_tin_no',
        'bu_type',
        'arn_no',
        'sub_arn_no',
        'euin_no',
        'sub_brk_cd',
        'ins_type_id',
        'proposer_id',
        'same_as_above',
        'insured_person_id',
        'comp_id',
        'product_type_id',
        'product_id',
        'sum_insured',
        'renewal_dt',
        'upload_file',
        'remarks',
        'branch_code',
        'delete_flag',
        'deleted_date',
        'deleted_by',
        'created_by',
        'updated_by',
    ];
}
