<?php

namespace App\Mail;

// use Illuminate\Bus\Queueable;
// use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Event;
use App\Models\LandingPageSetting;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

// class UserWelcome extends Mailable implements ShouldQueue
class UserWelcome extends Mailable
{
    // use Queueable, SerializesModels;
    use SerializesModels;

    public $user;
    public $bodyText;
    public $subjectLine;
    public $mailLogId;
    public $event;
    public $websiteUrl;
    public $androidUrl;
    public $iosUrl;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $subject = null, $bodyText = null, $mailLogId = null, $event = null)
    {
        $this->user = $user;
        $this->bodyText = $bodyText;
        $this->subjectLine = $subject;
        $this->mailLogId = $mailLogId;
        $this->event = $event instanceof Event ? $event->loadMissing(['eventLogo', 'photo']) : $event;

        $landingSetting = LandingPageSetting::query()->first();
        $this->websiteUrl = $landingSetting->website ?? config('app.url');
        $this->androidUrl = 'https://play.google.com/store/apps/details?id=com.canadianSME.app';
        $this->iosUrl = 'https://apps.apple.com/us/app/sme-summit-2025/id6753012008';
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
                'event' => $this->event,
                'websiteUrl' => $this->websiteUrl,
                'androidUrl' => $this->androidUrl,
                'iosUrl' => $this->iosUrl,
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
