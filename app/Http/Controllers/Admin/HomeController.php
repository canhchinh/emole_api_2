<?php

namespace App\Http\Controllers\Admin;

use App\Entities\Career;
use App\Entities\Notification;
use App\Entities\User;
use App\Http\Controllers\Controller;
use App\Imports\ActivityBaseImport;
use App\Imports\CareerImport;
use App\Imports\DetailCareer\OneImport;
use App\Repositories\CareerRepository;
use App\Repositories\CategoryRepositoryEloquent;
use App\Repositories\NotificationRepository;
use App\Repositories\UserNotificationRepository;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class HomeController extends Controller
{
    /** @var CareerRepository */
    protected $careerRepository;
    /** @var NotificationRepository */
    protected $notificationRepository;
    /** @var UserNotificationRepository */
    protected $userNotificationRepository;

    /**
     * HomeController constructor.
     *
     * @param CareerRepository $careerRepository
     * @param NotificationRepository $notificationRepository
     * @param UserNotificationRepository $userNotificationRepository
     */
    public function __construct(CareerRepository $careerRepository, NotificationRepository $notificationRepository, UserNotificationRepository $userNotificationRepository)
    {
        $this->careerRepository = $careerRepository;
        $this->notificationRepository = $notificationRepository;
        $this->userNotificationRepository = $userNotificationRepository;
    }

    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        return redirect()->route('admin.users.list');
    }
    /**
     * listUser
     *
     * @return void
     */
    public function listUser()
    {
        return view('admin.pages.user.index');
    }

    /**
     * listPortfolio
     *
     * @return void
     */
    public function listPortfolio()
    {
        return view('admin.pages.portfolio.index');
    }

    /**
     * @param Request $request
     * @param string $status
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function listNotify(Request $request, $status = 'all')
    {
        Log::info($status);
        if ($status == 'all') {
            $statusList = [Notification::STATUS_DRAFT, Notification::STATUS_PUBLIC];
        } else {
            $statusList = [$status];
        }

        /** @var Builder $notifications */
        $notifications = $this->notificationRepository->query()
            ->whereIn('status', $statusList)
            ->orderBy($request->input('sort', 'id'), $request->input('arrange', 'desc'))->paginate(3);

        $careersList = $this->careerRepository->query()->select(['id', 'title'])->get();

        return view('admin.pages.notify.index', [
            'notifications' => $notifications,
            'careersList' => $careersList,
            'notifyStatus' => $status
        ]);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Validation\ValidationException
     */
    public function createNotify(Request $request, $id = 0)
    {
        $career = $this->careerRepository->select()->get();
        if ($request->isMethod('post')) {
            $messages = [
                'career_ids.required' => 'キャリアIDフィールドは必須です。',
                'delivery_name.required' => '配達名フィールドが必要です。',
                'delivery_contents.required' => 'コンテンツフィールドは必須です。',
                'subject.required' => '件名フィールドは必須です。',
                'url.required' => '転送先URLが必要です。',
                'url.url' => '転送先のURLが正しい形式ではありません。',
            ];

            $validator = Validator::make($request->all(), [
                'delivery_name' => 'required|min:2',
                'career_ids' => 'required',
                'delivery_contents' => 'required|max:160|min:2',
                'subject' => 'required|max:100|min:2',
                'url' => 'required|url',
            ], $messages);

            if ($validator->validated()) {
                $notify = new Notification();
                $notify->populate($request->all());
                $notify->setCareerIds($request->get('career_ids'));
                $notify->status = $request->get('publicSubmit') ? Notification::STATUS_PUBLIC : Notification::STATUS_DRAFT;

                DB::beginTransaction();
                try {
                    if ($notify->save() && $notify->status == Notification::STATUS_PUBLIC) {
                        $this->userNotificationRepository->addNotification($notify);
                    }
                    DB::commit();
                    return redirect()->route('admin.notify.list');
                } catch (\Exception $e) {
                    DB::rollBack();
                    abort('Some thing error, please try again or contact admin. Thank very much!');
                }
            }
        }

        return view('admin.pages.notify.create', ['delivery_target' => $career])->withInput($request->all());
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateNotifyStatus(Request $request, $id)
    {
        if ($request->isMethod('put')) {
            DB::beginTransaction();
            try {
                $newStatus = trim($request->get('status'));
                /** @var Builder $query */
                $query = $this->notificationRepository->query();
                $notify = $query->where(['id' => $id])->first();
                if ($newStatus != $notify->status) {
                    $notify->status = $newStatus;
                    if ($notify->save() && $notify->status == Notification::STATUS_PUBLIC) {
                        $this->userNotificationRepository->addNotification($notify);
                    }
                }

                DB::commit();
                return response()->json(['success' => true]);
            } catch (\Exception $e) {
                DB::rollBack();
                abort('Some thing error, please try again or contact admin. Thank very much!');
            }
        }

        return response()->json(['success' => false, 'message' => 'No request found!']);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteNotify(Request $request, $id)
    {
        if ($request->isMethod('delete')) {
            try {
                /** @var Builder $query */
                $query = $this->notificationRepository->query();
                $query->where(['id' => $id])->delete();

                return response()->json(['success' => true, 'redirectUrl' => route('admin.notify.list')]);
            } catch (\Exception $e) {
            }
        }

        return response()->json(['success' => false, 'message' => 'No request found!']);
    }

    /**
     * detailUser
     *
     * @param  mixed $id
     * @return void
     */
    public function detailUser($id)
    {
        dd($id);
    }


    /**
     * import
     *
     * @param  mixed $request
     * @return void
     */
    public function import(Request $request)
    {
        if ($request->getMethod() === "GET") {
            return view('import');
        }
        // Excel::import(new CareerImport,request()->file('file'));
        // Excel::import(new ActivityBaseImport,request()->file('file'));
        Excel::import(new OneImport,request()->file('file'));
        return back();
    }
}
