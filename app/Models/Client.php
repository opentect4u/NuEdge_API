<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\{Document,Client,ClientPertner};

class Client extends Model
{
    use HasFactory;
    protected $table="md_client";
    protected $fillable = [
        'client_code',
        'client_name',
        'dob',
        'dob_actual',
        'anniversary_date',
        'add_line_1',
        'add_line_2',
        'city',
        'dist',
        'state',
        'pincode',
        'pan',
        'mobile',
        'sec_mobile',
        'email',
        'sec_email',
        'client_type',
        'guardians_pan',
        'guardians_name',
        'relation',
        'client_type_mode',


        'country_id',
        'karta_name',
        'inc_date',
        'proprietor_name',
        'date_of_incorporation',

        'created_by',
        'updated_by',
    ];

    public function ClientDoc(){
        return $this->hasMany(Document::class,'client_id','id')
            ->leftjoin('md_document_type','md_document_type.id','=','md_documents.doc_type_id')
            ->select('md_documents.*','md_document_type.doc_type as doc_type_name');
    }

    public function PertnerDetails()
    {
        return $this->hasMany(ClientPertner::class,'client_id','id');
    }
}
