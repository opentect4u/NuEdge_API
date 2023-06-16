<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsMedicalStatus extends Model
{
    use HasFactory;
    protected $table="md_ins_medical_status";
    protected $fillable = [
        'status_name',
        'delete_flag',
        'deleted_date',
        'deleted_by',
        'created_by',
        'updated_by',
    ];
}
