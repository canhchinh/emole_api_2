<?php

namespace App\Http\Controllers\Admin;

use App\Entities\Notification;
use App\Http\Controllers\Controller;
use App\Repositories\ActivityBaseRepository;
use App\Repositories\CareerRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\PortfolioRepository;
use App\Repositories\UserNotificationRepository;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PortfolioController extends Controller
{
    /** @var CareerRepository */
    protected $careerRepository;

    /** @var PortfolioRepository */
    protected $portfolioRepository;

    /** @var ActivityBaseRepository */
    protected $activityBaseRepository;

    /**
     * UserController constructor.
     * @param CareerRepository $careerRepository
     * @param UserRepository $userRepository
     * @param ActivityBaseRepository $activityBaseRepository
     */
    public function __construct(CareerRepository $careerRepository, PortfolioRepository $portfolioRepository, ActivityBaseRepository $activityBaseRepository)
    {
        $this->careerRepository = $careerRepository;
        $this->portfolioRepository = $portfolioRepository;

        $this->activityBaseRepository = $activityBaseRepository;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function listPortfolio(Request $request)
    {
        $search = $request->input('search-key', '');
        $status = $request->input('status', 'all');
        $arrange = $request->input('arrange', 'desc');

        $portfolios = $this->portfolioRepository->paginateQuery($request, $status, $search);
        $careersList = $this->careerRepository->query()->select(['id', 'title'])->get();

        return view('admin.pages.portfolio.index', [
            'portfolios' => $portfolios,
            'careersList' => $careersList,
            'searchKey' => $search,
            'arrange' => $arrange
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function detailPortfolio(Request $request, $id)
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
    public function deletePortfolio(Request $request, $id)
    {
        if ($request->isMethod('delete')) {
            try {
                /** @var Builder $query */
                $query = $this->portfolioRepository->query();
                $portfolio = $query->where(['id' => $id])->first();
                if ($portfolio->id) {
                    DB::beginTransaction();
                    $portfolio->delete();
                    DB::commit();

                    return response()->json(['success' => true, 'redirectUrl' => route('admin.portfolio.list')]);
                } else {
                    return response()->json(['success' => false, 'message' => 'このメッセージは送信されたため、削除できません。']);
                }
            } catch (\Exception $e) {
                DB::rollBack();
            }
        }

        return response()->json(['success' => false, 'message' => 'No request found!']);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePortfolioStatus(Request $request, $id)
    {
        if ($request->isMethod('put')) {
            DB::beginTransaction();
            try {
                $newStatus = trim($request->get('status'));
                /** @var Builder $query */
                $query = $this->portfolioRepository->query();
                $portfolio = $query->where(['id' => $id])->first();
                if ($newStatus != $portfolio->is_public) {
                    $portfolio->is_public = $newStatus;
                    $portfolio->save();
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

    public function sendEmailToUser()
    {
        // TODO: send email
    }
}
