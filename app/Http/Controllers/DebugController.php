<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Career;
use App\Models\CareerChildren;
use App\Models\Sns;
use App\Models\UserImage;
class DebugController extends Controller
{
    public function seed(Request $request)
    {
        $career = [
            [
                'id' => 1,
                'title' => 'Performer',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 2,
                'title' => 'model',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 3,
                'title' => 'dancer',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        Career::insert($career);

        $careerChildren = [
            [
                'id' => 1,
                'title' => 'Video actor',
                'career_id' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        ];
        CareerChildren::insert($careerChildren);

        $sns = [
            'user_id' => 1,
            'twitter' => 'twitter',
            'instagram' => 'instagram',
            'youtube' => 'youtube',
            'tiktok' => 'tiktok',
            'facebook' => 'facebook',
            'line' => 'line',
            'note' => 'note',
            'pinterest' => 'pinterest',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        Sns::insert($sns);

        $userImage = [
            'id' => 1,
            'user_id' => 1,
            'url' => '/storage/1/1.png',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        UserImage::insert($userImage);
    }
}
