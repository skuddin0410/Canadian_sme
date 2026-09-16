<?php

namespace App\Http\Middleware;

use App\Support\AdminActivityLogger;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogAdminActivity
{
    private const SKIP_ROUTE_NAMES = [
        'admin.activity-logs.index',
        'admin.activity-logs.show',
        'notifications.markAsRead',
        'notifications.markAllAsRead',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($this->shouldLog($request, $response)) {
            AdminActivityLogger::logFromRequest($request, $response);
        }

        return $response;
    }

    private function shouldLog(Request $request, Response $response): bool
    {
        if (!in_array(strtoupper($request->method()), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return false;
        }

        if (!$request->is('admin') && !$request->is('admin/*')) {
            return false;
        }

        $routeName = optional($request->route())->getName();
        if ($routeName && in_array($routeName, self::SKIP_ROUTE_NAMES, true)) {
            return false;
        }

        if ($request->is('admin/activity-logs') || $request->is('admin/activity-logs/*')) {
            return false;
        }

        // Skip pure validation / auth failures noise optionally — still log 4xx as attempted actions
        $status = $response->getStatusCode();
        if ($status >= 500) {
            return false;
        }

        return true;
    }
}
