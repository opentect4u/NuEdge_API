<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disclaimer extends Model
{
    use HasFactory;
    protected $table="md_disclaimer";
    protected $fillable = [
        'dis_for',
        'dis_des',
        'font_size',
        'color_code',
        'created_by',
        'updated_by',
    ];
}