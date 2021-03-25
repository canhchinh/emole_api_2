<?php

namespace App\Repositories;

use App\Entities\UserCareer;
use App\Repositories\UserCareerRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class UserCareerRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UserCareerRepositoryEloquent extends BaseRepository implements UserCareerRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserCareer::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function getListForUser($userId)
    {
        return $this->where('user_careers.user_id', $userId)
            ->join('careers', 'careers.id', '=', 'user_careers.career_id')
            ->selectRaw("
                user_careers.tags,
                user_careers.setting,
                careers.id,
                careers.title
            ")
            ->get();
    }
}
