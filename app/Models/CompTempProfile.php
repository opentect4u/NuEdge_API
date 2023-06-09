<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompTempProfile extends Model
{
    use HasFactory;
    protected $table="md_cm_temp_profile";
    protected $fillable = [
        'cm_profile_id',
        'upload_logo',
        'from_dt',
        'to_dt',
    ];
}
