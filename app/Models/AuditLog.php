<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class AuditLog extends Model
{
    use HasFactory;

    protected static array $eventColumnCache = [];

    protected static array $eventColumnOverrides = [
        \App\Models\Lead::class => 'matched_event_id',
    ];
    
    protected $fillable = [
        'user_id', 'user_type', 'event', 'auditable_type',
        'auditable_id', 'old_values', 'new_values', 'url',
        'ip_address', 'user_agent', 'tags'
    ];
    
    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];
    
    public function user()
    {
        return $this->morphTo();
    }
    
    public function auditable()
    {
        return $this->morphTo();
    }

    public function scopeVisibleTo(Builder $query, ?User $user): Builder
    {
        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->hasRole('Super Admin')) {
            return $query;
        }

        $eventIds = $user->eventAndEntityLinks()
            ->pluck('event_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($eventIds)) {
            return $query->whereRaw('1 = 0');
        }

        $auditableTypes = (clone $query)
            ->select('auditable_type')
            ->distinct()
            ->pluck('auditable_type')
            ->filter();

        if ($auditableTypes->isEmpty()) {
            return $query;
        }

        return $query->where(function (Builder $visibleQuery) use ($auditableTypes, $eventIds) {
            foreach ($auditableTypes as $auditableType) {
                $this->applyEventVisibilityRule($visibleQuery, $auditableType, $eventIds);
            }
        });
    }

    protected function applyEventVisibilityRule(Builder $query, string $auditableType, array $eventIds): void
    {
        if (!class_exists($auditableType)) {
            return;
        }

        $model = new $auditableType();

        if (!$model instanceof Model) {
            return;
        }

        $keyName = $model->getKeyName();

        if ($auditableType === Event::class) {
            $query->orWhere(function (Builder $eventQuery) use ($auditableType, $eventIds) {
                $eventQuery->where('auditable_type', $auditableType)
                    ->whereIn('auditable_id', $eventIds);
            });

            return;
        }

        $eventColumn = $this->resolveEventColumn($auditableType, $model);

        if ($eventColumn) {
            $query->orWhere(function (Builder $eventOwnedQuery) use ($auditableType, $eventColumn, $eventIds, $keyName) {
                $eventOwnedQuery->where('auditable_type', $auditableType)
                    ->whereIn('auditable_id', $auditableType::query()
                        ->whereIn($eventColumn, $eventIds)
                        ->select($keyName));
            });
        }

        if (method_exists($model, 'eventAndEntityLinks')) {
            $entityType = $model->getTable();

            $query->orWhere(function (Builder $mappedEntityQuery) use ($auditableType, $entityType, $eventIds) {
                $mappedEntityQuery->where('auditable_type', $auditableType)
                    ->whereIn('auditable_id', EventAndEntityLink::query()
                        ->where('entity_type', $entityType)
                        ->whereIn('event_id', $eventIds)
                        ->select('entity_id'));
            });
        }
    }

    protected function resolveEventColumn(string $auditableType, Model $model): ?string
    {
        if (array_key_exists($auditableType, static::$eventColumnCache)) {
            return static::$eventColumnCache[$auditableType];
        }

        $eventColumn = static::$eventColumnOverrides[$auditableType] ?? null;

        if (!$eventColumn && Schema::hasColumn($model->getTable(), 'event_id')) {
            $eventColumn = 'event_id';
        }

        static::$eventColumnCache[$auditableType] = $eventColumn;

        return $eventColumn;
    }

    public function getEventLabelAttribute(): string
    {
        return ucfirst((string) $this->event);
    }

    public function getEntityLabelAttribute(): string
    {
        return class_basename((string) $this->auditable_type);
    }

    public function getActorLabelAttribute(): string
    {
        return $this->user?->full_name ?? $this->user?->name ?? 'System';
    }

    public function getSummaryAttribute(): string
    {
        return $this->entity_label . ' #' . $this->auditable_id . ' was ' . $this->event . ' by ' . $this->actor_label;
    }

    public function getEventBadgeClassAttribute(): string
    {
        return match ($this->event) {
            'created' => 'success',
            'updated' => 'primary',
            'deleted' => 'danger',
            default => 'secondary',
        };
    }

    public function getEventIconAttribute(): string
    {
        return match ($this->event) {
            'created' => 'bi-plus-circle',
            'updated' => 'bi-pencil',
            'deleted' => 'bi-trash',
            default => 'bi-info-circle',
        };
    }

    public function getActorInitialsAttribute(): string
    {
        $name = trim((string) $this->actor_label);

        if ($name === '' || $name === 'System') {
            return 'SY';
        }

        $parts = preg_split('/\s+/', $name) ?: [];
        $initials = collect($parts)
            ->filter()
            ->take(2)
            ->map(fn (string $part) => mb_strtoupper(mb_substr($part, 0, 1)))
            ->implode('');

        return $initials !== '' ? $initials : 'NA';
    }

    public function getChangeRowsAttribute(): array
    {
        if ($this->event === 'updated') {
            $oldValues = $this->old_values ?? [];
            $newValues = $this->new_values ?? [];
            $fields = array_unique(array_merge(array_keys($oldValues), array_keys($newValues)));

            return collect($fields)->map(function (string $field) use ($oldValues, $newValues) {
                return [
                    'field' => $field,
                    'field_label' => $this->formatFieldName($field),
                    'old' => $oldValues[$field] ?? null,
                    'new' => $newValues[$field] ?? null,
                ];
            })->values()->all();
        }

        $values = $this->event === 'deleted'
            ? ($this->old_values ?? [])
            : ($this->new_values ?? []);

        return collect($values)->map(function ($value, string $field) {
            return [
                'field' => $field,
                'field_label' => $this->formatFieldName($field),
                'old' => null,
                'new' => $value,
            ];
        })->values()->all();
    }

    public function getChangeCountAttribute(): int
    {
        return count($this->change_rows);
    }

    public function getChangedFieldsSummaryAttribute(): string
    {
        $labels = collect($this->change_rows)
            ->pluck('field_label')
            ->take(3)
            ->implode(', ');

        if ($labels === '') {
            return 'No field details recorded';
        }

        if ($this->change_count > 3) {
            $labels .= ' +' . ($this->change_count - 3) . ' more';
        }

        return $labels;
    }

    public function displayValue(mixed $value): string
    {
        if ($value === null || $value === '') {
            return 'Empty';
        }

        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if (is_array($value)) {
            return json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '[]';
        }

        return (string) $value;
    }

    protected function formatFieldName(string $field): string
    {
        return str_replace('_', ' ', ucfirst($field));
    }

    public static function eventOptions(): array
    {
        return static::query()
            ->select('event')
            ->distinct()
            ->orderBy('event')
            ->pluck('event')
            ->filter()
            ->values()
            ->all();
    }

    public static function typeOptions(?User $user = null): array
    {
        $query = static::query();

        if ($user) {
            $query->visibleTo($user);
        }

        return $query
            ->select('auditable_type')
            ->distinct()
            ->orderBy('auditable_type')
            ->pluck('auditable_type')
            ->filter()
            ->values()
            ->all();
    }
}
