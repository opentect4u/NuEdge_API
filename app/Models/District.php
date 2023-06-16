<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;
    protected $table="md_district";
    protected $fillable = [
        'state_id',
        'name',
        'created_by',
        'updated_by',
    ];
}
