<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RNT extends Model
{
    use HasFactory;
    protected $table="md_rnt";
    protected $fillable = [
        'rnt_name',
        'created_by',
        'updated_by',
    ];
}
