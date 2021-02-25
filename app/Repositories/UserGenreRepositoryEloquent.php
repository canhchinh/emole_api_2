<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\UserGenreRepository;
use App\Entities\UserGenre;
use App\Validators\UserGenreValidator;

/**
 * Class UserGenreRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UserGenreRepositoryEloquent extends BaseRepository implements UserGenreRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserGenre::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
