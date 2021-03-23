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
        if ($row[2] === 1) {
            $type = "category";
        } else if ($row[2] === 2) {
            $type = "job";
        } else if ($row[2] === 3) {
            $type = "genre";
        } else if ($row[2] === 4) {
            $type = "musical_instrument";
        } else if ($row[2] === 5) {
            $type = "sns";
        } else if ($row[2] === 6) {
            $type = "equipment_manufacturer";
        } else if ($row[2] === 7) {
            $type = "editing_software";
        } else if ($row[2] === 8) {
            $type = "tool";
        }
        return new ActivityContent([
            "career_id" => $row[1],
            "key" => $type,
            "title" => $row[0]
        ]);
    }
}