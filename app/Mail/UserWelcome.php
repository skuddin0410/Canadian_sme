<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserWelcome extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $bodyText;
    public $subjectLine;
    public $mailLogId;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $subject = null, $bodyText = null, $mailLogId = null)
    {
        $this->user = $user;
        $this->bodyText = $bodyText;
        $this->subjectLine = $subject;
        $this->mailLogId = $mailLogId;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectLine ?? 'Welcome',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.new_email',
             with: [
                'user' => $this->user,
                'bodyText' => $this->bodyText,
                'mailLogId' => $this->mailLogId,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
