<?php

namespace App\Imports;

use App\Models\Accessorie;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AccessoriesImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Accessorie([
            'name'  => $row['name'],
            'fee' => $row['fee'],
            'image_path'    => $row['image_path'],
        ]);
    }
}
