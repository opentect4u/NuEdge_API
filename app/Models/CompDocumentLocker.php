<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompDocumentLocker extends Model
{
    use HasFactory;
    protected $table="md_cm_document_locker";
    protected $fillable = [
        'cm_profile_id',
        'doc_name',
        'doc_no',
        'valid_from',
        'valid_to',
        'upload_file',
    ];
}
