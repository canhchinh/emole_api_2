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
}
