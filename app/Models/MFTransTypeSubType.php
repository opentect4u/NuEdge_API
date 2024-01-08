<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MFTransTypeSubType extends Model
{
    use HasFactory;
    protected $table="md_mf_trans_type_subtype";
    protected $fillable = [
        'trans_type',
        'trans_sub_type',
        'rnt_id',
        'c_trans_type_code',
        'c_k_trans_type',
        'c_k_trans_sub_type',
        'k_divident_flag',
        'process_type',
    ];
}
