<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubBroker extends Model
{
    use HasFactory;
    protected $table="md_sub_broker";
    protected $fillable = [
        'arn_no',
        'code',
        'bro_name',
        'created_by',
        'updated_by',
    ];
}
