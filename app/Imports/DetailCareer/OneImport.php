<?php

namespace App\Imports\DetailCareer;

use App\Entities\ActivityContent;
use Maatwebsite\Excel\Concerns\ToModel;

class OneImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $type = null;
        if ($row[1] === 1) {
            $type = "category";
        } else if ($row[1] === 2) {
            $type = "job";
        } else {
            $type = "genre";
        }
        return new ActivityContent([
            "career_id" => 1,
            "key" => $type,
            "title" => $row[0]
        ]);
    }
}