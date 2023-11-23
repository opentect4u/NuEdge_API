<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UploadFileHelp extends Model
{
    use HasFactory;
    protected $table="md_file_upload_help";
    protected $fillable = [
        'rnt_id',
        'file_type_id',
        'file_id',
        'file_format_id',
        'uploaded_mode_id',
        'rec_upload_freq',
        'created_by',
        'updated_by',
    ];
}
