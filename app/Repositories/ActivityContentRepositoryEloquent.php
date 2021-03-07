<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Entities\ActivityContent;

/**
 * Class CareerRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ActivityContentRepositoryEloquent extends BaseRepository implements ActivityContentRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ActivityContent::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
