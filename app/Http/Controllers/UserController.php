<?php

namespace App\Http\Controllers;

use App\Entities\PasswordReset;
use App\Http\Requests\FollowRequest;
use App\Http\Requests\ForgotPassword;
use App\Http\Requests\NewPassword;
use App\Http\Requests\PortfolioImageRequest;
use App\Http\Requests\PortfolioRequest;
use App\Http\Requests\ResetPassword;
use App\Http\Requests\SelfIntroductionRequest;
use App\Http\Requests\UpdateAccountNameRequest;
use App\Http\Requests\UpdateBasicInformationRequest;
use App\Http\Requests\UpdateEmailNotificationRequest;
use App\Http\Requests\UpdateEmailRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Jobs\SendMailResetPassword;
use App\Repositories\ActivityBaseRepository;
use App\Repositories\ActivityContentRepository;
use App\Repositories\CareerRepository;
use App\Repositories\EducationRepository;
use App\Repositories\FollowRepository;
use App\Repositories\PortfolioRepository;
use App\Repositories\UserCareerRepository;
use App\Repositories\UserCategoryRepository;
use App\Repositories\UserGenreRepository;
use App\Repositories\UserImageRepository;
use App\Repositories\UserJobRepository;
use App\Repositories\UserRepository;
use App\Repositories\PortfolioJobRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mockery\Exception;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    private $userRepo;
    private $userCategoryRepo;
    private $portfolioRepo;
    private $educationRepo;
    private $userCareerRepo;
    private $userGenreRepo;
    private $userJobRepo;
    private $activityBaseRepo;
    private $followRepo;
    private $careerRepo;
    private $activityContentRepo;
    private $userImageRepo;
    private $portfolioJobRepo;

    public function __construct(
        UserRepository $userRepo,
        UserCategoryRepository $userCategoryRepo,
        PortfolioRepository $portfolioRepo,
        EducationRepository $educationRepo,
        UserCareerRepository $userCareerRepo,
        UserGenreRepository $userGenreRepo,
        UserJobRepository $userJobRepo,
        ActivityBaseRepository $activityBaseRepo,
        FollowRepository $followRepo,
        CareerRepository $careerRepo,
        ActivityContentRepository $activityContentRepo,
        UserImageRepository $userImageRepo,
        PortfolioJobRepository $portfolioJobRepo
    ) {
        $this->userRepo = $userRepo;
        $this->userCategoryRepo = $userCategoryRepo;
        $this->portfolioRepo = $portfolioRepo;
        $this->educationRepo = $educationRepo;
        $this->userCareerRepo = $userCareerRepo;
        $this->userGenreRepo = $userGenreRepo;
        $this->userJobRepo = $userJobRepo;
        $this->activityBaseRepo = $activityBaseRepo;
        $this->followRepo = $followRepo;
        $this->careerRepo = $careerRepo;
        $this->activityContentRepo = $activityContentRepo;
        $this->userImageRepo = $userImageRepo;
        $this->portfolioJobRepo = $portfolioJobRepo;
    }

    /**
     * @OA\Post(
     *   path="/user/login",
     *   summary="login user",
     *   operationId="login_user_by_uuid",
     *   tags={"Auth"},
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *         mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="email", type="string", example="a@example.com"),
     *                 @OA\Property(property="password", type="string", example="123456"),
     *             )
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
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'email|required',
            'password' => 'required',
        ]);
        $user = $this->userRepo->where('email', $request->email)->first();

        if (empty($user->email) || empty($user->id)) {
            return response()->json([
                'status' => false,
                'message' => "email doesn't exist.",
            ], 403);
        } elseif (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => "password doesn't match.",
            ], 403);
        }

        $tokenResult = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'status' => true,
            'access_token' => $tokenResult,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * @OA\Post(
     *   path="/user/register-step1",
     *   summary="register user step1",
     *   operationId="register_user_step1",
     *   tags={"Auth"},
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *         mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="email", type="string", example="a@example.com"),
     *                 @OA\Property(property="password", type="string", example="123456"),
     *             )
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
    public function registerStep1(Request $request)
    {
        $request->validate([
            'email' => 'email|required|unique:users',
            'password' => 'required',
        ]);
        $user = $this->userRepo->create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'register_finish_step' => 1,
        ]);
        $tokenResult = $user->createToken('authToken')->plainTextToken;
        return response()->json([
            'status' => true,
            'access_token' => $tokenResult,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * @OA\Post(
     *   path="/user/register-step2",
     *   summary="register user step2",
     *   operationId="register_user_step2",
     *   tags={"Auth"},
     *   security={ {"token": {}} },
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *         mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="user_name", type="string", example="gotech"),
     *             )
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
    public function registerStep2(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'user_name' => 'required|unique:users,user_name,' . $user->id,
        ]);

        $user->user_name = $request->user_name;
        $user->register_finish_step = 2;
        $user->save();

        return response()->json([
            'status' => true,
            'user' => $user,
        ]);
    }

    /**
     * @OA\Post(
     *   path="/user/register-step3",
     *   summary="register user step3",
     *   operationId="register_user_step3",
     *   tags={"Auth"},
     *   security={ {"token": {}} },
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *         mediaType="multipart/form-data",
     *             @OA\Schema(
     *                @OA\Property(property="avatar",type="string", format="binary"),
     *                 @OA\Property(property="given_name", type="string", example="gotech"),
     *                 @OA\Property(property="title", type="string", example="gotech"),
     *                 @OA\Property(property="email", type="string", example="a@example.com"),
     *                 @OA\Property(property="gender", type="integer", example="MALE"),
     *                 @OA\Property(property="birthday", type="string", example="2020-01-31"),
     *                 @OA\Property(property="activity_base_id", type="integer", example=1),

     *             )
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
    public function registerStep3(Request $request)
    {
        $data = $request->all();
        $user = auth()->user();
        $file = $request->avatar;
        if (!empty($file)) {
            $extension = explode('/', mime_content_type($file))[1];
            $path = 'user/';
            if (in_array($extension, ['jpg', 'png', 'jpeg', 'gif'])) {
                $fileName = $this->saveImgBase64($file, $path, $user->id);
                $url = '/storage/' . $path . $fileName;
                $user->avatar = $url;
            }
        }

        $activityBase = $this->activityBaseRepo->where('id', $data['activity_base_id'])->first();
        if (empty($activityBase)) {
            return response()->json([
                'status' => false,
                'message' => 'Activity base not found',
            ], config('common.status_code.500'));
        }

        $birthday = \DateTime::createFromFormat('Y-m-d', $data['birthday'])->format('Y-m-d');
        $user->given_name = $data['given_name'];
        $user->email = $user->email ?? $data['email'];
        $user->title = $data['title'];
        $user->birthday = $birthday;
        $user->gender = $data['gender'];
        $user->register_finish_step = 3;
        $user->activity_base_id = $data['activity_base_id'];

        $user->save();

        return response()->json([
            'status' => true,
            'user' => $user,
        ]);
    }

    /**
     * @OA\Post(
     *   path="/user/avatar",
     *   summary="avatar user",
     *   operationId="avatar",
     *   tags={"User"},
     *   security={ {"token": {}} },
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *         mediaType="multipart/form-data",
     *             @OA\Schema(
     *                @OA\Property(property="avatar",type="string", format="binary")
     *             )
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
    public function avatar(Request $request)
    {
        $request->all([
            'avatar',
        ]);
        $user = auth()->user();
        $file = $request->file('avatar');
        $extension = $file->getClientOriginalExtension();
        if (in_array($extension, ['jpg', 'png', 'jpeg'])) {
            $path = 'user/' . $user->id . '_' . time() . '.' . $extension;
            Storage::disk('public')->put($path, File::get($file));
            $url = '/storage/' . $path;

            $user->avatar = $url;
        }
        return response()->json([
            'status' => true,
            'user' => $user,
        ]);
    }

    /**
     * @OA\Post(
     *   path="/forgot-password",
     *   summary="password user",
     *   operationId="password_user",
     *   tags={"User"},
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *         mediaType="multipart/form-data",
     *             @OA\Schema(
     *                @OA\Property(property="email",type="string", example="abc@gmail.com")
     *             )
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
    public function forgotPassword(ForgotPassword $request)
    {
        $req = $request->all();
        $user = $this->userRepo->where('email', $req['email'])->first();
        if (empty($user)) {
            return response()->json([
                'status' => false,
                'message' => 'Email not found',
            ], 422);
        }
        $token = Str::random(60);
        PasswordReset::insert([
            'email' => $req['email'],
            'token' => $token,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        SendMailResetPassword::dispatch($req['email'], $token)->onQueue('processing');
        return response()->json([
            'status' => true,
        ]);
    }

    /**
     * @OA\Post(
     *   path="/reset-password",
     *   summary="password user reset",
     *   operationId="password_user_reset",
     *   tags={"User"},
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *         mediaType="multipart/form-data",
     *             @OA\Schema(
     *                @OA\Property(property="password",type="string", example="abc123"),
     *                @OA\Property(property="password_confirmation",type="string", example="abc123"),
     *                @OA\Property(property="token",type="string", example="abc123")
     *             )
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
    public function resetPassword(ResetPassword $request)
    {
        $req = $request->all();
        $passwordReset = PasswordReset::where('token', $req['token'])
            ->orderBy('created_at', 'desc')
            ->first();

        if (empty($passwordReset)) {
            return response()->json([
                'status' => false,
                'message' => 'Token invalid',
            ], 404);
        }
        $user = $this->userRepo->where('email', $passwordReset->email)->first();
        $user->password = Hash::make($req['password']);
        $user->save();

        $tokenResult = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'token' => $tokenResult,
        ], 200);
    }

    public function newPassword(NewPassword $request)
    {
        $req = $request->all();
        $user = $request->user();
        if (empty($user)) {
            return response()->json([
                'status' => false,
                'message' => 'User invalid',
            ], config('common.status_code.500'));
        }
        if (!Hash::check($req['exist_password'], $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Password invalid',
            ], config('common.status_code.500'));
        }
        $user->update([
            'password' => Hash::make($req['new_password']),
        ]);
        return response()->json([
            'status' => true,
        ]);
    }

    /**
     * @OA\Post(
     *   path="/user/self-introduction",
     *   summary="login self-introduction",
     *   operationId="self-introduction",
     *   tags={"User"},
     *   security={ {"token": {}} },
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *         mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="introduce", type="string", example="note"),
     *             )
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
    public function updateSelfIntroduction(SelfIntroductionRequest $request)
    {
        try {
            $req = $request->all();
            $user = auth()->user();
            $user->update(['self_introduction' => $req['introduce']]);
            return response()->json([
                'status' => true,
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'user' => $e->getMessage(),
            ], config('common.status_code.500'));
        }
    }
    /**
     * @OA\Post(
     *   path="/career/activity",
     *   summary="user save activity",
     *   operationId="save activity",
     *   tags={"User"},
     *   security={ {"token": {}} },
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                 @OA\Property(property="career_id", type="integer", example="1"),
     *                  @OA\Property(
     *                      property="category_ids",
     *                      type="json",
     *                      example={1,2}
     *                  ),
     *                  @OA\Property(
     *                      property="job_ids",
     *                      type="json",
     *                      example={1,2}
     *                  ),
     *                  @OA\Property(
     *                      property="genre_ids",
     *                      type="json",
     *                      example={1,2}
     *                  ),
     *               @OA\Property(
     *                  property="tags",
     *                  type="array",
     *                  example={ "tag1","tag2" },
     *                  @OA\Items(
     *                       type="string",
     *                  ),
     *               ),
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
    public function activity(Request $request)
    {
        try {
            $user = auth()->user();
            $careerId = $request->input('career_id');
            $categoryIds = $request->input('category_ids');
            $jobIds = $request->input('job_ids');
            $genreIds = $request->input('genre_ids');
            $tags = $request->input('tags');
            $career = $this->careerRepo->where('id', $careerId)->first();
            if (empty($career)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Career not found',
                ], config('common.status_code.500'));
            }
            $activityContent = $this->activityContentRepo->where('career_id', $careerId)
                ->select(['id', 'key'])->get();
            if (empty($activityContent)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Some thing wrong',
                ], config('common.status_code.500'));
            }
            $categoryIdsDb = [];
            $jobIdsDb = [];
            $genreIdsDb = [];
            $activityContent = $activityContent->toArray();
            foreach ($activityContent as $item) {
                if ($item['key'] == 'category') {
                    array_push($categoryIdsDb, $item['id']);
                }
                if ($item['key'] == 'job') {
                    array_push($jobIdsDb, $item['id']);
                }
                if ($item['key'] == 'genre') {
                    array_push($genreIdsDb, $item['id']);
                }
            }

            if (!empty(array_diff($categoryIds, $categoryIdsDb))) {
                return response()->json([
                    'status' => false,
                    'message' => 'Category not found',
                ], config('common.status_code.500'));
            }
            if (!empty(array_diff($jobIds, $jobIdsDb))) {
                return response()->json([
                    'status' => false,
                    'message' => 'Job not found',
                ], config('common.status_code.500'));
            }
            if (!empty(array_diff($genreIds, $genreIdsDb))) {
                return response()->json([
                    'status' => false,
                    'message' => 'Genre not found',
                ], config('common.status_code.500'));
            }
            $param = [
                'category_ids' => json_encode($categoryIds),
                'job_ids' => json_encode($jobIds),
                'genre_ids' => json_encode($genreIds),
                'tags' => $tags,
            ];

            $this->userCareerRepo->where('user_id', $user->id)
                ->where('career_id', $careerId)->update($param);

            return response()->json([
                'status' => true,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], config('common.status_code.500'));
        }
    }

    /**
     * @OA\Post(
     *   path="/education",
     *   summary="user education",
     *   operationId="education",
     *   tags={"Work-Education"},
     *   security={ {"token": {}} },
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *               @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(
     *                          @OA\Property(property="title", type="string", example="Rikkyo University"),
     *                  @OA\Property(property="role", type="string", example="Faculty of Business Administration Department of Business Administration"),
     *                  @OA\Property(property="start_date", type="string", example="2000-10-10"),
     *                  @OA\Property(property="end_date", type="string", example="2030-10-10"),
     *                  @OA\Property(property="is_still_active", type="boolean", example=true),
     *                  @OA\Property(property="link", type="string", example="www.https.//aaaaaaaaaaa.jp"),
     *                  @OA\Property(property="description", type="string", example="For the first time in the band I was forming, I performed live in front of a large number of people")
     *                  )
     *               ),
     *             )
     *          )
     *     ),
     *   @OA\Response(response=200, description="successful operation", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Bad request", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Forbidden", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource Not Found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent()),
     * )
     */

    public function education(Request $request)
    {
        $user = auth()->user();
        $req = $request->all();
        foreach ($req['data'] as $item) {
            $param = [
                'user_id' => $user->id,
                'title' => $item['title'],
                'role' => $item['role'],
                'start_date' => \DateTime::createFromFormat('Y-m-d', $item['start_date'])->format('Y-m-d'),
                'end_date' => \DateTime::createFromFormat('Y-m-d', $item['end_date'])->format('Y-m-d'),
                'is_still_active' => $item['is_still_active'],
                'link' => $item['link'],
                'description' => $item['description'],
            ];
            $this->educationRepo->create($param);
        }
        return response()->json([
            'status' => true,
        ]);
    }

    /**
     * @OA\Post(
     *   path="/portfolio",
     *   summary="user portfolio",
     *   operationId="portfolio",
     *   tags={"Portfolio"},
     *   security={ {"token": {}} },
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *               @OA\Property(
     *                  property="images[]",
     *                  type="array",
     *                  @OA\Items(
     *                       type="string",
     *                       format="binary",
     *                  ),
     *               ),
     *                  @OA\Property(
     *                      property="member_ids[]",
     *                      type="array",
     *                      @OA\Items(
     *                         type="integer"
     *                     )
     *                  ),
     *                  @OA\Property(property="title", type="string", example="Drum advertisement"),
     *                  @OA\Property(
     *                      property="job_ids[]",
     *                      type="array",
     *                      @OA\Items(
     *                         type="integer"
     *                     ),
     *                  ),
     *                  @OA\Property(property="start_date", type="string", example="2006-01"),
     *                  @OA\Property(property="end_date", type="string", example="2006-02"),
     *                  @OA\Property(property="is_still_active", type="boolean", example=true),
     *                  @OA\Property(property="budget", type="string", example="¥900,000"),
     *                  @OA\Property(property="reach_number", type="string", example="285,000pv / 1ヶ月"),
     *                  @OA\Property(property="view_count", type="string", example="1,000,000回"),
     *                  @OA\Property(property="like_count", type="string", example="80,000件"),
     *                  @OA\Property(property="comment_count", type="string", example="433件"),
     *                  @OA\Property(property="cpa_count", type="string", example="約 3,900"),
     *                  @OA\Property(property="video_link", type="string", example="https://www.youtube.com"),
     *                  @OA\Property(property="work_link", type="string", example="https://camp-fire.jp/projects/view/370963?list"),
     *                  @OA\Property(property="work_description", type="string", example="CM")
     *              )
     *          )
     *     ),
     *   @OA\Response(response=200, description="successful operation", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Bad request", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Forbidden", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource Not Found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent()),
     * )
     */
    public function portfolio(PortfolioRequest $request)
    {
        try {
            $user = auth()->user();
            $req = $request->all();
            $imageUrl = [];
            if (!empty($req['member_ids'])) {
                $userIds = $this->userRepo->whereIn('id', $req['member_ids'])->pluck('id');
                if (empty($userIds) || (count($req['member_ids']) != count($userIds))) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Member not found',
                    ], 500);
                }
            }

            $jobIds = $this->activityContentRepo->whereIn('id', $req['job_ids'])
                ->where('key', 'job')
                ->pluck('id');

            if (empty($jobIds) || (count($req['job_ids']) != count($jobIds))) {
                return response()->json([
                    'status' => false,
                    'message' => 'Job description not found',
                ], 500);
            }

            if ($request->hasFile('images')) {
                $files = $request->file('images');
                foreach ($files as $k => $file) {
                    $extension = $file->getClientOriginalExtension();
                    if (in_array($extension, ['jpg', 'png', 'jpeg'])) {
                        $imageName = time() + $k;
                        $path = 'user/' . $user->id . '/work/' . $imageName . '.' . $extension;
                        Storage::disk('public')->put($path, File::get($file));
                        $url = '/storage/' . $path;
                        array_push($imageUrl, $url);
                    }
                }
            }
            $startDate = $req['start_date'] . '-01';
            $endDate = $req['end_date'] . '-01';

            $param = [
                'user_id' => $user->id,
                'title' => $req['title'],
                'job_ids' => !empty($req['job_ids']) ? json_encode($req['job_ids']) : null,
                'start_date' => \DateTime::createFromFormat('Y-m-d', $startDate)->format('Y-m-d'),
                'end_date' => \DateTime::createFromFormat('Y-m-d', $endDate)->format('Y-m-d'),
                'is_still_active' => $req['is_still_active'],
                'budget' => $req['budget'],
                'reach_number' => $req['reach_number'],
                'view_count' => $req['view_count'],
                'like_count' => $req['like_count'],
                'comment_count' => $req['comment_count'],
                'cpa_count' => $req['cpa_count'],
                'video_link' => $req['video_link'],
                'work_link' => $req['work_link'],
                'work_description' => $req['work_description'],
                'member_ids' => !empty($req['member_ids']) ? json_encode($req['member_ids']) : null,
            ];

            if (!empty($imageUrl)) {
                $param['image'] = json_encode($imageUrl);
            }
            $portfolio = $this->portfolioRepo->create($param);
            if (empty($portfolio)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Fail',
                ], 500);
            }
            foreach($req['job_ids'] as $jobId) {
                $this->portfolioJobRepo->updateOrCreate([
                    'user_id' => $user->id,
                    'portfolio_id' => $portfolio->id,
                    'job_id' => $jobId
                ]);
            }

            return response()->json([
                'status' => true,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *   path="/portfolio/image",
     *   summary="portfolio image",
     *   operationId="portfolio_image",
     *   tags={"Portfolio"},
     *   security={ {"token": {}} },
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *         mediaType="multipart/form-data",
     *             @OA\Schema(
     *                @OA\Property(property="image",type="string", format="binary")
     *             )
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
    public function portfolioImage(PortfolioImageRequest $request)
    {
        $user = auth()->user();
        $file = $request->file('image');
        $extension = $file->getClientOriginalExtension();
        if (in_array($extension, ['jpg', 'png', 'jpeg'])) {
            $path = 'user/' . $user->id . '/work/' . time() . '.' . $extension;
            Storage::disk('public')->put($path, File::get($file));
            $url = '/storage/' . $path;
            $this->portfolioRepo->where('user_id', $user->id)->update([
                'image' => $url,
            ]);
        }
        return response()->json([
            'status' => true,
        ]);
    }

    /**
     * @OA\Get(
     *   path="/user",
     *   summary="user information",
     *   operationId="user_information",
     *   tags={"User"},
     *   security={ {"token": {}} },
     *   @OA\Response(response=200, description="successful operation", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Bad request", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Forbidden", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource Not Found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent()),
     * )
     */
    public function userInfo(Request $request)
    {
        $user = auth()->user();
        $userInfo = $this->userRepo->where('id', $user->id)->first();
        $careerIds = $this->userCareerRepo->where('user_id', $user->id)->pluck('career_id');
        if (empty($careerIds)) {
            $userInfo['careers'] = [];
        } else {
            $careerIds = $careerIds->toArray();
            $userInfo['careers'] = $this->careerRepo->whereIn('id', $careerIds)->get();
            $userInfo->avatar = config('common.app_url') . $userInfo->avatar;
        }
        return response()->json([
            'status' => true,
            'data' => $userInfo,
        ]);
    }

    /**
     * @OA\Put(
     *   path="/user/account-name",
     *   summary="change account name",
     *   operationId="change_account_name",
     *   tags={"Account setting"},
     *   security={ {"token": {}} },
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="user_name", type="string", example="aimiho")
     *              )
     *          )
     *     ),
     *   @OA\Response(response=200, description="successful operation", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Bad request", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Forbidden", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource Not Found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent()),
     * )
     */
    public function accountName(UpdateAccountNameRequest $request)
    {
        try {
            $user = auth()->user();
            $req = $request->all();
            $user->update(['user_name' => $req['user_name']]);
            return response()->json([
                'status' => true,
                'data' => $user,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'data' => $e->getMessage(),
            ], config('common.status_code.500'));
        }
    }

    /**
     * @OA\Put(
     *   path="/user/basic-information",
     *   summary="change basic information",
     *   operationId="change_basic_information",
     *   tags={"Account setting"},
     *   security={ {"token": {}} },
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="given_name", type="string", example="Miho Ai"),
     *                  @OA\Property(property="title", type="string", example="Actress / artist"),
     *                  @OA\Property(property="gender", type="string", example="FEMALE"),
     *                  @OA\Property(property="birthday", type="string", example="1996-07-18"),
     *                  @OA\Property(property="activity_base_id", type="integer", example=1)
     *              )
     *          )
     *     ),
     *   @OA\Response(response=200, description="successful operation", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Bad request", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Forbidden", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource Not Found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent()),
     * )
     */
    public function basicInformation(UpdateBasicInformationRequest $request)
    {
        try {
            $req = $request->all();

            $user = auth()->user();

            $activityBase = $this->activityBaseRepo->where('id', $req['activity_base_id'])->first();
            if (empty($activityBase)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Activity base not found',
                ], config('common.status_code.500'));
            }

            $birthday = \DateTime::createFromFormat('Y-m-d', $req['birthday'])->format('Y-m-d');
            $user->given_name = $req['given_name'];
            $user->title = $req['title'];
            $user->gender = $req['gender'];
            $user->birthday = $birthday;
            $user->activity_base_id = $req['activity_base_id'];

            $user->save();

            return response()->json([
                'status' => true,
                'data' => $user,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], config('common.status_code.500'));
        }
    }

    /**
     * @OA\Put(
     *   path="/user/email",
     *   summary="change email address",
     *   operationId="change_email_address",
     *   tags={"Account setting"},
     *   security={ {"token": {}} },
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="email", type="email", example="aimiho@gmail.com"),
     *              )
     *          )
     *     ),
     *   @OA\Response(response=200, description="successful operation", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Bad request", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Forbidden", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource Not Found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent()),
     * )
     */
    public function email(UpdateEmailRequest $request)
    {
        try {
            $req = $request->all();
            $user = auth()->user();
            $user->email = $req['email'];

            $user->save();

            return response()->json([
                'status' => true,
                'data' => $user,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], config('common.status_code.500'));
        }
    }

    /**
     * @OA\Put(
     *   path="/user/email-notification",
     *   summary="change email notification",
     *   operationId="change_email_notification",
     *   tags={"Account setting"},
     *   security={ {"token": {}} },
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="status", type="boolean", example=true),
     *              )
     *          )
     *     ),
     *   @OA\Response(response=200, description="successful operation", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Bad request", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Forbidden", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource Not Found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent()),
     * )
     */
    public function emailNotification(UpdateEmailNotificationRequest $request)
    {
        try {
            $req = $request->all();
            $user = auth()->user();
            $user->is_enable_email_notification = $req['status'];

            $user->save();

            return response()->json([
                'status' => true,
                'data' => $user,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], config('common.status_code.500'));
        }
    }

    /**
     * @OA\Put(
     *   path="/user/change-password",
     *   summary="change password",
     *   operationId="change_password",
     *   tags={"Account setting"},
     *   security={ {"token": {}} },
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="exist_password", type="string", example="123456"),
     *                  @OA\Property(property="new_password", type="string", example="1234567")
     *              )
     *          )
     *     ),
     *   @OA\Response(response=200, description="successful operation", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Bad request", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Forbidden", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource Not Found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent()),
     * )
     */
    public function changePassword(UpdatePasswordRequest $request)
    {
        try {
            $req = $request->all();
            $user = $request->user();
            if (empty($user)) {
                return response()->json([
                    'status' => false,
                    'message' => 'User invalid',
                ], config('common.status_code.500'));
            }
            if (!Hash::check($req['exist_password'], $user->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Password invalid',
                ], config('common.status_code.500'));
            }
            $user->update([
                'password' => Hash::make($req['new_password']),
            ]);
            return response()->json([
                'status' => true,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], config('common.status_code.500'));
        }
    }

    /**
     * @OA\Post(
     *   path="/user/follow",
     *   summary="follow/unfollow user",
     *   operationId="follow_unfollow_user",
     *   tags={"User"},
     *   security={ {"token": {}} },
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="target_id", type="integer", example="1"),
     *                  @OA\Property(property="status", type="string", example="FOLLOW || UNFOLLOW")
     *              )
     *          )
     *     ),
     *   @OA\Response(response=200, description="successful operation", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Bad request", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Forbidden", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource Not Found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent()),
     * )
     */
    public function postFollow(FollowRequest $request)
    {
        $data = $request->all([
            'target_id', 'status',
        ]);

        $owner = auth()->user();
        $record = $this->followRepo->where('user_id', $owner->id)
            ->where('target_id', $data['target_id'])
            ->first();

        if ($data['status'] == 'UNFOLLOW' && !empty($record->id)) {
            $record->delete();
        } elseif ($data['status'] == 'FOLLOW' && empty($record->id)) {
            $this->followRepo->create([
                'user_id' => $owner->id,
                'target_id' => $data['target_id'],
            ]);
        }

        return response()->json([
            'status' => true,
        ]);
    }

    /**
     * @OA\Get(
     *   path="/user/follow",
     *   summary="get list follow by user",
     *   operationId="get_list_follow_by_user",
     *   tags={"User"},
     *   security={ {"token": {}} },
     *   @OA\Response(response=200, description="successful operation", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Bad request", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Forbidden", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource Not Found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent()),
     * )
     */
    public function getFollow()
    {
        $owner = auth()->user();

        $list = $this->followRepo->getListFollowByUser($owner->id);

        return response()->json([
            'status' => true,
            'data' => $list,
        ]);
    }

    /**
     * @OA\Get(
     *   path="/user/follower",
     *   summary="get list follower by user",
     *   operationId="get_list_follower_by_user",
     *   tags={"User"},
     *   security={ {"token": {}} },
     *      @OA\Parameter(
     *          name="current_page",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="limit",
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
    public function getFollower(Request $request)
    {
        $owner = auth()->user();
        $req = $request->all();
        $currentPage = 1;
        $limit  = config('common.paging');
        if(!empty($req['current_page'])) {
            $currentPage = $req['current_page'];
        }
        if(!empty($req['limit'])) {
            $limit = $req['limit'];
        }

        $list = $this->followRepo->getListFollowerByUser($owner->id, $currentPage, $limit);

        return response()->json([
            'status' => true,
            'data' => $list,
        ]);
    }

    /**
     * @OA\Get(
     *   path="/users/list",
     *   summary="get list users",
     *   operationId="get_list_users",
     *   tags={"Users"},
     *   security={ {"token": {}} },
     *      @OA\Parameter(
     *          name="current_page",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="keyword",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="limit",
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
    public function listUsers(Request $request)
    {
        $req = $request->all();
        $page = 1;
        $filters = [];
        $limit = 10;
        if (!empty($req['current_page'])) {
            $page = $req['current_page'];
        }
        if (!empty($req['keyword'])) {
            $filters['keyword'] = $req['keyword'];
        }
        if (!empty($req['limit'])) {
            $limit = $req['limit'];
        }

        $user = $this->userRepo->listUsers($filters, $page, $limit);

        return response()->json([
            'status' => true,
            'data' => $user['data'],
            'pagination' => [
                'total' => $user['total'],
                'per_page' => (int) $user['per_page'],
                'current_page' => $user['current_page'],
                'total_page' => $user['last_page'],
            ],
        ]);
    }

    /**
     * @OA\Get(
     *   path="/education",
     *   summary="work education",
     *   operationId="work-education",
     *   tags={"Work-Education"},
     *   security={ {"token": {}} },
     *   @OA\Response(response=200, description="successful operation", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Bad request", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Forbidden", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource Not Found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent()),
     * )
     */
    public function listWorkEducation(Request $request)
    {
        try {
            $user = $request->user();
            $data = $this->educationRepo->where('user_id', $user->id)->orderBy('start_date', 'ASC')
                ->select(['id', 'title', 'role', 'start_date', 'end_date', 'is_still_active', 'description', 'link'])
                ->get();
            return response()->json([
                'status' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'data' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *   path="/portfolio/detail/{portfolio_id}",
     *   summary="portfolio detail",
     *   operationId="portfolio-detail",
     *   tags={"Portfolio"},
     *   security={ {"token": {}} },
     *      @OA\Parameter(
     *          name="id",
     *          description="User id",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="portfolio_id",
     *          description="Portfolio id",
     *          required=true,
     *          in="path",
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
    public function portfolioDetail(Request $request, $portfolioId)
    {
        try {
            $req = $request->all();
            $portfolioOwner = true;
            if (!empty($req['id'])) {
                $portfolioOwner = false;
                $user = $this->userRepo->where('id', $req['id'])->first();
            } else {
                $user = $request->user();
            }
            if (empty($user)) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found',
                ], 500);
            }

            $portfolio = $this->portfolioRepo->where('user_id', $user->id)
                ->where('id', $portfolioId)
                ->first();
            if (empty($portfolio)) {
                return response()->json([
                    'status' => true,
                    'data' => [],
                ]);
            }
            if (!$portfolio->is_public && !$portfolioOwner) {
                return response()->json([
                    'status' => false,
                    'message' => 'Permission',
                ], 401);
            }
            $memberIds = json_decode($portfolio->member_ids);
            if (!empty($memberIds)) {
                $members = $this->userRepo->whereIn('id', $memberIds)
                    ->select(['id', 'user_name', 'given_name', 'email', 'title', 'gender', 'avatar'])
                    ->get();
                $portfolio['members'] = $members;
            }
            $jobIds = [];
            $jobIds = $this->portfolioJobRepo
                ->where('user_id', $user->id)
                ->where('portfolio_id', $portfolio->id)
                ->pluck('job_id');
            if(!empty($jobIds)) {
                $jobIds = $jobIds->toArray();
            }
            if (!empty($jobIds)) {
                $jobs = $this->activityContentRepo->whereIn('id', $jobIds)
                    ->where('key', 'job')
                    ->select(['id', 'title'])
                    ->get();
                $portfolio['jobs'] = $jobs;
            }

            unset($portfolio['member_ids']);
            unset($portfolio['job_ids']);

            return response()->json([
                'status' => true,
                'data' => $portfolio,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'data' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *   path="/profile",
     *   summary="update profile",
     *   operationId="update_profile",
     *   tags={"Profile"},
     *   security={ {"token": {}} },
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="self_introduction", type="string", example="note update"),
     *                  @OA\Property(
     *                      property="remove_image_ids",
     *                      type="array",
     *                      example={ 1 },
     *                  @OA\Items(
     *                       type="integer",
     *                      ),
     *                  ),
     *                  @OA\Property(
     *                  property="images",
     *                  type="array",
     *                  example={"base64_string"},
     *                  @OA\Items(
     *                       type="string",
     *                      ),
     *                  ),
     *                  @OA\Property(
     *                  property="career_ids",
     *                  type="array",
     *                  example={ 1,3 },
     *                  @OA\Items(
     *                      type="integer",
     *                    ),
     *                 ),
     *              )
     *          )
     *     ),
     *   @OA\Response(response=200, description="successful operation", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Bad request", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Forbidden", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource Not Found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent()),
     * )
     */
    public function updateProfile(Request $request)
    {
        try {
            $req = $request->all();
            $user = $request->user();
            $careerIds = $req['career_ids'];
            if (!empty($req['remove_image_ids'])) {
                $imageUrls = $this->userImageRepo->whereIn('id', $req['remove_image_ids'])
                    ->where('user_id', $user->id)
                    ->get();
                if (empty($imageUrls)) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Image not found',
                    ], 500);
                }

                $this->userImageRepo->whereIn('id', $req['remove_image_ids'])
                    ->where('user_id', $user->id)
                    ->delete();

                // todo unlink image server or delete on s3
            }

            if(!empty($req['images'])) {
                foreach ($req['images'] as $file) {
                    $isBase64  = $this->is_base64($file);
                    if (!$isBase64){
                        return response()->json([
                            'status' => false,
                            'data' => 'Image invalid format',
                        ], 500);
                    }
                    $extension = explode('/', mime_content_type($file))[1];
                    if (in_array($extension, ['jpg', 'png', 'jpeg', 'gif'])) {
                        $path = 'user/';
                        $fileName = $this->saveImgBase64($file, $path, $user->id, true);
                        $url = '/storage/' . $path . 'group/' . $fileName;
                        $this->userImageRepo->create([
                            'user_id' => $user->id,
                            'url' => $url,
                        ]);
                    }
                }
            }

            $existCareerIds = $this->userCareerRepo->where('user_id', $user->id)->pluck('career_id');

            if(!empty($existCareerIds)) {
                $existCareerIds = $existCareerIds->toArray();
            } else {
                $existCareerIds = [];
            }
            $addCareerIds = array_diff($careerIds, $existCareerIds);
            if(!empty($addCareerIds)) {
                foreach($addCareerIds as $addCareerId) {
                    $param = [
                        'user_id' => $user->id,
                        'career_id' => $addCareerId
                    ];
                    $this->userCareerRepo->updateOrCreate($param, $param);
                }
            }
            $removeCareerIds = array_diff($existCareerIds, $careerIds);

            if(!empty($removeCareerIds)) {
                $this->userCareerRepo->where('user_id', $user->id)
                ->whereIn('career_id', $removeCareerIds)->delete();
            }

            $user->update(['self_introduction' => $req['self_introduction']]);

            return response()->json([
                'status' => true,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'data' => $e->getMessage(),
            ], 500);
        }
    }

    private function is_base64($file){
        try {
            $extension = explode('/', mime_content_type($file))[1];
            return true;
        } catch (\Exception $e) {
            return false;
        }

    }

    /**
     * @OA\Get(
     *   path="/portfolio/list",
     *   summary="list portfolio",
     *   operationId="list-portfolio",
     *   tags={"Portfolio"},
     *   security={ {"token": {}} },
     *   @OA\Response(response=200, description="successful operation", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Bad request", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Forbidden", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource Not Found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent()),
     * )
     */
    public function ListPortfolio(Request $request)
    {
        $req = $request->all();
        $user = $request->user();
        $portfolioJobs = $this->portfolioJobRepo->where('user_id', $user->id)
            ->select(DB::raw('group_concat(portfolio_id) as portfolio_ids'), 'job_id')
            ->groupBy('job_id')
            ->get();
        if(!empty($portfolioJobs)) {
            foreach($portfolioJobs as $k=>$portfolioJob) {
                $job = $this->activityContentRepo->where('key', 'job')
                    ->where('id', $portfolioJob['job_id'])
                    ->select(['id', 'title'])
                    ->first();
                $portfolioJobs[$k]['job'] = $job;
                $portfolioIds = explode(",", $portfolioJob->portfolio_ids);
                $portfolios = $this->portfolioRepo->whereIn('id', $portfolioIds)
                    ->get();
                $portfolioJobs[$k]['portfolios'] = $portfolios;
                unset($portfolioJob['job_id']);
                unset($portfolioJob['portfolio_ids']);
            }
        }
        return response()->json([
            'status' => true,
            'data' => $portfolioJobs
        ]);
    }
}
