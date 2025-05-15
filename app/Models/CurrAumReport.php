<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrAumReport extends Model
{
    use HasFactory;
    protected $table="td_mutual_fund_curr_aum";
    // protected $primaryKey = 'tin_no';
    protected $fillable = [
        'aum_date',
        'curr_aum',
        'created_by',
        'updated_by',
    ];
}