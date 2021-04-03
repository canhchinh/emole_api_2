<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\UserRepository;
use App\Entities\User;
use App\Validators\UserValidator;
use Illuminate\Pagination\Paginator;
use App\Entities\Follow;

/**
 * Class UserRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UserRepositoryEloquent extends BaseRepository implements UserRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return User::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));

        $this->deleted(function ($user) {
            /// do something with $user->id
            $this->unlinkAvatar($user);
        });
    }

    public function unlinkAvatar(self $user) {

    }

    public function listUsers($userId, $filters = [],$page = 1, $limit=10)
    {
        $users = User::select('*')
            ->where('id', '<>', $userId)
            ->with(['activity_base', 'portfolio']);
        if(!empty($filters['keyword'])) {
            $users->where('user_name', 'like', '%'.$filters['keyword'].'%')
            ->orWhere('given_name', 'like', '%'.$filters['keyword'].'%');
        }
        if ($page >= 1) {
            Paginator::currentPageResolver(function () use ($page) {
                return $page;
            });
        }

        $targetIds  = Follow::where('user_id', $userId)->pluck('target_id');
        if(!empty($targetIds)) {
            $targetIds = $targetIds->toArray();
        } else {
            $targetIds = [];
        }
        $users = $users->paginate($limit);
        $users->getCollection()->transform(function ($user) use ($targetIds){
            if(in_array($user->id, $targetIds)) {
                $user->is_follow = true;
            } else {
                $user->is_follow = false;
            }
            return $user;
        });
        return $users->toArray();
    }

    public function is_base64($file){
        try {
            $extension = explode('/', mime_content_type($file))[1];
            return true;
        } catch (\Exception $e) {
            return false;
        }

    }

    public function paginateQuery(Request $request, $status, $search)
    {
        /** @var Builder $notifications */
        $query = $this->getModel()->query();
        if ($status != 'all') {
            $query->where(['status' => $status]);
        }

        if ($search) {
            $query->where('user_name', 'LIKE', '%' . $search . '%');
        }
        $query->leftJoin('user_careers as uc', 'users.id', '=', 'uc.user_id');
        $query->leftJoin('activity_base as ua', 'users.activity_base_id', '=', 'ua.id');
        $query->select([
            'users.*',
            DB::raw('group_concat(uc.career_id separator ", ") AS career_ids'),
            'ua.title as activity_base_title'
        ]);
        $query->groupBy('users.id');
        $query->orderBy($request->input('sort', 'created_at'), $request->input('arrange', 'desc'));

        return $query->get();
    }

}
