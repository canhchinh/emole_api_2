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

}