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
            'searchKey'    => $search,
            'arrange' => $arrange
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function detailUser(Request $request, $id)
    {
        /** @var Builder $query */
        $user = $this->userRepository->query()->where(['id' => $id])->first();
        if (!$user->id) {
            abort('Request not found!');
        }
        return view('admin.pages.user.detail', [
            'user' => $user
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteUser(Request $request, $id)
    {
        if ($request->isMethod('delete')) {
            try {
                /** @var Builder $query */
                $query = $this->userRepository->query();
                $user = $query->where(['id' => $id])->first();
                if ($user->id) {
                    DB::beginTransaction();
                    $user->delete();
                    DB::commit();

                    return response()->json(['success' => true, 'redirectUrl' => route('admin.users.list')]);
                } else {
                    return response()->json(['success' => false, 'message' => 'このメッセージは送信されたため、削除できません。']);
                }
            } catch (\Exception $e) {
                DB::rollBack();
            }
        }

        return response()->json(['success' => false, 'message' => 'No request found!']);
    }

    public function sendEmailToUser()
    {
        // TODO: send email
    }
}
