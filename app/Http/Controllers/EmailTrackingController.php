<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MailLog;
use App\Models\EmailEngagement;
use App\Mail\CustomSpeakerMail;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use  Illuminate\Support\Facades\Log;

class EmailTrackingController extends Controller
{
    public function index(Request $request)
    {
        $baseQuery = MailLog::query()->with('user');
        if (!isSuperAdmin()) {
            $baseQuery->where('send_by', auth()->id());
        }

        if ($request->filled('event_id')) {
            $eventId = (int) $request->event_id;
            if (!isSuperAdmin() && !in_array($eventId, getEventIds())) {
                $eventId = 0;
            }

            $baseQuery->whereHas('user.eventAndEntityLinks', function ($query) use ($eventId) {
                $query->where('event_id', $eventId)
                    ->where('entity_type', 'users');
            });
        }

        if ($request->filled('user_id')) {
            $baseQuery->where('user_id', (int) $request->user_id);
        }

        if ($request->filled('q')) {
            $search = trim($request->q);
            $baseQuery->where(function ($query) use ($search) {
                $query->where('email', 'like', '%' . $search . '%')
                    ->orWhere('subject', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', '%' . $search . '%')
                            ->orWhere('lastname', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%')
                            ->orWhereRaw("CONCAT(name, ' ', lastname) LIKE ?", ["%{$search}%"]);
                    });
            });
        }

        if ($request->filled('opened_status')) {
            if ($request->opened_status === 'opened') {
                $baseQuery->where('opened', true);
            } elseif ($request->opened_status === 'unopened') {
                $baseQuery->where('opened', false);
            }
        }

        if ($request->filled('date_from')) {
            $baseQuery->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $baseQuery->whereDate('created_at', '<=', $request->date_to);
        }

        $totalSent = (clone $baseQuery)->count();

        $totalOpened = (clone $baseQuery)->where('opened', true)->count();

        $totalClicks = (clone $baseQuery)->sum('click_count');

        $openRate = $totalSent > 0 ? round(($totalOpened / $totalSent) * 100, 2) : 0;

        $clickRate = $totalSent > 0 ? round(($totalClicks / $totalSent) * 100, 2) : 0;

        $emailLogs = (clone $baseQuery)
            ->latest()
            ->paginate(15)
            ->appends($request->query());

        $events = isSuperAdmin()
            ? Event::orderBy('title')->get(['id', 'title'])
            : Event::whereIn('id', getEventIds())->orderBy('title')->get(['id', 'title']);

        $filterUserIds = (clone $baseQuery)
            ->whereNotNull('user_id')
            ->distinct()
            ->pluck('user_id');

        $users = User::whereIn('id', $filterUserIds)
            ->orderBy('name')
            ->get(['id', 'name', 'lastname', 'email']);

        return view('admin.analytics.email', compact(
            'totalSent',
            'totalOpened',
            'totalClicks',
            'openRate',
            'clickRate',
            'emailLogs',
            'events',
            'users'
        ));
    }
  public function trackOpen($mailLogId)
{
    $mailLog = MailLog::find($mailLogId);

    if (!$mailLog) {
        return $this->gifResponse();
    }

    $userAgent = request()->userAgent() ?? '';
    $ip        = client_ip();

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
                'ip_address' => client_ip($request),
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
