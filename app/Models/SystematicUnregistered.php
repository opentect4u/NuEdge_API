<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystematicUnregistered extends Model
{
    use HasFactory;
    protected $table="md_systematic_unregistered";
    protected $fillable = [
        'rnt_id',
        'remarks',
    ];
}
