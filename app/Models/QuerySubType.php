<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuerySubType extends Model
{
    use HasFactory;
    protected $table="md_query_sub_type";
    protected $fillable = [
        'query_type_id',
        'query_subtype',
        'query_tat',
        'created_by',
        'updated_by',
    ];
}