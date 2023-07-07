<?php

namespace App\Imports;

use App\Models\Driver;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DriversImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Driver([
            "fname" => $row["firstname"],
            "lname" => $row["lastname"],
            "licensed_no" => $row["licensed"],
            "description" => $row["description"],
            "address" => $row["address"],
            "image_path" => $row["image"],
            "driver_status" => $row["status"],
        ]);
    }
}
