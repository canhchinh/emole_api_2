<?php

namespace App\Jobs;

use App\Mail\ResetPassword;
use App\Mail\ToAllUser;
use App\Mail\ToUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendMailToAllUser implements ShouldQueue
{
    private $email, $data;
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email, $data)
    {
        $this->email = $email;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     *
     */
    public function handle()
    {
        Mail::to($this->email)->send(new ToAllUser($this->data));
    }
}
