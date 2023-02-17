<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RNT extends Model
{
    use HasFactory;
    protected $table="md_rnt";
    protected $fillable = [
        'rnt_name',
        'website',
        'head_ofc_addr',
        'head_ofc_contact_per',
        'head_contact_per_mob',
        'head_contact_per_email',
        'local_ofc_addr',
        'local_ofc_contact_per',
        'local_contact_per_mob',
        'local_contact_per_email',
        'cus_care_no',
        'cus_care_email',
        'created_by',
        'updated_by',
    ];
}
