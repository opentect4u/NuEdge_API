<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $table="md_category";
    protected $fillable = [
        'product_id',
        'cat_name',
        'created_by',
        'updated_by',
    ];
}
