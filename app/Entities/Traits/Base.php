<?php


namespace App\Entities\Traits;

use Illuminate\Support\Facades\Schema;

trait Base
{
    protected $restrictedFields = ['created_at', 'updated_at'];

    /**
     * @param $data
     * @param null $nesting
     * @return $this
     */
    public function populate($data, $nesting = null)
    {
        foreach ($data as $key => $value) {
            if (!$this->validate($key, $value)) continue;
            $this->{$key} = gettype($value) === "string" ? trim($value) : $value;
        }

        if (!empty($nesting)) {
            $this->populate($data[$nesting]);
        }

        return $this;
    }

    /**
     * @param $key
     * @param $value
     * @return bool
     */
    protected function validate($key, $value)
    {
        if (in_array($key, $this->restrictedFields)) {
            return false;
        }
        if (is_array($value) || is_object($value) || !Schema::hasColumn($this->getTable(), $key)) {
            return false;
        }
        return true;
    }
}
