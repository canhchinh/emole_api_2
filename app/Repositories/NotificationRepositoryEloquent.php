<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Entities\Notification;

/**
 * Class NotificationRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class NotificationRepositoryEloquent extends BaseRepository implements NotificationRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Notification::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        return $this->getModel()->newQuery();
    }

    /**
     * @param Request $request
     * @param $status
     * @param $search
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginateQuery(Request $request, $status, $search)
    {
        /** @var Builder $notifications */
        $query = $this->getModel()->query();
        if ($status != 'all') {
            $query->where(['status' => $status]);
        }

        if ($search) {
            $query->where('delivery_name', 'LIKE', '%' . $search . '%');
        }

        $query->where('delivery_name', '!=' , 'EMOLE');
        $query->where('delivery_contents', 'NOT LIKE' , '%にフォローされました%');

        $query->orderBy($request->input('sort', 'created_at'), $request->input('arrange', 'desc'));

        return $query->paginate(3);
    }

    public function simplePaginateQuery()
    {

    }
}
