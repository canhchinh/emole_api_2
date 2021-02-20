<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;

class JobController extends Controller
{
    public function listJob(Request $request, $careerId)
    {
        $job = Job::where('career_id', $careerId)
            ->select(['id', 'career_id', 'title'])->get();
        return response()->json([
            'status' => true,
            'date' => $job
        ]);
    }
}
