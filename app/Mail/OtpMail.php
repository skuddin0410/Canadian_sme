<?php

namespace App\Mail;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $otp;
    public ?Event $event;
    public int $expiresMinutes;
    public string $appName;
    public string $brandUrl;

    public function __construct(string|int $otp, ?Event $event = null, int $expiresMinutes = 10)
    {
        $this->otp = (string) $otp;
        $this->event = $event;
        $this->expiresMinutes = $expiresMinutes;

        // Never use local/dev branding in transactional auth emails
        $configuredName = trim((string) config('app.name', 'Eventzen'));
        $this->appName = preg_replace('/\s+Local$/i', '', $configuredName) ?: 'Eventzen';
        $this->brandUrl = rtrim((string) (config('mail.brand_url') ?: env('MAIL_BRAND_URL', 'https://eventzen.io')), '/');
    }

    public function envelope(): Envelope
    {
        $subject = $this->event?->title
            ? "Your {$this->appName} verification code"
            : "Your {$this->appName} verification code";

        $fromAddress = (string) config('mail.from.address');
        $replyTo = (string) (config('mail.reply_to.address') ?: env('MAIL_REPLY_TO_ADDRESS', 'support@eventzen.io'));

        return new Envelope(
            from: new Address($fromAddress, $this->appName),
            replyTo: [
                new Address($replyTo, $this->appName . ' Support'),
            ],
            subject: $subject,
        );
    }

    public function headers(): Headers
    {
        return new Headers(
            text: [
                'X-Auto-Response-Suppress' => 'OOF, AutoReply',
                'Auto-Submitted' => 'auto-generated',
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            html: 'emails.otp',
            text: 'emails.otp_plain',
            with: [
                'otp' => $this->otp,
                'event' => $this->event,
                'expiresMinutes' => $this->expiresMinutes,
                'appName' => $this->appName,
                'brandUrl' => $this->brandUrl,
                'eventTitle' => $this->event?->title,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
