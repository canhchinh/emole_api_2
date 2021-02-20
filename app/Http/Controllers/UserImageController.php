<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\UserImage;
use Illuminate\Support\Facades\File;
class UserImageController extends Controller
{
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
