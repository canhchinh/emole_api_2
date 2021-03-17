<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\PortfolioRepository;
use App\Entities\PortfolioJob;
use App\Validators\PortfolioValidator;

/**
 * Class PortfolioRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PortfolioJobRepositoryEloquent extends BaseRepository implements PortfolioJobRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return PortfolioJob::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
