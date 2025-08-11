<?php

namespace App\Models;

use App\Models\User;
use App\Models\Drive;
use App\Models\CompanyContact;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use App\Traits\AutoHtmlDecode;

class Company extends Model
{
    
    use  Auditable;
    use AutoHtmlDecode;
    protected $fillable = [
        'user_id','name', 'industry', 'size', 'location', 'email', 'phone',
        'description', 'website', 'linkedin', 'twitter', 'facebook', 'instagram', 'certifications','certification_image'
    ];
   public function contacts()
    {
        return $this->hasMany(CompanyContact::class);
    }
    public function user()
    {
    return $this->belongsTo(User::class);
    }
    public function certificationFile()
    {
        return $this->hasOne(Drive::class, 'table_id', 'id')
            ->where('table_type', 'companies')
            ->where('file_type', 'certifications')
            ->whereNotNull('file_name');
    }
    public function logoFile()
    {
        return $this->hasOne(Drive::class, 'table_id', 'id')
            ->where('table_type', 'companies')
            ->where('file_type', 'logo');
    }
    public function mediaGallery()
    {
        return $this->hasMany(Drive::class, 'table_id', 'id')
            ->where('table_type', 'companies')
            ->where('file_type', 'media_gallery')
            ->whereNotNull('file_name');
    }
    public function videos()
    {
        return $this->hasMany(Drive::class, 'table_id', 'id')
            ->where('table_type', 'companies')
            ->where('file_type', 'company_video');
    }




}
