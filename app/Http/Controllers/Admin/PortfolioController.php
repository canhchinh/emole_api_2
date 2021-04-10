<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendMailToPortfolio;
use App\Repositories\ActivityBaseRepository;
use App\Repositories\ActivityContentRepository;
use App\Repositories\CareerRepository;
use App\Repositories\PortfolioRepository;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PortfolioController extends Controller
{
    /** @var CareerRepository */
    protected $careerRepository;

    /** @var PortfolioRepository */
    protected $portfolioRepository;

    /** @var ActivityContentRepository */
    protected $activityContentRepository;

    /** @var UserRepository */
    protected $userRepository;

    /**
     * PortfolioController constructor.
     * @param CareerRepository $careerRepository
     * @param PortfolioRepository $portfolioRepository
     * @param UserRepository $userRepository
     * @param ActivityContentRepository $activityContentRepository
     */
    public function __construct(CareerRepository $careerRepository, PortfolioRepository $portfolioRepository, UserRepository $userRepository, ActivityContentRepository $activityContentRepository)
    {
        $this->careerRepository = $careerRepository;
        $this->portfolioRepository = $portfolioRepository;
        $this->userRepository = $userRepository;
        $this->activityContentRepository = $activityContentRepository;
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

        $categories = $this->activityContentRepository->query()
            ->where(['key' => 'job'])
            ->where(['career_id' => $request->input('career_id', 0)])
            ->select(['id', 'title'])->get();
        $portfolios = $this->portfolioRepository->paginateQuery($request, $status, $search, $categories);
        $careersList = $this->careerRepository->query()->select(['id', 'title'])->get();

        return view('admin.pages.portfolio.index', [
            'portfolios' => $portfolios,
            'careersList' => $careersList,
            'categories' => $categories,
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

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendEmailToPortfolio(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                $query = $this->userRepository->query();
                $user = $query->where(['id' => $request->get('user_id')])->first();
                $data = [
                    'subject' => $request->get('email_subject'),
                    'content' => $request->get('email_content')
                ];
                SendMailToPortfolio::dispatch($user->email, $data)->onQueue('processing');

                return response()->json(['success' => true, 'message' => 'リクエストが送信されました']);
            } catch (\Exception $e) {
                Log::error('Can not send email to user, error message: ' . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'このリクエストを実行できません。後でしてください。']);
            }
        }

        abort(404);
    }
}
