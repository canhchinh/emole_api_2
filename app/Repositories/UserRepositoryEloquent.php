<?php

namespace App\Repositories;

use App\Entities\UserCareer;
use App\Entities\UserCategory;
use App\Entities\UserImage;
use App\Entities\UserNotification;
use App\Helpers\Helper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use League\CommonMark\Inline\Renderer\ImageRenderer;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Entities\User;
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
            $this->unlinkAvatar($user);
            $this->deleteUserImages($user);
            $this->handleRelationship($user);
        });
    }

    /**
     * @param User $user
     */
    public function unlinkAvatar(User $user)
    {
        if (File::exists(public_path($user->avatar))) {
            File::delete(public_path($user->avatar));
        }
    }

    /**
     * @param User $user
     */
    public function deleteUserImages(User $user)
    {
        $userImgs = UserImage::query()->where(['user_id' => $user->id])->get();
        foreach ($userImgs as $img) {
            File::delete(public_path($img->url));
            $img->delete();
        }
    }

    /**
     * @param User $user
     */
    public function handleRelationship(User $user)
    {
        UserNotification::query()->where(['user_id' => $user->id])->delete();
        UserCategory::query()->where(['user_id' => $user->id])->delete();
        UserCareer::query()->where(['user_id' => $user->id])->delete();
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
            $query->where(['status' => $status]);
        }

        $query->leftJoin('user_careers as uc', 'users.id', '=', 'uc.user_id');
        $query->leftJoin('activity_base as ua', 'users.activity_base_id', '=', 'ua.id');
        $query->leftJoin('portfolios', 'users.id', '=', 'portfolios.user_id');

        if ($search) {
            $query->where(function($query) use ($search) {
                $query
                    ->orWhere('user_name', 'LIKE', "%{$search}%")
                    ->orWhere('given_name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        if ($request->input('career_id')) {
            $query->where(['uc.career_id' => $request->input('career_id')]);
        }

        if ($request->input('birthday')) {
            $query->where(['users.birthday' => $request->input('birthday')]);
        }

        if ($request->input('area')) {
            $query->where(['users.activity_base_id' => $request->input('area')]);
        }

        $query->select([
            'users.*',
            DB::raw('group_concat(DISTINCT uc.career_id separator ", ") AS career_ids'),
            DB::raw('group_concat(DISTINCT portfolios.image separator ";=;") AS portfolios_image'),
            DB::raw('group_concat(DISTINCT portfolios.id separator ",") AS portfolios_ids'),
            'ua.title as activity_base_title'
        ]);
        $query->groupBy('users.id');
        $query->orderBy($request->input('sort', 'created_at'), $request->input('arrange', 'desc'));

        return $query->get();
    }

}
