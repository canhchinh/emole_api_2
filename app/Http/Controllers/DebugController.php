<?php

namespace App\Http\Controllers;

use App\Repositories\CareerRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\GenreRepository;
use App\Repositories\JobRepository;
use App\Repositories\SnsRepository;
use App\Repositories\UserImageRepository;

class DebugController extends Controller
{
    private $careerRepo;
    private $categoryRepo;
    private $jobRepo;
    private $genreRepo;
    private $userImageRepo;
    private $snsRepo;

    public function __construct(
        CareerRepository $careerRepo,
        CategoryRepository $categoryRepo,
        JobRepository $jobRepo,
        GenreRepository $genreRepo,
        UserImageRepository $userImageRepo,
        SnsRepository $snsRepo
    ) {
        $this->careerRepo = $careerRepo;
        $this->categoryRepo = $categoryRepo;
        $this->jobRepo = $jobRepo;
        $this->genreRepo = $genreRepo;
        $this->jobRepo = $jobRepo;
        $this->userImageRepo = $userImageRepo;
        $this->snsRepo = $snsRepo;
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
                'title' => 'video_actor',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 2,
                'career_id' => 1,
                'title' => 'stage_actor',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 3,
                'career_id' => 1,
                'title' => 'audio_actor',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->categoryRepo->insert($category);

        $job = [
            [
                'id' => 1,
                'career_id' => 1,
                'title' => 'CM',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 2,
                'career_id' => 1,
                'title' => 'MV',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 3,
                'career_id' => 1,
                'title' => 'Image advertising',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->jobRepo->insert($job);

        $genre = [
            [
                'id' => 1,
                'career_id' => 1,
                'title' => 'comedy',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 2,
                'career_id' => 1,
                'title' => 'suspense',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 3,
                'career_id' => 1,
                'title' => 'horror',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->genreRepo->insert($genre);

        $sns = [
            [
                'id' => 1,
                'title' => 'twitter',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 2,
                'title' => 'instagram',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 3,
                'title' => 'youtube',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 4,
                'title' => 'tiktok',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 5,
                'title' => 'facebook',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 6,
                'title' => 'line',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 7,
                'title' => 'note',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 8,
                'title' => 'pinterest',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],

        ];

        $this->snsRepo->insert($sns);

        $userImage = [
            'id' => 1,
            'user_id' => 1,
            'url' => '/storage/1/1.png',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        $this->userImageRepo->insert($userImage);
    }
}