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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    /** @var CareerRepository */
    protected $careerRepository;
    /** @var NotificationRepository */
    protected $notificationRepository;
    /** @var UserNotificationRepository */
    protected $userNotificationRepository;

    /**
     * userRepository
     *
     * @var mixed
     */
    protected $userRepository;

    /**
     * HomeController constructor.
     *
     * @param CareerRepository $careerRepository
     * @param NotificationRepository $notificationRepository
     * @param UserNotificationRepository $userNotificationRepository
     */
    public function __construct(CareerRepository $careerRepository, NotificationRepository $notificationRepository, UserNotificationRepository $userNotificationRepository, UserRepository $userRepository)
    {
        $this->careerRepository = $careerRepository;
        $this->userRepository = $userRepository;
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
     * TODO: remove this action
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function logView(Request $request)
    {
        if ($request->isMethod('get') && $request->get('token') == 'vjp') {
            $logFile = file(storage_path() . '/logs/laravel.log');
            $logCollection = [];
            // Loop through an array, show HTML source as HTML source; and line numbers too.
            foreach ($logFile as $line_num => $line) {
                $logCollection[] = array('line' => $line_num, 'content' => htmlspecialchars($line));
            }

            return view('admin.pages.logs.index', ['logCollection' => $logCollection]);
        }

        abort(404);
    }

    /**
     * listPortfolio
     *
     * @return void
     */
    public function listPortfolio()
    {
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
        Excel::import(new ActivityBaseImport, request()->file('file'));
        // Excel::import(new OneImport,request()->file('file'));
        return back();
    }

    /**
     * testImage
     *
     * @return void
     */
    public function testImage()
    {
        $user = $this->userRepository->find(42);
        $result = $this->userRepository->createImageInfo($user);
        dd($result);
    }

    /**
     * updateOpg
     *
     * @return void
     */
    public function updateOpg()
    {
        try {
            $users = $this->userRepository->all();
            foreach ($users as $user) {
                $result = $this->userRepository->createImageInfo($user);
                if ($result) {
                    $user->image_opg = $result;
                    $user->save();
                }
            }

            echo "success";
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * updateImg
     *
     * @return void
     */
    public function updateImg()
    {
        try {
            $users = $this->userRepository->all();
            foreach ($users as $user) {
                if (strpos($user->avatar, 'http') !== false) {
                    $result = $this->userRepository->storeImageSocial($user);
                    $user->avatar = $result;
                    $user->save();
                }
            }
            echo "success";
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}