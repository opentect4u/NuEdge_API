<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QueryType extends Model
{
    use HasFactory;
    protected $table="md_query_type";
    protected $fillable = [
        'product_id',
        'query_type',
        'created_by',
        'updated_by',
    ];
}