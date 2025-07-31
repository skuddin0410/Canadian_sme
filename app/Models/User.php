<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, HasRoles;

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
        'email_verified_at',
        'mobile',
        'mobile_verified_at',
        'username',
        'password',
        'remember_token',
        'dob',
        'gender',
        'place',
        'street',
        'zipcode',
        'city',
        'state',
        'country',
        'google_id',
        'meta_id',
        'referral_coupon',
        'referral_percentage',
        'kyc_verified',
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

    public function background()
    {
        return $this->hasOne(Drive::class, 'table_id', 'id')
            ->where('table_type', 'users')
            ->where('file_type', 'background')
            ->whereNotNull('file_name');
    }

    public function bank()
    {
        return $this->hasOne(Bank::class, 'user_id', 'id')
            ->where('is_default', 1);
    }

    protected $appends = ['full_name'];
}
