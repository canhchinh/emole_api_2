<?php

namespace App\Http\Controllers;

class PublicController extends Controller
{
    /**
     * @OA\Get(
     *   path="/",
     *   tags={"Public"},
     *   summary="public index page",
     *   operationId="public_index_page",
     *   security={ {"token": {}} },
     *   @OA\Response(response=200, description="successful operation", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Bad request", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Forbidden", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource Not Found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent()),
     *   )
     * )
     */
    public function index()
    {
        return "index page";
    }
}
