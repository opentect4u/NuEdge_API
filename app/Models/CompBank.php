<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompBank extends Model
{
    use HasFactory;
    protected $table="md_cm_bank";
    protected $fillable = [
        'cm_profile_id',
        'acc_no',
        'bank_name',
        'ifsc',
        'micr',
        'branch_name',
        'branch_add',
    ];
}
