<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompShareHolder extends Model
{
    use HasFactory;
    protected $table="md_cm_share_holder";
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
        'percentage',
        'certificate_no',
        'date',
        'no_of_share',
        'registered_folio',
        'distinctive_no_from',
        'distinctive_no_to',
        'nominee',
        'type',
        'upload_scan',
        // 'created_by',
        // 'updated_by',
    ];
}
