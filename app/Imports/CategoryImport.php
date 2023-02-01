<?php

namespace App\Imports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Validator;
use App\Helpers\Helper;

class CategoryImport implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $validator = Validator::make($row,[
            'product_id' =>'required',
            'cat_name' =>'required',
        ]);
        if($validator->fails()) {
            $errors = $validator->errors();
            return Helper::ErrorResponse(parent::VALIDATION_ERROR);
        }
        return new Category([
            'product_id'=>$row['product_id'],
            'cat_name'=>$row['cat_name'],
        ]);
    }
}
