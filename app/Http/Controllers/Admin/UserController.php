<?php

namespace App\Http\Controllers\Admin;

use App\Entities\Notification;
use App\Http\Controllers\Controller;
use App\Repositories\ActivityBaseRepository;
use App\Repositories\CareerRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\UserNotificationRepository;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /** @var CareerRepository */
    protected $careerRepository;

    /** @var UserRepository */
    protected $userRepository;

    /** @var ActivityBaseRepository */
    protected $activityBaseRepository;

    /**
     * UserController constructor.
     * @param CareerRepository $careerRepository
     * @param UserRepository $userRepository
     * @param ActivityBaseRepository $activityBaseRepository
     */
    public function __construct(CareerRepository $careerRepository, UserRepository $userRepository, ActivityBaseRepository $activityBaseRepository)
    {
        $this->careerRepository = $careerRepository;
        $this->userRepository = $userRepository;
        $this->activityBaseRepository = $activityBaseRepository;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function listUser(Request $request)
    {
        $search = $request->input('search-key', '');
        $status = $request->input('status', 'all');
        $arrange = $request->input('arrange', 'desc');

        $users = $this->userRepository->paginateQuery($request, $status, $search);
        $careersList = $this->careerRepository->query()->select(['id', 'title'])->get();

        return view('admin.pages.user.index', [
            'users' => $users,
            'careersList' => $careersList,
//            'notifyStatus' => $status,
            'searchKey'    => $search,
            'arrange' => $arrange
        ]);
    }
}
