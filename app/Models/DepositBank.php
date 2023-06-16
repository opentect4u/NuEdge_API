<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepositBank extends Model
{
    use HasFactory;
    protected $table="md_deposit_bank";
    protected $fillable = [
        'bank_name',
        'ifs_code',
        'branch_name',
        'micr_code',
        'branch_addr',
        'deleted_flag',
        'deleted_at',
        'deleted_by',
        'created_by',
        'updated_by',
    ];
}
