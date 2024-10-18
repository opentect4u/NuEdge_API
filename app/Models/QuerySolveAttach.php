<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuerySolveAttach extends Model
{
    use HasFactory;
    protected $table="td_query_solve_attch";
    protected $fillable = [
        'query_id',
        'name',
        'created_by',
        'updated_by',
    ];
}