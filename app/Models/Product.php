<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table="md_products";
    protected $fillable = [
        'product_name',
        'created_by',
        'updated_by',
    ];
}
