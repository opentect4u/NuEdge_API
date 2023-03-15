<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsuranceType extends Model
{
    use HasFactory;
    protected $table="md_ins_type";
    protected $fillable = [
        'type',
        'created_by',
        'updated_by',
    ];
}
