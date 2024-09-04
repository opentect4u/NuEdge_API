<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QueryTypeSubtype extends Model
{
    use HasFactory;
    protected $table="md_query_type_subtype";
    protected $fillable = [
        'product_id',
        'query_type',
        'query_subtype',
        'query_tat',
        'created_by',
        'updated_by',
    ];
}