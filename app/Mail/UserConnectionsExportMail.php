<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserConnectionsExportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $csvContent;
    public $filename;
    public $user;

    public function __construct($mailData)
    {
        $this->csvContent = $mailData['csvContent'];
        $this->filename = $mailData['filename'];
        $this->user = $mailData['user'];
    }

    public function build()
    {
            return $this->subject("Your Connections Export")
                    ->view('emails.user_connections_export')
                    ->attachData($this->csvContent, $this->filename, [
                        'mime' => 'text/csv',
            ]);
    }
}
