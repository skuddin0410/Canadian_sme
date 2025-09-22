<?php

namespace App\Services;

use App\Models\UserActivity;
use App\Models\Lead;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityTrackingService
{   
    public static function trackPageView($pageUrl, $pageTitle = null, $email = null)
    {
        $sessionId = session()->getId();
        $userId = Auth::id();
        $email = $email ?? (Auth::user()->email ?? null);

        // Record the activity
        UserActivity::create([
            'session_id' => $sessionId,
            'user_id' => $userId,
            'email' => $email,
            'activity_type' => 'page_view',
            'page_url' => $pageUrl,
            'page_title' => $pageTitle,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'activity_at' => now()
        ]);

        // Update lead metrics if email exists
        if ($email) {
            self::updateLeadMetrics($email, 'page_views');
        }
    }

    public static function trackTimeOnSite($timeSpent, $pageUrl, $email = null)
    {
        $sessionId = session()->getId();
        $userId = Auth::id();
        $email = $email ?? (Auth::user()->email ?? null);

        // Find the most recent page view for this session/page
        $activity = UserActivity::where('session_id', $sessionId)
            ->where('page_url', $pageUrl)
            ->where('activity_type', 'page_view')
            ->latest('activity_at')
            ->first();

        if ($activity) {
            $activity->update(['time_spent' => $timeSpent]);
        }

        // Update lead metrics
        if ($email) {
            self::updateLeadTimeOnSite($email, $timeSpent);
        }
    }

    public static function trackEmailOpen($email, $campaignId = null)
    {
        UserActivity::create([
            'email' => $email,
            'activity_type' => 'email_open',
            'metadata' => json_encode(['campaign_id' => $campaignId]),
            'activity_at' => now()
        ]);

        self::updateLeadMetrics($email, 'email_opens');
    }

    public static function trackDownload($fileName, $fileUrl, $email = null)
    {
        $sessionId = session()->getId();
        $userId = Auth::id();
        $email = $email ?? (Auth::user()->email ?? null);

        UserActivity::create([
            'session_id' => $sessionId,
            'user_id' => $userId,
            'email' => $email,
            'activity_type' => 'download',
            'page_url' => $fileUrl,
            'metadata' => json_encode(['file_name' => $fileName]),
            'ip_address' => Request::ip(),
            'activity_at' => now()
        ]);

        if ($email) {
            self::updateLeadMetrics($email, 'downloads');
        }
    }

    public static function trackFormSubmission($formType, $email, $formData = [])
    {
        $sessionId = session()->getId();
        $userId = Auth::id();

        UserActivity::create([
            'session_id' => $sessionId,
            'user_id' => $userId,
            'email' => $email,
            'activity_type' => 'form_submission',
            'metadata' => json_encode([
                'form_type' => $formType,
                'form_data' => $formData
            ]),
            'ip_address' => Request::ip(),
            'activity_at' => now()
        ]);

        self::updateLeadMetrics($email, 'form_submissions');
    }

    public static function trackPropertyInquiry($propertyId, $email, $inquiryType = 'general')
    {
        $sessionId = session()->getId();
        $userId = Auth::id();

        UserActivity::create([
            'session_id' => $sessionId,
            'user_id' => $userId,
            'email' => $email,
            'activity_type' => 'property_inquiry',
            'metadata' => json_encode([
                'property_id' => $propertyId,
                'inquiry_type' => $inquiryType
            ]),
            'ip_address' => Request::ip(),
            'activity_at' => now()
        ]);

        self::updateLeadMetrics($email, 'property_inquiries');
    }

    private static function updateLeadMetrics($email, $metricType)
    {   
        if(!empty($email)){
        $lead = Lead::where('email', $email)->first();
            if ($lead) {
                $lead->increment($metricType);
                $lead->update(['last_activity_at' => now()]);
            } else {
                // Create lead if doesn't exist
              $lead =  Lead::create([
                   "first_name" => Auth::user()->name ?? '', 
                   "last_name"=> Auth::user()->lastname ?? '',
                   "email"=> $email,
                   "phone"=> Auth::user()->mobile ?? "",
                   $metricType => 1,
                  'last_activity_at' => now()
                ]);
            } 
        }
    }

    private static function updateLeadTimeOnSite($email, $timeSpent)
    {
        $lead = Lead::where('email', $email)->first();
        
        if ($lead) {
            // Convert seconds to minutes and add to existing time
            $additionalMinutes = ceil($timeSpent / 60);
            $lead->increment('time_on_site', $additionalMinutes);
            $lead->update(['last_activity_at' => now()]);
        }else{

          $lead =  Lead::create([
               "first_name" => Auth::user()->name, 
               "last_name"=> Auth::user()->lastname ?? '',
               "email"=> Auth::user()->email,
               "phone"=> Auth::user()->mobile ?? '',
               $metricType => 1,
              'last_activity_at' => now()
            ]);
        }
    }

    // Aggregate metrics for a specific lead
    public static function getLeadMetrics($email)
    {
        $activities = UserActivity::where('email', $email)->get();
        
        return [
            'page_views' => $activities->where('activity_type', 'page_view')->count(),
            'total_time_on_site' => $activities->where('activity_type', 'page_view')->sum('time_spent'),
            'email_opens' => $activities->where('activity_type', 'email_open')->count(),
            'downloads' => $activities->where('activity_type', 'download')->count(),
            'form_submissions' => $activities->where('activity_type', 'form_submission')->count(),
            'property_inquiries' => $activities->where('activity_type', 'property_inquiry')->count(),
            'unique_pages_visited' => $activities->where('activity_type', 'page_view')->pluck('page_url')->unique()->count(),
            'last_activity' => $activities->max('activity_at')
        ];
    }

    public static function trackApiHit(string $endpoint, array $input = [], ?string $email = null, ?int $status = null, string $method = 'GET'): void
    {
        $sessionId = session()->getId();
        $userId    = Auth::id();
        $email     = $email ?? optional(Auth::user())->email;

        // Scrub sensitive fields
        $scrubbed = self::scrubSensitive($input);

        UserActivity::create([
            'session_id'   => $sessionId,
            'user_id'      => $userId,
            'email'        => $email,
            'activity_type'=> 'api_hit',
            'page_url'     => $endpoint,                 // reuse page_url column for URL
            'metadata'     => json_encode([
                'method' => $method,
                'status' => $status,
                'input'  => $scrubbed,
            ]),
            'ip_address'   => Request::ip(),
            'user_agent'   => Request::userAgent(),
            'activity_at'  => now(),
        ]);

        // If you want API hits to also influence lead scoring, opt-in here:
        if ($email) {
            self::updateLeadMetrics($email, 'api_hits'); // make sure 'api_hits' exists in leads table if you use this
        }
    }


    protected static function scrubSensitive(array $data): array
    {
        $keys = ['password', 'password_confirmation', 'token', 'access_token', 'refresh_token', 'authorization', 'secret', 'api_key'];
        $lower = array_change_key_case($data, CASE_LOWER);
        foreach ($keys as $k) {
            if (array_key_exists($k, $lower)) {
                $lower[$k] = '[REDACTED]';
            }
        }
        return $lower;
    }

}