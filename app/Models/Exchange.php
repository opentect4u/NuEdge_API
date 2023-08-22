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
        'created_by',
        'updated_by',
    ];
}
