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