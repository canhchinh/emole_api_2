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
//        UserCategory::query()->where(['user_id' => $user->id])->delete(); // TODO: not found
        UserCareer::query()->where(['user_id' => $user->id])->delete();
    }

    public function listUsers($userId, $filters = [],$page = 1, $limit=10, $query)
    {
        $users = User::select('*')
            ->where('id', '<>', $userId)
            ->where('active', '<>', 0)
            ->with(['activity_base', 'portfolio'])->orderBy('id', 'DESC');
        if($query == "true") {
            $users->has('portfolio');
        }
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
     * activeAccount
     *
     * @param  mixed $token
     * @return void
     */
    
    public function activeAccount($token){
        DB::beginTransaction();
        $token = str_replace(" ", "", $token);
        $data = DB::table('password_resets')->where('token', $token)->latest()->first();
        if (!$data) {
            return null;
        }
        $email = $data->email;
        $user = $this->model->where('email',$email)->first();
        if ($user) {
            $user->active = 1;
            $user->save();
            /* Delete Token Record */
            DB::table('password_resets')->where('email', $email)->delete();
        }
        DB::commit();
        return $user;
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
    
    /**
     * createImageInfo
     *
     * @return void
     */
    public function createImageInfo($user)
    {
        try {
            if (!empty($user->avatar)) {
                $img = \Image::make(public_path('images/default/background.png'));
                $image = \Image::make(public_path($user->avatar));
                $image->encode('png');
                $image->fit(300, 300);
                $width = $image->getWidth();
                $height = $image->getHeight();
                $mask = \Image::canvas($width, $height);
                // draw a white circle
                $mask->circle($width, $width/2, $height/2, function ($draw) {
                    $draw->background('#fff');
                });
                $image->mask($mask, false);
                $img->insert($image, "top-left", 15, 90);
                $img->text($user->given_name, 370, 190, function($font) {
                    $font->file(public_path('images/default/NotoSansJP-Bold.otf'));
                    $font->size(38);
                    $font->color('#050518');
                }); 
                $img->text($user->title, 370, 250, function($font) {
                    $font->file(public_path('images/default/NotoSansJP-Medium.otf'));
                    $font->size(26);
                    $font->color('#050519');
                });
                if (count($user->careers) > 0) {
                    $career = $user->careers;
                    $img->text($career[0]->title, 370, 300, function($font) {
                        $font->file(public_path('images/default/NotoSansJP-Medium.otf'));
                        $font->size(18);
                        $font->color('#050518');
                    });
                    $img->text($career[1]->title, 500, 300, function($font) {
                        $font->file(public_path('images/default/NotoSansJP-Medium.otf'));
                        $font->size(18);
                        $font->color('#050518');
                    });
                }
                $pathSave = "storage/opg/$user->id.png";
                $path = "public/opg/$user->id.png";
                $img->save(storage_path($path));
                return $pathSave;
            }
            return false;
       } catch (\Exception $e) {
          echo $e->getMessage();
       }
    }
}