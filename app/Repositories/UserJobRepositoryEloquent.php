<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\UserJobRepository;
use App\Entities\UserJob;
use App\Validators\UserJobValidator;

/**
 * Class UserJobRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UserJobRepositoryEloquent extends BaseRepository implements UserJobRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserJob::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
