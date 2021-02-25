<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\UserImageRepository;
use App\Entities\UserImage;
use App\Validators\UserImageValidator;

/**
 * Class UserImageRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UserImageRepositoryEloquent extends BaseRepository implements UserImageRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserImage::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
