<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompLicenseDetails extends Model
{
    use HasFactory;
    protected $table="md_cm_licence_details";
    protected $fillable = [
        'product_id',
        'licence_no',
        'valid_from',
        'valid_to',
        'upload_file',
    ];
}
