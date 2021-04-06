<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use \Illuminate\Support\Facades\File;

class Params
{
    /**
     * @param string $fieldName
     * @param $arrange
     * @return string
     */
    public static function buildSortDescAsc($arrange, $fieldName = 'created_at')
    {
        $params = request()->input();
        $params['sort'] = $fieldName;
        $params['arrange'] = $arrange;
        $pr = $params ? '?' . http_build_query($params) : '';

        return url()->current() . $pr;
    }

    /**
     * @param $value
     * @param string $fieldName
     * @return string
     */
    public static function buildUrl($value, $fieldName = 'created_at')
    {
        $params = request()->input();
        $params[$fieldName] = $value;
        if ($value === false) {
            unset($params[$fieldName]);
        }
        $pr = $params ? '?' . http_build_query($params) : '';

        return url()->current() . $pr;
    }

    public static function setSelected($key, $value)
    {
        if ($value == request()->input($key)) {
            return 'selected';
        }
    }
}
