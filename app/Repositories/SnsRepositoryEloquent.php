<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\SnsRepository;
use App\Entities\Sns;
use App\Validators\SnsValidator;

/**
 * Class SnsRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class SnsRepositoryEloquent extends BaseRepository implements SnsRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Sns::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
