<?php

namespace App\Http\Controllers\Admin;

use App\Entities\Notification;
use App\Http\Controllers\Controller;
use App\Repositories\CareerRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\UserNotificationRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
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
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function listNotify(Request $request)
    {
        $search = $request->input('search-key', '');
        $status = $request->input('status', 'all');
        $arrange = $request->input('arrange', 'desc');

        $notifications = $this->notificationRepository->paginateQuery($request, $status, $search);
        $careersList = $this->careerRepository->query()->select(['id', 'title'])->get();

        return view('admin.pages.notify.index', [
            'notifications' => $notifications,
            'careersList' => $careersList,
            'notifyStatus' => $status,
            'searchKey'    => $search,
            'arrange' => $arrange
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function createNotify(Request $request)
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
                $notify->status = $request->get('storingSubmit') ? Notification::STATUS_PUBLIC : Notification::STATUS_DRAFT;

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
     * @return mixed
     */
    public function viewNotify(Request $request, $id)
    {
        $notify = $this->notificationRepository->query()->where(['id' => $id])->first();
        $career = $this->careerRepository->select()->get();
        return view('admin.pages.notify.view', [
            'delivery_target' => $career,
            'notify' => $notify,
            'backUrl' => $request->input('back', route('admin.notify.list'))
        ]);
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
                $notify = $query->where(['id' => $id])->first();
                if ($notify->status == Notification::STATUS_PUBLIC) {
                    return response()->json(['success' => false, 'message' => 'このメッセージは送信されたため、削除できません。']);
                } else {
                    $notify->delete();
                    return response()->json(['success' => true, 'redirectUrl' => route('admin.notify.list')]);
                }
            } catch (\Exception $e) {
            }
        }

        return response()->json(['success' => false, 'message' => 'No request found!']);
    }
}
