<?php

namespace App\Http\Controllers;

use App\Entities\Work;
use App\Repositories\WorkRepository;
use App\Repositories\ContestRepository;
use App\Repositories\CampaignRepository;

class WorkController extends Controller
{
    private $workRepo;
    private $contestRepo;
    private $campaignRepo;

    public function __construct(
        WorkRepository $workRepo,
        ContestRepository $contestRepo,
        CampaignRepository $campaignRepo
    ) {
        $this->workRepo = $workRepo;
        $this->contestRepo = $contestRepo;
        $this->campaignRepo = $campaignRepo;
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
        return $id;
    }
}