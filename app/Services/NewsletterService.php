<?php

namespace App\Services;

use App\Models\Newsletter;
use App\Models\NewsletterSubscriber;
use App\Models\NewsletterSend;
use App\Models\Lead;
use App\Models\User;
use App\Jobs\SendNewsletterJob;
use App\Jobs\ProcessNewsletterSendJob;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class NewsletterService
{
    /**
     * Create a new newsletter
     */
    public function createNewsletter(array $data): Newsletter
    {
        return Newsletter::create([
            'subject' => $data['subject'],
            'content' => $data['content'],
            'template_data' => $data['template_data'] ?? [],
            'template_name' => $data['template_name'] ?? 'default',
            'recipient_criteria' => $data['recipient_criteria'] ?? [],
            'created_by' => auth()->id()
        ]);
    }

    /**
     * Schedule newsletter for sending
     */
    public function scheduleNewsletter(Newsletter $newsletter, $scheduledAt = null): void
    {
        $recipients = $this->getRecipients($newsletter->recipient_criteria ?? []);
        
        $newsletter->update([
            'status' => 'scheduled',
            'scheduled_at' => $scheduledAt ?? now(),
            'total_recipients' => $recipients->count()
        ]);

        // Create send records for each recipient
        $sendRecords = $recipients->map(function ($recipient) use ($newsletter) {
            return [
                'newsletter_id' => $newsletter->id,
                'email' => $recipient['email'],
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now()
            ];
        });

        NewsletterSend::insert($sendRecords->toArray());

        // Queue the newsletter for processing
        ProcessNewsletterSendJob::dispatch($newsletter)
            ->delay($newsletter->scheduled_at);
    }

    /**
     * Send newsletter immediately
     */
    // public function sendNewsletter(Newsletter $newsletter): void
    // {
    //     $this->scheduleNewsletter($newsletter, now());
    // }
    public function sendNewsletter(Newsletter $newsletter)
{
    $subscribers = NewsletterSubscriber::subscribed()->get();

    foreach ($subscribers as $subscriber) {
        $send = NewsletterSend::create([
            'newsletter_id' => $newsletter->id,
            'email' => $subscriber->email,
            'status' => 'pending',
        ]);

        try {
            Mail::to($subscriber->email)->send(new NewsletterMail($newsletter, $send));
            $send->update(['status' => 'sent', 'sent_at' => now()]);
        } catch (\Exception $e) {
            $send->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
            Log::error('Newsletter send failed: ' . $e->getMessage());
        }
    }

    // Update newsletter status
    $newsletter->update(['status' => 'sent']);
}


    /**
     * Get recipients based on criteria
     */
    public function getRecipients(array $criteria = []): Collection
    {
        $recipients = collect();

        // Get newsletter subscribers
        $subscribers = NewsletterSubscriber::subscribed();
        
        // Apply filters based on criteria
        if (!empty($criteria['tags'])) {
            foreach ($criteria['tags'] as $tag) {
                $subscribers->withTag($tag);
            }
        }

        if (!empty($criteria['preferences'])) {
            foreach ($criteria['preferences'] as $preference) {
                $subscribers->withPreference($preference);
            }
        }

        $subscribers = $subscribers->get(['email', 'name']);
        $recipients = $recipients->merge($subscribers->map(function ($subscriber) {
            return [
                'email' => $subscriber->email,
                'name' => $subscriber->name,
                'type' => 'subscriber'
            ];
        }));

        // Include leads if specified
        if (empty($criteria['recipient_types']) || in_array('leads', $criteria['recipient_types'] ?? [])) {
            $leads = Lead::query();
            
            // Filter by lead score if specified
            if (!empty($criteria['min_lead_score'])) {
                $leads->where('ai_score', '>=', $criteria['min_lead_score']);
            }

            if (!empty($criteria['max_lead_score'])) {
                $leads->where('ai_score', '<=', $criteria['max_lead_score']);
            }

            // Filter by activity level
            if (!empty($criteria['min_activity_days'])) {
                $leads->where('last_activity_at', '>=', now()->subDays($criteria['min_activity_days']));
            }

            $leads = $leads->get(['email', 'first_name']);
            $recipients = $recipients->merge($leads->map(function ($lead) {
                return [
                    'email' => $lead->email,
                    'name' => $lead->first_name,
                    'type' => 'lead'
                ];
            }));
        }

        // Include users if specified
        if (empty($criteria['recipient_types']) || in_array('users', $criteria['recipient_types'] ?? [])) {
            $users = User::query();
            
            // Filter by user type if specified
            if (!empty($criteria['user_roles'])) {
                $users->whereIn('role', $criteria['user_roles']);
            }

            $users = $users->get(['email', 'name']);
            $recipients = $recipients->merge($users->map(function ($user) {
                return [
                    'email' => $user->email,
                    'name' => $user->name,
                    'type' => 'user'
                ];
            }));
        }

        // Remove duplicates based on email
        return $recipients->unique('email');
    }

    /**
     * Subscribe email to newsletter
     */
    public function subscribe(string $email, string $name = null, array $preferences = [], string $source = 'website'): NewsletterSubscriber
    {
        $subscriber = NewsletterSubscriber::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'preferences' => $preferences,
                'subscription_source' => $source,
                'subscribed_at' => now(),
                'status' => 'subscribed'
            ]
        );

        if ($subscriber->status !== 'subscribed') {
            $subscriber->subscribe();
        }

        // Track subscription activity
        ActivityTrackingService::trackFormSubmission('newsletter_signup', $email, [
            'source' => $source,
            'preferences' => $preferences
        ]);

        return $subscriber;
    }

    /**
     * Unsubscribe email from newsletter
     */
    public function unsubscribe(string $email): bool
    {
        $subscriber = NewsletterSubscriber::where('email', $email)->first();
        
        if ($subscriber) {
            $subscriber->unsubscribe();
            return true;
        }

        return false;
    }

    /**
     * Track email open
     */
    public function trackOpen(Newsletter $newsletter, string $email): void
    {
        $send = NewsletterSend::where('newsletter_id', $newsletter->id)
                             ->where('email', $email)
                             ->first();

        if ($send && $send->status === 'sent') {
            $send->markAsOpened();
            
            // Track in activity system
            ActivityTrackingService::trackEmailOpen($email, "newsletter_{$newsletter->id}");
        }
    }

    /**
     * Track email click
     */
    public function trackClick(Newsletter $newsletter, string $email, string $url): void
    {
        $send = NewsletterSend::where('newsletter_id', $newsletter->id)
                             ->where('email', $email)
                             ->first();

        if ($send) {
            $clickData = $send->click_data ?? [];
            $clickData[] = [
                'url' => $url,
                'clicked_at' => now()->toISOString()
            ];
            
            $send->markAsClicked($clickData);
        }
    }

    /**
     * Get newsletter analytics
     */
    public function getAnalytics(Newsletter $newsletter): array
    {
        return [
            'total_recipients' => $newsletter->total_recipients,
            'sent_count' => $newsletter->sent_count,
            'failed_count' => $newsletter->failed_count,
            'open_count' => $newsletter->opens()->count(),
            'click_count' => $newsletter->clicks()->count(),
            'open_rate' => $newsletter->open_rate,
            'click_rate' => $newsletter->click_rate,
            'delivery_rate' => $newsletter->delivery_rate,
            'sends_by_status' => $newsletter->sends()
                                           ->selectRaw('status, count(*) as count')
                                           ->groupBy('status')
                                           ->pluck('count', 'status')
                                           ->toArray()
        ];
    }

    /**
     * Create newsletter templates for investors
     */
    public function createInvestorNewsletterTemplates(): array
    {
        return [
            'market_update' => [
                'name' => 'Market Update',
                'subject' => 'Weekly Market Update - {date}',
                'template' => 'emails.newsletters.market-update',
                'description' => 'Weekly real estate market insights and trends'
            ],
            'new_properties' => [
                'name' => 'New Properties',
                'subject' => 'New Investment Opportunities Available',
                'template' => 'emails.newsletters.new-properties',
                'description' => 'Showcase new properties added to the platform'
            ],
            'investment_tips' => [
                'name' => 'Investment Tips',
                'subject' => 'Investment Tip of the Week',
                'template' => 'emails.newsletters.investment-tips',
                'description' => 'Educational content for investors'
            ],
            'portfolio_update' => [
                'name' => 'Portfolio Update',
                'subject' => 'Your Investment Portfolio Update',
                'template' => 'emails.newsletters.portfolio-update',
                'description' => 'Personalized portfolio performance updates'
            ],
            'event_announcement' => [
                'name' => 'Event Announcement',
                'subject' => 'Upcoming Real Estate Events',
                'template' => 'emails.newsletters.events',
                'description' => 'Announce webinars, seminars, and networking events'
            ]
        ];
    }
}