<?php

namespace App\Repositories;

use App\Entities\UserWork;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\UserWorkRepository;

/**
 * Class WorkRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UserWorkRepositoryEloquent extends BaseRepository implements UserWorkRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserWork::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}