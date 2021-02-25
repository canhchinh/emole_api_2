<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\UserSnsRepository;
use App\Entities\UserSns;
use App\Validators\UserSnsValidator;

/**
 * Class UserSnsRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UserSnsRepositoryEloquent extends BaseRepository implements UserSnsRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserSns::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
