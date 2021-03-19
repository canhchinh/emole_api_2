<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Entities\LineNotifyAccessToken;
use App\Validators\PortfolioValidator;

/**
 * Class  LineNotifyAccessTokenRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class  LineNotifyAccessTokenRepositoryEloquent extends BaseRepository implements LineNotifyAccessTokenRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return LineNotifyAccessToken::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
