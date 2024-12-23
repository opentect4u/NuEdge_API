<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\{QuerySolveAttach,QueryEntryAttach,QueryAttachment};

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
        'query_rec_by_id',
        // 'product_code',
        // 'isin_no',
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
        'rating',
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
        'query_tat',
        'short_url',
        'feedback_url',
        'tat_remarks',
        'query_solve_by_id',
        'query_solve_date',
        'created_by',
        'updated_by',
    ];

    public function allscheme()
    {
        return $this->hasMany(QueryScheme::class,'query_id','id');
    }

    public function allattach()
    {
        return $this->hasMany(QueryAttachment::class,'query_id','id')
            ->select('td_query_attchment.*')
            ->selectRaw('(select status_name from md_query_status where id=td_query_attchment.query_status_id limit 1) as query_status_name');
    }

    public function entryattach()
    {
        return $this->hasMany(QueryEntryAttach::class,'query_id','id');
    }
    
    public function solveattach()
    {
        return $this->hasMany(QuerySolveAttach::class,'query_id','id');
    }


}