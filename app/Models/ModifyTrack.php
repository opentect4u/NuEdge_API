<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModifyTrack extends Model
{
    use HasFactory;
    protected $table="td_modify_track";
    protected $fillable = [
        'date',
        'user_id',
        'temp_tin_no',
        'remarks',
        'created_by',
        'updated_by',
    ];
}
