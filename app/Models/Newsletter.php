<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;

class Newsletter extends Model
{
    //
      use HasFactory, Auditable;

    protected $fillable = [
        'subject',
        'content',
        'template_data',
        'template_name',
        'status',
        'scheduled_at',
        'sent_at',
        'total_recipients',
        'sent_count',
        'failed_count',
        'recipient_criteria',
        'created_by'
    ];

    protected $casts = [
        'template_data' => 'array',
        'recipient_criteria' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    // Relationships
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function sends(): HasMany
    {
        return $this->hasMany(NewsletterSend::class);
    }

    public function successfulSends(): HasMany
    {
        return $this->hasMany(NewsletterSend::class)->where('status', 'sent');
    }

    public function opens(): HasMany
    {
        return $this->hasMany(NewsletterSend::class)->where('status', 'opened');
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(NewsletterSend::class)->where('status', 'clicked');
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled')
                    ->where('scheduled_at', '>', now());
    }

    public function scopeReadyToSend($query)
    {   
        return $query->where(function ($query){ 
            $query->where('status', 'scheduled')->where('scheduled_at', '<=', now())
                ->orWhere('status', 'sending'); 
        });

        
    }

    // Accessors
    public function getOpenRateAttribute()
    {
        if ($this->sent_count == 0) return 0;
        return round(($this->opens()->count() / $this->sent_count) * 100, 2);
    }

    public function getClickRateAttribute()
    {
        if ($this->sent_count == 0) return 0;
        return round(($this->clicks()->count() / $this->sent_count) * 100, 2);
    }

    public function getDeliveryRateAttribute()
    {
        if ($this->total_recipients == 0) return 0;
        return round(($this->sent_count / $this->total_recipients) * 100, 2);
    }

    // Methods
    public function canBeSent(): bool
    {
        return in_array($this->status, ['draft', 'scheduled','sending']);
    }

    public function markAsSending(): void
    {
        $this->update(['status' => 'sending']);
    }

    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now()
        ]);
    }

    public function markAsFailed(): void
    {
        $this->update(['status' => 'failed']);
    }

}
