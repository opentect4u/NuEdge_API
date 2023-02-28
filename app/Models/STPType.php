<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class STPType extends Model
{
    use HasFactory;
    protected $table="md_stp_type";
    protected $fillable = [
        'stp_type_name',
        'created_by',
        'updated_by',
    ];
}
