<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class AdminActivityLog extends Model
{
    protected $fillable = [
        'actor_id',
        'actor_name',
        'actor_email',
        'action',
        'module',
        'description',
        'method',
        'route_name',
        'url',
        'event_id',
        'subject_type',
        'subject_id',
        'meta',
        'ip_address',
        'user_agent',
        'status_code',
    ];

    protected $casts = [
        'meta' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function getEventLabelAttribute(): string
    {
        if (!$this->event_id) {
            return '—';
        }

        $title = $this->event?->title;

        return $title !== null && $title !== ''
            ? $title
            : 'Unknown event';
    }

    public function scopeVisibleTo(Builder $query, ?User $user = null): Builder
    {
        $user = $user ?: auth()->user();

        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        if (isSuperAdmin() || (int) $user->id === 1) {
            return $query;
        }

        return $query->where('actor_id', $user->id);
    }

    public function getActionBadgeClassAttribute(): string
    {
        return match ($this->action) {
            'login', 'logout' => 'info',
            'login_failed' => 'danger',
            'create', 'store' => 'success',
            'update', 'edit', 'approve', 'allow' => 'primary',
            'delete', 'destroy', 'reject' => 'danger',
            default => 'secondary',
        };
    }

    public function getActionIconAttribute(): string
    {
        return match ($this->action) {
            'login' => 'bi-box-arrow-in-right',
            'logout' => 'bi-box-arrow-right',
            'login_failed' => 'bi-shield-exclamation',
            'create', 'store' => 'bi-plus-circle',
            'update', 'edit', 'approve', 'allow' => 'bi-pencil-square',
            'delete', 'destroy', 'reject' => 'bi-trash',
            default => 'bi-activity',
        };
    }

    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'login' => 'Logged In',
            'logout' => 'Logged Out',
            'login_failed' => 'Login Failed',
            'create' => 'Created',
            'update' => 'Updated',
            'delete' => 'Deleted',
            'approve' => 'Approved',
            'reject' => 'Rejected',
            default => ucwords(str_replace('_', ' ', (string) $this->action)),
        };
    }

    public function getActorLabelAttribute(): string
    {
        if ($this->actor_name) {
            return $this->actor_name;
        }

        return $this->actor?->full_name
            ?? $this->actor?->name
            ?? $this->actor_email
            ?? 'System';
    }

    public function getActorInitialsAttribute(): string
    {
        $parts = preg_split('/\s+/', trim((string) $this->actor_label)) ?: [];
        $initials = collect($parts)
            ->filter()
            ->take(2)
            ->map(fn ($part) => Str::upper(Str::substr($part, 0, 1)))
            ->implode('');

        return $initials !== '' ? $initials : 'SY';
    }

    public function getModuleLabelAttribute(): string
    {
        return self::labelForModule($this->resolved_module);
    }

    public function getResolvedModuleAttribute(): string
    {
        $route = (string) ($this->route_name ?? '');
        $url = (string) ($this->url ?? '');
        $module = (string) ($this->module ?? '');

        // Old gallery actions were stored under event_guides because of URL path
        if (
            Str::contains($route, ['Gallery', 'gallery'])
            || Str::contains($url, 'event-guides/gallery')
            || Str::contains($url, 'delete-gallery-image')
        ) {
            return 'gallery';
        }

        $aliases = [
            'speaker' => 'speakers',
            'attendee_users' => 'attendees',
            'exhibitor_users' => 'exhibitors',
            'event-guides' => 'event_guides',
        ];

        return $aliases[$module] ?? ($module !== '' ? $module : 'admin');
    }

    /**
     * Human-friendly title for lists / dashboard (never shows route names).
     */
    public function getDisplayDescriptionAttribute(): string
    {
        if ($this->module === 'auth' || $this->resolved_module === 'auth') {
            return match ($this->action) {
                'login' => 'Admin logged in',
                'logout' => 'Admin logged out',
                'login_failed' => 'Failed admin login attempt',
                default => $this->cleanStoredDescription(),
            };
        }

        $stored = $this->cleanStoredDescription();

        // Rebuild old technical / awkward phrases like "Create on speaker (speaker.store)"
        if (
            $stored === ''
            || preg_match('/^(Create|Update|Delete|Approve|Reject)\s+on\s+/i', $stored)
            || preg_match('/\([a-z0-9_.\-]+\)$/i', (string) $this->description)
        ) {
            return \App\Support\AdminActivityLogger::friendlyActionPhrase(
                (string) $this->action,
                $this->resolved_module
            );
        }

        return $stored;
    }

    public static function labelForModule(?string $module): string
    {
        if (!$module) {
            return 'General';
        }

        $labels = [
            'auth' => 'Authentication',
            'events' => 'Events',
            'attendees' => 'Attendees',
            'attendee_users' => 'Attendees',
            'exhibitors' => 'Exhibitors',
            'exhibitor_users' => 'Exhibitors',
            'speakers' => 'Speakers',
            'speaker' => 'Speakers',
            'sponsors' => 'Sponsors',
            'email_templates' => 'Email Templates',
            'activity_logs' => 'Activity Logs',
            'admin_users' => 'Admin Users',
            'ticket_types' => 'Ticket Types',
            'promo_codes' => 'Promo Codes',
            'subscriptions' => 'Subscriptions',
            'settings' => 'Settings',
            'polls' => 'Polls',
            'gallery' => 'Gallery',
            'event_guides' => 'Event Guides',
            'event_tracks' => 'Event Tracks',
            'calendar' => 'Calendar',
            'notifications' => 'Notifications',
            'users' => 'Users',
            'user_groups' => 'User Groups',
            'user_connections' => 'User Connections',
            'landing_page' => 'Landing Page',
            'navbar' => 'Navbar',
            'pages' => 'Pages',
            'categories' => 'Categories',
            'roles' => 'Roles & Permissions',
            'supports' => 'Support Requests',
            'pricing' => 'Pricing',
            'booths' => 'Booths',
            'demo_requests' => 'Demo Requests',
            'admin' => 'Admin',
        ];

        return $labels[$module]
            ?? ucwords(str_replace(['-', '_'], ' ', $module));
    }

    public function getStatusLabelAttribute(): string
    {
        $code = (int) ($this->status_code ?? 0);

        if ($this->action === 'login_failed' || ($code >= 400 && $code < 500)) {
            return 'Failed';
        }

        if ($code >= 500) {
            return 'Server Error';
        }

        if ($code >= 300 && $code < 400) {
            return 'Completed (Redirect)';
        }

        if ($code >= 200 || $code === 0) {
            return 'Successful';
        }

        return 'Unknown';
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status_label) {
            'Successful', 'Completed (Redirect)' => 'success',
            'Failed' => 'danger',
            'Server Error' => 'warning',
            default => 'secondary',
        };
    }

    public function getSummaryAttribute(): string
    {
        if ($this->resolved_module === 'auth') {
            return match ($this->action) {
                'login' => $this->actor_label . ' signed into the admin panel',
                'logout' => $this->actor_label . ' signed out of the admin panel',
                'login_failed' => 'Failed sign-in attempt' . ($this->actor_email ? ' for ' . $this->actor_email : ''),
                default => $this->display_description,
            };
        }

        return $this->actor_label . ' · ' . $this->display_description;
    }

    private function cleanStoredDescription(): string
    {
        $desc = trim((string) ($this->description ?? ''));

        // Strip technical route suffix: "Something (sponsors.update)"
        $desc = preg_replace('/\s*\([a-z][a-z0-9_.\-]*\)\s*$/i', '', $desc) ?? $desc;

        return trim($desc);
    }

    public function getBrowserLabelAttribute(): string
    {
        $ua = (string) ($this->user_agent ?? '');

        if ($ua === '') {
            return 'Unknown browser';
        }

        $browser = match (true) {
            Str::contains($ua, 'Edg/') => 'Microsoft Edge',
            Str::contains($ua, 'Chrome/') && !Str::contains($ua, 'Edg/') => 'Chrome',
            Str::contains($ua, 'Firefox/') => 'Firefox',
            Str::contains($ua, 'Safari/') && !Str::contains($ua, 'Chrome/') => 'Safari',
            Str::contains($ua, 'OPR/') || Str::contains($ua, 'Opera') => 'Opera',
            default => 'Unknown browser',
        };

        $os = match (true) {
            Str::contains($ua, 'Windows') => 'Windows',
            Str::contains($ua, 'Android') => 'Android',
            Str::contains($ua, 'iPhone') || Str::contains($ua, 'iPad') => 'iOS',
            Str::contains($ua, 'Mac OS X') || Str::contains($ua, 'Macintosh') => 'macOS',
            Str::contains($ua, 'Linux') => 'Linux',
            default => 'Unknown OS',
        };

        return "{$browser} on {$os}";
    }

    public function getPagePathAttribute(): string
    {
        if (!$this->url) {
            return 'N/A';
        }

        $path = parse_url($this->url, PHP_URL_PATH);

        return $path ?: $this->url;
    }

    /**
     * Flatten meta into readable label/value rows for the detail view.
     */
    public function getDetailRowsAttribute(): array
    {
        $meta = $this->meta ?? [];
        $rows = [];

        $routeParams = $meta['route_params'] ?? [];
        if (is_array($routeParams) && !empty($routeParams)) {
            foreach ($routeParams as $key => $value) {
                $rows[] = [
                    'group' => 'Related Record',
                    'label' => $this->humanizeKey((string) $key),
                    'value' => $this->displayValueForKey((string) $key, $value),
                ];
            }
        }

        $input = $meta['input'] ?? [];
        if (is_array($input) && !empty($input)) {
            foreach ($input as $key => $value) {
                if (in_array(strtolower((string) $key), ['_method', '_token'], true)) {
                    continue;
                }

                $rows[] = [
                    'group' => 'Submitted Data',
                    'label' => $this->humanizeKey((string) $key),
                    'value' => $this->displayValueForKey((string) $key, $value),
                ];
            }
        }

        foreach ($meta as $key => $value) {
            if (in_array($key, ['route_params', 'input'], true)) {
                continue;
            }

            $rows[] = [
                'group' => 'Extra Info',
                'label' => $this->humanizeKey((string) $key),
                'value' => $this->displayValue($value),
            ];
        }

        return $rows;
    }

    public function getGroupedDetailRowsAttribute(): array
    {
        return collect($this->detail_rows)
            ->groupBy('group')
            ->map(fn ($items) => $items->values()->all())
            ->all();
    }

    public function displayValue(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '—';
        }

        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if (is_array($value)) {
            $flat = [];
            foreach ($value as $k => $v) {
                if (is_array($v) || is_object($v)) {
                    $flat[] = $this->humanizeKey((string) $k) . ': ' . $this->displayValueForKey((string) $k, $v);
                } else {
                    $label = is_int($k) ? '' : $this->humanizeKey((string) $k) . ': ';
                    $flat[] = $label . $this->displayValueForKey((string) $k, $v);
                }
            }

            return empty($flat) ? '—' : implode(', ', $flat);
        }

        if (is_object($value)) {
            return method_exists($value, '__toString')
                ? (string) $value
                : class_basename($value);
        }

        $string = (string) $value;

        if (strlen($string) > 300) {
            return Str::limit($string, 300);
        }

        return $string;
    }

    private function displayValueForKey(string $key, mixed $value): string
    {
        $keyLower = strtolower($key);

        if (in_array($keyLower, ['event_id', 'event'], true)) {
            return $this->resolveEventName($value);
        }

        if (is_array($value) && in_array($keyLower, ['event_id', 'event_ids'], true)) {
            $names = [];
            foreach ($value as $item) {
                $names[] = $this->resolveEventName($item);
            }

            return empty($names) ? '—' : implode(', ', $names);
        }

        return $this->displayValue($value);
    }

    private function resolveEventName(mixed $value): string
    {
        if (is_array($value)) {
            $names = [];
            foreach ($value as $item) {
                $names[] = $this->resolveEventName($item);
            }

            return empty($names) ? '—' : implode(', ', $names);
        }

        if (is_object($value) && isset($value->title)) {
            return (string) $value->title;
        }

        if (!is_numeric($value)) {
            return $this->displayValue($value);
        }

        $event = Event::query()->find((int) $value);

        return $event?->title ?: 'Unknown event';
    }

    private function humanizeKey(string $key): string
    {
        $map = [
            'id' => 'ID',
            'event_id' => 'Event',
            'event' => 'Event',
            'user_id' => 'User ID',
            'email' => 'Email',
            'guard' => 'Auth Guard',
            'name' => 'Name',
            'status' => 'Status',
            'title' => 'Title',
            'slug' => 'Slug',
            'phone' => 'Phone',
            'mobile' => 'Mobile',
        ];

        if (isset($map[$key])) {
            return $map[$key];
        }

        return ucwords(str_replace(['_', '-'], ' ', $key));
    }
}
