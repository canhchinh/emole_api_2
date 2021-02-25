<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\EducationRepository;
use App\Entities\Education;
use App\Validators\EducationValidator;

/**
 * Class EducationRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class EducationRepositoryEloquent extends BaseRepository implements EducationRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Education::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
