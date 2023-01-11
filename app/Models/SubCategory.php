<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    use HasFactory;
    protected $table="md_subcategory";
    protected $fillable = [
        'category_id',
        'subcategory_name',
        'created_by',
        'updated_by',
    ];
}
