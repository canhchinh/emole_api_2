<?php


namespace App\Helpers;


class Helper
{
    use Traits;

    public function isUrl($string)
    {
        if (filter_var($string, FILTER_VALIDATE_URL)) {
            return true;
        }

        return false;
    }

    /**
     * @param $string1
     * @param $string2
     * @param string $slash
     * @return string
     */
    public static function concatStringBySlash($string1, $string2, $slash = '/')
    {
        if (substr($string1, -1) == $slash) {
            return $string1 . $string2;
        }

        return $string1 . $slash . $string2;
    }
}
