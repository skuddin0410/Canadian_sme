<?php

namespace App\Traits;

use App\Support\AuditLogger;

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
        AuditLogger::logModelEvent($model, $event);
    }
}
