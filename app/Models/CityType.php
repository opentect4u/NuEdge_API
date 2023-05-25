<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CityType extends Model
{
    use HasFactory;
    protected $table="md_city_type";
    protected $fillable = [
        'name',
        'created_by',
        'updated_by',
    ];
}
