<?php

namespace App\Http\Controllers;

use App\Mail\SendMailApplyJob;
use App\Repositories\WorkRepository;
use App\Repositories\ContestRepository;
use App\Repositories\CampaignRepository;
use App\Repositories\UserWorkRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class WorkController extends Controller
{
    private $workRepo;
    private $contestRepo;
    private $campaignRepo;
    private $userWorkRepo;

    public function __construct(
        WorkRepository $workRepo,
        ContestRepository $contestRepo,
        CampaignRepository $campaignRepo,
        UserWorkRepository $userWorkRepo
    ) {
        $this->workRepo = $workRepo;
        $this->contestRepo = $contestRepo;
        $this->campaignRepo = $campaignRepo;
        $this->userWorkRepo = $userWorkRepo;
    }

    /**
     * list
     *
     * @return void
     */
    public function list()
    {
        $data = $this->workRepo->orderBy('id', 'DESC')->get();

        return response()->json([
            'status' => 'success',
            'data' => $data->makeHidden(['content', 'created_at', 'updated_at'])
        ], 200);
    }

    /**
     * listContest
     *
     * @return void
     */
    public function listContest()
    {
        $data = $this->contestRepo->orderBy('id', 'DESC')->get();

        return response()->json([
            'status' => 'success',
            'data' => $data
        ], 200);
    }

    /**
     * listCampaign
     *
     * @return void
     */
    public function listCampaign()
    {
        $data = $this->campaignRepo->orderBy('id', 'DESC')->get();

        return response()->json([
            'status' => 'success',
            'data' => $data
        ], 200);
    }

    /**
     * detailWork
     *
     * @param  mixed $id
     * @return void
     */
    public function detailWork($id)
    {
        try {
            $detail = $this->workRepo->findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => $detail
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * apply
     *
     * @param  mixed $request
     * @return void
     */
    public function apply(Request $request)
    {
        try {
            DB::beginTransaction();
            $user = auth()->user();
            $create = $this->userWorkRepo->create([
                'user_id' => $user->id,
                'work_id' => $request->work_id,
                'email' => $request->email,
                'phone' => $request->phone,
                'make_up' => $request->checkbox_one,
                'transportation_cost' => $request->checkbox_two,
                'post_media' => $request->checkbox_three,
            ]);
            $data = [
                'title' => $request->title,
            ];
            Mail::to($request->email)->queue(new SendMailApplyJob($data));
            DB::commit();
            return response()->json([
                'status' => 'success',
                'data' => $create
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * checkApply
     *
     * @param  mixed $id
     * @return void
     */
    public function checkApply($id)
    {
        try {
            $user = auth()->user();
            $check = $this->userWorkRepo->where('user_id', $user->id)->where('work_id', $id)->first();
            if ($check) {
                return response()->json([
                    'status' => 'success',
                    'data' => true
                ], 200);
            }
            return response()->json([
                'status' => 'success',
                'data' => false
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'fail',
                'data' => $e->getMessage()
            ], 404);
        }
    }
}
