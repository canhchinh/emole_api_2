<?php

namespace App\Http\Controllers;

use App\Repositories\CareerRepository;
use App\Repositories\UserRepository;
use App\Repositories\UserCareerRepository;
use Illuminate\Http\Request;

class CareerController extends Controller
{

    private $careerRepo;
    private $userCareerRepo;
    private $userRepo;

    public function __construct(
        CareerRepository $careerRepo,
        UserCareerRepository $userCareerRepo,
        UserRepository $userRepo
    ) {
        $this->careerRepo = $careerRepo;
        $this->userCareerRepo = $userCareerRepo;
        $this->userRepo = $userRepo;
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
        foreach ($careerIds as $careerId) {
            $param = ['user_id' => $user->id, 'career_id' => $careerId];
            $this->userCareerRepo->updateOrCreate($param, $param);
        }
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
        $userInfo = $this->userRepo->where('id', $user->id)
            ->with(['careers' => function($q) {
                $q->select(['careers.id', 'careers.title']);
            }])
            ->first();
        return response()->json([
            'status' => true,
            'data' => $userInfo
        ]);
    }
}
