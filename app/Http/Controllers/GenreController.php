<?php

namespace App\Http\Controllers;

use App\Repositories\GenreRepository;
use App\Repositories\ActivityContentRepository;

class GenreController extends Controller
{
    private $genreRepo, $activityContentRepo;

    public function __construct(
        GenreRepository $genreRepo,
        ActivityContentRepository $activityContentRepo
    ) {
        $this->genreRepo = $genreRepo;
        $this->activityContentRepo = $activityContentRepo;
    }

    /**
     * @OA\Get(
     *   path="/career/{career_id}/genre/list",
     *   summary="genre sns",
     *   operationId="list_genre",
     *   tags={"Genre"},
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
    public function listGenre($careerId)
    {
        $genre = $this->activityContentRepo->where('career_id', $careerId)
            ->where('key', config('common.activity_content.genre.key'))
            ->select(['id', 'career_id', 'title'])->get();
        return response()->json([
            'status' => true,
            'data' => $genre,
        ]);
    }
}
