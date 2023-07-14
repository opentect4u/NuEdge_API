<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchemeISIN extends Model
{
    use HasFactory;
    protected $table="md_scheme_isin";
    protected $fillable = [
        'scheme_id',
        'plan_id',
        'option_id',
        'isin_no',
        'product_code',
        'delete_flag',
        'deleted_date',
        'deleted_by',
        'created_by',
        'updated_by',
    ];
}
