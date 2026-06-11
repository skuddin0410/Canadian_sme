<?php

namespace App\Mail;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistrationCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $recipientName,
        public ?Event $event,
        public string $loginUrl,
        public array $attendees,
        public bool $isTeamRegistration = false
    ) {
    }

    public function envelope(): Envelope
    {
        $subject = $this->isTeamRegistration
            ? 'Team Registration Successful'
            : 'Registration Successful';

        if (!empty($this->event?->title)) {
            $subject .= ' - ' . $this->event->title;
        }

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.registration_credentials',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
