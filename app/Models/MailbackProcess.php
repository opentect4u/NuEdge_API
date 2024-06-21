<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailbackProcess extends Model
{
    use HasFactory;
    protected $table="md_mailback_process";
    // protected $primaryKey = 'tin_no';
    protected $fillable = [
        'rnt_id',
        'file_type_id',
        'file_id',
        'original_file_name',
        'upload_file',
        'process_date',
        'process_type',
        'file_process_type',
        'total_count',
        'process_count',
    ];
}