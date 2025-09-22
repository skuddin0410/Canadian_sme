<?php

namespace App\Services;

use App\Models\UserActivity;
use App\Models\Lead;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Cache;

class ActivityTrackingService
{   
    private const INACTIVITY_WINDOW_SECONDS = 300;

    private static function lastSeenKey(string $sessionId, string $pageUrl): string
    {
        return 'ua:last_seen:' . $sessionId . ':' . sha1($pageUrl);
    }

    public static function trackPageView($pageUrl, $pageTitle = null, $email = null)
    {
        $sessionId = session()->getId();
        $user      = Auth::user();
        $userId    = $user?->getAuthIdentifier();
        $email     = $email ?? $user?->email;

        UserActivity::create([
            'session_id'   => $sessionId,
            'user_id'      => $userId,
            'email'        => $email,
            'activity_type'=> 'page_view',
            'page_url'     => $pageUrl,
            'page_title'   => $pageTitle,
            'ip_address'   => Request::ip(),
            'user_agent'   => Request::userAgent(),
            'source'=>'website',
            'activity_at'  => now(),
        ]);

        if ($email) {
            self::updateLeadMetrics($email, 'page_views');
        }
    }

    /**
     * Add time spent (in seconds) to the most recent activity for this URL.
     * Will attach to last 'page_view' or fall back to last 'api_hit'.
     */
  public static function trackTimeOnSite($timeSpent, $pageUrl, $email = null): void
{
    $sessionId = session()->getId();
    $user      = Auth::user();
    $userId    = $user?->getAuthIdentifier();
    $email     = $email ?? $user?->email;

    // --- Auto-calc delta when $timeSpent is null ---
    $delta = null;
    if ($timeSpent === null) {
        $nowTs = now()->timestamp;
        $key   = self::lastSeenKey($sessionId, $pageUrl);
        $last  = Cache::get($key);

        $delta = $last ? ($nowTs - (int)$last) : 0;

        // Cap: ignore long gaps beyond inactivity window (treat as new session)
        if ($delta < 0 || $delta > self::INACTIVITY_WINDOW_SECONDS) {
            $delta = 0;
        }

        // Update last-seen timestamp (keep for 24h)
        Cache::put($key, $nowTs, now()->addDay());
    } else {
        $delta = (int) max(0, round($timeSpent));
    }

    if ($delta === 0) {
        return; // nothing to add
    }

    // Prefer the latest page_view; else fall back to latest api_hit for this URL
    $activity = UserActivity::where('session_id', $sessionId)
        ->where('page_url', $pageUrl)
        ->whereIn('activity_type', ['page_view', 'api_hit'])
        ->latest('activity_at')
        ->first();

    if ($activity) {
        // Increment time_spent (seconds)
        $activity->increment('time_spent', $delta);
        // Optionally, update activity_at so the "latest" reflects this touch:
        // $activity->update(['activity_at' => now()]);
    } else {
        // No prior record: create a minimal page_view as a container for time
        UserActivity::create([
            'session_id'   => $sessionId,
            'user_id'      => $userId,
            'email'        => $email,
            'activity_type'=> 'page_view',
            'page_url'     => $pageUrl,
            'ip_address'   => Request::ip(),
            'user_agent'   => Request::userAgent(),
            'time_spent'   => $delta,
            'source'=>'website',
            'activity_at'  => now(),
        ]);
    }

    if ($email) {
        self::updateLeadTimeOnSite($email, $delta); // converts to minutes inside
    }
}

    public static function trackEmailOpen($email, $campaignId = null)
    {
        UserActivity::create([
            'email'         => $email,
            'activity_type' => 'email_open',
            'metadata'      => json_encode(['campaign_id' => $campaignId]),
            'activity_at'   => now(),
        ]);

        self::updateLeadMetrics($email, 'email_opens');
    }

    public static function trackDownload($fileName, $fileUrl, $email = null)
    {
        $sessionId = session()->getId();
        $user      = Auth::user();
        $userId    = $user?->getAuthIdentifier();
        $email     = $email ?? $user?->email;

        UserActivity::create([
            'session_id'   => $sessionId,
            'user_id'      => $userId,
            'email'        => $email,
            'activity_type'=> 'download',
            'page_url'     => $fileUrl,
            'metadata'     => json_encode(['file_name' => $fileName]),
            'ip_address'   => Request::ip(),
            'user_agent'   => Request::userAgent(),
            'source'=>'website',
            'activity_at'  => now(),
        ]);

        if ($email) {
            self::updateLeadMetrics($email, 'downloads');
        }
    }

    public static function trackFormSubmission($formType, $email, $formData = [])
    {
        $sessionId = session()->getId();
        $user      = Auth::user();
        $userId    = $user?->getAuthIdentifier();

        UserActivity::create([
            'session_id'   => $sessionId,
            'user_id'      => $userId,
            'email'        => $email,
            'activity_type'=> 'form_submission',
            'metadata'     => json_encode([
                'form_type' => $formType,
                'form_data' => $formData,
            ]),
            'ip_address'   => Request::ip(),
            'user_agent'   => Request::userAgent(),
            'activity_at'  => now(),
        ]);

        self::updateLeadMetrics($email, 'form_submissions');
    }

