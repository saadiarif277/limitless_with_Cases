<?php

namespace App\Imports;

use App\Models\IcdCode;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class IcdCodeImport implements ToModel, WithHeadingRow
{
    /**
     * Map each row of the CSV to the IcdCode model
     */
    public function model(array $row)
    {
        return new IcdCode([
            'code' => $row['code'],
            'description' => $row['description'],
        ]);
    }
}

