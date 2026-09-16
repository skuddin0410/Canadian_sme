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
            $module = $request->module;

            if ($module === 'gallery') {
                // Include older logs wrongly saved under event_guides
                $query->where(function ($q) {
                    $q->where('module', 'gallery')
                        ->orWhere(function ($inner) {
                            $inner->where('module', 'event_guides')
                                ->where(function ($g) {
                                    $g->where('route_name', 'like', '%Gallery%')
                                        ->orWhere('url', 'like', '%event-guides/gallery%')
                                        ->orWhere('url', 'like', '%delete-gallery-image%');
                                });
                        });
                });
            } elseif (in_array($module, ['speakers', 'speaker'], true)) {
                $query->whereIn('module', ['speakers', 'speaker']);
            } elseif (in_array($module, ['attendees', 'attendee_users'], true)) {
                $query->whereIn('module', ['attendees', 'attendee_users']);
            } elseif (in_array($module, ['exhibitors', 'exhibitor_users'], true)) {
                $query->whereIn('module', ['exhibitors', 'exhibitor_users']);
            } else {
                $query->where('module', $module);
            }
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
        $moduleOptions = (clone $baseVisible)->select('module')->whereNotNull('module')->distinct()->orderBy('module')->pluck('module')
            ->map(function ($module) {
                // Normalize known aliases for the filter dropdown
                return match ($module) {
                    'speaker' => 'speakers',
                    'attendee_users' => 'attendees',
                    'exhibitor_users' => 'exhibitors',
                    default => $module,
                };
            })
            ->unique()
            ->values();

        // Ensure gallery appears even if only old event_guides gallery rows exist
        $hasGallery = AdminActivityLog::query()->visibleTo($user)
            ->where(function ($q) {
                $q->where('module', 'gallery')
                    ->orWhere('route_name', 'like', '%Gallery%')
                    ->orWhere('url', 'like', '%event-guides/gallery%');
            })
            ->exists();
        if ($hasGallery && !$moduleOptions->contains('gallery')) {
            $moduleOptions->push('gallery');
            $moduleOptions = $moduleOptions->sort()->values();
        }

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

        $activityLog->loadMissing(['actor', 'event']);

        return view('admin-activity-logs.show', ['log' => $activityLog]);
    }
}
