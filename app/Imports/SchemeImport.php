<?php

namespace App\Imports;

use App\Models\Scheme;
use Maatwebsite\Excel\Concerns\ToModel;

class SchemeImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Scheme([
            //
        ]);
    }
}
