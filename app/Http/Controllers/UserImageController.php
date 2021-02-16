<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\UserImage;
class UserImageController extends Controller
{
    public function newImageUpload(Request $request)
    {
        try {
            $req = $request->all();
            $user = $request->user();
            $base64_str = substr($req['image'], strpos($req['image'], ",")+1);
            //decode base64 string
            $image = base64_decode($base64_str);
            $suffix = $user->id.'/'.time().'.png';
            $path = 'public/'.$suffix;
            Storage::disk('local')->put($path, $image);
            $link = '/storage/'.$suffix;
            UserImage::create([
                'user_id' => $user->id,
                'url' => $link
            ]);
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
