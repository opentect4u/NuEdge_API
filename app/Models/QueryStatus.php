<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QueryStatus extends Model
{
    use HasFactory;
    protected $table="md_query_status";
    protected $fillable = [
        'status_name',
        'color_code',
        'created_by',
        'updated_by',
    ];
}