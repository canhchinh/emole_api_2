<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\FollowRepository;
use App\Entities\Follow;
use App\Validators\FollowValidator;

/**
 * Class FollowRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class FollowRepositoryEloquent extends BaseRepository implements FollowRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Follow::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function getListFollowByUser($userId)
    {
        $query = $this->join('users', 'users.id', '=', 'follows.target_id')
            ->where('follows.user_id', $userId)
            ->selectRaw("
                users.gender,
                users.given_name,
                users.title,
                users.profession,
                users.self_introduction,
                users.avatar,
                users.birthday
            ");

        return $query->paginate(config('common.paging'));
    }

    public function getListFollowerByUser($userId)
    {
        $query = $this->join('users', 'users.id', '=', 'follows.target_id')
            ->where('follows.target', $userId)
            ->selectRaw("
                users.gender,
                users.given_name,
                users.title,
                users.profession,
                users.self_introduction,
                users.avatar,
                users.birthday
            ");

        return $query->paginate(config('common.paging'));
    }
}
