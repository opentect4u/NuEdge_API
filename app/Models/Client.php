<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\{Document,Client};

class Client extends Model
{
    use HasFactory;
    protected $table="md_client";
    protected $fillable = [
        'client_code',
        'client_name',
        'dob',
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
        'Guardians_pan',
        'Guardians_name',
        'relation',
        'created_by',
        'updated_by',
    ];

    public function ClientDoc(){
        return $this->hasMany(Document::class,'client_id','id');  
    }
}
