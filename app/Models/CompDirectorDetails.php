<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompDirectorDetails extends Model
{
    use HasFactory;
    protected $table="md_cm_director_details";
    protected $fillable = [
        'cm_profile_id',
        'name',
        'dob',
        'pan',
        'add_1',
        'add_2',
        'country_id',
        'state_id',
        'district_id',
        'city_id',
        'pincode',
        'mob',
        'email',
        'din_no',
        'valid_from',
        'valid_to',
        // 'created_by',
        // 'updated_by',
    ];
}
