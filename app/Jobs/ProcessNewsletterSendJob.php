<?php

namespace App\Jobs;

use App\Models\Newsletter;
use App\Jobs\SendNewsletterEmailJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessNewsletterSendJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $newsletter;

    public function __construct(Newsletter $newsletter)
    {
        $this->newsletter = $newsletter;
    }

    public function handle()
    {   
          
        if (!$this->newsletter->canBeSent()) {
            Log::warning("Newsletter {$this->newsletter->id} cannot be sent. Status: {$this->newsletter->status}");
            return;
        }

        $this->newsletter->markAsSending();
        
        // Get pending sends for this newsletter
        $pendingSends = $this->newsletter->sends()
                                       ->whereIn('status', ['pending','sending'])
                                       ->get();
    
        if ($pendingSends->isEmpty()) {
            Log::warning("No pending sends found for newsletter {$this->newsletter->id}");
            $this->newsletter->markAsFailed();
            return;
        }

        // Dispatch individual email jobs
        foreach ($pendingSends as $send) {
            SendNewsletterEmailJob::dispatch($this->newsletter, $send)
                                 ->delay(now()->addSeconds(rand(1, 30))); // Stagger sends
        }

        Log::info("Dispatched {$pendingSends->count()} email jobs for newsletter {$this->newsletter->id}");
    }

    public function failed(\Throwable $exception)
    {
        Log::error("Failed to process newsletter {$this->newsletter->id}: " . $exception->getMessage());
        $this->newsletter->markAsFailed();
    }
}