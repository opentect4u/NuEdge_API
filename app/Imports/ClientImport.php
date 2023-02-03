<?php

namespace App\Imports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Validator;
use App\Helpers\Helper;

class ClientImport implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $validator = Validator::make($row,[
            'client_name' =>'required',
        ]);
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        $client_code="";
        if ($row['client_type']=='E') {
            $client_code="";
        } else {
            $client_name=ucwords($row['client_name']);
            $words = explode(" ",$client_name);
            $client_code_1 = mb_substr($words[0], 0, 1).mb_substr($words[(count($words)-1)], 0, 1);;
                        
            $is_has=Client::where('client_code',$client_code_1)->get();
            if (count($is_has)>0) {
                $client_code=$client_code_1.date('dmy',strtotime($row['dob'])).count($is_has);
            }else {
                $client_code=$client_code_1.date('dmy',strtotime($row['dob']));
            }
        }
                    
        return new Client([
            'client_code'=> $client_code,
            'client_name'=> ucwords($row['client_name']),
            'dob'=> isset($row['dob'])?date('Y-m-d',strtotime($row['dob'])):NULL,
            'add_line_1'=> $row['add_line_1'],
            'add_line_2'=> $row['add_line_2'],
            'city'=> $row['city'],
            'dist'=> $row['dist'],
            'state'=> $row['state'],
            'pincode'=> $row['pincode'],
            'pan'=> $row['pan'],
            'mobile'=> $row['mobile'],
            'sec_mobile'=> $row['sec_mobile'],
            'email'=> $row['email'],
            'sec_email'=> $row['sec_email'],
            'client_type'=> $row['client_type'],
            'guardians_pan'=> $row['guardians_pan'],
            'guardians_name'=> $row['guardians_name'],
            'relation'=> $row['relation'],
        ]);
    }
}
