<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

/**
 * Class SocialController
 * @package App\Http\Controllers
 */
class SocialController extends Controller
{
    /**
     * @OA\Get(
     *   path="/callback/facebook",
     *   summary="Function call back facebook",
     *   operationId="callback_facebook",
     *   tags={"Callback facebook"},
     *   security={ {"token": {}} },
     *   @OA\Response(response=200, description="successful operation", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Bad request", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Forbidden", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource Not Found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent()),
     * )
     */
    public function callback(Request $request)
    {
        $user = Socialite::driver('facebook')->userFromToken($request->get('token'));
        return response()->json($user);
    }
}
