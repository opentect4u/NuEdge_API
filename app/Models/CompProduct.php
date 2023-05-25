<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompProduct extends Model
{
    use HasFactory;
    protected $table="md_cm_products";
    protected $fillable = [
        'cm_profile_id',
        'product_name',
        'created_by',
        'updated_by',
    ];
}
