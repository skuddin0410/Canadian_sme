<?php

namespace App\Support;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    private const DEFAULT_EXCLUDED_ATTRIBUTES = [
        'created_at',
        'updated_at',
        'deleted_at',
        'remember_token',
        'last_activity',
    ];

    public static function logModelEvent(Model $model, string $event): ?AuditLog
    {
        if (!self::shouldAudit($model)) {
            return null;
        }

        $payload = self::buildPayload($model, $event);

        if ($payload === null) {
            return null;
        }

        return AuditLog::create($payload);
    }

    public static function logCustom(Model|string $auditable, int|string|null $auditableId, string $event, array $oldValues = [], array $newValues = [], array $extra = []): AuditLog
    {
        $user = Auth::user();
        $auditableType = $auditable instanceof Model ? $auditable::class : $auditable;
        $auditableKey = $auditable instanceof Model ? $auditable->getKey() : $auditableId;

        return AuditLog::create(array_filter([
            'user_id' => $user?->getKey(),
            'user_type' => $user ? get_class($user) : null,
            'event' => $event,
            'auditable_type' => $auditableType,
            'auditable_id' => $auditableKey,
            'old_values' => $oldValues ?: null,
            'new_values' => $newValues ?: null,
            'url' => Request::fullUrl(),
            'ip_address' => client_ip(),
            'user_agent' => Request::userAgent(),
            'tags' => $extra['tags'] ?? null,
        ], static fn ($value) => $value !== null));
    }

    private static function shouldAudit(Model $model): bool
    {
        return !method_exists($model, 'auditEnabled') || $model->auditEnabled() !== false;
    }

    private static function buildPayload(Model $model, string $event): ?array
    {
        $user = Auth::user();
        $oldValues = null;
        $newValues = null;

        if ($event === 'created') {
            $newValues = self::sanitizeAttributes($model, $model->getAttributes());
        } elseif ($event === 'updated') {
            $changes = self::sanitizeAttributes($model, $model->getChanges());

            if (empty($changes)) {
                return null;
            }

            $oldValues = array_intersect_key(self::sanitizeAttributes($model, $model->getOriginal()), $changes);
            $newValues = $changes;
        } elseif ($event === 'deleted') {
            $oldValues = self::sanitizeAttributes($model, $model->getAttributes());
        }

        return array_filter([
            'user_id' => $user?->getKey(),
            'user_type' => $user ? get_class($user) : null,
            'event' => $event,
            'auditable_type' => $model::class,
            'auditable_id' => $model->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'url' => Request::fullUrl(),
            'ip_address' => client_ip(),
            'user_agent' => Request::userAgent(),
        ], static fn ($value) => $value !== null);
    }

    private static function sanitizeAttributes(Model $model, array $attributes): array
    {
        foreach (self::excludedAttributes($model) as $attribute) {
            unset($attributes[$attribute]);
        }

        return $attributes;
    }

    private static function excludedAttributes(Model $model): array
    {
        $excluded = self::DEFAULT_EXCLUDED_ATTRIBUTES;

        if (method_exists($model, 'auditExclude')) {
            $customExcluded = $model->auditExclude();

            if (is_array($customExcluded)) {
                $excluded = array_merge($excluded, $customExcluded);
            }
        }

        return array_values(array_unique($excluded));
    }
}
