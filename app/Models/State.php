<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;
    protected $table="md_states";
    protected $fillable = [
        'country_id',
        'name',
        'created_by',
        'updated_by',
    ];
}
