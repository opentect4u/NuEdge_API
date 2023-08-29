<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BenchmarkScheme extends Model
{
    use HasFactory;
    protected $table="td_benchmark_scheme";
    protected $fillable = [
        'ex_id',
        'benchmark',
        'date',
        'open',
        'high',
        'low',
        'close',
        'delete_flag',
        'delete_date',
        'delete_by',
        'created_by',
        'updated_by',
    ];
}
