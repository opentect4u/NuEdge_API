<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsCompany extends Model
{
    use HasFactory;
    protected $table="md_ins_company";
    protected $fillable = [
        'ins_type_id',
        'comp_short_name',
        'comp_full_name',
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
        'login_url',
        'login_id',
        'login_pass',
        'security_qus_ans',
        'gstin',
        'cus_care_whatsapp_no',
        'delete_flag',
        'deleted_date',
        'deleted_by',
        'created_by',
        'updated_by',
    ];
}
