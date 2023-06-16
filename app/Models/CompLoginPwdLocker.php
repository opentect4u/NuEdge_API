<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompLoginPwdLocker extends Model
{
    use HasFactory;
    protected $table="md_cm_login_pwd_locker";
    protected $fillable = [
        'product_id',
        'login_url',
        'login_id',
        'login_pass',
        'sec_qus_ans',
    ];
}
