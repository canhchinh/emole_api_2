<?php

namespace App\Http\Controllers;

use App\Repositories\CategoryRepository;
use App\Repositories\ActivityContentRepository;
class CategoryController extends Controller
{
    private $categoryRepo, $activityContentRepo;

    public function __construct(
        CategoryRepository $categoryRepo,
        ActivityContentRepository $activityContentRepo
    ) {
        $this->categoryRepo = $categoryRepo;
        $this->activityContentRepo = $activityContentRepo;
    }
    /**
     * @OA\Get(
     *   path="/career/{career_id}/category/list",
     *   summary="list category",
     *   operationId="list_category",
     *   tags={"Category"},
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

    public function listCategory($careerId)
    {
        $category = $this->activityContentRepo->where('key', config('common.activity_content.category.key'))
            ->where('career_id', $careerId)
            ->select(['id', 'career_id', 'title'])
            ->get();
        return response()->json([
            'status' => true,
            'data' => $category
        ]);
    }
}
