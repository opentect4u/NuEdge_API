<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxImplication extends Model
{
    use HasFactory;
    protected $table="md_tax_implication";
    protected $fillable = [
        'tax_type',
        'created_by',
        'updated_by',
    ];
}