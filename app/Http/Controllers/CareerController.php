<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Career;
use App\Models\UserCareer;
use Illuminate\Support\Facades\Auth;
class CareerController extends Controller
{
    /**
     * @OA\Get(
     *   path="/career/list",
     *   summary="list career",
     *   operationId="list_career",
     *   tags={"Career"},
     *   security={ {"token": {}} },
     *   @OA\Response(response=200, description="successful operation", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Bad request", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Forbidden", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource Not Found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent()),
     * )
     */
    public function listCareer(Request $request)
    {
        $careers = Career::select(['id', 'title'])->get();
        return response()->json([
            'status' => true,
            'data' => $careers
        ]);
    }

    public function careerUserSelect(Request $request)
    {
        $request->validate([
            'career_ids' => 'required|array',
        ]);
        $careerIds = $request->input('career_ids');
        $user = auth()->user();
        foreach($careerIds as $careerId) {
            $param = ['user_id'=>$user->id, 'career_id' => $careerId];
            UserCareer::updateOrCreate($param, $param);
        }
        return response()->json([
            'status' => true,
        ]);
    }
}
