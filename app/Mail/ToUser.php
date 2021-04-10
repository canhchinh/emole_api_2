<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ToUser extends Mailable
{
    private $emailSubject;
    private $emailContent;
    use Queueable, SerializesModels;

    /**
     * ToUser constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->emailSubject = $data['subject'];
        $this->emailContent = $data['content'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->emailSubject)->view('mail.send_to_user', ['content' => $this->emailContent]);
    }
}
