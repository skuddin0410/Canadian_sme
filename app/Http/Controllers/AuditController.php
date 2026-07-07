<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index(Request $request)
    {   
        $user = auth()->user();
        $query = AuditLog::query()->visibleTo($user);
        
        // Filter by event type
        if ($request->has('event') && $request->event != null) {
            $query->where('event', $request->event);
        }
        
        // Filter by auditable type
        if ($request->has('type') && $request->type != null) {
            $query->where('auditable_type', $request->type);
        }
        
        // Filter by date range
        if ($request->has('from') && $request->from != null) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        
        if ($request->has('to') && $request->to != null ) {
            $query->whereDate('created_at', '<=', $request->to);
        }
        
        // Filter by user
        if ($request->has('user_id') && $request->user_id != null ) {
            $query->where('user_id', $request->user_id);
        }

        $logs = $query->with('user')->orderBy('created_at', 'desc')->paginate(15);
        $eventOptions = AuditLog::eventOptions();
        $typeOptions = AuditLog::typeOptions($user);
        
        return view('audit.index', compact('logs', 'eventOptions', 'typeOptions'));
    }
    
    public function show(AuditLog $log)
    {
        abort_unless(
            AuditLog::query()
                ->whereKey($log->id)
                ->visibleTo(auth()->user())
                ->exists(),
            403
        );

        $log->loadMissing('user');

        return view('audit.show', compact('log'));
    }
    
    public function entityLogs(Request $request, $entityType, $entityId)
    {
        $logs = AuditLog::query()
            ->where('auditable_type', $entityType)
            ->where('auditable_id', $entityId)
            ->visibleTo(auth()->user())
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
                      
        return view('audit.entity-logs', compact('logs', 'entityType', 'entityId'));
    }
}
