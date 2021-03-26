<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        if ($e instanceof \Illuminate\Auth\AuthenticationException) {
            $routeName = $request->route()->getName();
            if($routeName === 'user.searchUser') {
                $username = $request->route('username');
                return redirect(route('user.searchUserPublic', ['username' => $username]));
            }

            if($routeName === 'portfolio.detail') {
                $id = $request->route('portfolio_id');
                return redirect(route('portfolio.publicDetail', ['id' => $id]));
            }
        }
    }
}
