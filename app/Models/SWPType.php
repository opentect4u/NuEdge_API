<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SWPType extends Model
{
    use HasFactory;
    protected $table="md_swp_type";
    protected $fillable = [
        'swp_type_name',
        'created_by',
        'updated_by',
    ];
}
