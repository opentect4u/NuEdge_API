<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;
    protected $table="md_branch";
    protected $fillable = [
        'brn_code',
        'brn_name',
        'created_by',
        'updated_by',
    ];
}
