<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;
    protected $table="md_plan";
    protected $fillable = [
        'plan_name',
        'created_by',
        'updated_by',
    ];
}
