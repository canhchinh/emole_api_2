<?php

namespace App\Http\Controllers;

use App\Repositories\ActivityBaseRepository;
use App\Repositories\ActivityContentRepository;
use App\Repositories\CareerRepository;
use App\Repositories\UserCareerRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;

class ActivityController extends Controller
{

    private $activityRepo, $userRepo, $userCareerRepo, $activityContentRepo, $careerRepo;

    public function __construct(
        ActivityBaseRepository $activityRepo,
        UserRepository $userRepo,
        UserCareerRepository $userCareerRepo,
        ActivityContentRepository $activityContentRepo,
        CareerRepository $careerRepo
    ) {
        $this->activityRepo = $activityRepo;
        $this->userRepo = $userRepo;
        $this->userCareerRepo = $userCareerRepo;
        $this->activityContentRepo = $activityContentRepo;
        $this->careerRepo = $careerRepo;
    }
    /**
     * @OA\Get(
     *   path="/activity-base",
     *   summary="list activity base",
     *   operationId="list_activity_base",
     *   tags={"Activity base"},
     *   security={ {"token": {}} },
     *   @OA\Response(response=200, description="successful operation", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Bad request", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Forbidden", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource Not Found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent()),
     * )
     */
    public function listActivityBase(Request $request)
    {
        $activityBase = $this->activityRepo->select(['id', 'title'])->get();
        return response()->json([
            'status' => true,
            'data' => $activityBase,
        ]);
    }

    /**
     * @OA\Get(
     *   path="/career/{career_id}/info",
     *   summary="info career",
     *   operationId="info_career",
     *   tags={"Activity"},
     *   security={ {"token": {}} },
     *      @OA\Parameter(
     *          name="user_id",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *     @OA\Parameter(
     *         description="ID of career",
     *         in="path",
     *         name="career_id",
     *         required=true,
     *         @OA\Schema(
     *           type="integer",
     *           format="int64"
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
    public function info(Request $request, $careerId)
    {
        $user = auth()->user();
        $userId = $user->id;
        $req = $request->all();
        if(!empty($req['user_id'])) {
            $userId = $req['user_id'];
        }
        $userCareer = $this->userCareerRepo->where([
            'user_id' => $userId,
            'career_id' => $careerId,
        ])
            ->first();

        if (empty($userCareer->id)) {
            return response()->json([
                'status' => false,
                'message' => 'Career not found',
            ], 500);
        }
        $titleCareer = $this->careerRepo->where('id', $careerId)->first();
        $userCareer['career_title'] = $titleCareer->title;

        return response()->json([
            'status' => true,
            'data' => $userCareer,
        ]);
    }
}
