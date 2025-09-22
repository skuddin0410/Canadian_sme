<?php

namespace App\Models;

use App\Models\User;
use App\Models\Event;
use App\Models\Company;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lead extends Model
{
    //
    use HasFactory;
    use Auditable;

    protected $fillable = [
        'first_name', 
        'last_name', 
        'email', 
        'phone', 
        'status', 
        'tags' ,
        'priority',
        'source',
        'budget_min', 
        'budget_max', 
        'desired_bedrooms',
        'move_in_date', 
        'lease_duration', 
        'employment_status',
        'special_requirements', 
        'desired_amenities', 
        'assigned_agent_id',
        'matched_property_id', 
        'last_contact_date', 
        'lead_score',
        'time_on_site',
        'email_opens', 
        'downloads', 
        'form_submissions', 
        // 'property_inquiries',
        'ai_score', 
        'ai_reasoning',
        'page_views'
    ];

    protected $casts = [
        'tags' => 'array',
        'desired_amenities' => 'array',
        'budget_min' => 'decimal:2',
        'budget_max' => 'decimal:2',
        'move_in_date' => 'date',
        'last_contact_date' => 'datetime',
        'lead_score' => 'decimal:2',
        'ai_score' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function assignedAgent()
    {
        return $this->belongsTo(User::class, 'assigned_agent_id');
    }

    public function matchedEvent()
    {
        return $this->belongsTo(Event::class, 'matched_event_id');
    }
  public function user()
    {
        return $this->belongsTo(User::class, 'assigned_agent_id'); // or relevant field
    }

    public function company()
    {
        return $this->hasOneThrough(
            Company::class, // Final model
            User::class,    // Intermediate model
            'id',           // User PK
            'user_id',      // Company FK
            'assigned_agent_id', // Lead FK (user)
            'id'            // User PK
        );
    }

    // public function activities()
    // {
    //     return $this->hasMany(LeadActivity::class);
    // }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    // public function getBudgetRangeAttribute()
    // {
    //     if ($this->budget_min && $this->budget_max) {
    //         return "$" . number_format($this->budget_min) . " - $" . number_format($this->budget_max);
    //     }
    //     return 'Not specified';
    // }

    public function getInitialsAttribute()
    {
        return strtoupper(substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1));
    }



    public function getEngagementScoreAttribute()
    {
        $score = 0;
        $score += $this->page_views * 1;          // 1 point per page view
        $score += $this->time_on_site * 0.5;     // 0.5 points per minute
        $score += $this->email_opens * 3;        // 3 points per email open
        $score += $this->downloads * 5;          // 5 points per download
        $score += $this->form_submissions * 10;  // 10 points per form submission
        $score += $this->property_inquiries * 15; // 15 points per property inquiry
        
        return min(100, $score); // Cap at 100
    }


public function userActivities()
{
     return $this->hasMany(UserActivity::class, 'email', 'email');
}

public function recentActivities($days = 30)
{
    return $this->userActivities()->recent($days);
}

public function pageViews()
{
    return $this->userActivities()->pageViews();
}

public function emailOpens()
{
    return $this->userActivities()->emailOpens();
}

public function downloads()
{
    return $this->userActivities()->downloads();
}

public function formSubmissions()
{
    return $this->userActivities()->formSubmissions();
}

public function propertyInquiries()
{
    return $this->userActivities()->propertyInquiries();
}

// public function getDetailedEngagementSummary()
// {
//     return UserActivity::getEngagementSummary($this->email);
// }

    public function updateEngagementMetrics()
    {
        $summary = $this->getDetailedEngagementSummary();
        
        $this->update([
            'page_views' => $summary['page_views'],
            'time_on_site' => ceil($summary['total_time_spent'] / 60), // Convert to minutes
            'email_opens' => $summary['email_opens'],
            'downloads' => $summary['downloads'],
            'form_submissions' => $summary['form_submissions'],
            'property_inquiries' => $summary['property_inquiries'],
            'last_activity_at' => $summary['last_activity']
        ]);
    }


    // public function interactions()
    // {
    //     return $this->hasMany(LeadInteraction::class);
    // }

    // public function scoreHistories()
    // {
    //     return $this->hasMany(LeadScoreHistory::class);
    // }

    // protected static function booted()
    // {
    //     static::saving(function (self $lead) {
    //         $lead->lead_score = $lead->ai_score ?? 0;
    //     });
    // }
}
