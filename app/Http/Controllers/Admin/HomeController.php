<?php

namespace App\Http\Controllers\Admin;

use App\Entities\ActivityContent;
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
use App\Repositories\UserCareerRepository;

class HomeController extends Controller
{
    /** @var CareerRepository */
    protected $careerRepository;
    /** @var NotificationRepository */
    protected $notificationRepository;
    /** @var UserNotificationRepository */
    protected $userNotificationRepository;
    protected $userCareerRepo;

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
    public function __construct(UserCareerRepository $userCareerRepo, CareerRepository $careerRepository, NotificationRepository $notificationRepository, UserNotificationRepository $userNotificationRepository, UserRepository $userRepository)
    {

        $this->careerRepository = $careerRepository;
        $this->userRepository = $userRepository;
        $this->notificationRepository = $notificationRepository;
        $this->userNotificationRepository = $userNotificationRepository;
        $this->userCareerRepo = $userCareerRepo;
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

    /**
     * updateCareer
     *
     * @return void
     */
    public function updateCareer()
    {
        $records = $this->userCareerRepo->get();
        foreach ($records as $record) {
            $idCareer = $record->career_id;
            if (!empty($record->setting) && $record->setting) {
                $listRecord = [];
                foreach ($record->setting as $item) {
                    $lists = ActivityContent::where('career_id', $idCareer)->where('key', $item['key'])->get();
                    $currentList = array_filter($item['list'], function ($detail) {
                        return $detail['is_checked'] === true;
                    });
                    $array = [];
                    foreach ($lists as $list) {
                        $listItem = [
                            'id' => $list->id,
                            'is_checked' => false,
                            'title' => trim($list->title) == "その他（自由入力）" ? "" : $list->title,
                            'free_text' => trim($list->title) == "その他（自由入力）"
                        ];
                        $array[] = $listItem;
                    }
                    foreach ($currentList as $current) {
                        foreach ($array as $k => $a) {
                            if ($current['id'] === $a['id']) {
                                unset($array[$k]);
                            }
                        }
                    }

                    $newArray = array_merge($currentList, $array);
                    $item["list"] = $newArray;
                    $listRecord[] = $item;
                }
                $record->setting = json_encode($listRecord);
            }
            $record->save();
        }
    }
}