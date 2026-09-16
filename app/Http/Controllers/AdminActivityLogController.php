<?php

namespace App\Http\Controllers;

use App\Models\AdminActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class AdminActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = AdminActivityLog::query()->visibleTo($user)->with('actor');

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        if (isSuperAdmin() && $request->filled('actor_id')) {
            $query->where('actor_id', $request->actor_id);
        }

        if ($request->filled('q')) {
            $term = trim($request->q);
            $query->where(function ($q) use ($term) {
                $q->where('description', 'like', "%{$term}%")
                    ->orWhere('actor_name', 'like', "%{$term}%")
                    ->orWhere('actor_email', 'like', "%{$term}%")
                    ->orWhere('module', 'like', "%{$term}%")
                    ->orWhere('route_name', 'like', "%{$term}%");
            });
        }

        $logs = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        $baseVisible = AdminActivityLog::query()->visibleTo($user);
        $actionOptions = (clone $baseVisible)->select('action')->distinct()->orderBy('action')->pluck('action');
        $moduleOptions = (clone $baseVisible)->select('module')->whereNotNull('module')->distinct()->orderBy('module')->pluck('module');

        $actors = collect();
        if (isSuperAdmin()) {
            $actorIds = AdminActivityLog::query()->whereNotNull('actor_id')->distinct()->pluck('actor_id');
            $actors = User::whereIn('id', $actorIds)->orderBy('name')->get(['id', 'name', 'lastname', 'email']);
        }

        return view('admin-activity-logs.index', compact('logs', 'actionOptions', 'moduleOptions', 'actors'));
    }

    public function show(AdminActivityLog $activityLog)
    {
        abort_unless(
            AdminActivityLog::query()
                ->whereKey($activityLog->id)
                ->visibleTo(auth()->user())
                ->exists(),
            403
        );

        $activityLog->loadMissing('actor');

        return view('admin-activity-logs.show', ['log' => $activityLog]);
    }
}
