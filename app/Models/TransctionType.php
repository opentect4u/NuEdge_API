<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransctionType extends Model
{
    use HasFactory;
    protected $table="md_trns_type";
    protected $fillable = [
        'product_id',
        'trns_type',
        'created_by',
        'updated_by',
    ];
}
