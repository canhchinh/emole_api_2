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
            $files = $request->images;
            foreach ($files as $k => $file) {
                $extension = explode('/', mime_content_type($file))[1];
                if (in_array($extension, ['jpg', 'png', 'jpeg', 'gif'])) {
                    $fileName = time() + $k;
                    $path = 'user/' . $user->id . '/' . $fileName . '.' . $extension;
                    $this->saveImgBase64($file, $path);
                    $url = '/storage/' . $path;
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

    /**
     * @OA\Get(
     *   path="/user/image",
     *   summary="list image",
     *   operationId="list_image",
     *   tags={"Image"},
     *   security={ {"token": {}} },
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
            $data = [];
            $images = $this->userImageRepo->where('user_id', $user->id)
                ->select(['id', 'url'])->get();
            if(!empty($images)) {
                $images->map(function ($value) {
                    $value->url = config('common.app_url').$value->url;
                });
            }
            return response()->json([
                'status' => true,
                'data' => $images
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
