<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QueryScheme extends Model
{
    use HasFactory;
    protected $table="td_query_scheme";
    protected $fillable = [
        'query_id',
        'product_code',
        'isin_no',
        'created_by',
        'updated_by',
    ];

    public function schemename()
    {
        return $this->hasOne(SchemeISIN::class,'product_code','product_code')
            ->leftJoin('md_scheme','md_scheme.id','=','md_scheme_isin.scheme_id')
            ->leftJoin('md_amc','md_amc.id','=','md_scheme.amc_id')
            ->leftJoin('md_plan','md_plan.id','=','md_scheme_isin.plan_id')
            ->leftJoin('md_option','md_option.id','=','md_scheme_isin.option_id')
            ->select('md_scheme_isin.product_code','md_scheme.scheme_name as scheme_name','md_plan.plan_name as plan_name','md_option.opt_name as option_name','md_amc.amc_name','md_amc.id as amc_id');
    }
}