<?php

namespace App\Support;

use App\Models\AdminActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AdminActivityLogger
{
    private const SENSITIVE_KEYS = [
        'password',
        'password_confirmation',
        'current_password',
        'new_password',
        'token',
        '_token',
        'remember',
        'otp',
        'code',
        'secret',
        'api_key',
        'authorization',
    ];

    private const ADMIN_ROLES = [
        'Admin',
        'Super Admin',
        'Exhibitor',
        'Representative',
        'Speaker',
        'Support Staff Or Helpdesk',
        'Registration Desk',
    ];

    public static function log(array $data): ?AdminActivityLog
    {
        try {
            $actor = $data['actor'] ?? Auth::user();
            unset($data['actor']);

            if ($actor instanceof User) {
                $data['actor_id'] = $data['actor_id'] ?? $actor->id;
                $data['actor_name'] = $data['actor_name'] ?? ($actor->full_name ?? $actor->name);
                $data['actor_email'] = $data['actor_email'] ?? $actor->email;
            }

            $data['ip_address'] = $data['ip_address'] ?? client_ip();
            $data['user_agent'] = $data['user_agent'] ?? request()?->userAgent();
            $data['url'] = $data['url'] ?? request()?->fullUrl();
            $data['method'] = $data['method'] ?? request()?->method();
            $data['route_name'] = $data['route_name'] ?? optional(request()?->route())->getName();

            if (!isset($data['description']) || $data['description'] === '') {
                $data['description'] = self::defaultDescription($data);
            }

            return AdminActivityLog::create($data);
        } catch (\Throwable $e) {
            report($e);

            return null;
        }
    }

    public static function logAuth(string $action, ?User $user = null, array $extra = []): ?AdminActivityLog
    {
        $descriptions = [
            'login' => 'Admin logged in',
            'logout' => 'Admin logged out',
            'login_failed' => 'Failed admin login attempt',
        ];

        return self::log(array_merge([
            'actor' => $user,
            'actor_id' => $user?->id,
            'actor_name' => $user?->full_name ?? $user?->name,
            'actor_email' => $user?->email ?? ($extra['email'] ?? null),
            'action' => $action,
            'module' => 'auth',
            'description' => $extra['description'] ?? ($descriptions[$action] ?? ucfirst($action)),
            'meta' => $extra['meta'] ?? null,
            'status_code' => $extra['status_code'] ?? null,
        ], $extra));
    }

    public static function logFromRequest(Request $request, Response $response): ?AdminActivityLog
    {
        $user = $request->user();
        if (!$user || !self::isAdminPanelUser($user)) {
            return null;
        }

        $action = self::resolveAction($request);
        $module = self::resolveModule($request);
        $routeName = optional($request->route())->getName();

        return self::log([
            'actor' => $user,
            'action' => $action,
            'module' => $module,
            'description' => self::buildRequestDescription($request, $action, $module),
            'method' => $request->method(),
            'route_name' => $routeName,
            'url' => $request->fullUrl(),
            'event_id' => self::resolveEventId($request),
            'meta' => [
                'route_params' => self::sanitize($request->route()?->parameters() ?? []),
                'input' => self::sanitize($request->except(self::SENSITIVE_KEYS)),
            ],
            'status_code' => $response->getStatusCode(),
        ]);
    }

    public static function isAdminPanelUser(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        if ((int) $user->id === 1) {
            return true;
        }

        return method_exists($user, 'hasAnyRole') && $user->hasAnyRole(self::ADMIN_ROLES);
    }

    public static function isAdminAuthRequest(?Request $request = null): bool
    {
        $request = $request ?: request();

        return $request->is('admin/login')
            || $request->is('admin')
            || $request->routeIs('admin.login*')
            || $request->is('admin/*');
    }

    private static function resolveAction(Request $request): string
    {
        $routeName = (string) optional($request->route())->getName();
        $method = strtoupper($request->method());

        if (Str::contains($routeName, ['approve', 'allow'])) {
            return 'approve';
        }
        if (Str::contains($routeName, ['reject', 'deny', 'block'])) {
            return 'reject';
        }
        if (Str::contains($routeName, ['destroy', 'delete', 'remove'])) {
            return 'delete';
        }
        if (Str::contains($routeName, ['store', 'create', 'clone', 'import', 'upload'])) {
            return 'create';
        }
        if (Str::contains($routeName, ['update', 'edit', 'status', 'order', 'bulk'])) {
            return 'update';
        }

        return match ($method) {
            'POST' => 'create',
            'PUT', 'PATCH' => 'update',
            'DELETE' => 'delete',
            default => strtolower($method),
        };
    }

    private static function resolveModule(Request $request): string
    {
        $path = trim($request->path(), '/');
        $segments = explode('/', $path);

        if (($segments[0] ?? null) === 'admin') {
            array_shift($segments);
        }

        $module = $segments[0] ?? 'admin';

        return Str::of($module)->replace(['_', '-'], ' ')->slug('_')->toString() ?: 'admin';
    }

    private static function resolveEventId(Request $request): ?int
    {
        $candidates = [
            $request->route('event'),
            $request->route('event_id'),
            $request->input('event_id'),
            $request->input('event'),
        ];

        foreach ($candidates as $candidate) {
            if (is_object($candidate) && isset($candidate->id)) {
                return (int) $candidate->id;
            }
            if (is_numeric($candidate)) {
                return (int) $candidate;
            }
        }

        return null;
    }

    private static function buildRequestDescription(Request $request, string $action, string $module): string
    {
        $routeName = optional($request->route())->getName();
        $label = ucfirst($action) . ' on ' . str_replace('_', ' ', $module);

        if ($routeName) {
            $label .= ' (' . $routeName . ')';
        }

        return $label;
    }

    private static function defaultDescription(array $data): string
    {
        $action = $data['action'] ?? 'action';
        $module = $data['module'] ?? 'admin';

        return ucfirst(str_replace('_', ' ', $action)) . ' · ' . str_replace('_', ' ', $module);
    }

    private static function sanitize(array $data): array
    {
        $clean = [];

        foreach ($data as $key => $value) {
            $keyLower = strtolower((string) $key);

            if (in_array($keyLower, self::SENSITIVE_KEYS, true) || Str::contains($keyLower, ['password', 'token', 'secret'])) {
                $clean[$key] = '[redacted]';
                continue;
            }

            if (is_array($value)) {
                $clean[$key] = self::sanitize($value);
                continue;
            }

            if (is_object($value)) {
                if (method_exists($value, 'getKey')) {
                    $clean[$key] = $value->getKey();
                } else {
                    $clean[$key] = class_basename($value);
                }
                continue;
            }

            if (is_string($value) && strlen($value) > 500) {
                $clean[$key] = Str::limit($value, 500);
                continue;
            }

            $clean[$key] = $value;
        }

        return $clean;
    }
}
