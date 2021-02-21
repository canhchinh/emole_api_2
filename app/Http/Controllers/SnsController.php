<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sns;
use App\Models\UserSns;

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

    /**
     * @OA\Post(
     *   path="/sns/user-select",
     *   summary="user select sns",
     *   operationId="select_sns",
     *   tags={"Sns"},
     *   security={ {"token": {}} },
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *         mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="sns", type="json", example={
                            {
                            "id": 1,
                            "content": "twitter@twitter.com"
                            },
                            {
                            "id": 2,
                            "content": "instagram@instagram.com"
                            },
                            {
                            "id": 3,
                            "content": "youtube@youtube.com"
                            },
                            {
                            "id": 4,
                            "content": ""
                            },
                            {
                            "id": 5,
                            "content": ""
                            },
                            {
                            "id": 6,
                            "content": ""
                            },
                            {
                            "id": 7,
                            "content": ""
                            },
                            {
                            "id": 8,
                            "content": ""
                            }
                        })
     *             )
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
    public function userSelectSns(Request $request)
    {
        $request->validate([
            'sns' => 'required'
        ]);
        $req = $request->all();
        $user = auth()->user();
        foreach($req['sns'] as $item) {
            $data = [
                'user_id' => $user->id,
                'sns_id' => $item['id'],
                'content' => $item['content']
            ];
            UserSns::updateOrCreate($data, $data);
        }
        return response()->json([
            'status' => true
        ]);
    }
}
