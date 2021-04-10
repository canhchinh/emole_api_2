<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use \Illuminate\Support\Facades\File;

class Params extends Helper
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
        ksort($params);
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
        ksort($params);
        if ($value === false) {
            unset($params[$fieldName]);
        }
        $pr = $params ? '?' . http_build_query($params) : '';

        return url()->current() . $pr;
    }

    /**
     * @param string $excludeFieldNames
     * @return string
     */
    public static function buildHiddenFields($excludeFieldNames = [])
    {
        $params = request()->input();
        ksort($params);
        $ins = '';
        foreach ($params as $key => $param) {
            if (!in_array($key, $excludeFieldNames)) {
                $ins .= '<input type="hidden" name="' . $key . '" value="' . $param . '">';
            }
        }

        return $ins;
    }

    public static function setSelected($key, $value)
    {
        if ($value == request()->input($key)) {
            return 'selected';
        }
    }
}
