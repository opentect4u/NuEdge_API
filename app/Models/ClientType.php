<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientType extends Model
{
    use HasFactory;
    protected $table="md_client_type";
    protected $fillable = [
        'type_name',
        'flag',
        'created_by',
        'updated_by',
    ];
}
