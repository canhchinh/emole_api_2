<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\ActivityBaseImport;
use App\Imports\CareerImport;
use App\Imports\DetailCareer\OneImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class HomeController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        return redirect()->route('admin.users.list');
    }
    /**
     * listUser
     *
     * @return void
     */
    public function listUser()
    {
        return view('admin.pages.user.index');
    }

    /**
     * listPortfolio
     *
     * @return void
     */
    public function listPortfolio()
    {
        return view('admin.pages.portfolio.index');
    }

    /**
     * listNotify
     *
     * @return void
     */
    public function listNotify()
    {
        return view('admin.pages.notify.index');
    }
    
    /**
     * createNotify
     *
     * @param  mixed $request
     * @return void
     */
    public function createNotify(Request $request) 
    {
        if ($request->getMethod() === "GET"){
            return view('admin.pages.notify.create');
        }
    }

    /**
     * detailUser
     *
     * @param  mixed $id
     * @return void
     */
    public function detailUser($id)
    {
        dd($id);
    }

    
    /**
     * import
     *
     * @param  mixed $request
     * @return void
     */
    public function import(Request $request)
    {
        if ($request->getMethod() === "GET") {
            return view('import');
        }
        Excel::import(new CareerImport,request()->file('file'));
        // Excel::import(new ActivityBaseImport,request()->file('file'));
        // Excel::import(new OneImport,request()->file('file'));
        return back();
    }
}