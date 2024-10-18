<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\{QuerySolveAttach,QueryEntryAttach};

class Query extends Model
{
    use HasFactory;
    protected $table="td_query";
    protected $fillable = [
        'product_id',
        'emp_name',
        'query_id',
        'date_time',
        'invester_id',
        'folio_no',
        'application_no',
        'query_given_by_id',
        'entry_name',
        'product_code',
        'isin_no',
        'query_type_id',
        'query_subtype_id',
        'query_details',
        'query_nature_id',
        'query_given_to_id',
        'query_rec_through_id',
        'query_given_through_id',
        'concern_person_name',
        'contact_no',
        'email_id',
        'expected_close_date',
        'actual_close_date',
        'query_status_id',
        'remarks',
        'query_feedback',
        'suggestion',
        'query_mode_id',
        'policy_no',
        'ins_product_id',
        'fd_no',
        'fd_scheme_id',

        'call_flag',
        'whats_app_flag',
        'email_flag',
        'sms_flag',
        'call_date',
        'whats_app_date',
        'email_date',
        'sms_date',
        
        'created_by',
        'updated_by',
    ];

    public function entryattach()
    {
        return $this->hasMany(QueryEntryAttach::class,'query_id','id');
    }
    
    public function solveattach()
    {
        return $this->hasMany(QuerySolveAttach::class,'query_id','id');
    }
}