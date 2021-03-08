<?php

namespace App\Http\Controllers;

use App\Repositories\CareerRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\GenreRepository;
use App\Repositories\JobRepository;
use App\Repositories\SnsRepository;
use App\Repositories\UserImageRepository;
use App\Repositories\ActivityContentRepository;
use App\Repositories\ActivityBaseRepository;

class DebugController extends Controller
{
    private $careerRepo;
    private $categoryRepo;
    private $jobRepo;
    private $genreRepo;
    private $userImageRepo;
    private $snsRepo;
    private $activityContentRepo, $activityBaseRepo;

    public function __construct(
        CareerRepository $careerRepo,
        CategoryRepository $categoryRepo,
        JobRepository $jobRepo,
        GenreRepository $genreRepo,
        UserImageRepository $userImageRepo,
        SnsRepository $snsRepo,
        ActivityContentRepository $activityContentRepo,
        ActivityBaseRepository $activityBaseRepo
    ) {
        $this->careerRepo = $careerRepo;
        $this->categoryRepo = $categoryRepo;
        $this->jobRepo = $jobRepo;
        $this->genreRepo = $genreRepo;
        $this->jobRepo = $jobRepo;
        $this->userImageRepo = $userImageRepo;
        $this->snsRepo = $snsRepo;
        $this->activityContentRepo = $activityContentRepo;
        $this->activityBaseRepo = $activityBaseRepo;
    }

    public function seed()
    {
        $career = [
            [
                'id' => 1,
                'title' => 'Performer',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 2,
                'title' => 'model',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 3,
                'title' => 'dancer',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];
        $this->careerRepo->insert($career);

        $category = [
            [
                'id' => 1,
                'career_id' => 1,
                'key' => config('common.activity_content.category.key'),
                'title' => 'video_actor',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 2,
                'career_id' => 1,
                'key' => config('common.activity_content.category.key'),
                'title' => 'stage_actor',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 3,
                'career_id' => 1,
                'key' => config('common.activity_content.category.key'),
                'title' => 'audio_actor',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 4,
                'career_id' => 1,
                'key' => config('common.activity_content.job.key'),
                'title' => 'CM',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 5,
                'career_id' => 1,
                'key' => config('common.activity_content.job.key'),
                'title' => 'MV',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 6,
                'career_id' => 1,
                'key' => config('common.activity_content.job.key'),
                'title' => 'Image advertising',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 7,
                'career_id' => 1,
                'key' => config('common.activity_content.genre.key'),
                'title' => 'comedy',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 8,
                'career_id' => 1,
                'key' => config('common.activity_content.genre.key'),
                'title' => 'suspense',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 9,
                'career_id' => 1,
                'key' => config('common.activity_content.genre.key'),
                'title' => 'horror',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 10,
                'career_id' => null,
                'key' => config('common.activity_content.sns.key'),
                'title' => 'twitter',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 11,
                'career_id' => null,
                'key' => config('common.activity_content.sns.key'),
                'title' => 'instagram',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 12,
                'career_id' => null,
                'key' => config('common.activity_content.sns.key'),
                'title' => 'youtube',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 13,
                'career_id' => null,
                'key' => config('common.activity_content.sns.key'),
                'title' => 'tiktok',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 14,
                'career_id' => null,
                'key' => config('common.activity_content.sns.key'),
                'title' => 'facebook',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 15,
                'career_id' => null,
                'key' => config('common.activity_content.sns.key'),
                'title' => 'line',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 16,
                'career_id' => null,
                'key' => config('common.activity_content.sns.key'),
                'title' => 'note',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 17,
                'career_id' => null,
                'key' => config('common.activity_content.sns.key'),
                'title' => 'pinterest',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 18,
                'career_id' => 2,
                'key' => config('common.activity_content.category.key'),
                'title' => 'video_actor',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 19,
                'career_id' => 2,
                'key' => config('common.activity_content.category.key'),
                'title' => 'stage_actor',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 20,
                'career_id' => 2,
                'key' => config('common.activity_content.category.key'),
                'title' => 'audio_actor',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 21,
                'career_id' => 2,
                'key' => config('common.activity_content.job.key'),
                'title' => 'CM',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 22,
                'career_id' => 2,
                'key' => config('common.activity_content.job.key'),
                'title' => 'MV',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 23,
                'career_id' => 2,
                'key' => config('common.activity_content.job.key'),
                'title' => 'Image advertising',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 24,
                'career_id' => 2,
                'key' => config('common.activity_content.genre.key'),
                'title' => 'comedy',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 25,
                'career_id' => 2,
                'key' => config('common.activity_content.genre.key'),
                'title' => 'suspense',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 26,
                'career_id' => 2,
                'key' => config('common.activity_content.genre.key'),
                'title' => 'horror',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 27,
                'career_id' => 3,
                'key' => config('common.activity_content.category.key'),
                'title' => 'video_actor',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 28,
                'career_id' => 3,
                'key' => config('common.activity_content.category.key'),
                'title' => 'stage_actor',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 29,
                'career_id' => 3,
                'key' => config('common.activity_content.category.key'),
                'title' => 'audio_actor',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 30,
                'career_id' => 3,
                'key' => config('common.activity_content.job.key'),
                'title' => 'CM',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 31,
                'career_id' => 3,
                'key' => config('common.activity_content.job.key'),
                'title' => 'MV',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 32,
                'career_id' => 3,
                'key' => config('common.activity_content.job.key'),
                'title' => 'Image advertising',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 33,
                'career_id' => 3,
                'key' => config('common.activity_content.genre.key'),
                'title' => 'comedy',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 34,
                'career_id' => 3,
                'key' => config('common.activity_content.genre.key'),
                'title' => 'suspense',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 35,
                'career_id' => 3,
                'key' => config('common.activity_content.genre.key'),
                'title' => 'horror',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->activityContentRepo->insert($category);

//        $userImage = [
//            'id' => 1,
//            'user_id' => 1,
//            'url' => '/storage/1/1.png',
//            'created_at' => date('Y-m-d H:i:s'),
//            'updated_at' => date('Y-m-d H:i:s'),
//        ];
//        $this->userImageRepo->insert($userImage);
//
//        $activityBase = [
//            [
//                'id' => 1,
//                'title' => 'tokyo',
//                'created_at' => date('Y-m-d H:i:s'),
//                'updated_at' => date('Y-m-d H:i:s'),
//            ]
//        ];
//        $this->activityBaseRepo->insert($activityBase);
    }
}
