<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MailLog;
use App\Models\EmailEngagement;
use App\Mail\CustomSpeakerMail;
use Illuminate\Support\Facades\Mail;
use  Illuminate\Support\Facades\Log;

class EmailTrackingController extends Controller
{

    public function index()
    {
        $baseQuery = MailLog::query();
        if (!isSuperAdmin()) {
            $baseQuery->where('send_by', auth()->id());
        }

        $totalSent = (clone $baseQuery)->count();

        $totalOpened = (clone $baseQuery)->where('opened', true)->count();

        $totalClicks = (clone $baseQuery)->sum('click_count');

        $openRate = $totalSent > 0 ? round(($totalOpened / $totalSent) * 100, 2) : 0;

        $clickRate = $totalSent > 0 ? round(($totalClicks / $totalSent) * 100, 2) : 0;

        $emailLogs = (clone $baseQuery)->with('user')
            ->latest()
            ->paginate(15);

        return view('admin.analytics.email', compact(
            'totalSent',
            'totalOpened',
            'totalClicks',
            'openRate',
            'clickRate',
            'emailLogs'
        ));
    }
  public function trackOpen($mailLogId)
{
    $mailLog = MailLog::find($mailLogId);

    if (!$mailLog) {
        return $this->gifResponse();
    }

    $userAgent = request()->userAgent() ?? '';
    $ip        = request()->ip();

    // Gmail prefetch user-agents — do NOT count as real open
    $botAgents = [
        'GoogleImageProxy',
        'Google Image Proxy',
        'YahooMailProxy',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Gmail', // Gmail bot
    ];

    $isBot = false;
    foreach ($botAgents as $bot) {
        if (stripos($userAgent, $bot) !== false) {
            $isBot = true;
            break;
        }
    }

    Log::info('TRACK OPEN', [
        'mail_log_id' => $mailLogId,
        'user_agent'  => $userAgent,
        'ip'          => $ip,
        'is_bot'      => $isBot,
    ]);

    if (!$isBot) {
        // Real human open
        if (!$mailLog->opened) {
            $mailLog->update([
                'opened'    => true,
                'opened_at' => now(),
            ]);
        }

        EmailEngagement::create([
            'mail_log_id' => $mailLogId,
            'user_id'     => $mailLog->user_id,
            'event_type'  => 'opened',
            'ip_address'  => $ip,
            'user_agent'  => $userAgent,
        ]);
    } else {
        // Log Gmail prefetch separately — don't mark as opened
        Log::info('Gmail prefetch detected — not marking as opened', [
            'mail_log_id' => $mailLogId,
        ]);
    }

    return $this->gifResponse();
}

private function gifResponse()
{
    return response(base64_decode(
        'R0lGODlhAQABAIABAP///wAAACwAAAAAAQABAAACAkQBADs='
    ), 200)
        ->header('Content-Type', 'image/gif')
        ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0');
}
    public function trackClick(Request $request, $mailLogId)
    {
        $url = $request->get('url');

        $mailLog = MailLog::find($mailLogId);
        if (!$mailLog->opened) {
            $mailLog->update([
                'opened' => true,
                'opened_at' => now()
            ]);
        }

        if ($mailLog) {

            $mailLog->increment('click_count');

            EmailEngagement::create([
                'mail_log_id' => $mailLogId,
                'user_id' => $mailLog->user_id,
                'event_type' => 'clicked',
                'clicked_url' => $url,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
        }

        // fallback if URL missing
        if (!$url) {
            return redirect('/');
        }

        return redirect()->away($url);
    }
}
