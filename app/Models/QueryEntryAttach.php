<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QueryEntryAttach extends Model
{
    use HasFactory;
    protected $table="td_query_entry_attch";
    protected $fillable = [
        'query_id',
        'name',
        'created_by',
        'updated_by',
    ];
}