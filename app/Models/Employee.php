<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $table="md_employee";
    // protected $primaryKey="emp_code";
    protected $fillable = [
        'arn_no',
        'branch_id',
        'bu_type_id',
        'euin_no',
        'emp_name',
        'created_by',
        'updated_by',
    ];
}
