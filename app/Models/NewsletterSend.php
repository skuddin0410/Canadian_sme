<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Auditable;


class NewsletterSend extends Model
{
    //
     use Auditable;
    protected $fillable = [
        'newsletter_id',
        'email',
        'status',
        'sent_at',
        'opened_at',
        'clicked_at',
        'click_data',
        'error_message'
    ];

    protected $casts = [
        'click_data' => 'array',
        'sent_at' => 'datetime',
        'opened_at' => 'datetime',
        'clicked_at' => 'datetime',
    ];

    public function newsletter(): BelongsTo
    {
        return $this->belongsTo(Newsletter::class);
    }

    public function markAsOpened(): void
    {
        if ($this->status !== 'opened') {
            $this->update([
                'status' => 'opened',
                'opened_at' => now()
            ]);
        }
    }

    public function markAsClicked(array $clickData = []): void
    {
        $this->update([
            'status' => 'clicked',
            'clicked_at' => now(),
            'click_data' => $clickData
        ]);
    }
}
