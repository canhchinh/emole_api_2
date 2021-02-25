<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\UserCategoryRepository;
use App\Entities\UserCategory;
use App\Validators\UserCategoryValidator;

/**
 * Class UserCategoryRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UserCategoryRepositoryEloquent extends BaseRepository implements UserCategoryRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserCategory::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
