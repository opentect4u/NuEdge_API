<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessType extends Model
{
    use HasFactory;
    protected $table="md_business_type";
    protected $fillable = [
        'branch_id',
        'bu_code',
        'bu_type',
    ];
}
