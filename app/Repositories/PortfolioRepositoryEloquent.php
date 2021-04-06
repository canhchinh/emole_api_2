<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\PortfolioRepository;
use App\Entities\Portfolio;
use App\Validators\PortfolioValidator;

/**
 * Class PortfolioRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PortfolioRepositoryEloquent extends BaseRepository implements PortfolioRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Portfolio::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));

        $this->deleted(function ($portfolio) {
            $this->unlinkAvatar($portfolio);
        });
    }

    public function unlinkAvatar(self $portfolio) {

    }

    /**
     * @param Request $request
     * @param $status
     * @param $search
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function paginateQuery(Request $request, $status, $search)
    {
        /** @var Builder $query */
        $query = $this->getModel()->query();
        if ($status != 'all') {
//            $query->where(['portfolios.status' => $status]);
        }

        $query->leftJoin('users as u', 'portfolios.user_id', '=', 'u.id');
//        $query->leftJoin('activity_base as ua', 'users.activity_base_id', '=', 'ua.id');

        if ($search) {
            $query->where(function($query) use ($search) {
                $query
                    ->orWhere('portfolios.title', 'LIKE', "%{$search}%")
                    ->orWhere('portfolios.tags', 'LIKE', "%{$search}%");
            });
        }

        if ($request->input('career_id')) {
//            $query->where(['uc.career_id' => $request->input('career_id')]);
        }

        if ($request->input('birthday')) {
//            $query->where(['users.birthday' => $request->input('birthday')]);
        }

        if ($request->input('area')) {
//            $query->where(['uc.career_id' => $request->input('area')]);
        }

        $query->select([
            'portfolios.*',
            'u.given_name as u_given_name',
            'u.avatar as u_avatar',
//            DB::raw('group_concat(uc.career_id separator ", ") AS career_ids'),
//            'ua.title as activity_base_title'
        ]);
//        $query->groupBy('users.id');
        $query->orderBy($request->input('sort', 'created_at'), $request->input('arrange', 'desc'));

        return $query->get();
    }
}
