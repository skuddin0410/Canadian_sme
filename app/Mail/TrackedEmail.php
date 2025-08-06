<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Email;

class TrackedEmail extends Mailable
{
    use Queueable, SerializesModels;

    public Email $emailRecord;

    /**
     * Create a new message instance.
     */
    public function __construct(Email $emailRecord)
    {
        $this->emailRecord = $emailRecord;
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        return $this->subject($this->emailRecord->subject)
                    ->view('emails.tracked')
                    ->with([
                        'emailRecord' => $this->emailRecord,
                    ]);
    }
}
