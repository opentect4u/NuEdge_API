<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsProductType extends Model
{
    use HasFactory;
    protected $table="md_ins_product_type";
    protected $fillable = [
        'ins_type_id',
        'product_type',
        'delete_flag',
        'deleted_date',
        'deleted_by',
        'created_by',
        'updated_by',
    ];
}
