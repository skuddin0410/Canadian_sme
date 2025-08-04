<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    protected static function bootAuditable()
    {
        static::created(function ($model) {
            self::auditEvent($model, 'created');
        });

        static::updated(function ($model) {
            self::auditEvent($model, 'updated');
        });

        static::deleted(function ($model) {
            self::auditEvent($model, 'deleted');
        });
    }

    protected static function auditEvent($model, $event)
    {
        $user = Auth::user();
        
        $auditData = [
            'user_id' => $user ? $user->id : null,
            'user_type' => $user ? get_class($user) : null,
            'event' => $event,
            'auditable_type' => get_class($model),
            'auditable_id' => $model->id,
            'url' => Request::fullUrl(),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent()
        ];
        
        if ($event === 'created') {
            $auditData['new_values'] = $model->getAttributes();
        } elseif ($event === 'updated') {
            $changes = $model->getChanges();
            $original = array_intersect_key($model->getOriginal(), $changes);
            
            $auditData['old_values'] = $original;
            $auditData['new_values'] = $changes;
        } elseif ($event === 'deleted') {
            $auditData['old_values'] = $model->getAttributes();
        }
        
        AuditLog::create($auditData);
    }
}