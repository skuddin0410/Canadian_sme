<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminInquiryReceived extends Mailable
{
    use Queueable, SerializesModels;

    public string $subjectLine;
    public array $payload;

    public function __construct(string $subjectLine, array $payload)
    {
        $this->subjectLine = $subjectLine;
        $this->payload = $payload;
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->subjectLine);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.admin_inquiry_received');
    }

    public function attachments(): array
    {
        return [];
    }
}
