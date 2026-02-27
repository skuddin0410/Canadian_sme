<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Booth;
use App\Models\Company;
use App\Models\PollAnswer;
use App\Traits\Auditable;
use App\Traits\AutoHtmlDecode;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, HasRoles;
    use  Auditable;
    use AutoHtmlDecode;
    /**
     * Define the default guard for this model.
     */
    protected $guard_name = 'web'; // Set the default guard (e.g., 'api' or 'web')
    protected $dates = ['deleted_at'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'lastname',
        'email',
        'designation',
        'tags',
        'email_verified_at',
        'website_url',
        'linkedin_url',
        'mobile',
        'mobile_verified_at',
        'username',
        'password',
        'bio',
        'remember_token',
        'is_block',
        'is_approve',
        'dob',
        'gender',
        'place',
        'street',
        'zipcode',
        'city',
        'state',
        'country',
        'created_by',
        'company_id',
        'qr_code',
        'secondary_group',
        'primary_group',
        'company',
        'status',
        'gdpr_consent',
        'access_speaker_ids',
        'access_exhibitor_ids',
        'access_sponsor_ids',
        'title',
        'onesignal_userid',
        'jwt_token',
        'slug',
        'cometchat_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',

    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'mobile_verified_at' => 'datetime',
            'password' => 'hashed',
            'dob' => 'date',
            'is_approve' => 'boolean',
            'is_block' => 'boolean',
            // 'secondary_group' => 'array',
            // 'tags' => 'array',
            // 'access_speaker_ids' => 'array',
            // 'access_exhibitor_ids' => 'array',
            // 'access_sponsor_ids' => 'array',
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getFullNameAttribute()
    {
        return $this->name . ' ' . $this->lastname;
    }

    public function photo()
    {
        return $this->hasOne(Drive::class, 'table_id', 'id')
            ->where('table_type', 'users')
            ->where('file_type', 'photo')
            ->whereNotNull('file_name');
    }

    public function files()
    {
        return $this->hasMany(Drive::class, 'table_id', 'id')
            ->where('table_type', 'users')
            ->where('file_type', 'files')
            ->whereNotNull('file_name');
    }


    public function coverphoto()
    {
        return $this->hasOne(Drive::class, 'table_id', 'id')
            ->where('table_type', 'users')
            ->where('file_type', 'cover_photo')
            ->whereNotNull('file_name');
    }

    public function privateDocs()
    {
        return $this->hasMany(Drive::class, 'table_id', 'id')
            ->where('table_type', 'users')
            ->where('file_type', 'private_docs')
            ->whereNotNull('file_name');
    }


    public function bank()
    {
        return $this->hasOne(Bank::class, 'user_id', 'id')
            ->where('is_default', 1);
    }

    public function loginLogs()
    {
        return $this->hasMany(\App\Models\UserLogin::class);
    }


    public function usercompany()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    protected function approvalStatusLabel(): Attribute
    {
        return Attribute::get(fn() => $this->is_approve ? 'Approved' : 'Pending Approval');
    }

    protected function approvalStatusClass(): Attribute
    {
        return Attribute::get(fn() => $this->is_approve ? 'success' : 'warning');
    }

    public function sessions()
    {
        return $this->belongsToMany(Session::class, 'session_speakers', 'user_id', 'session_id')->withTimestamps();
    }
    public function pollAnswers()
    {
        return $this->hasMany(PollAnswer::class);
    }

    public function agendas()
    {
        return $this->hasMany(UserAgenda::class);
    }

    public function favoriteSessions()
    {
        return $this->hasMany(FavoriteSession::class);
    }

    public function connections()
    {
        return $this->belongsToMany(User::class, 'user_connections', 'user_id', 'connection_id')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function connectedWithMe()
    {
        return $this->belongsToMany(User::class, 'user_connections', 'connection_id', 'user_id')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function allConnections()
    {
        return $this->connections
            ->merge($this->connectedWithMe)
            ->unique('id')
            ->values();
    }

    public function visitingcard()
    {
        return $this->hasOne(Drive::class, 'table_id', 'id')
            ->where('table_type', 'users')
            ->where('file_type', 'visiting_card')
            ->whereNotNull('file_name');
    }

    // Define the relationship between User and EventAndEntityLink
    public function eventAndEntityLinks()
    {
        return $this->hasMany(EventAndEntityLink::class, 'entity_id', 'id')
            ->where('entity_type', 'users');
    }

    protected $appends = ['full_name'];
}
