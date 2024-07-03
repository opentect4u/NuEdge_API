<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\{MutualFundTransaction};
use Session;
use DB;

class MutualFundTransaction extends Model
{
    use HasFactory;
    protected $table="td_mutual_fund_trans";
    // protected $primaryKey = 'tin_no';
    protected $fillable = [
        'mailback_process_id',
        'rnt_id',
        'arn_no',
        'sub_brk_cd',
        'euin_no',
        'old_euin_no',
        'first_client_name',
        'first_client_pan',
        'amc_code',
        'folio_no',
        'product_code',
        'trans_no',
        'trans_mode',
        'trans_status',
        'user_trans_no',
        'trans_date',
        'post_date',
        'pur_price',
        'units',
        'amount',
        'rec_date',
        'trxn_type',
        'trxn_type_flag',
        'trxn_nature',
        'trans_desc',

        'trxn_type_code',
        'trxn_nature_code',

        'kf_trans_type',
        'trans_flag',

        // 'trans_type_code_flag',
        // 'trans_type',
        // 'trans_sub_type',

        'te_15h',
        'micr_code',
        'sw_flag',
        'old_folio',
        'seq_no',
        'reinvest_flag',
        'stt',
        'stamp_duty',
        'tds',
        'acc_no',
        'bank_name',
        'remarks',
        'dividend_option',
        'isin_no',

        'bu_type_flag',
        'bu_type_lock_flag',
        'amc_flag',
        'scheme_flag',
        'plan_option_flag',
        'divi_mismatch_flag',
        'divi_lock_flag',
        'delete_flag',
        'deleted_at',
        'deleted_date',
    ];

