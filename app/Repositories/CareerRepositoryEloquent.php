<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\CareerRepository;
use App\Entities\Career;

/**
 * Class CareerRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class CareerRepositoryEloquent extends BaseRepository implements CareerRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Career::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}