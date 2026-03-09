<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Session;
use App\Models\Event;
use App\Models\UserActivity;
use App\Models\FavoriteSession;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function session(Request $request)
    {
        $events = Event::select('id', 'title')->orderBy('title')->get();
        $tracks = Session::select('track')->distinct()->whereNotNull('track')->where('track', '!=', '')->pluck('track');
        return view('admin.analytics.session', compact('events', 'tracks'));
    }

    /**
     * AJAX endpoint: returns attendance (agendas) + popularity (favorites) data
     */
    public function sessionData(Request $request)
    {
        // 1. Fetch Sessions with Filters
        $sessionQuery = Session::query();
        if ($request->filled('event_id')) $sessionQuery->where('event_id', $request->event_id);
        if ($request->filled('track')) $sessionQuery->where('track', $request->track);
        
        $sessions = $sessionQuery->orderBy('title')->get();
        $sessionIds = $sessions->pluck('id')->toArray();

        // 2. Aggregate Data (Favorites & Agendas)
        $dateFrom = $request->filled('date_from') ? Carbon::parse($request->date_from)->startOfDay() : null;
        $dateTo = $request->filled('date_to') ? Carbon::parse($request->date_to)->endOfDay() : null;

        // Count Favorites
        $favCounts = DB::table('favorite_sessions')
            ->whereIn('session_id', $sessionIds)
            ->when($dateFrom, fn($q) => $q->where('created_at', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->where('created_at', '<=', $dateTo))
            ->select('session_id', DB::raw('count(*) as count'))
            ->groupBy('session_id')
            ->pluck('count', 'session_id');

        // Count Agendas (Attendance)
        $agendaCounts = DB::table('user_agendas')
            ->whereIn('session_id', $sessionIds)
            ->when($dateFrom, fn($q) => $q->where('created_at', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->where('created_at', '<=', $dateTo))
            ->select('session_id', DB::raw('count(*) as count'))
            ->groupBy('session_id')
            ->pluck('count', 'session_id');

        // 3. Format Response
        $attendanceData = [];
        $popularityData = [];

        foreach ($sessions as $s) {
            $label = strlen($s->title) > 30 ? substr($s->title, 0, 30) . '...' : $s->title;
            
            $favs = $favCounts[$s->id] ?? 0;
            $agendas = $agendaCounts[$s->id] ?? 0;

            $attendanceData[] = [
                'label' => $label,
                'full_title' => $s->title,
                'count' => $agendas
            ];

            $popularityData[] = [
                'label' => $label,
                'full_title' => $s->title,
                'count' => $favs
            ];
        }

        // Sort both by count descending for better visualization
        usort($attendanceData, fn($a, $b) => $b['count'] <=> $a['count']);
        usort($popularityData, fn($a, $b) => $b['count'] <=> $a['count']);

        return response()->json([
            'attendance' => $attendanceData,
            'popularity' => $popularityData,
            'kpis' => [
                'total_sessions' => count($sessions),
                'total_favorites' => array_sum($favCounts->toArray()),
                'total_agendas' => array_sum($agendaCounts->toArray()),
            ],
        ]);
    }
}
