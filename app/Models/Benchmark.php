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
        'launch_price',
        'created_by',
        'updated_by',
    ];
}
