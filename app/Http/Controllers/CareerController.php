<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Career;
use App\Models\UserCareer;
use Illuminate\Support\Facades\Auth;
class CareerController extends Controller
{
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
