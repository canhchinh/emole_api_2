<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Entities\PortfolioMember;

/**
 * Class PortfolioRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PortfolioMemberRepositoryEloquent extends BaseRepository implements PortfolioMemberRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return PortfolioMember::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}

