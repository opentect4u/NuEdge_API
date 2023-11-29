<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FolioTaxStaus extends Model
{
    use HasFactory;
    protected $table="md_folio_tax_status";
    protected $fillable = [
        'rnt_id',
        'status',
        'status_code',
        'created_by',
        'updated_by',
    ];

}
