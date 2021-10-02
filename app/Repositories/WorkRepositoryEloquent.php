<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\WorkRepository;
use App\Entities\Work;

/**
 * Class WorkRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class WorkRepositoryEloquent extends BaseRepository implements WorkRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Work::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}