<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RNT extends Model
{
    use HasFactory;
    protected $table="md_rnt";
    protected $fillable = [
        'rnt_name',
        'website',
        'ofc_addr',
        'cus_care_no',
        'cus_care_email',
        'created_by',
        'updated_by',
    ];
}