    use \Awobaz\Compoships\Compoships;
    public function foliotrans()
    {
        // Session::get('valuation_as_on');
        $all_flag='N';
        return $this->hasMany(MutualFundTransaction::class, ['folio_no', 'product_code'], ['folio_no', 'product_code'])
            ->where([
                ['delete_flag','=',$all_flag],
                ['amc_flag','=',$all_flag],
                ['scheme_flag','=',$all_flag],
                ['plan_option_flag','=',$all_flag],
                ['bu_type_flag','=',$all_flag],
                ['divi_mismatch_flag','=',$all_flag],
                ['trans_date','<=',Session::get('valuation_as_on')]
            ])
            ->select('rnt_id','folio_no','product_code','isin_no','trans_date','trxn_type','trxn_type_flag','trxn_nature','amount','stamp_duty','tds',
            'units','pur_price')
            ->selectRaw('IF(rnt_id=1,
            (SELECT trans_type FROM md_mf_trans_type_subtype WHERE c_trans_type_code=trxn_type_code AND c_k_trans_type=trxn_type_flag AND c_k_trans_sub_type=trxn_nature_code limit 1),
            (CASE 
                WHEN trans_flag="DP" || trans_flag="DR" THEN (SELECT trans_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=kf_trans_type AND k_divident_flag=trans_flag limit 1)
                WHEN trans_flag="TO" THEN "Transfer Out"
                ELSE (SELECT trans_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=kf_trans_type limit 1)
            END)
            )as transaction_type')
            ->selectRaw('IF(rnt_id=1,
            (SELECT trans_sub_type FROM md_mf_trans_type_subtype WHERE c_trans_type_code=trxn_type_code AND c_k_trans_type=trxn_type_flag AND c_k_trans_sub_type=trxn_nature_code limit 1),
            (CASE 
                WHEN trans_flag="DP" || trans_flag="DR" THEN (SELECT trans_sub_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=kf_trans_type AND k_divident_flag=trans_flag limit 1)
                WHEN trans_flag="TO" THEN "Transfer Out"
                ELSE (SELECT trans_sub_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=kf_trans_type limit 1)
            END)
            )as transaction_subtype')
            ->selectRaw('sum(units) as tot_units')
            ->selectRaw('sum(amount) as tot_amount')
            ->selectRaw('sum(stamp_duty) as tot_stamp_duty')
            ->selectRaw('IF(tds!="",sum(tds),0.00)as tot_tds')
            ->selectRaw('count(*) as tot_rows')
            ->selectRaw('(select close from td_benchmark_scheme where benchmark=1 AND date=trans_date) as nifty50')
            ->selectRaw('(select close from td_benchmark_scheme where benchmark=70 AND date=trans_date) as sensex')
            ->groupBy('td_mutual_fund_trans.trans_no')
            ->groupBy('td_mutual_fund_trans.trxn_type_flag')
            ->groupBy('td_mutual_fund_trans.trxn_nature_code')
            // ->groupByRaw('IF(substr(trxn_nature,1,19)="Systematic-Reversed","Systematic-Reversed",trxn_nature)')
            ->groupBy('td_mutual_fund_trans.trans_desc')
            ->groupBy('td_mutual_fund_trans.kf_trans_type')
            ->groupBy('td_mutual_fund_trans.trans_flag')
            ->groupBy('td_mutual_fund_trans.pur_price')
            ->orderBy('td_mutual_fund_trans.trans_date','ASC');

        // $value->total_rows=DB::select('SELECT * FROM td_mutual_fund_trans WHERE folio_no="'.$value->folio_no.'" AND product_code="'.$value->product_code.'" AND isin_no="'.$value->isin_no.'" AND trans_date<="'.$valuation_as_on.'"');
        // return $this->hasMany(MutualFundTransaction::class)->where('folio_no', 'folio_no');
        // return $this->hasMany(MutualFundTransaction::class)->where('folio_no', 'folio_no');
        
        // return $this->hasMany(MutualFundTransaction::class);
        
        // return $this->hasMany(MutualFundTransaction::class,['folio_no','product_code'],['folio_no', 'product_code']);
        // ->where('product_code', 'product_code');
        // return $this->hasMany(MutualFundTransaction::class,'folio_no','folio_no')
        //     ->where('product_code', $this->product_code);
        
        // ->ofMany([
        //     'product_code' =>'product_code',
        //     'folio_no'=>'product_code' ,
        // ], function (Builder $query) {
        //     $query->where('product_code', '=', 'product_code');
        // });
        // return $this->folio_no()->where('product_code', 'product_code');
        // return $this->hasMany(MutualFundTransaction::class,'folio_no','folio_no')
        //     ->select('folio_no','product_code');
        // return $this->hasMany(MutualFundTransaction::class,'folio_no','folio_no')
        // ->where('product_code', 'product_code');
            // ->leftjoin('md_document_type','md_document_type.id','=','md_documents.doc_type_id')
            // ->select('md_documents.*','md_document_type.doc_type as doc_type_name');
    }

