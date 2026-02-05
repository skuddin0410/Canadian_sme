<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use App\Traits\AutoHtmlDecode;
use Illuminate\Database\Eloquent\SoftDeletes;

class Speaker extends Model
{       

    use  Auditable;
    use AutoHtmlDecode;
    use SoftDeletes;
    protected $dates = ['deleted_at']; 
    protected $fillable = [
        'name',
        'lastname',
        'email',
        'company',
        'designation',
        'website_url',
        'linkedin_url',
        'facebook_url',
        'instagram_url',
        'twitter_url',
        'mobile',
        'bio',
        'gdpr_consent',
        'slug'

    ];

    public function photo()
    {
        return $this->hasOne(Drive::class, 'table_id', 'id')
            ->where('table_type', 'speakers')
            ->where('file_type', 'photo')
            ->whereNotNull('file_name');
    }

    public function privateDocs()
    {
        return $this->hasMany(Drive::class, 'table_id', 'id')
            ->where('table_type', 'speakers')
            ->where('file_type', 'private_docs')
            ->whereNotNull('file_name');
    }

    public function coverphoto()
    {
        return $this->hasOne(Drive::class, 'table_id', 'id')
            ->where('table_type', 'speakers')
            ->where('file_type', 'cover_photo')
            ->whereNotNull('file_name');
    }
    
    public function getFullNameAttribute()
    {
        return $this->name . ' ' . $this->lastname;
    }

    public function eventAndEntityLinks()
    {
        return $this->hasMany(EventAndEntityLink::class, 'entity_id', 'id')
                    ->where('entity_type', 'speakers');
    }

    protected $appends = ['full_name'];
}
