<?php

namespace App\Repositories;

use App\Entities\ActivityContent;
use App\Entities\PortfolioJob;
use App\Entities\PortfolioMember;
use App\Helpers\Helper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
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
            $this->handleRelationship($portfolio);
        });
    }

    /**
     * @param Portfolio $portfolio
     */
    public function unlinkAvatar(Portfolio $portfolio)
    {
        foreach ($portfolio->image as $img) {
            if (File::exists(public_path($img['path']))) {
                @File::delete(public_path($img['path']));
            }
        }
    }

    /**
     * @param Portfolio $portfolio
     */
    public function handleRelationship(Portfolio $portfolio)
    {
        PortfolioJob::query()->where(['portfolio_id' => $portfolio->id])->delete();
        PortfolioMember::query()->where(['portfolio_id' => $portfolio->id])->delete();
    }

    /**
     * @param Request $request
     * @param $status
     * @param $search
     * @param array $categories
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function paginateQuery(Request $request, $status, $search, $categories = [])
    {
        /** @var Builder $query */
        $query = $this->getModel()->query();
        $query->leftJoin('users as u', 'portfolios.user_id', '=', 'u.id');

        if ($request->input('career_id')) {
            $query->join('portfolio_job as pj', 'portfolios.id', '=', 'pj.portfolio_id');
            if ($request->input('category_id')) {
                $query->where(['pj.job_id' => $request->input('category_id')]);
            } else {
                $jobIds = [];
                foreach ($categories as $category) {
                    $jobIds[] = $category->id;
                }
                if ($jobIds) {
                    $query->whereIn('pj.job_id', $jobIds);
                }
            }
        }

        if ($search) {
            $query->where(function ($query) use ($search) {
                $query
                    ->orWhere('portfolios.title', 'LIKE', "%{$search}%")
                    ->orWhere('portfolios.tags', 'LIKE', "%{$search}%");
            });
        }

        $query->select([
            'portfolios.*',
            'u.user_name as u_user_name',
            'u.given_name as u_given_name',
            'u.avatar as u_avatar',
            'u.title as u_title'
        ]);
        $query->groupBy('portfolios.id');
        $query->orderBy($request->input('sort', 'created_at'), $request->input('arrange', 'desc'));

        return $query->get();
    }
}
