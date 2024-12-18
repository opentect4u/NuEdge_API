<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QueryAttachment extends Model
{
    use HasFactory;
    protected $table="td_query_attchment";
    protected $fillable = [
        'query_id',
        'query_status_id',
        'name',
        'created_by',
        'updated_by',
    ];
}