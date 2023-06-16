<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FDRejectReason extends Model
{
    use HasFactory;
    protected $table="md_fd_reject_reason";
    protected $fillable = [
        'reject_name',
        'delete_flag',
        'deleted_date',
        'deleted_by',
        'created_by',
        'updated_by',
    ];
}
