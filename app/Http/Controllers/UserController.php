<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\CreateUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
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
                'status_code' => 200,
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status_code' => 500,
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
        $user = User::create([
            'name' => $req['email'],
            'email' => $req['email'],
            'password' => Hash::make($req['password'])
        ]);


        $tokenResult = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'status_code' => 200,
            'access_token' => $tokenResult,
            'token_type' => 'Bearer',
        ]);

    }

    public function index(Request $request)
    {
        return response()->json([
            'data' => User::all()
        ]);
    }
}
