<?php

namespace App\Http\Controllers;
use Fcorz\LaravelLineNotify\LaravelLineMessage;
use Illuminate\Http\Request;
use App\Repositories\LineNotifyAccessTokenRepository;

class LineNotifyController extends Controller
{
    private $lineNotifyAccessTokenRepo;

    public function __construct(LineNotifyAccessTokenRepository $lineNotifyAccessTokenRepo)
    {
        $this->lineNotifyAccessTokenRepo = $lineNotifyAccessTokenRepo;
    }

    /**
     * @OA\Get(
     *   path="/line-notify/get-auth-link",
     *   summary="line notify/",
     *   operationId="get-auth-link",
     *   tags={"LineNotify"},
     *   security={ {"token": {}} },
     *   @OA\Response(response=200, description="successful operation", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Bad request", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Forbidden", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource Not Found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent()),
     * )
     */
    public function getAuthLink(): \Illuminate\Http\JsonResponse
    {
        $data = array(
            'scope' => 'notify',
            'response_type' => 'code',
            'client_id' => config('line.channel_access_token'),
            'redirect_uri' => config('line.redirect_uri'),
            'state' => config('line.state')
        );
        $authUrl = config('line.line_auth_url');
        return response()->json([
            'status' => true,
            'data' => $authUrl . "?" . http_build_query($data)
        ]);
    }

    /**
     * @OA\Get(
     *   path="/line-notify/get-access-token",
     *   summary="line notify/",
     *   operationId="get_access_token",
     *   tags={"LineNotify"},
     *   security={ {"token": {}} },
     *      @OA\Parameter(
     *          name="code",
     *          required=true,
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
    public function getAccessToken(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'code' => 'required'
        ]);
        try {
            $result = json_decode(app('line-notify')->getAccessToken($request->code));
            $user = auth()->user();
            $this->lineNotifyAccessTokenRepo->updateOrCreate(
                ['user_id' => $user->id],
                ['line_notify_access_token' => $result->access_token]
            );

            return response()->json([
                'status' => true,
                'data' => [
                    'token' => $result->access_token
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @OA\Post(
     *   path="/line-notify/send-notify",
     *   summary="line notify/",
     *   operationId="send-notify",
     *   tags={"LineNotify"},
     *   security={ {"token": {}} },
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="message", type="string", example="Hello world")
     *              )
     *          )
     *     ),
     *   @OA\Response(response=200, description="successful operation", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Bad request", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Forbidden", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource Not Found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent()),
     * )
     */
    public function sendNotify(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'message' => 'required',
            "url" => 'required',
        ]);

        try {
            $message = (new LaravelLineMessage())->message($request->message)->http(["click" => $request->url]);
            $user = auth()->user();
            $token = $this->lineNotifyAccessTokenRepo->where('user_id', $user->id)->first();
            app('line-notify')->sendNotify($message, $token->line_notify_access_token);
            return response()->json([
                'status' => true
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}