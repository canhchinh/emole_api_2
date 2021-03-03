<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Repositories\UserImageRepository;
use App\Http\Requests\ImageUploadRequest;

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
     *   tags={"User"},
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
            $files = $request->file('images');
            foreach($files as $k=>$file) {
                $extension = $file->getClientOriginalExtension();
                if (in_array($extension, ['jpg', 'png', 'jpeg'])) {
                    $fileName = time()+$k;
                    $path = 'user/' . $user->id . '/' . $fileName . '.' . $extension;
                    Storage::disk('public')->put($path, File::get($file));
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
            ]);
        }
    }
}
