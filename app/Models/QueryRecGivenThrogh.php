<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QueryRecGivenThrogh extends Model
{
    use HasFactory;
    protected $table="md_query_rec_given_through";
    protected $fillable = [
        'name',
        'created_by',
        'updated_by',
    ];
}