    public function profitloss()
    {
        // Session::get('valuation_as_on');
        $all_flag='N';
        return $this->hasMany(MutualFundTransaction::class, ['folio_no', 'product_code'], ['folio_no', 'product_code'])
            ->where([
                ['delete_flag','=',$all_flag],
                ['amc_flag','=',$all_flag],
                ['scheme_flag','=',$all_flag],
                ['plan_option_flag','=',$all_flag],
                ['bu_type_flag','=',$all_flag],
                ['divi_mismatch_flag','=',$all_flag],
                ['trans_date','<=',Session::get('valuation_as_on')]
            ])
            ->select('rnt_id','folio_no','product_code','isin_no','trans_date','trxn_type','trxn_type_flag','trxn_nature','amount','stamp_duty','tds',
            'units','pur_price')
            ->selectRaw('IF(rnt_id=1,
            (SELECT trans_type FROM md_mf_trans_type_subtype WHERE c_trans_type_code=trxn_type_code AND c_k_trans_type=trxn_type_flag AND c_k_trans_sub_type=trxn_nature_code limit 1),
            (CASE 
                WHEN trans_flag="DP" || trans_flag="DR" THEN (SELECT trans_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=kf_trans_type AND k_divident_flag=trans_flag limit 1)
                ELSE (SELECT trans_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=kf_trans_type limit 1)
            END)
            )as transaction_type')
            ->selectRaw('IF(rnt_id=1,
            (SELECT trans_sub_type FROM md_mf_trans_type_subtype WHERE c_trans_type_code=trxn_type_code AND c_k_trans_type=trxn_type_flag AND c_k_trans_sub_type=trxn_nature_code limit 1),
            (CASE 
                WHEN trans_flag="DP" || trans_flag="DR" THEN (SELECT trans_sub_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=kf_trans_type AND k_divident_flag=trans_flag limit 1)
                ELSE (SELECT trans_sub_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=kf_trans_type limit 1)
            END)
            )as transaction_subtype')
            ->selectRaw('IF(rnt_id=1,
            (SELECT lmf_pl FROM md_mf_trans_type_subtype WHERE c_trans_type_code=trxn_type_code AND c_k_trans_type=trxn_type_flag AND c_k_trans_sub_type=trxn_nature_code limit 1),
            (CASE 
                WHEN trans_flag="DP" || trans_flag="DR" THEN (SELECT lmf_pl FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=kf_trans_type AND k_divident_flag=trans_flag limit 1)
                WHEN trans_flag="TO" THEN (SELECT lmf_pl FROM md_mf_trans_type_subtype WHERE trans_type="Transfer Out" AND trans_sub_type="Transfer Out" AND rnt_id=2 limit 1)
                ELSE (SELECT lmf_pl FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=kf_trans_type limit 1)
            END)
            )as lmf_pl')
            ->selectRaw('sum(units) as tot_units')
            ->selectRaw('sum(amount) as tot_amount')
            ->selectRaw('sum(stamp_duty) as tot_stamp_duty')
            ->selectRaw('IF(tds!="",sum(tds),0.00)as tot_tds')
            ->selectRaw('count(*) as tot_rows')
            ->groupBy('td_mutual_fund_trans.trans_no')
            ->groupBy('td_mutual_fund_trans.trxn_type_flag')
            ->groupBy('td_mutual_fund_trans.trxn_nature_code')
            // ->groupByRaw('IF(substr(trxn_nature,1,19)="Systematic-Reversed","Systematic-Reversed",trxn_nature)')
            ->groupBy('td_mutual_fund_trans.trans_desc')
            ->groupBy('td_mutual_fund_trans.kf_trans_type')
            ->groupBy('td_mutual_fund_trans.trans_flag')
            ->groupBy('td_mutual_fund_trans.pur_price')
            ->orderBy('td_mutual_fund_trans.trans_date','ASC');
    }

