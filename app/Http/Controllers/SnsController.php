<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sns;

class SnsController extends Controller
{
    public function listSns(Request $request, $careerId)
    {
        $category = Category::where('career_id', $careerId)
            ->select(['id', 'career_id', 'title'])->get();
        return response()->json([
            'status' => true,
            'date' => $category
        ]);
    }
}
