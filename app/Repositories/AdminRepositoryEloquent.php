<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\AdminRepository;
use App\Entities\Admin;
use Illuminate\Support\Facades\Auth;

/**
 * Class AdminRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class AdminRepositoryEloquent extends BaseRepository implements AdminRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Admin::class;
    }

    /**
     * checkLogin
     *
     * @param  mixed $request
     * @return bool
     */
    public function checkLogin($request)
    {
        $credentials = [
            'password' => $request['password'],
        ];
        $field = $this->returnEmailOrUsername($request['username']);
        $credentials[$field] = $request['username'];
        if (auth()->attempt($credentials)) {
            return true;
        }
        return false;
    }

    /**
     * returnEmailOrUsername
     *
     * @param  mixed $request
     * @return void
     */
    private function returnEmailOrUsername($request)
    {
        return filter_var($request, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
