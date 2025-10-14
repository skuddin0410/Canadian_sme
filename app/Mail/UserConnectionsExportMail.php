<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserConnectionsExportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $filePath;

    public function __construct($user, $filePath)
    {
        $this->user = $user;
        $this->filePath = $filePath;
    }

    public function build()
    {
        return $this->subject("Your Connections Export")
                    ->view('emails.user_connections_export')
                    ->attach($this->filePath);
    }
}
