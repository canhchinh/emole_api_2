<?php

namespace App\Helpers;

class DateTime extends \Illuminate\Support\Carbon
{
    /**
     * @param $time
     * @return string|null
     */
    public static function showDateTime($time)
    {
        return self::doParse($time, Constants::FORMAT_DATE_TIME);
    }

    public static function showBirthDay($time)
    {
        return self::doParse($time, 'Y年m月d日');
    }

    public static function doParse($time, $toFormat)
    {
        try {
            if (!$time) {
                return '--';
            }
            return static::parse($time)->format($toFormat);
        } catch (\Exception $e) {
            return null;
        }
    }
}
