<?php

namespace App\Imports;

use App\Models\CptCode;
use Maatwebsite\Excel\Concerns\ToModel;

class CptCodeImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new CptCode([
            'code' => $row['code'],
            'description' => $row['description'],
            'default_value'=>$row['default_value'],
        ]);
    }
}
