<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystematicFrequency extends Model
{
    use HasFactory;
    protected $table="md_systematic_frequency";
    protected $fillable = [
        'rnt_id',
        'freq_name',
        'freq_code',
    ];
}
