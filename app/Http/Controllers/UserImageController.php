<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImageUploadRequest;
use App\Repositories\UserImageRepository;
use Illuminate\Http\Request;

class UserImageController extends Controller
{
    private $userImageRepo;

    public function __construct(
        UserImageRepository $userImageRepo
    ) {
        $this->userImageRepo = $userImageRepo;
    }
    /**
     * @OA\Post(
     *   path="/user/image",
     *   summary="user upload image",
     *   operationId="upload_image",
     *   tags={"Image"},
     *   security={ {"token": {}} },
     *   @OA\RequestBody(
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *               type="object",
     *               @OA\Property(
     *                  property="images[]",
     *                  type="array",
     *                  @OA\Items(
     *                       type="string",
     *                       format="binary",
     *                  ),
     *               ),
     *           ),
     *       )
     *   ),
     *   @OA\Response(response=200, description="successful operation", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Bad request", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Forbidden", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource Not Found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent()),
     * )
     */
    public function imageUpload(ImageUploadRequest $request)
    {
        try {
            $user = $request->user();
            if ($request->imageRemove) {
                $this->isRemoveImage($request->imageRemove, $user->id);
            }
            $files = $request->images;
            foreach ($files as $file) {
                $extension = explode('/', mime_content_type($file))[1];
                if (in_array($extension, ['jpg', 'png', 'jpeg', 'gif'])) {
                    $path = 'user/';
                    $fileName = $this->saveImgBase64($file, $path, $user->id, true);
                    $url = '/storage/' . $path . 'group/' . $fileName;
                    $this->userImageRepo->create([
                        'user_id' => $user->id,
                        'url' => $url,
                    ]);
                }
            }
            return response()->json([
                'status' => true,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function isRemoveImage($listId, $userId) {
        return $this->userImageRepo->where('user_id', $userId)->whereIn("id", array_unique($listId))->delete();
    }

    /**
     * @OA\Get(
     *   path="/user/image",
     *   summary="list image",
     *   operationId="list_image",
     *   tags={"Image"},
     *   security={ {"token": {}} },
     *      @OA\Parameter(
     *          name="user_id",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *   @OA\Response(response=200, description="successful operation", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Bad request", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Forbidden", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource Not Found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent()),
     * )
     */
    public function listImage(Request $request)
    {
        try {
            $user = $request->user();
            $userId = $user->id;
            $req = $request->all();
            if(!empty($req['user_id'])) {
                $userId = $req['user_id'];
            }

            $images = $this->userImageRepo->where('user_id', $userId)
                ->select(['id', 'url'])->limit(9)->get();
            if (!empty($images)) {
                $images->map(function ($value) {
                    $value->url = config('common.app_url') . $value->url;
                });
            }
            return response()->json([
                'status' => true,
                'data' => $images,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    /**
     * @OA\Get(
     *   path="/user/image/{idUser}",
     *   summary="list image by id user",
     *   operationId="list_image_by_user_id",
     *   tags={"Image"},
     *   security={ {"token": {}} },
     *   @OA\Parameter(
     *         description="ID of user",
     *         in="path",
     *         name="idUser",
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
    public function listImageByIdUser($idUser)
    {
        try {
            $images = $this->userImageRepo->where('user_id', $idUser)
                ->select(['id', 'url'])->get();
            if (!empty($images)) {
                $images->map(function ($value) {
                    $value->url = config('common.app_url') . $value->url;
                });
            }
            return response()->json([
                'status' => true,
                'data' => $images,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}