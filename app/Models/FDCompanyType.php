<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FDCompanyType extends Model
{
    use HasFactory;
    protected $table="md_fd_type_of_company";
    protected $fillable = [
        'comp_type',
        'delete_flag',
        'deleted_date',
        'deleted_by',
        'created_by',
        'updated_by',
    ];
}
