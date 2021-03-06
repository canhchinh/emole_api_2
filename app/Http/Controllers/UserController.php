<?php

namespace App\Http\Controllers;

use App\Entities\PasswordReset;
use App\Http\Requests\ForgotPassword;
use App\Http\Requests\NewPassword;
use App\Http\Requests\PortfolioImageRequest;
use App\Http\Requests\PortfolioRequest;
use App\Http\Requests\ResetPassword;
use App\Http\Requests\UpdateAccountNameRequest;
use App\Jobs\SendMailResetPassword;
use App\Repositories\EducationRepository;
use App\Repositories\PortfolioRepository;
use App\Repositories\UserCareerRepository;
use App\Repositories\UserCategoryRepository;
use App\Repositories\UserGenreRepository;
use App\Repositories\UserJobRepository;
use App\Repositories\ActivityBaseRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Requests\UpdateBasicInformationRequest;
use App\Http\Requests\UpdateEmailRequest;
use App\Http\Requests\UpdateEmailNotificationRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\SelfIntroductionRequest;

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

    public function __construct(
        UserRepository $userRepo,
        UserCategoryRepository $userCategoryRepo,
        PortfolioRepository $portfolioRepo,
        EducationRepository $educationRepo,
        UserCareerRepository $userCareerRepo,
        UserGenreRepository $userGenreRepo,
        UserJobRepository $userJobRepo,
        ActivityBaseRepository $activityBaseRepo
    ) {
        $this->userRepo = $userRepo;
        $this->userCategoryRepo = $userCategoryRepo;
        $this->portfolioRepo = $portfolioRepo;
        $this->educationRepo = $educationRepo;
        $this->userCareerRepo = $userCareerRepo;
        $this->userGenreRepo = $userGenreRepo;
        $this->userJobRepo = $userJobRepo;
        $this->activityBaseRepo = $activityBaseRepo;
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
     *                 @OA\Property(property="profession", type="string", example="gotech"),
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

        $file = $request->file('avatar');
        if (!empty($file)) {
            $extension = $file->getClientOriginalExtension();
            if (in_array($extension, ['jpg', 'png', 'jpeg'])) {
                $path = 'user/' . $user->id . '_' . time() . '.' . $extension;
                Storage::disk('public')->put($path, File::get($file));
                $url = '/storage/' . $path;

                $user->avatar = $url;
            }
        }
        $activityBase = $this->activityBaseRepo->where('id', $data['activity_base_id'])->first();
        if(empty($activityBase)) {
            return response()->json([
                'status' => false,
                'message' => 'Activity base not found',
            ]);
        }


        $birthday = \DateTime::createFromFormat('Y-m-d', $data['birthday'])->format('Y-m-d');
        $user->given_name = $data['given_name'];
        $user->email = $user->email ?? $data['email'];
        $user->title = $data['title'];
        $user->birthday = $birthday;
        $user->gender = $data['gender'];
        $user->profession = $data['profession'];
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
            ],422);
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
            ]);
        }
        if (!Hash::check($req['exist_password'], $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Password invalid',
            ]);
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
                'user' => $e->getMessage()
            ]);
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
     *                  @OA\Property(property="tag", type="string", example="#letdoit")
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
        $user = auth()->user();
        $careerId = $request->input('career_id');
        $categoryIds = $request->input('category_ids');
        $jobIds = $request->input('job_ids');
        $genreIds = $request->input('genre_ids');
        $tag = $request->input('tag');
        foreach ($categoryIds as $categoryId) {
            $userCategory = [
                'user_id' => $user->id,
                'career_id' => $careerId,
                'category_id' => $categoryId,
            ];
            $this->userCategoryRepo->updateOrCreate($userCategory, $userCategory);
        }
        foreach ($jobIds as $jobId) {
            $userCategory = [
                'user_id' => $user->id,
                'career_id' => $careerId,
                'job_id' => $jobId,
            ];
            $this->userJobRepo->updateOrCreate($userCategory, $userCategory);
        }
        foreach ($genreIds as $genreId) {
            $userCategory = [
                'user_id' => $user->id,
                'career_id' => $careerId,
                'genre_id' => $genreId,
            ];
            $this->userGenreRepo->updateOrCreate($userCategory, $userCategory);
        }
        if (!empty($tag)) {
            $this->userCareerRepo->where('user_id', $user->id)->where('career_id', $careerId)->update([
                'tag' => $tag,
            ]);
        }

        return response()->json([
            'status' => true,
        ]);
    }

    /**
     * @OA\Post(
     *   path="/education",
     *   summary="user education",
     *   operationId="education",
     *   tags={"User"},
     *   security={ {"token": {}} },
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="title", type="string", example="Rikkyo University"),
     *                  @OA\Property(property="role", type="string", example="Faculty of Business Administration Department of Business Administration"),
     *                  @OA\Property(property="start_date", type="string", example="2000-10-10"),
     *                  @OA\Property(property="end_date", type="string", example="2030-10-10"),
     *                  @OA\Property(property="is_still_active", type="boolean", example=true),
     *                  @OA\Property(property="link", type="string", example="www.https.//aaaaaaaaaaa.jp"),
     *                  @OA\Property(property="description", type="string", example="For the first time in the band I was forming, I performed live in front of a large number of people")
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
    public function education(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'role' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'is_still_active' => 'required',
            'link' => 'required',
            'description' => 'required',
        ]);
        $user = auth()->user();
        $req = $request->all();
        $param = [
            'user_id' => $user->id,
            'title' => $req['title'],
            'role' => $req['role'],
            'start_date' => \DateTime::createFromFormat('Y-m-d', $req['start_date'])->format('Y-m-d'),
            'end_date' => \DateTime::createFromFormat('Y-m-d', $req['end_date'])->format('Y-m-d'),
            'is_still_active' => $req['is_still_active'],
            'link' => $req['link'],
            'description' => $req['description'],
        ];
        $this->educationRepo->updateOrCreate(['user_id' => $user->id], $param);
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
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="title", type="string", example="Drum advertisement"),
     *                  @OA\Property(property="job_description", type="string", example="CM"),
     *                  @OA\Property(property="start_date", type="string", example="2020-10-20"),
     *                  @OA\Property(property="end_date", type="string", example="2022-10-20"),
     *                  @OA\Property(property="is_still_active", type="boolean", example=true),
     *                  @OA\Property(property="member", type="string", example="Masakazu Hattori"),
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
        $user = auth()->user();
        $req = $request->all();
        $param = [
            'user_id' => $user->id,
            'title' => $req['title'],
            'job_description' => $req['job_description'],
            'start_date' => \DateTime::createFromFormat('Y-m-d', $req['start_date'])->format('Y-m-d'),
            'end_date' => \DateTime::createFromFormat('Y-m-d', $req['end_date'])->format('Y-m-d'),
            'is_still_active' => $req['is_still_active'],
            'member' => $req['member'],
            'budget' => $req['budget'],
            'reach_number' => $req['reach_number'],
            'view_count' => $req['view_count'],
            'like_count' => $req['like_count'],
            'comment_count' => $req['comment_count'],
            'cpa_count' => $req['cpa_count'],
            'video_link' => $req['video_link'],
            'work_link' => $req['work_link'],
            'work_description' => $req['work_description'],
        ];
        $this->portfolioRepo->updateOrCreate(['user_id' => $user->id], $param);
        return response()->json([
            'status' => true,
        ]);
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
        return response()->json([
            'status' => true,
            'data' => $user,
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
                'data' => $user
            ]);
        } catch(\Exception $e) {
            return response()->json([
                'status' => false,
                'data' => $e->getMessage()
            ]);
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
            if(empty($activityBase)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Activity base not found',
                ]);
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
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
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
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
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
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
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
                ]);
            }
            if (!Hash::check($req['exist_password'], $user->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Password invalid',
                ]);
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
            ]);
        }
    }
}
