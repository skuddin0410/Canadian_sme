<?php

namespace App\Models;

use App\Models\FormSubmission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Auditable;
use App\Traits\AutoHtmlDecode;

class Form extends Model
{
    //
    protected $fillable = [
        'title',
        'description',
        'form_data',
        'validation_rules',
        'conditional_logic',
        'is_active'
    ];

    protected $casts = [
        'form_data' => 'array',
        'validation_rules' => 'array',
        'conditional_logic' => 'array',
        'is_active' => 'boolean'
    ];

    public function submissions(): HasMany
    {
        return $this->hasMany(FormSubmission::class);
    }
    
}
