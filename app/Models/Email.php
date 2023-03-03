<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    use HasFactory;
    protected $table="md_mail";
    protected $fillable = [
        'event',
        'subject',
        'body',
        'created_by',
        'updated_by',
    ];
}
