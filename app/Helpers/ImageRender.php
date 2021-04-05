<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use \Illuminate\Support\Facades\File;

class ImageRender
{

    const NO_AVATAR = '/storage/users/none/avatar.png';

    /**
     * @param $path
     * @param int $w
     * @param int $h
     * @return string
     */
    public static function userAvatar($path, $w = 44, $h = 44)
    {
        if (!$path) {
            $path = ImageRender::NO_AVATAR;
        }

        $path = str_replace('/storage/', '', $path);
        $fullPath = storage_path($path);

        if (file_exists($fullPath)) {
            $image_resize = Image::make($fullPath);
            $image_resize->extension;
            $image_resize->resize($w, $h);

            $savePathRelative = 'assets/images/resize/' . $path;
            $savePath = public_path($savePathRelative);
            $excludeExt = str_replace($image_resize->filename . '.' . $image_resize->extension, '', $savePath);

            if (!File::isDirectory($excludeExt)) {
                File::makeDirectory($excludeExt, 777, true, true);
            }
            $image_resize->save($savePath);

            return asset($savePathRelative);
        }
    }
}
