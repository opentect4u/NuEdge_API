<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\{Document,Client};

class InsFormReceived extends Model
{
    use HasFactory;
    protected $table="td_ins_form_received";
    protected $fillable = [
        'rec_datetime',
        'temp_tin_no',
        'bu_type',
        'arn_no',
        'sub_arn_no',
        'euin_no',
        'sub_brk_cd',
        'proposer_id',
        'insure_bu_type',
        'ins_type_id',
        'recv_from',
        'branch_code',
        'deleted_at',
        'deleted_by',
        'deleted_flag',
        'created_by',
        'updated_by',
    ];

    public function ClientDoc(){
        return $this->hasMany(Document::class,'client_id','client_id');  
    }
}
