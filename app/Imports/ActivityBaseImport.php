<?php

namespace App\Imports;

use App\Entities\ActivityBase;
use Maatwebsite\Excel\Concerns\ToModel;

class ActivityBaseImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new ActivityBase([
            "title" => $row[1]
        ]);
    }
}