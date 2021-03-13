<?php

namespace App\Http\Controllers;

use App\Repositories\JobRepository;
use App\Repositories\ActivityContentRepository;
use Illuminate\Http\Request;

class JobController extends Controller
{
    private $jobRepo, $activityContentRepo;

    public function __construct(
        JobRepository $jobRepo,
        ActivityContentRepository $activityContentRepo
    ) {
        $this->jobRepo = $jobRepo;
        $this->activityContentRepo = $activityContentRepo;
    }

    /**
     * @OA\Get(
     *   path="/career/{career_id}/job/list",
     *   summary="list job",
     *   operationId="list_job",
     *   tags={"Job description"},
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
    public function listJob(Request $request, $careerId)
    {
        $job = $this->activityContentRepo->where('key', 'job')
            ->where('career_id', $careerId)
            ->select(['id', 'career_id', 'title'])
            ->get();
        return response()->json([
            'status' => true,
            'data' => $job
        ]);
    }
}
