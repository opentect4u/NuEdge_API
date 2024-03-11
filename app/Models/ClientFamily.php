<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientFamily extends Model
{
    use HasFactory;
    protected $table="md_client_family";
    protected $fillable = [
        'client_id',
        'family_id',
        'relationship',
        'created_by',
        'updated_by',
    ];

    // public function ClientDoc(){
    //     return $this->hasMany(Document::class,'client_id','id')
    //         ->leftjoin('md_document_type','md_document_type.id','=','md_documents.doc_type_id')
    //         ->select('md_documents.*','md_document_type.doc_type as doc_type_name');
    // }

    // public function PertnerDetails()
    // {
    //     return $this->hasMany(ClientPertner::class,'client_id','id');
    // }
}