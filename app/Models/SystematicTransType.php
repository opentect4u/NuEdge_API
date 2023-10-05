<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystematicTransType extends Model
{
    use HasFactory;
    protected $table="md_systematic_trans_type";
    protected $fillable = [
        'rnt_id',
        'trans_type',
        'trans_sub_type',
        'trans_type_code',
    ];
}
