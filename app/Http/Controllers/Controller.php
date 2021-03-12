<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Emole API",
 *      description="Swagger OpenApi of Emole",
 *      @OA\Contact(
 *          email="phanxuanbachkh@gmail.com"
 *      ),
 *     @OA\License(
 *         name="Apache 2.4",
 *         url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *     )
 * )
 */

/**
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="Emole API Server"
 *  )
 */

/**
 * @OA\SecurityScheme(
 *     type="apiKey",
 *     description="Using bearer token. Example: Bearer [token]",
 *     in="header",
 *     name="authorization",
 *     securityScheme="token",
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function saveImgBase64($param, $folder, $idUser, $group = false)
    {
        list($extension, $content) = explode(';', $param);
        $tmpExtension = explode('/', $extension);
        preg_match('/.([0-9]+) /', microtime(), $m);
        $fileName = sprintf('img%s%s.%s', date('YmdHis'), $m[1], $tmpExtension[1]);
        $content = explode(',', $content)[1];
        $storage = \Storage::disk('public');

        $checkDirectory = $storage->exists($folder);
        if (!$checkDirectory) {
            $storage->makeDirectory($folder);
        }
        $newFileName = $idUser . '_' . $fileName;

        if ($group) {
            \Log::info('group ');
            $storage->put($folder . '/group/' . $newFileName, base64_decode($content), 'public');
        } else {
            \Log::info('avatar ');
            $storage->put($folder . '/' . $newFileName, base64_decode($content), 'public');
        }
        \Log::info('file Name ', $newFileName);
        return $newFileName;
    }
}