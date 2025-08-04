<?php

namespace App\Traits;

trait AutoHtmlDecode
{
    protected static function bootAutoHtmlDecode()
    {
        static::retrieved(function ($model) {
            foreach ($model->getAttributes() as $key => $value) {
                if (is_string($value)) {
                    $model->setAttribute($key, html_entity_decode($value));
                }
            }
        });
    }
}
