<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transction extends Model
{
    use HasFactory;
    protected $table="md_trans";
    protected $fillable = [
        'trans_id',
        'trns_name',
        'created_by',
        'updated_by',
    ];
}
