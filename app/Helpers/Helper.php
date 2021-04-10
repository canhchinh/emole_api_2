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
}
