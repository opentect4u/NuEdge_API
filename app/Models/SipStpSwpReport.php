<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\{MutualFundTransaction};
use Session;

class SipStpSwpReport extends Model
{
    use HasFactory;
    protected $table="tt_sip_stp_swp_report";
    protected $fillable = [
        'rnt_id',
        'arn_no',
        'product_code',
        'isin_no',
        'folio_no',
        'first_client_name',
        'auto_trans_type',
        'auto_trans_no',
        'auto_amount',
        'from_date',
        'to_date',
        'cease_terminate_date',
        'periodicity',
        'period_day',
        'inv_iin',
        'payment_mode',
        'reg_date',
        'sub_brk_cd',
        'euin_no',
        'old_euin_no',
        'remarks',
        'top_up_req',
        'top_up_amount',
        'ac_type',
        'bank',
        'bank_branch',
        'instrm_no',
        'chq_micr_no',
        'first_client_pan',
        'amc_code',
        'sub_trans_desc',
        'pause_from_date',
        'pause_to_date',
        'req_ref_no',
        'frequency',
        'f_status',
        'no_of_installment',
        'to_product_code',
        'to_scheme_code',
        'amc_flag',
        'scheme_flag',
        'bu_type_flag',
        'bu_type_lock_flag',
        'plan_option_flag',
        'plan_option_lock_flag',
        'idcw_mismatch_flag',
        'freq_mismatch_flag',
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
            ->groupBy('td_mutual_fund_trans.trans_no')
            ->groupBy('td_mutual_fund_trans.trxn_type_flag')
            ->groupBy('td_mutual_fund_trans.trxn_nature_code')
            // ->groupByRaw('IF(substr(trxn_nature,1,19)="Systematic-Reversed","Systematic-Reversed",trxn_nature)')
            ->groupBy('td_mutual_fund_trans.trans_desc')
            ->groupBy('td_mutual_fund_trans.kf_trans_type')
            ->groupBy('td_mutual_fund_trans.trans_flag')
            ->orderBy('td_mutual_fund_trans.trans_date','ASC');
            
    }
}