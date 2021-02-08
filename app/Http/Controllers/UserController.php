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
use Illuminate\Support\Facades\Storage;
class UserController extends Controller
{
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'email|required',
                'password' => 'required'
            ]);

            $credentials = request(['email', 'password']);

            if (!Auth::attempt($credentials)) {
                return response()->json([
                    'status_code' => 500,
                    'message' => 'Unauthorized'
                ]);
            }
            $user = User::where('email', $request->email)->first();

            if (!Hash::check($request->password, $user->password, [])) {
                throw new \Exception('Error in Login');
            }

            $tokenResult = $user->createToken('authToken')->plainTextToken;

            return response()->json([
                'status' => true,
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error in Login',
                'error' => $e->getMessage(),
            ]);
        }
    }


    public function register(CreateUser $request)
    {
        $req = $request->all();
        $user = User::where('email', $req['email'])->first();
        if(!empty($user)) {
            return response()->json([
                'status' => false,
                'message' => 'User exist'
            ]);
        }
        $link = null;
        $user = User::create([
            'name' => $req['name'],
            'email' => $req['email'],
            'password' => Hash::make($req['password']),
            'profession' => $req['profession'],
            'gender' => $req['gender'],
            'birthday' => $req['birthday'],
            'self_introduction' => @$req['self_introduction'],
        ]);


        // process base64 image
        if(!empty($req['avatar'])) {
            $base64_str = substr($req['avatar'], strpos($req['avatar'], ",")+1);
            //decode base64 string
            $image = base64_decode($base64_str);
            $path = 'public/'.$user->id.'/avatar.png';
            Storage::disk('local')->put($path, $image);
            $link = '/storage/'.$user->id.'/avatar.png';
        }
        if(!empty($link)) {
            User::where('id', $user->id)->update(['avatar' => $link]);
        }

        $tokenResult = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'status' => true,
            'access_token' => $tokenResult,
            'token_type' => 'Bearer',
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
}
