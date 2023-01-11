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
        'created_by',
        'updated_by',
    ];
}
