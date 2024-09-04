<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QueryGivenBy extends Model
{
    use HasFactory;
    protected $table="md_query_given_by";
    protected $fillable = [
        'name',
        'created_by',
        'updated_by',
    ];
}