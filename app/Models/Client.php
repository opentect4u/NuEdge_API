<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    protected $table="md_client";
    protected $fillable = [
        'client_code',
        'client_name',
        'dob',
        'add_line_1',
        'add_line_2',
        'city',
        'dist',
        'state',
        'pincode',
        'pan',
        'mobile',
        'sec_mobile',
        'email',
        'sec_email',
        'created_by',
        'updated_by',
    ];
}
