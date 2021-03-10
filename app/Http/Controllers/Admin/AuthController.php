<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\AdminRepository;

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
        if ($request->getMethod() === "GET") {
            return view('admin.auth.login');
        }
        $credentials = $request->only(['username', 'password']);
        $result = $this->adminRepo->checkLogin($credentials);
        if ($result) {
            return redirect()->route('admin.home.index');
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
}
