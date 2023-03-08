<?php

namespace App\Imports;

use App\Models\RNT;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Validator;
use App\Helpers\Helper;

class RNTImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $validator = Validator::make($row,[
            'rnt_name' =>'required',
            'cus_care_no'=>'numeric',
            'cus_care_email' =>'email',
        ]);
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        return new RNT([
            'rnt_name'=> $row['rnt_name'],
            'rnt_full_name'=> $row['R&T Full Name'],
            'website'=> $row['website'],
            'head_ofc_addr'=> $row['head_ofc_addr'],
            'head_ofc_contact_per'=> $row['head_ofc_contact_per'],
            'head_contact_per_mob'=> $row['head_contact_per_mob'],
            'head_contact_per_email'=> $row['head_contact_per_email'],
            'local_ofc_addr'=> $row['local_ofc_addr'],
            'local_ofc_contact_per'=> $row['local_ofc_contact_per'],
            'local_contact_per_mob'=> $row['local_contact_per_mob'],
            'local_contact_per_email'=> $row['local_contact_per_email'],
            'cus_care_no'=>$row['cus_care_no'],
            'cus_care_email'=>$row['cus_care_email'],
        ]);
    }
}
