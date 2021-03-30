<?php
namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use App\Services\TwitterLoginService;
use Illuminate\Http\Request;
class TwitterLoginController extends Controller
{
    private $userRepo;

    private $twitterLoginService;

    public function __construct(UserRepository $userRepo, TwitterLoginService $twitterLoginService)
    {
        $this->twitterLoginService = $twitterLoginService;
        $this->userRepo = $userRepo;
    }

    public function getInfo(): \Illuminate\Http\JsonResponse
    {
        try {
            $loginInfo = $this->twitterLoginService->getLoginInfo();
            if (empty($loginInfo)) {
                throw new \Exception('Data is empty.');
            }

            return response()->json([
                'status' => true,
                'url' => $loginInfo['url'],
                'request_oauth_token' => $loginInfo['request_oauth_token'],
                'request_oauth_token_secret' => $loginInfo['request_oauth_token_secret']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false
            ]);
        }
    }

    public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $request->validate([
                'oauth_token' => 'required',
                'oauth_verifier' => 'required',
                'request_oauth_token' => 'required',
                'request_oauth_token_secret' => 'required'
            ]);
            $userTwitter = $this->twitterLoginService->getUserInfo($request);
            $user = $this->userRepo->where(['provider' => "twitter", 'provider_id' => $userTwitter->uid])->first();
            if (!$user) {
                $user = $this->userRepo->create(
                    [
                        'provider' => "twitter",
                        'provider_id' => $userTwitter->uid,
                        'user_name' => $userTwitter->name,
                        'given_name' => $userTwitter->name,
                        'email' => $userTwitter->email,
                        'avatar' => $userTwitter->imageUrl
                    ]
                );
            }
            $tokenResult = $user->createToken('authToken')->plainTextToken;
            return response()->json([
                'status' => true,
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false
            ]);
        }
    }

}