    // Avoid parameter shadowing; use $eventSessionId as the entity you're inquiring about
    public static function trackSessionInquiry($eventSessionId, $email, $inquiryType = 'general')
    {
        $sessionId = session()->getId();
        $user      = Auth::user();
        $userId    = $user?->getAuthIdentifier();

        UserActivity::create([
            'session_id'   => $sessionId,
            'user_id'      => $userId,
            'email'        => $email,
            'activity_type'=> 'session_inquiry',
            'metadata'     => json_encode([
                'event_session_id' => $eventSessionId,
                'inquiry_type'     => $inquiryType,
            ]),
            'ip_address'   => Request::ip(),
            'user_agent'   => Request::userAgent(),
            'source'=>'website',
            'activity_at'  => now(),
        ]);

        // rename metric key if your leads table uses something else
        self::updateLeadMetrics($email, 'session_inquiries');
    }

    private static function updateLeadMetrics($email, $metricType)
    {
        if (empty($email)) return;

        $user = Auth::user();

        $lead = Lead::where('email', $email)->first();
        if ($lead) {
            // make sure column exists in DB, or guard elsewhere
            $lead->increment($metricType);
            $lead->update(['last_activity_at' => now()]);
        } else {
            $lead = Lead::create([
                'first_name'       => $user?->name ?? '',
                'last_name'        => $user?->lastname ?? ($user?->last_name ?? ''),
                'email'            => $email,
                'phone'            => $user->mobile ?? '',
                $metricType        => 1,
                'last_activity_at' => now(),
            ]);
        }
    }

    private static function updateLeadTimeOnSite($email, $timeSpentSeconds)
    {
        $lead = Lead::where('email', $email)->first();
        $minutes = max(0, (int) ceil($timeSpentSeconds / 60));

        if ($lead) {
            if ($minutes > 0) {
                $lead->increment('time_on_site', $minutes);
            }
            $lead->update(['last_activity_at' => now()]);
        } else {
            $user = Auth::user();
            $lead = Lead::create([
                'first_name'       => $user?->name ?? '',
                'last_name'        => $user?->lastname ?? ($user?->last_name ?? ''),
                'email'            => $email ?? $user?->email,
                'phone'            => $user->mobile ?? '',
                'time_on_site'     => $minutes,
                'last_activity_at' => now(),
            ]);
        }
    }

    // Aggregate metrics for a specific lead
    public static function getLeadMetrics($email)
    {
        $activities = UserActivity::where('email', $email)->get();

        return [
            'page_views'           => $activities->where('activity_type', 'page_view')->count(),
            // Sum time from both web and api activities
            'total_time_on_site'   => $activities
                                        ->whereIn('activity_type', ['page_view', 'api_hit'])
                                        ->sum('time_spent'),
            'email_opens'          => $activities->where('activity_type', 'email_open')->count(),
            'downloads'            => $activities->where('activity_type', 'download')->count(),
            'form_submissions'     => $activities->where('activity_type', 'form_submission')->count(),
            'session_inquiries'    => $activities->where('activity_type', 'session_inquiry')->count(),
            'unique_pages_visited' => $activities->where('activity_type', 'page_view')->pluck('page_url')->unique()->count(),
            'last_activity'        => $activities->max('activity_at'),
        ];
    }

    public static function trackApiHit(string $endpoint, array $input = [], ?string $email = null, ?int $status = null, string $method = 'GET'): void
    {
        $sessionId = session()->getId();
        $user      = Auth::user();
        $userId    = $user?->getAuthIdentifier();
        $email     = $email ?? $user?->email;

        $scrubbed = self::scrubSensitive($input);

        UserActivity::create([
            'session_id'   => $sessionId,
            'user_id'      => $userId,
            'email'        => $email,
            'activity_type'=> 'page_view',
            'page_url'     => $endpoint,
            'metadata'     => json_encode([
                'method' => $method,
                'status' => $status,
                'input'  => $scrubbed,
            ]),
            'ip_address'   => Request::ip(),
            'user_agent'   => Request::userAgent(),
            'activity_at'  => now(),
        ]);

        // Optional: maintain an 'api_hits' counter column on leads if you added it
        // if ($email) self::updateLeadMetrics($email, 'api_hits');
    }

    protected static function scrubSensitive(array $data): array
    {
        $keys  = ['password','password_confirmation','token','access_token','refresh_token','authorization','secret','api_key','otp'];
        $lower = array_change_key_case($data, CASE_LOWER);
        foreach ($keys as $k) {
            if (array_key_exists($k, $lower)) {
                $lower[$k] = '[REDACTED]';
            }
        }
        return $lower;
    }
}
