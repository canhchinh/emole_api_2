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

    private function replaceUrlAvatarTwitter($url) {
        if ($url) {
            return chop($url, "_normal"); 
        }
        return "";
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
            $user = $this->userRepo->where(['provider' => "twitter", 'provider_id' => $userTwitter->uid]);
            if (!empty($userTwitter->email)) {
                $user = $user->orWhere('email', $userTwitter->email);
            }
            $user = $user->first();
            $unregistered = true;
            if (!$user) {
                $user = $this->userRepo->create(
                    [
                        'provider' => "twitter",
                        'provider_id' => $userTwitter->uid,
                        'user_name' => $userTwitter->name,
                        'given_name' => $userTwitter->name,
                        'email' => $userTwitter->email,
                        'avatar' => $this->replaceUrlAvatarTwitter($userTwitter->imageUrl),
                        'active' => 1
                    ]
                );
            } else {
                if ($user->email == $userTwitter->email) {
                    if ($user->provider != "twitter" || $user->provider_id != $userTwitter->uid) {
                        return response()->json([
                            'status' => false,
                            'message' => 'Email already exists'
                        ]);
                    }
                }
                $unregistered = empty($user->user_name);
            }
            $tokenResult = $user->createToken('authToken')->plainTextToken;
            return response()->json([
                'status' => true,
                'access_token' => $tokenResult,
                'unregistered' => $unregistered,
                'token_type' => 'Bearer',
                'user_name' => $user->user_name,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false
            ]);
        }
    }

}