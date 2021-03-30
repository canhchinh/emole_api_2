<?php

namespace App\Http\Controllers;

use App\Repositories\SnsRepository;
use App\Repositories\UserSnsRepository;
use App\Repositories\ActivityContentRepository;
use Illuminate\Http\Request;
use App\Services\TwitterService;
use MetzWeb\Instagram\Instagram;
use Sovit\TikTok\Api;

class SnsController extends Controller
{
    private $snsRepo, $userSnsRepo, $activityContentRepo;

    /**
     * @var TwitterService
     */
    private $twitterService;

    /**
     * @var \Sovit\TikTok\Api
     */
    private $tiktok;

    public function __construct(
        SnsRepository $snsRepo,
        UserSnsRepository $userSnsRepo,
        ActivityContentRepository $activityContentRepo,
        TwitterService $twitterService,
        \Sovit\TikTok\Api $tiktok
    ) {
        $this->snsRepo = $snsRepo;
        $this->userSnsRepo = $userSnsRepo;
        $this->activityContentRepo = $activityContentRepo;
        $this->twitterService = $twitterService;
        $this->tiktok = $tiktok;
    }
    /**
     * @OA\Get(
     *   path="/sns/list",
     *   summary="list sns",
     *   operationId="list_sns",
     *   tags={"Sns"},
     *   security={ {"token": {}} },
     *   @OA\Response(response=200, description="successful operation", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Bad request", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Forbidden", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource Not Found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent()),
     * )
     */
    public function listSns()
    {
        $category = $this->activityContentRepo->where('key', 'sns')
            ->select(['id', 'title'])->get();
        return response()->json([
            'status' => true,
            'data' => $category,
        ]);
    }

    /**
     * @OA\Post(
     *   path="/sns/save",
     *   summary="user save sns",
     *   operationId="select_sns",
     *   tags={"Sns"},
     *   security={ {"token": {}} },
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *         mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="sns", type="json", example={
    {
    "id": 1,
    "content": "twitter@twitter.com"
    },
    {
    "id": 2,
    "content": "instagram@instagram.com"
    },
    {
    "id": 3,
    "content": "youtube@youtube.com"
    },
    {
    "id": 4,
    "content": ""
    },
    {
    "id": 5,
    "content": ""
    },
    {
    "id": 6,
    "content": ""
    },
    {
    "id": 7,
    "content": ""
    },
    {
    "id": 8,
    "content": ""
    }
    })
     *             )
     *         )
     *     ),
     *   @OA\Response(response=200, description="successful operation", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Bad request", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Forbidden", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource Not Found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent()),
     * )
     */
    public function save(Request $request)
    {
        $request->validate([
            'sns' => 'required',
        ]);
        $req = $request->all();
        $user = auth()->user();
        foreach ($req['sns'] as $item) {
            $data = [
                'user_id' => $user->id,
                'sns_id' => $item['id'],
                'content' => $item['content'],
            ];
            $this->userSnsRepo->updateOrCreate($data, $data);
        }
        return response()->json([
            'status' => true,
        ]);
    }

    /**
     * @OA\Get(
     *   path="/sns/followers-count",
     *   summary="sns followers count",
     *   operationId="followers_count",
     *   tags={"Sns"},
     *   security={ {"token": {}} },
     *   @OA\Response(response=200, description="successful operation", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Bad request", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Forbidden", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource Not Found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent()),
     * )
     */
    public function followersCount()
    {
        $snsFollowersCount = [];
        $twitterInfoUser = $this->twitterService->getUsers(null, 'BarackObama');
        if ($twitterInfoUser) {
            $snsFollowersCount['twitter'] = $twitterInfoUser['followers_count'];
        }

        $tiktokInfoUser = $this->tiktok->getUser('tiktok');
        if ($tiktokInfoUser) {
            $snsFollowersCount['tiktok'] = $tiktokInfoUser->stats->followerCount;
        }
//        $instagram = new Instagram([
//            'apiKey'      => 'YOUR_APP_KEY',
//            'apiSecret'   => 'YOUR_APP_SECRET',
//            'apiCallback' => 'YOUR_APP_CALLBACK'
//        ]);
//
//        var_dump($instagram->searchUser('zareiimojii')); exit;

        return response()->json([
            'status' => true,
            'data' => ['snsFollowerCount' => $snsFollowersCount],
        ]);
    }
}
