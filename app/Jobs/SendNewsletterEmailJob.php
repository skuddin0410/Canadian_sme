<?php

namespace App\Jobs;

use App\Models\Newsletter;
use App\Models\NewsletterSend;
use App\Mail\NewsletterMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendNewsletterEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $newsletter;
    protected $send;

    public function __construct(Newsletter $newsletter, NewsletterSend $send)
    {
        $this->newsletter = $newsletter;
        $this->send = $send;
    }

    public function handle()
    {
        try {
            Mail::to($this->send->email)
                ->send(new NewsletterMail($this->newsletter, $this->send));

            $this->send->update([
                'status' => 'sent',
                'sent_at' => now()
            ]);

            // Update newsletter counters
            $this->newsletter->increment('sent_count');

            // Check if all emails have been processed
            $this->checkIfNewsletterComplete();

        } catch (\Exception $e) {
            Log::error("Failed to send newsletter email to {$this->send->email}: " . $e->getMessage());
            
            $this->send->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);

            $this->newsletter->increment('failed_count');
            $this->checkIfNewsletterComplete();
        }
    }

    private function checkIfNewsletterComplete()
    {
        $pendingCount = $this->newsletter->sends()
                                        ->where('status', 'pending')
                                        ->count();

        if ($pendingCount === 0) {
            $this->newsletter->markAsSent();
            Log::info("Newsletter {$this->newsletter->id} completed sending");
        }
    }

    public function failed(\Throwable $exception)
    {
        $this->send->update([
            'status' => 'failed',
            'error_message' => $exception->getMessage()
        ]);

        $this->newsletter->increment('failed_count');
    }
}