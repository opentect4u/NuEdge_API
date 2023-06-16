<?php

namespace App\Imports;

use App\Models\DepositBank;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Validator;
use App\Helpers\Helper;

class DepositBankImport implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $validator = Validator::make($row,[
            'bank_name' =>'required',
            'ifs_code' =>'required',
            'branch_name' =>'required',
        ]);
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        return new DepositBank([
            'bank_name'=>$row['bank_name'],
            'ifs_code'=>$row['ifs_code'],
            'branch_name'=>$row['branch_name'],
            'micr_code'=>$row['micr_code'],
            'branch_addr'=>$row['branch_addr'],
            'deleted_flag'=>'N',
        ]);
    }
}
