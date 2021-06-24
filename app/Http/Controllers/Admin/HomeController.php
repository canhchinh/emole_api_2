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
        Excel::import(new ActivityBaseImport,request()->file('file'));
        // Excel::import(new OneImport,request()->file('file'));
        return back();
    }
    
    /**
     * testImage
     *
     * @return void
     */
    public function testImage() {
       try {
            $img = \Image::make(public_path('images/default/background.png'));
            $image = \Image::make(public_path('images/default/aimiho.jpg'));
            $image->encode('png');
            $image->fit(425, 425);
            $width = $image->getWidth();
            $height = $image->getHeight();
            $mask = \Image::canvas($width, $height);
            // draw a white circle
            $mask->circle($width, $width/2, $height/2, function ($draw) {
                $draw->background('#fff');
            });
            $image->mask($mask, false);
            $img->insert($image, "left", 60);
            $img->text('藍 美帆', 600, 250, function($font) {
                $font->file(public_path('images/default/NotoSansJP-Bold.otf'));
                $font->size(28);
                $font->color('#050518');
            });
            $img->text('女優・アーティスト', 600, 300, function($font) {
                $font->file(public_path('images/default/NotoSansJP-Medium.otf'));
                $font->size(16);
                $font->color('#050519');
            });
            $img->text("アクター", 600, 350, function($font) {
                $font->file(public_path('images/default/NotoSansJP-Bold.otf'));
                $font->size(16);
                $font->color('#050518');
            });
            $img->text("モデル", 700, 350, function($font) {
                $font->file(public_path('images/default/NotoSansJP-Bold.otf'));
                $font->size(16);
                $font->color('#050518');
            });
            // foreach($arrayCareers as $arrayCareer) {
            //     $img->text($arrayCareer, 600, 350, function($font) {
            //         $font->file(public_path('images/default/NotoSansJP-Bold.otf'));
            //         $font->size(16);
            //         $font->color('#050518');
            //     });
            // }
            $img->text('のポートフォリオを', 600, 400, function($font) {
                $font->file(public_path('images/default/NotoSansJP-Bold.otf'));
                $font->size(16);
                $font->color('#050518');
            });
            $img->text('Check', 750, 400, function($font) {
                $font->file(public_path('images/default/NotoSansJP-Bold.otf'));
                $font->size(24);
                $font->color('#EF2E1A');
            });
            $img->save(public_path('images/default/newImage.png'));
       } catch (\Exception $e) {
          echo $e->getMessage();
       }
    }
}