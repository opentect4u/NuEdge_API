<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FolioDetailsReport extends Model
{
    use HasFactory;
    protected $table="tt_folio_details_reports";
    protected $fillable = [
        'rnt_id',
        'product_code',
        'isin_no',
        'old_euin_no',
        'euin_no',
        'amc_code',
        'folio_no',
        'folio_date',
        'dividend_option',
        'first_client_name',
        'joint_name_1',
        'joint_name_2',
        'add_1',
        'add_2',
        'add_3',
        'city',
        'pincode',
        'rupee_bal',
        'state',
        'country',
        'tpin',
        'f_name',
        'dob',
        'dob_2nd_holder',
        'dob_3rd_holder',
        'm_name',
        'phone_residence',
        'phone_res_1',
        'phone_res_2',
        'phone_ofc',
        'phone_ofc_1',
        'phone_ofc_2',
        'fax_residence',
        'fax_ofc',
        'tax_status',
        'tax_status_2_holder',
        'tax_status_3_holder',
        'occ_code',
        'email',
        'email_2nd_holder',
        'email_3rd_holder',
        'bank_acc_no',
        'bank_name',
        'bank_ifsc',
        'bank_micr',
        'acc_type',
        'bank_branch',
        'bank_add_1',
        'bank_add_2',
        'bank_add_3',
        'bank_city',
        'bank_phone',
        'bank_state',
        'bank_country',
        'bank_pincode',
        'invs_id',
        'arn_no',
        'pan',
        'pan_2_holder',
        'pan_3_holder',
        'mobile',
        'mobile_2nd_holder',
        'mobile_3rd_holder',
        'report_date',
        'report_time',
        'occupation_des',
        'occupation_des_2nd',
        'occupation_des_3rd',
        'mode_of_holding',
        'mode_of_holding_des',
        'mapin_id',
        'aadhaar_1_holder',
        'aadhaar_2_holder',
        'aadhaar_3_holder',
        'guardian_name',
        'guardian_dob',
        'guardian_aadhaar',
        'guardian_pan',
        'guardian_mobile',
        'guardian_email',
        'guardian_relation',
        'guardian_ckyc_no',
        'guardian_tax_status',
        'guardian_occu_des',
        'guardian_pa_link_ststus',
        'reinvest_flag',
        'nom_optout_status',
        'nom_name_1',
        'nom_relation_1',
        'nom_per_1',
        'nom_name_2',
        'nom_relation_2',
        'nom_per_2',
        'nom_name_3',
        'nom_relation_3',
        'nom_per_3',
        'nom_pan_1',
        'nom_pan_2',
        'nom_pan_3',
        'ckyc_no_1st',
        'ckyc_no_2nd',
        'ckyc_no_3rd',
        'pa_link_ststus_1st',
        'pa_link_ststus_2nd',
        'pa_link_ststus_3rd',
        'kyc_status_1st',
        'kyc_status_2nd',
        'kyc_status_3rd',
        'guardian_kyc_status',
        'bu_type_flag',
        'bu_type_lock_flag',
        'amc_flag',
        'scheme_flag',
        'plan_option_flag',
        'plan_option_lock_flag',
        'folio_balance',
        'folio_status',
        'delete_flag',
        'deleted_date',
        'deleted_by',
    ];

    use \Awobaz\Compoships\Compoships;
    public function foliotrans()
    {
        
            $all_flag='N';
            return $this->hasMany(MutualFundTransaction::class, ['folio_no', 'product_code'], ['folio_no', 'product_code'])
                ->where([
                    ['delete_flag','=',$all_flag],
                    ['amc_flag','=',$all_flag],
                    ['scheme_flag','=',$all_flag],
                    ['plan_option_flag','=',$all_flag],
                    ['bu_type_flag','=',$all_flag],
                    ['divi_mismatch_flag','=',$all_flag],
                    ['trans_date','<=',date('Y-m-d')]
                ])
                ->select('rnt_id','folio_no','product_code','isin_no','trans_date','trxn_type','trxn_type_flag','trxn_nature','amount','stamp_duty','tds',
                'units','pur_price','trans_mode')
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
                ->selectRaw('IF(stamp_duty!="",sum(stamp_duty),0.00)as tot_stamp_duty')
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
}