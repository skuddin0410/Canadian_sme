<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait HasTenant
{
    public static function bootHasTenant(): void
    {
        if (auth()->check() && auth()->user()->roles->first()->name == "Admin") {
            return ;
        }
        
        if (auth()->check()){
            $tenantId = auth()->user()->tenant_id;
            static::creating(function (Model $model) use ($tenantId) {
                $model->tenant_id = $tenantId;
            });
       }
    }
}