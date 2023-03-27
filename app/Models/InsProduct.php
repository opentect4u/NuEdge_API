<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsProduct extends Model
{
    use HasFactory;
    protected $table="md_ins_products";
    protected $fillable = [
        'ins_type_id',
        'company_id',
        'product_type_id',
        'product_name',
        'delete_flag',
        'deleted_date',
        'deleted_by',
        'created_by',
        'updated_by',
    ];
}
