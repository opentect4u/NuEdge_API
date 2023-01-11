<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;
    protected $table="md_documents";
    protected $fillable = [
        'client_id',
        'doc_type_id',
        'doc_name',
        'created_by',
        'updated_by',
    ];
}
