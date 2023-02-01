<?php

namespace App\Imports;

use App\Models\SubCategory;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Validator;
use App\Helpers\Helper;

class SubCategoryImport implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $validator = Validator::make($row,[
            'category_id' =>'required',
            'subcategory_name' =>'required',
        ]);
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        return new SubCategory([
            'category_id'=>$row['category_id'],
            'subcategory_name'=>$row['subcategory_name'],
        ]);
    }
}
