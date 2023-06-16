<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientPertner extends Model
{
    use HasFactory;
    protected $table="md_client_pertner";
    protected $fillable = [
        'client_id',
        'name',
        'mobile',
        'email',
        'dob',
        'pan',
        'created_by',
        'updated_by',
    ];
}
