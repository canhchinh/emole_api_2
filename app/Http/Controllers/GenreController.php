<?php

namespace App\Http\Controllers;

use App\Repositories\GenreRepository;

class GenreController extends Controller
{
    private $genreRepo;

    public function __construct(
        GenreRepository $genreRepo
    ) {
        $this->genreRepo = $genreRepo;
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
        $genre = $this->genreRepo->where('career_id', $careerId)
            ->select(['id', 'career_id', 'title'])->get();
        return response()->json([
            'status' => true,
            'data' => $genre,
        ]);
    }
}
