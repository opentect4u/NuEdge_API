<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pincode extends Model
{
    use HasFactory;
    protected $table="md_pincode";
    protected $fillable = [
        'country_id',
        'state_id',
        'district_id',
        'city_id',
        'pincode',
        'city_type_id',
        'created_by',
        'updated_by',
    ];
}