    public function capitalgainloss()
    {
        $all_flag='N';
        return $this->hasMany(MutualFundTransaction::class, ['folio_no', 'product_code'], ['folio_no', 'product_code'])
            ->where([
                ['delete_flag','=',$all_flag],
                ['amc_flag','=',$all_flag],
                ['scheme_flag','=',$all_flag],
                ['plan_option_flag','=',$all_flag],
                ['bu_type_flag','=',$all_flag],
                ['divi_mismatch_flag','=',$all_flag]
            ])
            ->select('rnt_id','folio_no','product_code','isin_no','trans_date','trxn_type','trxn_type_flag','trxn_nature','amount','stamp_duty','tds',
            'units','pur_price')
            ->selectRaw('IF(rnt_id=1,
            (SELECT trans_type FROM md_mf_trans_type_subtype WHERE c_trans_type_code=trxn_type_code AND c_k_trans_type=trxn_type_flag AND c_k_trans_sub_type=trxn_nature_code limit 1),
            (CASE 
                WHEN trans_flag="DP" || trans_flag="DR" THEN (SELECT trans_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=kf_trans_type AND k_divident_flag=trans_flag limit 1)
                WHEN trans_flag="TO" THEN "Transfer Out"
                ELSE (SELECT trans_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=kf_trans_type limit 1)
            END)
            )as transaction_type')
            ->selectRaw('IF(rnt_id=1,
            (SELECT trans_sub_type FROM md_mf_trans_type_subtype WHERE c_trans_type_code=trxn_type_code AND c_k_trans_type=trxn_type_flag AND c_k_trans_sub_type=trxn_nature_code limit 1),
            (CASE 
                WHEN trans_flag="DP" || trans_flag="DR" THEN (SELECT trans_sub_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=kf_trans_type AND k_divident_flag=trans_flag limit 1)
                WHEN trans_flag="TO" THEN "Transfer Out"
                ELSE (SELECT trans_sub_type FROM md_mf_trans_type_subtype WHERE c_k_trans_sub_type=kf_trans_type limit 1)
            END)
            )as transaction_subtype')
            ->selectRaw('sum(units) as tot_units')
            ->selectRaw('sum(amount) as tot_amount')
            ->selectRaw('sum(stamp_duty) as tot_stamp_duty')
            ->selectRaw('IF(tds!="",sum(tds),0.00)as tot_tds')
            ->selectRaw('count(*) as tot_rows')
            ->selectRaw('(select close from td_benchmark_scheme where benchmark=1 AND date=trans_date) as nifty50')
            ->selectRaw('(select close from td_benchmark_scheme where benchmark=70 AND date=trans_date) as sensex')
            ->groupBy('td_mutual_fund_trans.trans_no')
            ->groupBy('td_mutual_fund_trans.trxn_type_flag')
            ->groupBy('td_mutual_fund_trans.trxn_nature_code')
            // ->groupByRaw('IF(substr(trxn_nature,1,19)="Systematic-Reversed","Systematic-Reversed",trxn_nature)')
            ->groupBy('td_mutual_fund_trans.trans_desc')
            ->groupBy('td_mutual_fund_trans.kf_trans_type')
            ->groupBy('td_mutual_fund_trans.trans_flag')
            ->groupBy('td_mutual_fund_trans.pur_price')
            ->orderBy('td_mutual_fund_trans.trans_date','ASC');

    }

    public function schemes()
    {
        $all_flag='N';
        return $this->hasMany(MutualFundTransaction::class, 'amc_code', 'amc_code')
            ->where('delete_flag',$all_flag)
            ->where('amc_flag',$all_flag)
            ->where('scheme_flag',$all_flag)
            ->where('plan_option_flag',$all_flag)
            ->where('bu_type_flag',$all_flag)
            ->where('divi_mismatch_flag',$all_flag)
            ->where('trans_date','<=',Session::get('date'))
            ->groupBy('product_code')
            ->groupBy('isin_no')
            // ->groupBy('trans_no')
            // ->groupBy('trxn_type_flag')
            // ->groupBy('trxn_nature_code')
            // ->groupBy('trans_desc')
            // ->groupBy('kf_trans_type')
            // ->groupBy('trans_flag')
            // ->groupBy('pur_price')
            ->orderBy('trans_date','ASC');
    }
    public function transdetails()
    {
        $all_flag='N';
        return $this->hasMany(MutualFundTransaction::class, 'product_code', 'product_code')
            ->where('delete_flag',$all_flag)
            ->where('amc_flag',$all_flag)
            ->where('scheme_flag',$all_flag)
            ->where('plan_option_flag',$all_flag)
            ->where('bu_type_flag',$all_flag)
            ->where('divi_mismatch_flag',$all_flag)
            ->where('trans_date','<=',Session::get('date'))
            ->groupBy('trans_no')
            ->groupBy('trxn_type_flag')
            ->groupBy('trxn_nature_code')
            ->groupBy('trans_desc')
            ->groupBy('kf_trans_type')
            ->groupBy('trans_flag')
            ->groupBy('pur_price')
            ->orderBy('trans_date','ASC');
    }
    
}