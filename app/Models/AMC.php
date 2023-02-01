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
        'website',
        'ofc_addr',
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
        'sip_start_date',
        'sip_end_date',
        'created_by',
        'updated_by',
    ];
}
