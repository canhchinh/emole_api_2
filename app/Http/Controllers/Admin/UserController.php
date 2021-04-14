<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\SocialServices;
use App\Http\Controllers\Controller;
use App\Jobs\SendMailToUser;
use App\Repositories\ActivityBaseRepository;
use App\Repositories\CareerRepository;
use App\Repositories\UserRepository;
use App\Services\GoogleService;
use App\Services\TwitterService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Sovit\TikTok\Api;

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
     *
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
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function listUser(Request $request)
    {
        $search = $request->input('search-key', '');
        $status = $request->input('status', 'all');
        $arrange = $request->input('arrange', 'desc');

        $users = $this->userRepository->paginateQuery($request, $status, $search);
        $careersList = $this->careerRepository->query()->select(['id', 'title'])->get();
        $area = $this->activityBaseRepository->query()->select(['id', 'title'])->get();

        $snsFollowersCount = (new SocialServices)->getStatistics(clone $users);

        return view('admin.pages.user.index', [
            'users' => $users,
            'snsFollowersCount' => $snsFollowersCount,
            'careersList' => $careersList,
            'area' => $area,
            'searchKey' => $search,
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
                return response()->json(['success' => false, 'message' => 'An error has occurred, message: ' . $e->getMessage()]);
            }
        }

        return response()->json(['success' => false, 'message' => 'No request found!']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendEmailToUser(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                $query = $this->userRepository->query();
                $user = $query->where(['id' => $request->get('user_id')])->first();
                $data = [
                    'subject' => $request->get('email_subject'),
                    'content' => $request->get('email_content')
                ];
                SendMailToUser::dispatch($user->email, $data)->onQueue('processing');

                return response()->json(['success' => true, 'message' => 'リクエストが送信されました']);
            } catch (\Exception $e) {
                Log::error('Can not send email to user, error message: ' . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'このリクエストを実行できません。後でしてください。']);
            }
        }

        abort(404);
    }
}
