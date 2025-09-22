<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use App\Traits\Auditable;

class UserActivity extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'session_id',
        'user_id',
        'email',
        'activity_type',
        'page_url',
        'page_title',
        'time_spent',
        'metadata',
        'ip_address',
        'user_agent',
        'activity_at'
    ];

    protected $casts = [
        'metadata' => 'array',
        'activity_at' => 'datetime',
        'time_spent' => 'integer',
    ];

    protected $dates = [
        'activity_at',
        'created_at',
        'updated_at'
    ];

    // Activity type constants
    const TYPE_PAGE_VIEW = 'page_view';
    const TYPE_EMAIL_OPEN = 'email_open';
    const TYPE_DOWNLOAD = 'download';
    const TYPE_FORM_SUBMISSION = 'form_submission';
    const TYPE_PROPERTY_INQUIRY = 'property_inquiry';
    const TYPE_NEWSLETTER_SIGNUP = 'newsletter_signup';
    const TYPE_PHONE_CALL = 'phone_call';
    const TYPE_CHAT_MESSAGE = 'chat_message';
    const TYPE_VIDEO_VIEW = 'video_view';
    const TYPE_DOCUMENT_VIEW = 'document_view';

    // Available activity types
    public static function getActivityTypes(): array
    {
        return [
            self::TYPE_PAGE_VIEW => 'Page View',
            self::TYPE_EMAIL_OPEN => 'Email Open',
            self::TYPE_DOWNLOAD => 'Download',
            self::TYPE_FORM_SUBMISSION => 'Form Submission',
            self::TYPE_PROPERTY_INQUIRY => 'Property Inquiry',
            self::TYPE_NEWSLETTER_SIGNUP => 'Newsletter Signup',
            self::TYPE_PHONE_CALL => 'Phone Call',
            self::TYPE_CHAT_MESSAGE => 'Chat Message',
            self::TYPE_VIDEO_VIEW => 'Video View',
            self::TYPE_DOCUMENT_VIEW => 'Document View',
        ];
    }

    /**
     * Relationships
     */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'email', 'email');
    }

    // If you have a Property model for property inquiries
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'property_id', 'id')
                    ->when($this->activity_type === self::TYPE_PROPERTY_INQUIRY, function ($query) {
                        return $query->whereJsonContains('metadata->property_id', $this->getPropertyIdFromMetadata());
                    });
    }

    /**
     * Scopes
     */

    public function scopeByEmail(Builder $query, string $email): Builder
    {
        return $query->where('email', $email);
    }

    public function scopeByUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeBySession(Builder $query, string $sessionId): Builder
    {
        return $query->where('session_id', $sessionId);
    }

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('activity_type', $type);
    }

    public function scopePageViews(Builder $query): Builder
    {
        return $query->where('activity_type', self::TYPE_PAGE_VIEW);
    }

    public function scopeEmailOpens(Builder $query): Builder
    {
        return $query->where('activity_type', self::TYPE_EMAIL_OPEN);
    }

    public function scopeDownloads(Builder $query): Builder
    {
        return $query->where('activity_type', self::TYPE_DOWNLOAD);
    }

    public function scopeFormSubmissions(Builder $query): Builder
    {
        return $query->where('activity_type', self::TYPE_FORM_SUBMISSION);
    }

    public function scopePropertyInquiries(Builder $query): Builder
    {
        return $query->where('activity_type', self::TYPE_PROPERTY_INQUIRY);
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('activity_at', today());
    }

    public function scopeThisWeek(Builder $query): Builder
    {
        return $query->whereBetween('activity_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereMonth('activity_at', now()->month)
                    ->whereYear('activity_at', now()->year);
    }

    public function scopeRecent(Builder $query, int $days = 30): Builder
    {
        return $query->where('activity_at', '>=', now()->subDays($days));
    }

    public function scopeWithTimeSpent(Builder $query, int $minimumSeconds = 5): Builder
    {
        return $query->where('time_spent', '>=', $minimumSeconds);
    }

    /**
     * Accessors & Mutators
     */

    public function getActivityTypeNameAttribute(): string
    {
        return self::getActivityTypes()[$this->activity_type] ?? 'Unknown';
    }

    public function getTimeSpentHumanAttribute(): string
    {
        if ($this->time_spent < 60) {
            return $this->time_spent . ' seconds';
        } elseif ($this->time_spent < 3600) {
            return round($this->time_spent / 60, 1) . ' minutes';
        } else {
            return round($this->time_spent / 3600, 1) . ' hours';
        }
    }

    public function getIsRecentAttribute(): bool
    {
        return $this->activity_at->isAfter(now()->subHours(24));
    }

    public function getPageDomainAttribute(): ?string
    {
        if (!$this->page_url) {
            return null;
        }
        
        return parse_url($this->page_url, PHP_URL_HOST);
    }

    public function getPagePathAttribute(): ?string
    {
        if (!$this->page_url) {
            return null;
        }
        
        return parse_url($this->page_url, PHP_URL_PATH);
    }

    /**
     * Helper Methods
     */

    public function getPropertyIdFromMetadata(): ?int
    {
        return $this->metadata['property_id'] ?? null;
    }

    public function getCampaignIdFromMetadata(): ?string
    {
        return $this->metadata['campaign_id'] ?? null;
    }

    public function getFormTypeFromMetadata(): ?string
    {
        return $this->metadata['form_type'] ?? null;
    }

    public function getFileNameFromMetadata(): ?string
    {
        return $this->metadata['file_name'] ?? null;
    }

    public function isEngagement(): bool
    {
        return in_array($this->activity_type, [
            self::TYPE_FORM_SUBMISSION,
            self::TYPE_PROPERTY_INQUIRY,
            self::TYPE_DOWNLOAD,
            self::TYPE_PHONE_CALL,
            self::TYPE_CHAT_MESSAGE
        ]);
    }

    public function isHighValue(): bool
    {
        return in_array($this->activity_type, [
            self::TYPE_PROPERTY_INQUIRY,
            self::TYPE_PHONE_CALL,
            self::TYPE_FORM_SUBMISSION
        ]) || ($this->activity_type === self::TYPE_PAGE_VIEW && $this->time_spent > 300); // 5+ minutes
    }

    /**
     * Static Methods for Analytics
     */

    public static function getEngagementSummary(string $email): array
    {
        $activities = self::byEmail($email)->get();
        
        return [
            'total_activities' => $activities->count(),
            'page_views' => $activities->where('activity_type', self::TYPE_PAGE_VIEW)->count(),
            'unique_pages' => $activities->where('activity_type', self::TYPE_PAGE_VIEW)->pluck('page_url')->unique()->count(),
            'total_time_spent' => $activities->sum('time_spent'),
            'average_session_time' => $activities->where('activity_type', self::TYPE_PAGE_VIEW)->avg('time_spent'),
            'email_opens' => $activities->where('activity_type', self::TYPE_EMAIL_OPEN)->count(),
            'downloads' => $activities->where('activity_type', self::TYPE_DOWNLOAD)->count(),
            'form_submissions' => $activities->where('activity_type', self::TYPE_FORM_SUBMISSION)->count(),
            'property_inquiries' => $activities->where('activity_type', self::TYPE_PROPERTY_INQUIRY)->count(),
            'engagement_score' => self::calculateEngagementScore($activities),
            'last_activity' => $activities->max('activity_at'),
            'first_activity' => $activities->min('activity_at'),
            'is_active_user' => $activities->where('activity_at', '>', now()->subDays(7))->count() > 0,
            'most_viewed_pages' => self::getMostViewedPages($activities),
            'activity_by_day' => self::getActivityByDay($activities)
        ];
    }

    public static function calculateEngagementScore($activities): int
    {
        $score = 0;
        
        foreach ($activities as $activity) {
            switch ($activity->activity_type) {
                case self::TYPE_PAGE_VIEW:
                    $score += 1;
                    if ($activity->time_spent > 60) $score += 1; // Bonus for time spent
                    if ($activity->time_spent > 300) $score += 2; // Extra bonus for 5+ minutes
                    break;
                case self::TYPE_EMAIL_OPEN:
                    $score += 3;
                    break;
                case self::TYPE_DOWNLOAD:
                    $score += 5;
                    break;
                case self::TYPE_FORM_SUBMISSION:
                    $score += 10;
                    break;
                case self::TYPE_PROPERTY_INQUIRY:
                    $score += 15;
                    break;
                case self::TYPE_PHONE_CALL:
                    $score += 20;
                    break;
                case self::TYPE_CHAT_MESSAGE:
                    $score += 8;
                    break;
                default:
                    $score += 2;
            }
        }
        
        return min($score, 100); // Cap at 100
    }

    private static function getMostViewedPages($activities): array
    {
        return $activities->where('activity_type', self::TYPE_PAGE_VIEW)
                         ->groupBy('page_url')
                         ->map(function ($group) {
                             return [
                                 'url' => $group->first()->page_url,
                                 'title' => $group->first()->page_title,
                                 'views' => $group->count(),
                                 'total_time' => $group->sum('time_spent')
                             ];
                         })
                         ->sortByDesc('views')
                         ->take(5)
                         ->values()
                         ->toArray();
    }

    private static function getActivityByDay($activities): array
    {
        return $activities->groupBy(function ($activity) {
                             return $activity->activity_at->format('Y-m-d');
                         })
                         ->map(function ($group, $date) {
                             return [
                                 'date' => $date,
                                 'total_activities' => $group->count(),
                                 'page_views' => $group->where('activity_type', self::TYPE_PAGE_VIEW)->count(),
                                 'engagements' => $group->filter(function ($activity) {
                                     return $activity->isEngagement();
                                 })->count()
                             ];
                         })
                         ->values()
                         ->toArray();
    }

    /**
     * Bulk operations
     */

    public static function createPageView(array $data): self
    {
        return self::create(array_merge($data, [
            'activity_type' => self::TYPE_PAGE_VIEW,
            'activity_at' => now()
        ]));
    }

    public static function createEmailOpen(string $email, string $campaignId = null): self
    {
        return self::create([
            'email' => $email,
            'activity_type' => self::TYPE_EMAIL_OPEN,
            'metadata' => ['campaign_id' => $campaignId],
            'activity_at' => now()
        ]);
    }

    public static function createDownload(array $data): self
    {
        return self::create(array_merge($data, [
            'activity_type' => self::TYPE_DOWNLOAD,
            'activity_at' => now()
        ]));
    }

    public static function createFormSubmission(array $data): self
    {
        return self::create(array_merge($data, [
            'activity_type' => self::TYPE_FORM_SUBMISSION,
            'activity_at' => now()
        ]));
    }

    public static function createPropertyInquiry(array $data): self
    {
        return self::create(array_merge($data, [
            'activity_type' => self::TYPE_PROPERTY_INQUIRY,
            'activity_at' => now()
        ]));
    }

    /**
     * Cleanup methods
     */

    public static function cleanupOldActivities(int $daysToKeep = 365): int
    {
        return self::where('activity_at', '<', now()->subDays($daysToKeep))->delete();
    }

    public static function anonymizeOldActivities(int $daysToAnonymize = 90): int
    {
        return self::where('activity_at', '<', now()->subDays($daysToAnonymize))
                   ->update([
                       'email' => null,
                       'user_id' => null,
                       'ip_address' => null,
                       'user_agent' => 'anonymized'
                   ]);
    }

    /**
     * Search and filtering
     */

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('email', 'like', "%{$search}%")
              ->orWhere('page_url', 'like', "%{$search}%")
              ->orWhere('page_title', 'like', "%{$search}%")
              ->orWhere('ip_address', 'like', "%{$search}%");
        });
    }

    public function scopeFilterByDateRange(Builder $query, string $startDate, string $endDate): Builder
    {
        return $query->whereBetween('activity_at', [
            Carbon::parse($startDate)->startOfDay(),
            Carbon::parse($endDate)->endOfDay()
        ]);
    }

    public function scopeFilterByTypes(Builder $query, array $types): Builder
    {
        return $query->whereIn('activity_type', $types);
    }

    /**
     * Export methods
     */

    public function toAnalyticsArray(): array
    {
        return [
            'id' => $this->id,
            'session_id' => $this->session_id,
            'user_id' => $this->user_id,
            'email' => $this->email,
            'activity_type' => $this->activity_type,
            'activity_type_name' => $this->activity_type_name,
            'page_url' => $this->page_url,
            'page_title' => $this->page_title,
            'time_spent' => $this->time_spent,
            'time_spent_human' => $this->time_spent_human,
            'metadata' => $this->metadata,
            'ip_address' => $this->ip_address,
            'activity_at' => $this->activity_at->toISOString(),
            'is_recent' => $this->is_recent,
            'is_engagement' => $this->isEngagement(),
            'is_high_value' => $this->isHighValue(),
        ];
    }
}