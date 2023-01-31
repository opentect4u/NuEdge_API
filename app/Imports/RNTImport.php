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
            'website'=> $row['website'],
            'ofc_addr'=>$row['ofc_addr'],
            'cus_care_no'=>$row['cus_care_no'],
            'cus_care_email'=>$row['cus_care_email'],
        ]);
    }
}
