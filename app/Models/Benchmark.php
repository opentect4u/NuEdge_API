<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Benchmark extends Model
{
    use HasFactory;
    protected $table="md_benchmark";
    protected $fillable = [
        'ex_id',
        'benchmark',
        'category_id',
        'subcat_id',
        'launch_date',
        'base_date',
        'base_value',
        'delete_flag',
        'delete_date',
        'delete_by',
        'created_by',
        'updated_by',
    ];
}
