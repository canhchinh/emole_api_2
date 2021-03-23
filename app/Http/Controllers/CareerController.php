<?php

namespace App\Http\Controllers;

use App\Repositories\CareerRepository;
use App\Repositories\UserRepository;
use App\Repositories\UserCareerRepository;
use App\Repositories\ActivityContentRepository;
use Illuminate\Http\Request;

class CareerController extends Controller
{

    private $careerRepo;
    private $userCareerRepo;
    private $userRepo;
    private $activityContentRepo;

    public function __construct(
        CareerRepository $careerRepo,
        UserCareerRepository $userCareerRepo,
        UserRepository $userRepo,
        ActivityContentRepository $activityContentRepo
    ) {
        $this->careerRepo = $careerRepo;
        $this->userCareerRepo = $userCareerRepo;
        $this->userRepo = $userRepo;
        $this->activityContentRepo = $activityContentRepo;
    }

    /**
     * @OA\Get(
     *   path="/career/list",
     *   summary="list career",
     *   operationId="list_career",
     *   tags={"Career"},
     *   security={ {"token": {}} },
     *   @OA\Response(response=200, description="successful operation", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Bad request", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Forbidden", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource Not Found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent()),
     * )
     */
    public function listCareer()
    {
        $careers = $this->careerRepo->select(['id', 'title'])->get();
        return response()->json([
            'status' => true,
            'data' => $careers,
        ]);
    }

    /**
     * @OA\Get(
     *   path="/career/detail/{id}",
     *   summary="list career detail for user",
     *   operationId="list_career_detail_for_user",
     *   tags={"Career"},
     *   security={ {"token": {}} },
     *      @OA\Parameter(
     *          name="id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer",
     *              example=1
     *          )
     *      ),
     *   @OA\Response(response=200, description="successful operation", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Bad request", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Forbidden", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource Not Found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent()),
     * )
     */
    public function detailForUser($careerId)
    {
        $user = auth()->user();
        $record = $this->userCareerRepo->where('user_id', $user->id)
            ->where('career_id', $careerId)
            ->first();

        if(empty($record->id)) {
            $list = $this->activityContentRepo->getFreshCareer($careerId);
            $tags = [];
        } else {
            $list = json_decode($record->setting, true);
            $tags = $record->tags;
        }

        return response()->json([
            'status' => true,
            'data' => [
                'career' => $list,
                'tags' => $tags
            ],
        ]);
    }

    /**
     * @OA\Post(
     *   path="/career/save",
     *   summary="user save career",
     *   operationId="save-career",
     *   tags={"Career"},
     *   security={ {"token": {}} },
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="appication/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="career_ids",
     *                      type="array",
     *                      @OA\Items(
     *                         type="integer",
     *                         format="query",
     *                         @OA\Property(type="integer")
     *                     )
     *                  )
     *              )
     *          )
     *      ),
     *   @OA\Response(response=200, description="successful operation", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Bad request", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Forbidden", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource Not Found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent()),
     * )
     */
    public function save(Request $request)
    {
        $request->validate([
            'career_ids' => 'required|array',
        ]);
        $careerIds = $request->input('career_ids');
        $user = auth()->user();
        $listOld = $this->userCareerRepo->where('user_id', $user->id)->get()->keyBy('id')->toArray();
        $listNew = [];
        foreach ($careerIds as $careerId) {
            if(empty($listOld[$careerId])) {
                $param = ['user_id' => $user->id, 'career_id' => $careerId];
                $this->userCareerRepo->create($param);
            }

            $listNew[] = $careerId;
        }

        $this->userCareerRepo->where('user_id', $user->id)
            ->whereNotIn('career_id', $listNew)
            ->delete();

        return response()->json([
            'status' => true,
        ]);
    }

    /**
     * @OA\Get(
     *   path="/user-career",
     *   summary="career info",
     *   operationId="career-info",
     *   tags={"Career"},
     *   security={ {"token": {}} },
     *      @OA\Parameter(
     *          name="user_id",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *   @OA\Response(response=200, description="successful operation", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Bad request", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Forbidden", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource Not Found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent()),
     * )
     */
    public function userCareer(Request $request)
    {
        $user = auth()->user();
        $req = $request->all();
        $userId = $user->id;
        if(!empty($req['user_id'])) {
            $userId = $req['user_id'];
        }
        $userInfo = $this->userRepo->where('id', $userId)
            ->with([
                'careers' => function ($q) {
                    $q->select(['careers.id', 'careers.title']);
                },
                'activity_base' => function ($q) {
                    $q->select(['activity_base.id', 'activity_base.title']);
                }
            ])
            ->first();
        return response()->json([
            'status' => true,
            'data' => $userInfo
        ]);
    }

    /**
     * @OA\Get(
     *   path="/user-career/{idUser}",
     *   summary="detail career info by id",
     *   operationId="detail-career-info",
     *   tags={"Career"},
     *   security={ {"token": {}} },
     *   @OA\Parameter(
     *         description="ID of user",
     *         in="path",
     *         name="idUser",
     *         required=true,
     *         @OA\Schema(
     *           type="integer",
     *           format="int64"
     *         )
     *     ),
     *   @OA\Response(response=200, description="successful operation", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Bad request", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Forbidden", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource Not Found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent()),
     * )
     */
    public function detailUserCareer($idUser)
    {
        $userInfo = $this->userRepo->where('id', $idUser)
            ->with([
                'careers' => function ($q) {
                    $q->select(['careers.id', 'careers.title']);
                },
                'activity_base' => function ($q) {
                    $q->select(['activity_base.id', 'activity_base.title']);
                }
            ])
            ->first();
        return response()->json([
            'status' => true,
            'data' => $userInfo
        ]);
    }

    /**
     * @OA\Get(
     *   path="/job-description",
     *   summary="job description",
     *   operationId="job-description",
     *   tags={"Job description"},
     *   security={ {"token": {}} },
     *   @OA\Response(response=200, description="successful operation", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Bad request", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Forbidden", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource Not Found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent()),
     * )
     */
    public function jobDescription(Request $request)
    {
        try {
            // list all job description
            $activityContent = $this->activityContentRepo->where('key', 'job')
                ->select(['id', 'career_id', 'title'])
                ->get();
            return response()->json([
                'status' => true,
                'data' => $activityContent
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'data' => $e->getMessage()
            ], 500);
        }
    }
}
