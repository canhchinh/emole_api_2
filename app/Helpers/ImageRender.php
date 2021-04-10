<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use \Illuminate\Support\Facades\File;

class ImageRender extends Helper
{
    const NO_AVATAR = '/assets/no-image/no-avatar.png';
    const NO_IMAGE = '/assets/no-image/no-img.png';

    /**
     * @param $string
     * @return bool
     */
//    public static function isUrl($string) {
//        if (filter_var($string, FILTER_VALIDATE_URL)) {
//            return true;
//        }
//
//        return false;
//    }

    /**
     * @param $path
     * @param int $w
     * @param int $h
     * @return string
     */
    public static function userAvatar($path, $w = 44, $h = 44)
    {
        if (!$path) {
            return self::NO_AVATAR;
        }

        if (self::getInstance()->isUrl($path)) {
            return $path;
        }

        $exists = File::exists(public_path($path));
        if ($exists) {
            return $path;
        }

        return self::NO_AVATAR;

        /** TODO: */
        if (!$path) {
            $path = self::NO_AVATAR;
        }

        $publicPath = public_path($path);
        $storagePathResize = public_path('storage/resize');

        if (File::exists($publicPath)) {
            $image_resize = Image::make($publicPath);
            $fileName = $image_resize->filename . '.' . $image_resize->extension;

            $image_resize->resize($w, $h);
            $savePathRelative = $storagePathResize . $path;
            $dir = str_replace($fileName, '', $savePathRelative);

            if (!File::isDirectory($dir)) {
                File::makeDirectory($dir, 777, true, true);
            }

            $image_resize->save($savePathRelative);

            return asset($savePathRelative);
        } else {
            return asset(self::NO_AVATAR);
        }
    }

    /**
     * @param $path
     * @param int $w
     * @param int $h
     * @return string
     */
    public static function portfolioAvatar($path, $w = 44, $h = 44)
    {
        if (!$path) {
            return self::NO_IMAGE;
        }

        if (self::getInstance()->isUrl($path)) {
            return $path;
        }

        $exists = File::exists(public_path($path));
        if ($exists) {
            return $path;
        }

        return self::NO_IMAGE;
    }

    /**
     * @param $data
     * @return array
     */
    public static function parserPortfolioList($data)
    {
        $imgs = [];
        if ($data) {
            $arrData = explode(';=;', $data);
            if ($arrData) {
                foreach ($arrData as $arrs) {
                    $arrs = str_replace('[', '', $arrs);
                    $arrs = str_replace(']', '', $arrs);
                    $subArr = explode(',', $arrs);
                    if (!isset($subArr[0])) {
                        continue;
                    }
                    $imgs[] = $subArr[0];
                }
            }
        }

        return $imgs;
    }
}
