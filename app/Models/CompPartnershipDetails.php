<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompPartnershipDetails extends Model
{
    use HasFactory;
    protected $table="md_cm_partnership_details";
    protected $fillable = [
        'cm_profile_id',
        'name',
        'dob',
        'pan',
        'mob',
        'email',
        'add_1',
        'add_2',
        'country_id',
        'state_id',
        'district_id',
        'city_id',
        'pincode',
        'percentage',
    ];
}
