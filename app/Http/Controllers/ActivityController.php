<?php

namespace App\Http\Controllers;

use App\Repositories\ActivityBaseRepository;
use App\Repositories\UserRepository;
use App\Repositories\UserCareerRepository;
use App\Repositories\ActivityContentRepository;
use Illuminate\Http\Request;

class ActivityController extends Controller
{

    private $activityRepo, $userRepo, $userCareerRepo, $activityContentRepo;

    public function __construct(
        ActivityBaseRepository $activityRepo,
        UserRepository $userRepo,
        UserCareerRepository $userCareerRepo,
        ActivityContentRepository $activityContentRepo
    ) {
        $this->activityRepo = $activityRepo;
        $this->userRepo = $userRepo;
        $this->userCareerRepo = $userCareerRepo;
        $this->activityContentRepo = $activityContentRepo;
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

    public function info(Request $request, $careerId)
    {
        $user = auth()->user();
        $userCareer = $this->userCareerRepo->where([
            'user_id' => $user->id,
            'career_id' => $careerId
        ])
        ->first();
        if(empty($userCareer)) {
            return response()->json([
                'status' => false,
                'message' => 'Career not found'
            ]);
        }
        

    }
}
