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

        if (isSuperAdmin()) {
            return $query;
        }

        $eventIds = getEventIds();

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
}
