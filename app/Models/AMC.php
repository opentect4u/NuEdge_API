<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AMC extends Model
{
    use HasFactory;
    protected $table="md_amc";
    protected $fillable = [
        'rnt_id',
        'product_id',
        'amc_name',
        'gstin',
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
        'l1_name',
        'l1_contact_no',
        'l1_email',
        'l2_name',
        'l2_contact_no',
        'l2_email',
        'l3_name',
        'l3_contact_no',
        'l3_email',
        'l4_name',
        'l4_contact_no',
        'l4_email',
        'l5_name',
        'l5_contact_no',
        'l5_email',
        'l6_name',
        'l6_contact_no',
        'l6_email',
        'l7_name',
        'l7_contact_no',
        'l7_email',
        'created_by',
        'updated_by',
    ];
}
