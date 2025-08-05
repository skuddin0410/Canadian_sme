<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;
    
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
}