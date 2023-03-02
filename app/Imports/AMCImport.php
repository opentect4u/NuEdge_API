<?php

namespace App\Imports;

use App\Models\AMC;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Validator;
use App\Helpers\Helper;

class AMCImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $validator = Validator::make($row,[
            'rnt_id' =>'required',
            'product_id' =>'required',
            'amc_name' =>'required',
            'l1_contact_no'=>'numeric',
            'l2_contact_no'=>'numeric',
            'l3_contact_no'=>'numeric',
            'l4_contact_no'=>'numeric',
            // 'l5_contact_no'=>'numeric',
            // 'l6_contact_no'=>'numeric',
            'l1_email' =>'email',
            'l2_email' =>'email',
            'l3_email' =>'email',
            'l4_email' =>'email',
            // 'l5_email' =>'email',
            // 'l6_email' =>'email',
        ]);
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        return new AMC([
            'rnt_id'=>$row['rnt_id'],
            'product_id'=>$row['product_id'],
            'amc_name'=>$row['amc_name'],
            'website'=>$row['website'],
            'gstin'=>$row['gstin'],
            'head_ofc_addr'=>$row['head_ofc_addr'],
            'head_ofc_contact_per'=>$row['head_ofc_contact_per'],
            'head_contact_per_mob'=>$row['head_contact_per_mob'],
            'head_contact_per_email'=>$row['head_contact_per_email'],
            'local_ofc_addr'=>$row['local_ofc_addr'],
            'local_ofc_contact_per'=>$row['local_ofc_contact_per'],
            'local_contact_per_mob'=>$row['local_contact_per_mob'],
            'local_contact_per_email'=>$row['local_contact_per_email'],
            'cus_care_no'=>$row['cus_care_no'],
            'cus_care_email'=>$row['cus_care_email'],
            'l1_name'=>$row['l1_name'],
            'l1_contact_no'=>$row['l1_contact_no'],
            'l1_email'=>$row['l1_email'],
            'l2_name'=>$row['l2_name'],
            'l2_contact_no'=>$row['l2_contact_no'],
            'l2_email'=>$row['l2_email'],
            'l3_name'=>$row['l3_name'],
            'l3_contact_no'=>$row['l3_contact_no'],
            'l3_email'=>$row['l3_email'],
            'l4_name'=>$row['l4_name'],
            'l4_contact_no'=>$row['l4_contact_no'],
            'l4_email'=>$row['l4_email'],
            'l5_name'=>$row['l5_name'],
            'l5_contact_no'=>$row['l5_contact_no'],
            'l5_email'=>$row['l5_email'],
            'l6_name'=>$row['l6_name'],
            'l6_contact_no'=>$row['l6_contact_no'],
            'l6_email'=>$row['l6_email'],
        ]);
    }
}
