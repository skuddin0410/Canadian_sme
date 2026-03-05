<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Speaker;
use Illuminate\Support\Facades\DB;

class SpeakerAnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function speaker()
    {
        $speakerAnalytics = Speaker::select(
            'speakers.id',
            'speakers.name',
            DB::raw('COUNT(user_agendas.id) as total_attendees')
        )
            ->leftJoin('session_speakers', 'speakers.id', '=', 'session_speakers.speaker_id')
            ->leftJoin('event_sessions', 'session_speakers.session_id', '=', 'event_sessions.id')
            ->leftJoin('user_agendas', 'event_sessions.id', '=', 'user_agendas.session_id')
            ->groupBy('speakers.id', 'speakers.name')
            ->get();

        return view('admin.analytics.speaker', compact('speakerAnalytics'));
    }
}
