<?php

namespace App\Imports;

use App\Models\AMC;
use Maatwebsite\Excel\Concerns\ToModel;

class AMCImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new AMC([
            //
        ]);
    }
}
