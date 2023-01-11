<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormType extends Model
{
    use HasFactory;
    protected $table="md_form_type";
    protected $fillable = [
        'product_id',
        'form_name',
        'created_by',
        'updated_by',
    ];
}
