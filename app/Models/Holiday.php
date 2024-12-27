<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    use HasFactory;
    protected $table="md_holiday";
    protected $fillable = [
        'occ_name',
        'occ_date',
        'created_by',
        'updated_by',
    ];
}