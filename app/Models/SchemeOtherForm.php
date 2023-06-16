<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchemeOtherForm extends Model
{
    use HasFactory;
    protected $table="md_scheme_other_form";
    protected $fillable = [
        'scheme_id',
        'form_name',
        'form_upload',
        'delete_flag',
        'deleted_date',
        'deleted_by',
        'created_by',
        'updated_by',
    ];
}
