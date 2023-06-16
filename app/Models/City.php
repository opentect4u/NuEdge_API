<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;
    protected $table="md_city";
    protected $fillable = [
        'country_id',
        'state_id',
        'district_id',
        'name',
        'created_by',
        'updated_by',
    ];
}
