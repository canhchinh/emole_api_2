<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sns;

class SnsController extends Controller
{
    /**
     * @OA\Get(
     *   path="/sns/list",
     *   summary="list sns",
     *   operationId="list_sns",
     *   tags={"Sns"},
     *   security={ {"token": {}} },
     *   @OA\Response(response=200, description="successful operation", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Bad request", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Forbidden", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource Not Found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent()),
     * )
     */
    public function listSns(Request $request)
    {
        $category = Sns::select(['id', 'title'])->get();
        return response()->json([
            'status' => true,
            'date' => $category
        ]);
    }
}
