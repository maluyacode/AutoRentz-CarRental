<?php

namespace App\Imports;

use App\Models\Accessorie;
use Maatwebsite\Excel\Concerns\ToModel;

class AccessoriesImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Accessorie([
            //
        ]);
    }
}
