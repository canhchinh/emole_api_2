<?php

namespace App\Http\Controllers;

use App\Repositories\ActivityBaseRepository;
use Illuminate\Http\Request;

class ActivityController extends Controller
{

    private $activityRepo;

    public function __construct(
        ActivityBaseRepository $activityRepo
    ) {
        $this->activityRepo = $activityRepo;
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
}
