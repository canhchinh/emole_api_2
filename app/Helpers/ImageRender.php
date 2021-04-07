<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use \Illuminate\Support\Facades\File;

class ImageRender
{

    const NO_AVATAR = '/storage/no-image/no-avatar.png';

    /**
     * @param $path
     * @param int $w
     * @param int $h
     * @return string
     */
    public static function userAvatar($path, $w = 44, $h = 44)
    {
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
}
