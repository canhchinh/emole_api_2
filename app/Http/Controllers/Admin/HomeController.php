<?php

namespace App\Http\Controllers\Admin;

use App\Entities\Career;
use App\Entities\Notify;
use App\Entities\User;
use App\Http\Controllers\Controller;
use App\Imports\ActivityBaseImport;
use App\Imports\CareerImport;
use App\Imports\DetailCareer\OneImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
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
     * @param Request $request
     * @return mixed
     * @throws \Illuminate\Validation\ValidationException
     */
    public function createNotify(Request $request)
    {
        $career = Career::query()
            ->select(['id', 'title'])
            ->get();

        if ($request->isMethod('post')) {
            $messages = [
                'careers_id.min' => 'Please select a careers',
            ];
            $validator = Validator::make($request->all(), [
                'delivery_name' => 'required|min:2',
                'career_id' => 'required|numeric|min:1',
                'delivery_contents' => 'required|max:160|min:2',
                'subject' => 'required|max:100|min:2',
                'url' => 'nullable|url',
            ], $messages);

            if ($validator->validated()) {
                $notify = new Notify();
                $notify->populate($request->all());
                $notify->status = $request->get('storingSubmit') ? Notify::STATUS_ACTIVE : Notify::STATUS_DRAFT;
                $notify->save();
            }
        }

        return view('admin.pages.notify.create', ['delivery_target' => $career])->withInput($request->all());
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
        // Excel::import(new CareerImport,request()->file('file'));
        // Excel::import(new ActivityBaseImport,request()->file('file'));
        Excel::import(new OneImport,request()->file('file'));
        return back();
    }
}
