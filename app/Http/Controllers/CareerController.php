<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Career;
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
}
