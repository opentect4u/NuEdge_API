<?php

namespace App\Imports;

use App\Models\Scheme;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Validator;
use App\Helpers\Helper;

class SchemeImport implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row,$request)
    {
        $validator = Validator::make($row,[
            'product_id' =>'required',
            'amc_id' =>'required',
            'category_id' =>'required',
        ]);
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        if ($row['nfo_start_dt']=='') {
            $nfo_start_dt=null;
        }else{
            $nfo_start_dt=date('Y-m-d',strtotime($row['nfo_start_dt']));
        }
        if ($row['nfo_end_dt']=='') {
            $nfo_end_dt=null;
        }else{
            $nfo_end_dt=date('Y-m-d',strtotime($row['nfo_end_dt']));
        }
        if ($row['nfo_reopen_dt']=='') {
            $nfo_reopen_dt=null;
        }else{
            $nfo_reopen_dt=date('Y-m-d',strtotime($row['nfo_reopen_dt']));
        }
        return new Scheme([
            'product_id'=>$row['product_id'],
            'amc_id'=>$row['amc_id'],
            'category_id'=>$row['category_id'],
            'subcategory_id'=>$row['subcategory_id'],
            'scheme_name'=>$row['scheme_name'],
            'isin_no'=>$row['isin_no'],
            'scheme_type'=>$row['scheme_type'],
            'nfo_start_dt'=>$nfo_start_dt,
            'nfo_end_dt'=>$nfo_end_dt,
            'nfo_reopen_dt'=>$nfo_reopen_dt,
            'pip_fresh_min_amt'=>$row['pip_fresh_min_amt'],
            'sip_fresh_min_amt'=>$row['sip_fresh_min_amt'],
            'pip_add_min_amt'=>$row['pip_add_min_amt'],
            'sip_add_min_amt'=>$row['sip_add_min_amt'],
        ]);
    }
}
