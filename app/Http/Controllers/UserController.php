<?php

namespace App\Http\Controllers;

use App\Entities\PasswordReset;
use App\Entities\Portfolio;
use App\Http\Requests\FollowRequest;
use App\Http\Requests\ForgotPassword;
use App\Http\Requests\LoginGoogle;
use App\Http\Requests\NewPassword;
use App\Http\Requests\PortfolioImageRequest;
use App\Http\Requests\PortfolioRequest;
use App\Http\Requests\ResetPassword;
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
use App\Repositories\PortfolioMemberRepository;
use App\Services\GoogleService;
use App\Services\TwitterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\UpdateAvatarRequest;
use App\Mail\ActiveRegisterMail;
use App\Mail\NotifyFollowMail;
use App\Mail\ToUser;
use App\Services\FacebookService;
use App\Repositories\UserNotificationRepository;
use App\Repositories\NotificationRepository;
use Illuminate\Support\Facades\Mail;

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
    private $portfolioMemberRepo;
    private $userNotificationRepository;
    private $notificationRepository;
    /**
     * @var TwitterService
     */

    private $twitterService;
    /**
     * @var GoogleService
     */
    private $googleService;

    /**
     * @var \Sovit\TikTok\Api
     */
    private $tiktok;
    /**
     * @var FacebookService
     */
    private $fb;

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
        PortfolioJobRepository $portfolioJobRepo,
        PortfolioMemberRepository $portfolioMemberRepo,
        TwitterService $twitterService,
        GoogleService $googleService,
        \Sovit\TikTok\Api $tiktok,
        FacebookService $fb,
        UserNotificationRepository $userNotificationRepository,
        NotificationRepository $notificationRepository
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
        $this->portfolioMemberRepo = $portfolioMemberRepo;
        $this->twitterService = $twitterService;
        $this->googleService = $googleService;
        $this->tiktok = $tiktok;
        $this->fb = $fb;
        $this->userNotificationRepository = $userNotificationRepository;
        $this->notificationRepository = $notificationRepository;
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
        } elseif (!$user->active) {
            return response()->json([
                'status' => false,
                'message' => "Account is not active",
            ], 403);
        }

        $tokenResult = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'status' => true,
            'access_token' => $tokenResult,
            'token_type' => 'Bearer',
        ]);
    }

    public function loginGoogle(LoginGoogle $request)
    {
        $data = $request->all(['email', 'user_name', 'google_id', 'avatar',"active"]);

        $user = $this->userRepo->where('email', $data['email'])->first();

        if(empty($user->id)) {
            $user = $this->userRepo->create([
                'email' => $data['email'],
                'google_id' => $data['google_id'],
                'avatar' => $data['avatar'],
                'user_name' => $data['user_name'],
                'active' => $data['active']
            ]);
            $gotoUsername = true;
        } else {
            if($user->google_id != $data['google_id']) {
                return response()->json([
                    'status' => false,
                    'message' => "please login by password",
                ], 403);
            } else {
                $gotoUsername = empty($user->user_name);
            }
        }

        $tokenResult = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'status' => true,
            'access_token' => $tokenResult,
            'goto_username' => $gotoUsername,
            'user_name' => $user->user_name,
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
     *                 @OA\Property(property="email", type="string", example="phancanhchinh@gmail.com"),
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

        $token = Str::uuid();

        $user = $this->userRepo->create([
            'remember_token' => $token,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'register_finish_step' => 1,
        ]);

        $data = [
            'token' => $token,
            'email' => $user->email,
            'urlActive' => config('common.frontend_url') . '/active/'
        ];

        Mail::to($user->email)->queue(new ActiveRegisterMail($data));

        return response()->json([
            'status' => true,
        ]);
    }

    /**
     * @OA\Post(
     *   path="/user/active-account",
     *   summary="active account",
     *   operationId="active_account",
     *   tags={"Auth"},
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *         mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="token", type="string", example="dad98130e8b84eb5831194061ad2864b"),
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
    public function postActive(Request $request)
    {
        $user = $this->userRepo->where('remember_token', $request->token)->first();

        if (!empty($user->id)) {
            $tokenResult = $user->createToken('authToken')->plainTextToken;
            $user->remember_token = null;
            $user->active = 1;
            $user->save();

            return response()->json([
                'status' => true,
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => "cannot find your account"
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
        // $user->email = $user->email ?? $data['email'];
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
     * @OA\Put(
     *   path="/user/avatar",
     *   summary="avatar user",
     *   operationId="avatar",
     *   tags={"Account setting"},
     *   security={ {"token": {}} },
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *         mediaType="application/json",
     *             @OA\Schema(
     *                @OA\Property(property="avatar",type="string")
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
    public function avatar(UpdateAvatarRequest $request)
    {
        try {
            $req = $request->all();
            $user = auth()->user();
            $file = $req['avatar'];
            $isBase64  = $this->userRepo->is_base64($file);
            if (!$isBase64){
                return response()->json([
                    'status' => false,
                    'data' => 'Image invalid format',
                ], 500);
            }
            $extension = explode('/', mime_content_type($file))[1];
            $path = 'user/';
            if(!in_array($extension, ['jpg', 'png', 'jpeg', 'gif'])) {
                return response()->json([
                    'status' => false,
                    'message' => 'Only support file jpg, png, jpeg, gif'
                ], 400);
            }

            $fileName = $this->saveImgBase64($file, $path, $user->id);
            $url = '/storage/' . $path . $fileName;

            // todo unlink image server or delete on s3

            $this->userRepo->where('id', $user->id)->update(['avatar' => $url]);

            return response()->json([
                'status' => true
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => true,
                'message' => $e->getMessage()
            ], 500);
        }

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
    public function updateSelfIntroduction(Request $request)
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
     *                      property="career",
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
        $data = $request->all([
            'career', 'career_id', 'tags'
        ]);
        $user = auth()->user();

        $userCareer = $this->userCareerRepo->where('user_id', $user->id)
            ->where('career_id', $data['career_id'])
            ->firstOrCreate();

        $userCareer->user_id = $user->id;
        $userCareer->career_id = $data['career_id'];
        $userCareer->setting = json_encode($data['career']);
        $userCareer->tags = implode(":|||:", $data['tags']);
        $userCareer->save();

        return response()->json([
            'status' => true
        ]);
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
        $this->educationRepo->where('user_id', $user->id)
            ->delete();
        foreach ($req['data'] as $item) {
            $param = [
                'user_id' => $user->id,
                'title' => $item['title'],
                'role' => $item['role'],
                'start_date' => $this->validateDate($item['start_date']) ? \DateTime::createFromFormat('Y-m-d', $item['start_date'])->format('Y-m-d') : null,
                'end_date' => $this->checkValidateEndDate($item['is_still_active'], $item['end_date']),
                'is_still_active' => $item['is_still_active'],
            ];
            $this->educationRepo->create($param);
        }
        return response()->json([
            'status' => true,
        ]);
    }

    private function checkValidateEndDate($isStillActive, $date) {
        if ($isStillActive) {
            return null;
        }
        if ($this->validateDate($date)) {
            return \DateTime::createFromFormat('Y-m-d', $date)->format('Y-m-d');
        }
        return null;
    }

    private function validateDate($date, $format = 'Y-m-d')
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
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
     *               @OA\Property(
     *                  property="images",
     *                      type="array",
     *                      @OA\Items(
     *                          type="string",
     *                      ),
     *                  ),
     *                  @OA\Property(
     *                      property="members",
     *                      type="array",
     *                      @OA\Items(
     *                          type="object",
     *                          @OA\Property(property="id", type="integer", example="1"),
     *                          @OA\Property(property="role", type="string", example="member"),
     *                      ),
     *                  ),
     *                  @OA\Property(property="title", type="string", example="Drum advertisement"),
     *                  @OA\Property(
     *                      property="job_ids",
     *                      type="array",
     *                      @OA\Items(
     *                         type="integer"
     *                     ),
     *                  ),
     *                  @OA\Property(property="start_date", type="string", example="2006-01"),
     *                  @OA\Property(property="end_date", type="string", example="2006-02"),
     *                  @OA\Property(property="is_still_active", type="boolean", example=true),
     *                  @OA\Property(property="is_public", type="boolean", example=true),
     *                  @OA\Property(property="budget", type="string", example="¥900,000"),
     *                  @OA\Property(property="reach_number", type="string", example="285,000pv / 1ヶ月"),
     *                  @OA\Property(property="view_count", type="string", example="1,000,000回"),
     *                  @OA\Property(property="like_count", type="string", example="80,000件"),
     *                  @OA\Property(property="comment_count", type="string", example="433件"),
     *                  @OA\Property(property="cpa_count", type="string", example="約 3,900"),
     *                  @OA\Property(property="video_link", type="string", example="https://www.youtube.com"),
     *                  @OA\Property(property="work_link", type="string", example="https://camp-fire.jp/projects/view/370963?list"),
     *                  @OA\Property(property="work_description", type="string", example="CM"),
     *                  @OA\Property(property="id", type="integer", example="0"),
     *                  @OA\Property(property="tags",
     *                      type="array",
     *                      @OA\Items(
     *                          type="string",
     *                      ),
     *                  ),
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
            $members = $req['members'];

            if(!empty($members)) {
                $memberIds = [];
                foreach($members as $member) {
                    array_push($memberIds, $member['id']);
                }
                $userIds = $this->userRepo->whereIn('id', $memberIds)->pluck('id');
                if (empty($userIds) || (count($memberIds) != count($userIds))) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Members not found',
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

            $imageUrl = [];
            if(count($req['images']) > 0) {
                foreach($req['images'] as $img) {
                    if(empty($img['is_old'])) {
                        $extension = explode('/', mime_content_type($img['img']))[1];
                        $path = 'user/';
                        if (in_array($extension, ['jpg', 'png', 'jpeg', 'gif'])) {
                            $fileName = $this->saveImgBase64($img['img'], $path, $user->id);
                            $url = '/storage/' . $path . $fileName;
                        }
                    } else {
                        $url = parse_url($img['img'])['path'];
                    }
                    array_push($imageUrl, $url);
                }
            }

            $startDate = empty($req['start_date']) ? null : $req['start_date'] . '-01';
            $endDate = empty($req['end_date']) ? null : $req['end_date'] . '-01';

            $param = [
                'user_id' => $user->id,
                'title' => $req['title'],
                'job_ids' => !empty($req['job_ids']) ? json_encode($req['job_ids']) : null,
                'start_date' => $startDate,
                'end_date' => empty($req['is_still_active']) ? $endDate : date('Y-m-d'),
                'is_still_active' => $req['is_still_active'],
                'budget' => isset($req['budget']) ? $req['budget'] : null,
                'reach_number' => isset($req['reach_number']) ? $req['reach_number'] : null,
                'view_count' => isset($req['view_count']) ? $req['view_count'] : null,
                'like_count' => isset($req['like_count']) ? $req['like_count'] : null,
                'is_public' => isset($req['is_public']) ? $req['is_public'] : null,
                'comment_count' => isset($req['comment_count']) ? $req['comment_count'] : null,
                'cpa_count' => isset($req['cpa_count']) ? $req['cpa_count'] : null,
                'video_link' => isset($req['video_link']) ? $req['video_link'] : null,
                'work_link' => isset($req['work_link']) ? $req['work_link'] : null,
                'work_description' => $req['work_description'],
                'tags' => !empty($req['tags']) ? implode(":|||:", $req['tags']) : null,
            ];

            if (!empty($imageUrl)) {
                $param['image'] = json_encode($imageUrl);
            }

            if(empty($req['id'])) {
                $portfolio = $this->portfolioRepo->create($param);
            } else {
                $portfolio = $this->portfolioRepo->where('id', $req['id'])
                    ->firstOrFail();
                //todo compare image field to delete unused file
                $portfolio->fill($param)
                    ->save();
            }

            $this->portfolioJobRepo->where('portfolio_id', $portfolio->id)
                    ->delete();
            foreach($req['job_ids'] as $jobId) {
                $this->portfolioJobRepo->create([
                    'user_id' => $user->id,
                    'portfolio_id' => $portfolio->id,
                    'job_id' => $jobId
                ]);
            }

            $this->portfolioMemberRepo->where('portfolio_id', $portfolio->id)
                ->delete();
            if(!empty($members)) {
                foreach($members as $member) {
                    $this->portfolioMemberRepo->create([
                        'portfolio_id' => $portfolio->id,
                        'member_id' => $member['id'],
                        'role' => $member['role']
                    ]);
                }
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
     *   tags={"User info"},
     *   security={ {"token": {}} },
     *      @OA\Parameter(
     *          name="user_id",
     *          description="User id",
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
    public function userInfo(Request $request)
    {
        $user = auth()->user();
        $req = $request->all();
        $userId = $user->id;
        if(!empty($req['user_id'])) {
            $userId = $req['user_id'];
        }
        $userInfo = $this->userRepo->where('id', $userId)->first();
        if(empty($userInfo)) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ], 500);
        }
        $careerIds = $this->userCareerRepo->where('user_id', $userId)->pluck('career_id');
        if (empty($careerIds)) {
            $userInfo['careers'] = [];
        } else {
            $careerIds = $careerIds->toArray();
            $userInfo['careers'] = $this->careerRepo->whereIn('id', $careerIds)->get();
        }

        return response()->json([
            'status' => true,
            'data' => $userInfo,
        ]);
    }

    /**
     * @OA\Put(
     *   path="/user/update-popup",
     *   summary="update popup first login",
     *   operationId="update-popup",
     *   tags={"User info"},
     *   security={ {"token": {}} },
     *   @OA\Response(response=200, description="successful operation", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Bad request", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Forbidden", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource Not Found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent()),
     * )
    */
    public function updateStatusPopup()
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $user->update([
                "status_popup" => 1,
            ]);
             DB::commit();
            return response()->json([
                'status' => true,
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }

    /**
     * @OA\Patch(
     *   path="/user/update-sns",
     *   summary="update sns",
     *   operationId="update-sns",
     *   tags={"User info"},
     *   security={ {"token": {}} },
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *         mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="twitter_user", type="string", example="xxxxx"),
     *                 @OA\Property(property="tiktok_user", type="string", example="https://vt.tiktok.com/ZSJkh7agh/"),
     *                 @OA\Property(property="instagram_user", type="string", example="xxxxx"),
     *                 @OA\Property(property="youtube_channel", type="string", example="xxxxx"),
     *                 @OA\Property(property="facebook_user", type="string", example="xxxxx"),
     *                 @OA\Property(property="line_user", type="string", example="xxxxx"),
     *                 @OA\Property(property="note_user", type="string", example="xxxxx"),
     *                 @OA\Property(property="pinterest_user", type="string", example="xxxxx"),
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
    public function updateSns(Request $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            // $url = $data['tiktok_user'];
            // if(!empty($url)) {
            //     $response = Http::withOptions([
            //         'User-Agent' => 'testing/1.0',
            //         'Accept'     => 'application/json',
            //     ])->get($url);
            //     return $response;
            //     // $client = new \GuzzleHttp\Client();
            //     // $response = $client->get($url);
            //     // $tiktokUser = $this->getContents($response->body(), 'href="https://www.tiktok.com/@', '"');
            //     $data['tiktok_user'] = !empty($tiktokUser) ? $tiktokUser[0] : "";
            //     $data['temp_tiktok_user'] = json_encode($data['temp_tiktok_user']);
            // }
            $user = auth()->user();
            $update = $user->update($data);
            if ($update) {
                DB::commit();
                return response()->json([
                    'status' => true,
                ]);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }

    private function getContents($str, $startDelimiter, $endDelimiter) {
        $contents = array();
        $startDelimiterLength = strlen($startDelimiter);
        $endDelimiterLength = strlen($endDelimiter);
        $startFrom = $contentStart = $contentEnd = 0;
        while (false !== ($contentStart = strpos($str, $startDelimiter, $startFrom))) {
          $contentStart += $startDelimiterLength;
          $contentEnd = strpos($str, $endDelimiter, $contentStart);
          if (false === $contentEnd) {
            break;
          }
          $contents[] = substr($str, $contentStart, $contentEnd - $contentStart);
          $startFrom = $contentEnd + $endDelimiterLength;
        }

        return $contents;
      }

     /**
     * @OA\Get(
     *   path="/user/notify",
     *   summary="get notify user",
     *   operationId="user-notify",
     *   tags={"User Notify"},
     *   security={ {"token": {}} },
     *   @OA\Response(response=200, description="successful operation", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Bad request", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Unauthorized", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Forbidden", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource Not Found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent()),
     * )
    */
    public function getNotify()
    {
        try {
            $user = auth()->user();
            $listNotifies = $this->userNotificationRepository->where("user_id", $user->id)
                ->first();
            if (!empty($listNotifies->id)) {
                $data = json_decode($listNotifies->notification_data, true);

                $listUnread = $this->notificationRepository->whereIn("id", $data['notification_id_unread'])
                    ->orderBy('id','DESC')
                    ->select('id','delivery_name','delivery_contents','subject','url')
                    ->get();
                $listRead = $this->notificationRepository->whereIn("id", $data['notification_id_read'])
                    ->orderBy('id','DESC')
                    ->select('id','delivery_name','delivery_contents','subject','url')
                    ->get();

                return response()->json([
                    'status' => true,
                    "data" => [
                        'listUnRead' => $listUnread,
                        'countUnRead' => count($data['notification_id_unread']),
                        'listRead' => $listRead,
                    ]
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                "data" => $e->getMessage()
            ]);
        }
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
     *   tags={"Follow"},
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

        $userTarget = $this->userRepo->find($data['target_id']);

        $owner = auth()->user();
        $record = $this->followRepo->where('user_id', $owner->id)
            ->where('target_id', $data['target_id'])
            ->first();
        

        if ($data['status'] == 'UNFOLLOW' && !empty($record->id)) {
            $noti = $this->notificationRepository->where('id', $record->notification_id)
                ->first();

            if(!empty($noti->id)) {
                $this->userNotificationRepository->removeNotiForUser($data['target_id'], $noti->id);
                $noti->delete();
            }

            $record->delete();
        } elseif ($data['status'] == 'FOLLOW' && empty($record->id)) {
            if ($userTarget->is_enable_email_notification) {
                $noti = $this->notificationRepository->create([
                    'delivery_name' => 'EMOLE',
                    'delivery_contents' => $owner->given_name . 'さんにフォローされました',
                    'subject' => '',
                    'url' => config('common.frontend_profile') . '/' . $owner->user_name
                ]);
                $this->userNotificationRepository->addNotiForUser($data['target_id'], $noti->id);
                if (!empty($userTarget->email)) {
                    Mail::to($userTarget->email)->queue(new NotifyFollowMail([
                        "content" => $owner->given_name . 'さんにフォローされました',
                    ]));
                }
            }
            
            $this->followRepo->create([
                'user_id' => $owner->id,
                'target_id' => $data['target_id'],
                'notification_id' => $noti->id ?? 0
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
     *   tags={"Follow"},
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

        $lists = $this->followRepo->getListFollowByUser($owner->id);
        foreach($lists as $list) {
            $listPortfolio = Portfolio::where('user_id', $list->id)->get();
            $arrayPortfolio = [];
            foreach($listPortfolio as $image) {
                array_push($arrayPortfolio, $image->image[0]);
            }
            $list->portfolio = $arrayPortfolio;
        }
        return response()->json([
            'status' => true,
            'data' => $lists,
        ]);
    }

    /**
     * @OA\Get(
     *   path="/user/follower",
     *   summary="get list follower by user",
     *   operationId="get_list_follower_by_user",
     *   tags={"Follow"},
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
    public function getFollower()
    {
        $owner = auth()->user();

        $lists = $this->followRepo->getListFollowerByUser($owner->id);
        foreach($lists as $list) {
            $listPortfolio = Portfolio::where('user_id', $list->id)->get();
            $arrayPortfolio = [];
            foreach($listPortfolio as $image) {
                array_push($arrayPortfolio, $image->image[0]);
            }
            $list->portfolio = $arrayPortfolio;
        }
        return response()->json([
            'status' => true,
            'data' => $lists,
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
        $user = auth()->user();
        if (!empty($req['current_page'])) {
            $page = $req['current_page'];
        }
        if (!empty($req['keyword'])) {
            $filters['keyword'] = $req['keyword'];
        }
        if (!empty($req['limit'])) {
            $limit = $req['limit'];
        }
        $user = $this->userRepo->listUsers($user->id, $filters, $page, $limit);
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
     *   path="/education/user/{id}",
     *   summary="work education",
     *   operationId="work-education",
     *   tags={"Work-Education"},
     *   security={ {"token": {}} },
     *      @OA\Parameter(
     *          name="user_id",
     *          required=false,
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
    public function listWorkEducation($userId)
    {
        $data = $this->educationRepo->where('user_id', $userId)
            ->orderBy('start_date', 'ASC')
            ->select(['id', 'title', 'role', 'start_date', 'end_date', 'is_still_active', 'description', 'link'])
            ->get();

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

    /**
     * @OA\Get(
     *   path="/education",
     *   summary="my work education",
     *   operationId="my_work-education",
     *   tags={"Work-Education"},
     *   security={ {"token": {}} },
     *      @OA\Parameter(
     *          name="user_id",
     *          required=false,
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
    public function myEducation()
    {
        $user = auth()->user();

        $data = $this->educationRepo->where('user_id', $user->id)
            ->orderBy('start_date', 'ASC')
            ->select(['id', 'title', 'role', 'start_date', 'end_date', 'is_still_active', 'description', 'link'])
            ->get();

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

    /**
     * @OA\Get(
     *   path="/portfolio/detail/{portfolio_id}",
     *   summary="portfolio detail",
     *   operationId="portfolio-detail",
     *   tags={"Portfolio"},
     *   security={ {"token": {}} },
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
    public function portfolioDetail($portfolioId)
    {
        $user = auth()->user();

        $portfolio = $this->portfolioRepo->where('id', $portfolioId)
            ->firstOrFail();

        if (!$portfolio->is_public && $portfolio->user_id !== $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'Permission',
            ], 403);
        } else {
            return $this->getPortfolioDetail($portfolio);
        }
    }

    /**
     * @OA\Delete(
     *   path="/portfolio/delete/{id}",
     *   summary="delete portfolio",
     *   operationId="delete portfolio",
     *   tags={"Portfolio"},
     *   security={ {"token": {}} },
     *      @OA\Parameter(
     *          name="id",
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
    public function deletePortfolio($id)
    {
        try {
            DB::beginTransaction();
            $user = auth()->user();
            $portfolio = $this->portfolioRepo->where("user_id", $user->id)->where("id", $id)->firstOrFail();
            if (!is_null($portfolio))
            {
                $this->portfolioJobRepo->where("portfolio_id", $portfolio->id)->delete();
                $this->portfolioMemberRepo->where("portfolio_id", $portfolio->id)->delete();
                $portfolio->delete();
                if (isset($portfolio->image)) {
                    foreach ($portfolio->image as $item) {
                        $portfolioImage = str_replace('/storage', '', $item["path"]);
                        if (Storage::disk('public')->exists($portfolioImage)) {
                            Storage::disk('public')->delete($portfolioImage);
                        }
                    }
                }
            }
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => "Delete SuccessFully",
            ], 202);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    public function publicPortfolioDetail($portfolioId)
    {
        $portfolio = $this->portfolioRepo->where('id', $portfolioId)
        ->firstOrFail();

        if (!$portfolio->is_public) {
            return response()->json([
                'status' => false,
                'message' => 'Permission',
            ], 401);
        } else {
            return $this->getPortfolioDetail($portfolio);
        }
    }

    private function getPortfolioDetail($portfolio)
    {
        $memberConvert = [];
        $portfolioMembers = $this->portfolioMemberRepo
            ->where('portfolio_id', $portfolio->id)
            ->with(['member'])
            ->get();
        if(!empty($portfolioMembers)) {
            foreach($portfolioMembers as $portfolioMember) {
                $member = $portfolioMember['member'];
                $member['role'] = $portfolioMember['role'];
                array_push($memberConvert, $member);
            }
        }
        $portfolio['members'] = $memberConvert;

        $jobIds = [];
        $jobIds = $this->portfolioJobRepo
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

        return response()->json([
            'status' => true,
            'data' => $portfolio,
        ]);
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
                    } else {
                        return response()->json([
                            'status' => false,
                            'message' => 'Only support file jpg, png, jpeg, gif'
                        ], 400);
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
     *   path="/portfolio/user/{id}",
     *   summary="list portfolio",
     *   operationId="list-portfolio",
     *   tags={"Portfolio"},
     *   security={ {"token": {}} },
     *      @OA\Parameter(
     *          name="id",
     *          required=false,
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
    public function ListPortfolio($userId)
    {
        $portfolioJobs = $this->portfolioJobRepo->where('user_id', $userId)
            ->select(DB::raw('group_concat(portfolio_id) as portfolio_ids'), 'job_id')
            ->groupBy('job_id')
            ->get();
        if(!empty($portfolioJobs)) {
            foreach($portfolioJobs as $k=>$portfolioJob) {
                $job = $this->activityContentRepo->where('key', 'job')
                    ->where('id', $portfolioJob->job_id)
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

    /**
     * @OA\Get(
     *   path="/user/search/{username}",
     *   summary="search user",
     *   operationId="search-user",
     *   tags={"User info"},
     *   security={ {"token": {}} },
     *      @OA\Parameter(
     *          name="username",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
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
    public function searchUser($username)
    {
        $owner = auth()->user();
        $userSearch = $this->userRepo->where('user_name', $username)
            ->with([
                'careers' => function ($q) {
                    $q->select(['careers.id', 'careers.title']);
                },
                'activity_base' => function ($q) {
                    $q->select(['activity_base.id', 'activity_base.title']);
                },
                'images' => function ($q) {
                    $q->select(['user_images.id', 'user_images.url', 'user_images.user_id']);
                }
            ])
            ->firstOrFail();

        if($owner->id !== $userSearch->id) {
            $count = $this->followRepo->where('user_id', $owner->id)
                ->where('target_id', $userSearch->id)
                ->count();
            $userSearch->is_follow = $count > 0;
        } else {
            $userSearch->is_follow = true;
        }

        $snsFollowersCount = [];

        if ($userSearch->twitter_user) {
            $twitterInfoUser = $this->twitterService->getUsers(null, $userSearch->twitter_user);
            if ($twitterInfoUser) {
                $snsFollowersCount['twitter'] = $twitterInfoUser['followers_count'];
            }
        }

        if ($userSearch->tiktok_user) {
            $tiktokInfoUser = $this->tiktok->getUser($userSearch->tiktok_user);
            if ($tiktokInfoUser) {
                $snsFollowersCount['tiktok'] = $tiktokInfoUser->stats->followerCount;
            }
        }

        if ($userSearch->youtube_channel) {
            $channelInfo = $this->googleService->getInfoChannel($userSearch->youtube_channel);
            if ($channelInfo) {
                $snsFollowersCount['youtube'] = $channelInfo['subscriberCount'];
            }
        }

        if ($userSearch->instagram_user) {
            $access_token = $userSearch->access_token;
            if ($access_token) {
                $this->fb->getFacebook()->setDefaultAccessToken($access_token);
            }

            $snsFollowersCount['instagram'] = $this->fb->getFollowersCount($userSearch->instagram_user);
        }

        unset($userSearch['access_token']);
        return response()->json([
            'status' => true,
            'data' => [
                'user' => $userSearch,
                'is_logged' => true,
                'sns_followers' => $snsFollowersCount,
                'is_owner' => $owner->user_name == $username
            ]
        ]);
    }

    public function searchUserPublic($username)
    {
        $userSearch = $this->userRepo->where('user_name', $username)
            ->with([
                'careers' => function ($q) {
                    $q->select(['careers.id', 'careers.title']);
                },
                'activity_base' => function ($q) {
                    $q->select(['activity_base.id', 'activity_base.title']);
                },
                'images' => function ($q) {
                    $q->select(['user_images.id', 'user_images.url', 'user_images.user_id']);
                }
            ])
            ->firstOrFail();

        $userSearch->is_follow = false;

        $snsFollowersCount = [];

        if ($userSearch->twitter_user) {
            $twitterInfoUser = $this->twitterService->getUsers(null, $userSearch->twitter_user);
            if ($twitterInfoUser) {
                $snsFollowersCount['twitter'] = $twitterInfoUser['followers_count'];
            }
        }

        if ($userSearch->tiktok_user) {
            $tiktokInfoUser = $this->tiktok->getUser($userSearch->tiktok_user);
            if ($tiktokInfoUser) {
                $snsFollowersCount['tiktok'] = $tiktokInfoUser->stats->followerCount;
            }
        }

        if ($userSearch->youtube_channel) {
            $channelInfo = $this->googleService->getInfoChannel($userSearch->youtube_channel);
            if ($channelInfo) {
                $snsFollowersCount['youtube'] = $channelInfo['subscriberCount'];
            }
        }

        if ($userSearch->instagram_user) {
            $access_token = Session::get('access_token');
            if ($access_token) {
                $this->fb->getFacebook()->setDefaultAccessToken($access_token);
            }

            $snsFollowersCount['instagram'] = $this->fb->getFollowersCount($userSearch->instagram_user);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'user' => $userSearch,
                'sns_followers' => $snsFollowersCount,
                'is_logged' => false,
                'is_owner' => false
            ]
        ]);
    }

    public function test()
    {
        $data = [
            'subject' => 'bachpx test email',
            'content' => 'hello world'
        ];

        Mail::to('phanxuanbachkh@gmail.com')->send(new ToUser($data));
        exit('done');
    }

    public function setRead(Request $request)
    {
        if ($request->id) {
            $user = auth()->user();
            $result = $this->userNotificationRepository->setReadAllForUser($user->id, $request->id);
        }
        return response()->json([
            'status' => true,
            'data' => $result
        ]);
    }
}