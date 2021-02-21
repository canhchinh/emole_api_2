<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\UserImage;
use Illuminate\Support\Facades\File;
class UserImageController extends Controller
{
    /**
     * @OA\Post(
     *   path="/user/image",
     *   summary="user upload image",
     *   operationId="upload_image",
     *   tags={"User"},
     *   security={ {"token": {}} },
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *         mediaType="multipart/form-data",
     *             @OA\Schema(
     *                @OA\Property(property="image",type="string", format="binary")
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
    public function newImageUpload(Request $request)
    {
        try {
            $user = $request->user();
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();
            if(in_array($extension, ['jpg', 'png', 'jpeg'])) {
                $path = 'user/' . $user->id . '/'. time() . '.' . $extension;
                Storage::disk('public')->put($path,  File::get($file));
                $url = '/storage/'.$path;

                UserImage::create([
                    'user_id' => $user->id,
                    'url' => $url
                ]);
            }
            return response()->json([
                'status' => true
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
