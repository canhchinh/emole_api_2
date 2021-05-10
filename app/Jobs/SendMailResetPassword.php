<?php

namespace App\Jobs;

use App\Mail\ResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendMailResetPassword implements ShouldQueue
{
    private $email, $resetPasswordLink;
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email, $token)
    {
        $this->email = $email;
        // $this->resetPasswordLink = config('common.frontend_url') . 'reset-password/' . http_build_query(['token' => $token]);
        $this->resetPasswordLink = config('common.frontend_url'). '/' . 'new-password/' .  $token;
    }

    /**
     * Execute the job.
     *
     * @return void
     * 
     */
    public function handle()
    {
        Mail::to($this->email)->send(new ResetPassword($this->resetPasswordLink));
    }
}