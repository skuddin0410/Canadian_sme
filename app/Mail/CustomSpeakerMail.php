<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomSpeakerMail extends Mailable
{
    use Queueable, SerializesModels;
    public $user;
    public $subjectLine;
    public $messageContent;
    public $mailLogId;

    /**
     * Create a new message instance.
     */
    public function __construct($user,$subjectLine,$messageContent , $mailLogId)
    {   
        $this->user = $user;
        $this->subjectLine = $subjectLine;
        $this->messageContent = $messageContent;
         $this->mailLogId = $mailLogId;
    }

    public function build()
    {
        return $this->subject($this->subjectLine)
                    ->view('emails.custom_mail')
                    ->with([
                        'user' => $this->user,
                        'messageContent' => $this->messageContent,
                        'mailLogId' => $this->mailLogId
                    ]);
    }
}
