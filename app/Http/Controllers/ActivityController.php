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
        $userCareer = $this->userCareerRepo->where([
            'user_id' => $user->id,
            'career_id' => $careerId,
        ])
            ->first();
        $titleCareer = $this->careerRepo->where('id', $careerId)->first();
        if (empty($userCareer)) {
            return response()->json([
                'status' => false,
                'message' => 'Career not found',
            ], 500);
        }
        $userCareer['career_title'] = $titleCareer->title;

        $categories = [];
        $jobs = [];
        $genres = [];
        $tags = [];

        if(!empty($userCareer->category_ids)) {
            $categories = $this->activityContentRepo->where('career_id', $careerId)
                ->whereIn('id', json_decode($userCareer->category_ids))->get();
        }

        if(!empty($userCareer->job_ids)) {
            $jobs = $this->activityContentRepo->where('career_id', $careerId)
                ->whereIn('id', json_decode($userCareer->job_ids))->get();
        }

        if(!empty($userCareer->genre_ids)) {
            $genres = $this->activityContentRepo->where('career_id', $careerId)
                ->whereIn('id', json_decode($userCareer->genre_ids))->get();
        }

        if(!empty($userCareer->tags)) {
            $tags = json_decode($userCareer->tags);
        }

        $userCareer['categories'] = $categories;
        $userCareer['jobs'] = $jobs;
        $userCareer['genres'] = $genres;

        $userCareer['tags'] = $tags;

        unset($userCareer['category_ids']);
        unset($userCareer['job_ids']);
        unset($userCareer['genre_ids']);

        return response()->json([
            'status' => true,
            'data' => $userCareer,
        ]);

    }
}
