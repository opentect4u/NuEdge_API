<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exchange extends Model
{
    use HasFactory;
    protected $table="md_exchange";
    protected $fillable = [
        'ex_name',
        'delete_flag',
        'delete_date',
        'delete_by',
        'created_by',
        'updated_by',
    ];
}
