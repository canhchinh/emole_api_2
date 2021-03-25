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
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class HomeController extends Controller
{
    /** @var CareerRepository */
    protected $careerRepository;
    /** @var NotificationRepository */
    protected $notificationRepository;

    /**
     * HomeController constructor.
     *
     * @param CareerRepository $careerRepository
     * @param NotificationRepository $notificationRepository
     */
    public function __construct(CareerRepository $careerRepository, NotificationRepository $notificationRepository)
    {
        $this->careerRepository = $careerRepository;
        $this->notificationRepository = $notificationRepository;
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
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function listNotify()
    {
        /** @var Builder $notifications */
        $notifications = $this->notificationRepository->query()->paginate(3);

        return view('admin.pages.notify.index', ['notifications' => $notifications]);
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
                'careers_id.min' => 'Please select a careers',
            ];
            $validator = Validator::make($request->all(), [
                'delivery_name' => 'required|min:2',
                'career_id' => 'required|numeric|min:1',
                'delivery_contents' => 'required|max:160|min:2',
                'subject' => 'required|max:100|min:2',
                'url' => 'nullable|url',
            ], $messages);

            if ($validator->validated()) {
                $notify = new Notification();
                $notify->populate($request->all());
                $notify->status = $request->get('storingSubmit') ? Notification::STATUS_PUBLIC : Notification::STATUS_DRAFT;
                if ($notify->save()) {
                    return redirect(route('admin.notify.list'));
                }
            }
        }

        return view('admin.pages.notify.create', ['delivery_target' => $career])->withInput($request->all());
    }

    /**
     * @param Request $request
     * @param $id
     */
    public function deleteNotify(Request $request, $id)
    {
        // TODO:
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
