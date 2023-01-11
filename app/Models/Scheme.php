<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scheme extends Model
{
    use HasFactory;
    protected $table="md_scheme";
    protected $fillable = [
        'product_id',
        'amc_id',
        'category_id',
        'subcategory_id',
        'scheme_name',
        'created_by',
        'updated_by',
    ];
}
