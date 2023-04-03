<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FDScheme extends Model
{
    use HasFactory;
    protected $table="md_fd_scheme";
    protected $fillable = [
        'comp_type_id',
        'comp_id',
        'scheme_name',
        'deleted_date',
        'deleted_by',
        'created_by',
        'updated_by',
    ];
}
