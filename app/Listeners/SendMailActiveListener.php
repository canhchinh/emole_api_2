<?php

namespace App\Listeners;

use App\Events\SendMailActiveEvent;
use App\Mail\ActiveRegisterMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Str;
use Mail;

class SendMailActiveListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(SendMailActiveEvent $event)
    {
        $user = $event->data;
        $token = str_replace('-', '', (string) Str::uuid());
        $user->activeAccount($token);
        $data = [
            'token' => $token,
            'email' => $user->email,
            'urlActive' => config('common.frontend_url') . '/active/'
        ];

        Mail::to($user['email'])->queue(new ActiveRegisterMail($data));
    }
}
