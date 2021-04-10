<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ActivityBaseRepository;
use App\Entities\ActivityBase;

/**
 * Class CareerRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ActivityBaseRepositoryEloquent extends BaseRepository implements ActivityBaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ActivityBase::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        return $this->getModel()->newQuery();
    }
}
