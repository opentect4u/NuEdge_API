<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QueryNature extends Model
{
    use HasFactory;
    protected $table="md_query_nature";
    protected $fillable = [
        'query_nature',
        'created_by',
        'updated_by',
    ];
}