<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PasswordReset;
use App\Http\Requests\CreateUser;
use App\Http\Requests\ForgotPassword;
use App\Http\Requests\ResetPassword;
use App\Http\Requests\NewPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Jobs\SendMailResetPassword;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\UserCategory;
use App\Models\UserJob;
use App\Models\UserGenre;
use App\Models\UserCareer;
class UserController extends Controller
{
    private $userRepo;

    public function __construct(
        UserRepository $userRepo
    )
    {
        $this->userRepo = $userRepo;
    }

    /**
     * @OA\Post(
     *   path="/user/login",
     *   summary="login user",
     *   operationId="login_user_by_uuid",
     *   tags={"Auth"},
     *   @OA\RequestBody(
     *      @OA\MediaType(
     *         mediaType="multipart/form-data",
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
            'password' => 'required'
        ]);

        $user = $this->userRepo->where('email', $request->email)->first();

        if(empty($user->email) || empty($user->id)) {
            return response()->json([
                'status' => false,
                'message' => "email doesn't exist."
            ], 403);
        } elseif (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => "password doesn't match."
            ], 403);
        }

        $tokenResult = $user->createToken('authToken')->plainTextToken;


        return response()->json([
            'status' => true,
            'access_token' => $tokenResult,
            'token_type' => 'Bearer'
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
     *         mediaType="multipart/form-data",
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
            'password' => 'required'
        ]);
        $user = $this->userRepo->create([
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
        $tokenResult = $user->createToken('authToken')->plainTextToken;
        return response()->json([
            'status' => true,
            'access_token' => $tokenResult,
            'token_type' => 'Bearer'
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
     *         mediaType="multipart/form-data",
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
            'user_name' => 'required|unique:users,user_name,' . $user->id
        ]);

        $user->user_name = $request->user_name;
        $user->save();

        return response()->json([
            'status' => true,
            'user' => $user
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
     *                 @OA\Property(property="gender", type="integer", example="MALE || FEMALE || OTHER"),
     *                 @OA\Property(property="birthday", type="string", example="2020-01-31"),
     *                 @OA\Property(property="profession", type="string", example="gotech"),

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
        $data = $request->all([
            'email', 'title', 'given_name', 'birthday', 'gender', 'profession'
        ]);

        $user = auth()->user();

        $file = $request->file('avatar');
        if(!empty($file)){
            $extension = $file->getClientOriginalExtension();
            if(in_array($extension, ['jpg', 'png', 'jpeg'])) {
                $path = 'user/' . $user->id . '_'. time() . '.' . $extension;
                Storage::disk('public')->put($path,  File::get($file));
                $url = '/storage/'.$path;

                $user->avatar = $url;
            }
        }
        $birthday = \DateTime::createFromFormat('Y-m-d', $data['birthday'])->format('Y-m-d');
        $user->given_name = $data['given_name'];
        $user->email = $user->email ?? $data['email'];
        $user->title = $data['title'];
        $user->birthday = $birthday;
        $user->gender = $data['gender'];
        $user->profession = $data['profession'];

        $user->save();

        return response()->json([
            'status' => true,
            'user' => $user
        ]);
    }

    public function forgotPassword(ForgotPassword $request)
    {
        $req = $request->all();
        $user = User::where('email', $req['email'])->first();
        if(empty($user)) {
            return response()->json([
                'status' => false,
                'message' => 'Email not found',
            ]);
        }
        $token = Str::random(60);
        PasswordReset::insert([
            'email' => $req['email'],
            'token' => $token,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        SendMailResetPassword::dispatch($req['email'], $token)->onQueue('default');
        return response()->json([
            'status' => true
        ]);
    }

    public function resetPassword(ResetPassword $request)
    {
        $req = $request->all();
        $passwordReset = PasswordReset::where('token', $req['token'])
            ->orderBy('created_at', 'desc')
            ->first();
        if(empty($passwordReset)) {
            return response()->json([
                'status' => false,
                'message' => 'Token invalid',
            ]);
        }
        $user = User::where('email', $passwordReset->email)->first();
        $tokenResult = $user->createToken('authToken')->plainTextToken;

        return redirect(config('common.frontend_url'))->with('access_token', $tokenResult);
    }

    public function newPassword(NewPassword $request)
    {
        $req = $request->all();
        $user = $request->user();
        if(empty($user)) {
            return response()->json([
                'status' => false,
                'message' => 'User invalid',
            ]);
        }
        if(!Hash::check($req['exist_password'], $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Password invalid',
            ]);
        }
        $user->update([
            'password' => Hash::make($req['new_password'])
        ]);
        return response()->json([
            'status' => true
        ]);
    }

    public function updateSelfIntroduction(Request $request)
    {
        $req = $request->all();
        $user = auth()->user();
        $user->update(['self_introduction' => $req['data']]);
        return response()->json([
            'status' => true,
            'user' => $user
        ]);
    }

    public function activity(Request $request)
    {
        $user = auth()->user();
        $careerId = $request->input('career_id');
        $categoryIds = $request->input('category_ids');
        $jobIds = $request->input('job_ids');
        $genreIds = $request->input('genre_ids');
        $tag = $request->input('tag');
        foreach($categoryIds as $categoryId) {
            $userCategory = [
                'user_id' => $user->id,
                'career_id' => $careerId,
                'category_id' => $categoryId
            ];
            UserCategory::updateOrCreate($userCategory, $userCategory);
        }
        foreach($jobIds as $jobId) {
            $userCategory = [
                'user_id' => $user->id,
                'career_id' => $careerId,
                'job_id' => $jobId
            ];
            UserJob::updateOrCreate($userCategory, $userCategory);
        }
        foreach($genreIds as $genreId) {
            $userCategory = [
                'user_id' => $user->id,
                'career_id' => $careerId,
                'genre_id' => $genreId
            ];
            UserGenre::updateOrCreate($userCategory, $userCategory);
        }
        if(!empty($tag)) {
            UserCareer::where('user_id', $user->id)->where('career_id', $careerId)->update([
                'tag' => $tag
            ]);
        }

        return response()->json([
            'status' => true
        ]);
    }
}
