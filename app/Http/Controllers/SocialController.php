<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FacebookService;
use App\Repositories\UserRepository;

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
    private $userRepo;


    public function __construct(
        UserRepository $userRepo,
        FacebookService $facebookService
    )
    {
        $this->fbService = $facebookService;
        $this->userRepo = $userRepo;
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
        try {
            $connected_instagram_account_id = null;
            $user_name = null;
            $token = $request->get('token');
            $access_token = $this->fbService->getLongTermToken($token);
            $this->fbService->getFacebook()->setDefaultAccessToken($token);
            $user_info = $this->fbService->getUserInfo('me?fields=accounts{connected_instagram_account}');
            return response()->json([
                'status' => true,
                'info' => $user_info,
            ]);
            if ($user_info && !empty($user_info['accounts'])) {
                $connected_instagram_account_id = $user_info['accounts']['data'][0]['connected_instagram_account']['id'];
                $user_info = $this->fbService->getUserInfo("$connected_instagram_account_id?fields=username");
                $user_name = $user_info["username"];
            }
            // $user = $this->userRepo->where('facebook_id', $connected_instagram_account_id)->first();
            // $tokenResult = null;
            // if(empty($user->id)) {
            //     $createUser = $this->userRepo->create([
            //         'user_name' => $user_name,
            //         'facebook_id' => $connected_instagram_account_id, 
            //         'active' => 1,
            //         'instagram_user' => $user_name,
            //         'access_token' => $access_token,
            //         'instagram_id' => $connected_instagram_account_id,
            //         'register_finish_step' => 2,
            //     ]);
            //     $tokenResult = $createUser->createToken('authToken')->plainTextToken;
            // } else {
            //     $tokenResult = $user->createToken('authToken')->plainTextToken;
            // }
            return response()->json([
                'status' => true,
                // 'accessToken' => $tokenResult,
                'access_token' => $access_token,
                'id_ig' => $connected_instagram_account_id,
                'username_ig' => $user_name,
                // 'token_type' => 'Bearer',
                // 'register_finish_step' => (!empty($user) && $user->register_finish_step) ? $user->register_finish_step : 0,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'statusCode' => 404,
                'message' => $e->getMessage(),
            ], 404);
        }
    }
}