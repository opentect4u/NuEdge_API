<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompProfile extends Model
{
    use HasFactory;
    protected $table="md_cm_profile";
    protected $fillable = [
        'type_of_comp',
        'name',
        'establishment_name',
        'proprietor_name',
        'cin_no',
        'date_of_inc',
        'pan',
        'gstin',
        'contact_no',
        'email',
        'add_1',
        'add_2',
        'country_id',
        'state_id',
        'district_id',
        'city_id',
        'pincode',
        'logo',
        'website',
        'facebook',
        'linkedin',
        'twitter',
        'instagram',
        'blog',
        'comp_default',
        // 'created_by',
        // 'updated_by',
    ];
}
