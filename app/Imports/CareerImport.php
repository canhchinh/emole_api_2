<?php

namespace App\Imports;

use App\Entities\Career;
use Maatwebsite\Excel\Concerns\ToModel;

class CareerImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Career([
            "title" => $row[0]
        ]);
    }
}