<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ContestRepository;
use App\Entities\Contest;

/**
 * Class ContestRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ContestRepositoryEloquent extends BaseRepository implements ContestRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Contest::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}