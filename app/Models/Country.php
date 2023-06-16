<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
    protected $table="md_country";
    protected $fillable = [
        'name',
        'created_by',
        'updated_by',
    ];
}
