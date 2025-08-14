<?php

namespace App\Mail;

use App\Models\Newsletter;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Models\NewsletterSend;
use Illuminate\Support\Facades\Log;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;


class NewsletterMail extends Mailable
{
    use Queueable, SerializesModels;

    public $newsletter;
    public $send;
    public $trackingPixelUrl;
    public $unsubscribeUrl;

    // public function __construct(Newsletter $newsletter, NewsletterSend $send)
    // {
    //     $this->newsletter = $newsletter;
    //     $this->send = $send;
    //     $this->trackingPixelUrl = route('newsletter.track-open', [
    //         'newsletter' => $newsletter->id,
    //         'email' => $send->email
    //     ]);
    //     $this->unsubscribeUrl = route('newsletter.unsubscribe', [
    //         'email' => $send->email
    //     ]);
    // }
    public function __construct(Newsletter $newsletter, NewsletterSend $send)
{
    Log::info('Preparing NewsletterMail', [
        'newsletter_id' => $newsletter->id,
        'template_name' => $newsletter->template_name,
        'recipient_email' => $send->email
    ]);

    $this->newsletter = $newsletter;
    $this->send = $send;

    $this->trackingPixelUrl = route('newsletter.track-open', [
        'newsletter' => $newsletter->id,
        'email' => $send->email
    ]);
    $this->unsubscribeUrl = route('newsletter.unsubscribe', [
        'email' => $send->email
    ]);

    Log::info('NewsletterMail URLs generated', [
        'trackingPixelUrl' => $this->trackingPixelUrl,
        'unsubscribeUrl'   => $this->unsubscribeUrl
    ]);
}


    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->newsletter->subject,
        );
    }

    public function content(): Content
    {
        $templateMap = [
            'market_update' => 'emails.newsletters.market_update',
            'new_properties' => 'emails.newsletters.new_properties',
            'investment_tips' => 'emails.newsletters.investment_tips',
            'portfolio_update' => 'emails.newsletters.portfolio_update',
            'event_announcement' => 'emails.newsletters.events',
            'default' => 'emails.newsletters.default'
        ];

        $template = $templateMap[$this->newsletter->template_name] ?? $templateMap['default'];

        return new Content(
            view: $template,
            with: [
                'newsletter' => $this->newsletter,
                'content' => $this->newsletter->content,
                'templateData' => $this->newsletter->template_data ?? [],
                'trackingPixelUrl' => $this->trackingPixelUrl,
                'unsubscribeUrl' => $this->unsubscribeUrl,
                'recipientEmail' => $this->send->email
            ]
        );
    }
}