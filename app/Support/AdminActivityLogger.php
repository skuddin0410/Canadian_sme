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

    /** Route-name => friendly one-line description */
    private const ROUTE_DESCRIPTIONS = [
        'event-guides.uploadGallery' => 'Added a gallery item',
        'event-guides.reorderGallery' => 'Reordered gallery items',
        'event-guides.approveGalleryItem' => 'Approved a gallery item',
        'event-guides.approveAllGalleryItems' => 'Approved all gallery items',
        'event-guides.deleteGalleryImage' => 'Deleted a gallery image',
        'events.clone' => 'Cloned an event',
        'events.removePhoto' => 'Removed an event photo',
        'events.floor-plan.update' => 'Updated event floor plan',
        'exhibitor-users.approve' => 'Approved an exhibitor',
        'exhibitor-users.assign-booth' => 'Assigned a booth to an exhibitor',
        'attendee-users.allow-access' => 'Allowed attendee access',
        'attendee-users.sendMail' => 'Sent email to an attendee',
        'attendee-users.send-both' => 'Sent email and notification to an attendee',
        'attendee-users.generateBadge' => 'Generated an attendee badge',
        'attendee-users.bulkAction' => 'Ran a bulk action on attendees',
        'speakers.allow-access' => 'Allowed speaker access',
        'speakers.sendMail' => 'Sent email to a speaker',
        'speaker.private-docs.delete' => 'Deleted a speaker private document',
        'admin-users.unblock' => 'Unblocked an admin user',
        'roles.assign.permission' => 'Updated role permissions',
        'user_import' => 'Imported users',
        'sendmail_to_user' => 'Sent a tracked email to a user',
        'user-connections.send-mail' => 'Sent connection email',
        'sponsors.export' => 'Exported sponsors',
        'speaker.export' => 'Exported speakers',
        'exhibitors.export' => 'Exported exhibitors',
    ];

    /** Route-name prefix / exact => module key */
    private const ROUTE_MODULE_MAP = [
        'event-guides.uploadGallery' => 'gallery',
        'event-guides.reorderGallery' => 'gallery',
        'event-guides.approveGalleryItem' => 'gallery',
        'event-guides.approveAllGalleryItems' => 'gallery',
        'event-guides.deleteGalleryImage' => 'gallery',
        'event-guides.showGallery' => 'gallery',
        'speaker.' => 'speakers',
        'speakers.' => 'speakers',
        'sponsors.' => 'sponsors',
        'attendee-users.' => 'attendees',
        'exhibitor-users.' => 'exhibitors',
        'exhibitors.' => 'exhibitors',
        'admin-users.' => 'admin_users',
        'email-templates.' => 'email_templates',
        'ticket-types.' => 'ticket_types',
        'promo-codes.' => 'promo_codes',
        'event-guides.' => 'event_guides',
        'event-tracks.' => 'event_tracks',
        'user-connections.' => 'user_connections',
        'navbar-dynamic.' => 'navbar',
        'admin.navbar-dynamic.' => 'navbar',
        'admin.home-page.' => 'landing_page',
        'admin.upload-image' => 'navbar',
        'calendar.' => 'calendar',
        'polls.' => 'polls',
        'supports.' => 'supports',
        'contact.' => 'supports',
        'subscriptions.' => 'subscriptions',
        'pricing.' => 'pricing',
        'roles.' => 'roles',
        'settings.' => 'settings',
        'events.' => 'events',
        'users.' => 'users',
        'usergroup.' => 'user_groups',
        'pages.' => 'pages',
        'categories.' => 'categories',
        'demo-requests.' => 'demo_requests',
        'booths.' => 'booths',
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

    public static function moduleLabel(string $module): string
    {
        return AdminActivityLog::labelForModule($module);
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
        if (Str::contains($routeName, ['update', 'edit', 'status', 'order', 'bulk', 'reorder'])) {
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
        $routeName = (string) optional($request->route())->getName();
        $path = trim($request->path(), '/');

        // Gallery routes live under /event-guides/gallery but are not "Event Guide"
        if (
            Str::contains($path, 'event-guides/gallery')
            || Str::contains($path, 'delete-gallery-image')
            || Str::contains($routeName, ['uploadGallery', 'reorderGallery', 'approveGallery', 'deleteGallery', 'showGallery'])
        ) {
            return 'gallery';
        }

        if ($routeName !== '') {
            if (isset(self::ROUTE_MODULE_MAP[$routeName])) {
                return self::ROUTE_MODULE_MAP[$routeName];
            }

            foreach (self::ROUTE_MODULE_MAP as $prefix => $module) {
                if (Str::endsWith($prefix, '.') && Str::startsWith($routeName, $prefix)) {
                    return $module;
                }
            }
        }

        if (Str::startsWith($path, 'admin/home-page')) {
            return 'landing_page';
        }

        if (Str::startsWith($path, 'admin/navbar-highlights') || $path === 'admin/upload-image') {
            return 'navbar';
        }

        $segments = explode('/', $path);
        if (($segments[0] ?? null) === 'admin') {
            array_shift($segments);
        }

        $raw = $segments[0] ?? 'admin';

        // Normalize common plurals / aliases
        $aliases = [
            'speaker' => 'speakers',
            'attendee-users' => 'attendees',
            'exhibitor-users' => 'exhibitors',
            'admin-users' => 'admin_users',
            'email-templates' => 'email_templates',
            'ticket-types' => 'ticket_types',
            'promo-codes' => 'promo_codes',
            'event-guides' => 'event_guides',
            'event-tracks' => 'event_tracks',
            'user-connections' => 'user_connections',
            'usergroup' => 'user_groups',
            'demo-requests' => 'demo_requests',
            'navbar-highlights' => 'navbar',
        ];

        if (isset($aliases[$raw])) {
            return $aliases[$raw];
        }

        return Str::of($raw)->replace(['_', '-'], ' ')->slug('_')->toString() ?: 'admin';
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
        $routeName = (string) optional($request->route())->getName();

        if ($routeName !== '' && isset(self::ROUTE_DESCRIPTIONS[$routeName])) {
            return self::ROUTE_DESCRIPTIONS[$routeName];
        }

        return self::friendlyActionPhrase($action, $module);
    }

    public static function friendlyActionPhrase(string $action, string $module): string
    {
        $section = self::moduleLabel($module);
        $singularHints = [
            'speakers' => 'speaker',
            'sponsors' => 'sponsor',
            'attendees' => 'attendee',
            'exhibitors' => 'exhibitor',
            'events' => 'event',
            'users' => 'user',
            'admin_users' => 'admin user',
            'gallery' => 'gallery item',
            'event_guides' => 'event guide',
            'event_tracks' => 'event track',
            'ticket_types' => 'ticket type',
            'promo_codes' => 'promo code',
            'email_templates' => 'email template',
            'polls' => 'poll',
            'pages' => 'page',
            'categories' => 'category',
            'settings' => 'setting',
            'subscriptions' => 'subscription',
            'supports' => 'support request',
            'user_groups' => 'user group',
            'user_connections' => 'user connection',
            'landing_page' => 'landing page content',
            'navbar' => 'navbar item',
            'calendar' => 'calendar item',
            'roles' => 'role',
            'pricing' => 'pricing plan',
            'booths' => 'booth',
            'demo_requests' => 'demo request',
        ];

        $target = $singularHints[$module] ?? Str::lower(Str::singular($section));

        return match ($action) {
            'create' => 'Added a ' . $target,
            'update' => 'Updated a ' . $target,
            'delete' => 'Deleted a ' . $target,
            'approve' => 'Approved a ' . $target,
            'reject' => 'Rejected a ' . $target,
            default => ucfirst(str_replace('_', ' ', $action)) . ' in ' . $section,
        };
    }

    private static function defaultDescription(array $data): string
    {
        $action = $data['action'] ?? 'action';
        $module = $data['module'] ?? 'admin';

        return self::friendlyActionPhrase($action, $module);
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
