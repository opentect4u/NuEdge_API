<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SIPType extends Model
{
    use HasFactory;
    protected $table="md_sip_type";
    protected $fillable = [
        'sip_type_name',
        'created_by',
        'updated_by',
    ];
}
