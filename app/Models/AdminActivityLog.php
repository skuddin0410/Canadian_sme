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
        if (!$this->module) {
            return 'General';
        }

        $labels = [
            'auth' => 'Authentication',
            'events' => 'Events',
            'attendee_users' => 'Attendees',
            'exhibitor_users' => 'Exhibitors',
            'speakers' => 'Speakers',
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
            'calendar' => 'Calendar',
            'notifications' => 'Notifications',
        ];

        return $labels[$this->module]
            ?? ucwords(str_replace(['-', '_'], ' ', $this->module));
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
        if ($this->module === 'auth') {
            return match ($this->action) {
                'login' => $this->actor_label . ' signed into the admin panel',
                'logout' => $this->actor_label . ' signed out of the admin panel',
                'login_failed' => 'Failed sign-in attempt' . ($this->actor_email ? ' for ' . $this->actor_email : ''),
                default => $this->description,
            };
        }

        $who = $this->actor_label;
        $what = Str::lower($this->action_label);
        $where = $this->module_label;

        return "{$who} {$what} something in {$where}";
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
                    'value' => $this->displayValue($value),
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
                    'value' => $this->displayValue($value),
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
                    $flat[] = $this->humanizeKey((string) $k) . ': ' . $this->displayValue($v);
                } else {
                    $label = is_int($k) ? '' : $this->humanizeKey((string) $k) . ': ';
                    $flat[] = $label . $this->displayValue($v);
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

    private function humanizeKey(string $key): string
    {
        $map = [
            'id' => 'ID',
            'event_id' => 'Event ID',
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
