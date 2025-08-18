<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Booth;
use App\Models\Company;
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
        'company_id'
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

    // public function setPasswordAttribute($value)
    // {
    //     return $this->attributes['password'] = bcrypt($value);
    // }
    // In App\Models\User.php

public function booths()
{
    return $this->hasManyThrough(
        Booth::class,
        Company::class,
        'user_id',      // Foreign key on Company (Company.user_id → User.id)
        'company_id',   // Foreign key on Booth (Booth.company_id → Company.id)
        'id',           // Local key on User (User.id)
        'id'            // Local key on Company (Company.id)
    );
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

    public function bank()
    {
        return $this->hasOne(Bank::class, 'user_id', 'id')
            ->where('is_default', 1);
    }

    public function loginLogs()
    {
        return $this->hasMany(\App\Models\UserLogin::class);
    }
    

    public function company()
    {
        return $this->hasOne(Company::class,'user_id','id');
    }
  protected function approvalStatusLabel(): Attribute
    {
        return Attribute::get(fn () => $this->is_approve ? 'Approved' : 'Pending Approval');
    }

    protected function approvalStatusClass(): Attribute
    {
        return Attribute::get(fn () => $this->is_approve ? 'success' : 'warning');
    }
    
    public function sessions()
    {
        return $this->belongsToMany(Session::class, 'session_speakers', 'user_id', 'session_id')->withTimestamps();
    }


    protected $appends = ['full_name'];
}
