<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;
use App\Services\FacebookService;
use Mockery\Exception;

/**
 * Class SocialController
 * @package App\Http\Controllers
 */
class SocialController extends Controller
{
    /**
     * @var \App\Services\FacebookService
     */
    private $fbService;


    public function __construct(
        FacebookService $facebookService
    )
    {
        $this->fbService = $facebookService;
    }

    /**
     * @OA\Patch(
     *   path="/callback/facebook",
     *   summary="Function call back facebook",
     *   operationId="callback_facebook",
     *   tags={"Callback facebook"},
     *   security={ {"token": {}} },
     *      @OA\Parameter(
     *          name="token",
     *          description="Access token",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
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
        $connected_instagram_account_id = null;
        $user_name = null;
        $token = $request->get('token');
        $access_token = $this->fbService->getLongTermToken($token);
        $this->fbService->getFacebook()->setDefaultAccessToken($token);
        $user_info = $this->fbService->getUserInfo('me?fields=accounts{connected_instagram_account}');
        return response()->json([
            'test' => $user_info['accounts'],
        ]);
        if ($user_info && !empty($user_info['accounts'])) {
            $connected_instagram_account_id = $user_info['accounts']['data'][0]['connected_instagram_account']['id'];
            $user_info = $this->fbService->getUserInfo("$connected_instagram_account_id?fields=username");
            $user_name = $user_info["username"];
        }
        
        return response()->json([
            'access_token' => $access_token,
            'id_ig' => $connected_instagram_account_id,
            'username_ig' => $user_name,
        ]);
    }
}