<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{    
    /**
     * listUser
     *
     * @return void
     */
    public function listUser()
    {
        return view('admin.pages.list_users');
    }    
    
    /**
     * listPortfolio
     *
     * @return void
     */
    public function listPortfolio()
    {
        return view('admin.pages.list_portfolio');
    }    
    
    /**
     * listNotify
     *
     * @return void
     */
    public function listNotify()
    {
        return view('admin.pages.list_notify');
    }
}