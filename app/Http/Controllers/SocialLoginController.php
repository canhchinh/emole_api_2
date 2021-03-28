<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use Illuminate\Http\Request;

class SocialLoginController extends Controller
{
    private $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    /**
     * @OA\Post(
     *   path="/social-login/login",
     *   summary="social login/",
     *   operationId="login",
     *   tags={"social login"},
     *   @OA\RequestBody(
     *        @OA\MediaType(
     *            mediaType="application/json",
     *            @OA\Schema(
     *                @OA\Property(property="provider", type="string"),
     *                @OA\Property(property="provider_id", type="string"),
     *                @OA\Property(property="user_name", type="string")
     *            )
     *        )
     *     ),
     *   @OA\Response(response=200, description="successful operation", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Bad request", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Forbidden", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource Not Found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent()),
     * )
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'provider' => 'required',
            'provider_id' => 'required'
        ]);
        try {
            $user = $this->userRepo->where(
                [
                    'provider' => $request->provider,
                    'provider_id' => $request->provider_id
                ]
            )->first();
            if (!$user) {
                $user = $this->userRepo->create(
                    [
                        'provider' => $request->provider,
                        'provider_id' => $request->provider_id,
                        'user_name' => $request->user_name
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
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
