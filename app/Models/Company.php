<?php

namespace App\Models;

use App\Models\User;
use App\Models\Booth;
use App\Models\Drive;
use App\Models\Session;
use App\Models\BoothUser;
use App\Traits\Auditable;
use App\Models\CompanyContact;
use App\Traits\AutoHtmlDecode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    
    use  Auditable;
    use AutoHtmlDecode;
    use SoftDeletes;
    protected $dates = ['deleted_at']; 
    
    protected $fillable = [
        'user_id','name', 'industry', 'size', 'booth_id', 'location', 'email', 'phone',
        'description', 'website', 'linkedin', 'twitter', 'facebook', 'instagram', 'certifications','certification_image','is_sponsor','booth','type','slug', 'event_name'
    ];
    protected $casts = [
        'is_sponsor' => 'boolean',
    ];
   public function contacts()
    {
        return $this->hasMany(CompanyContact::class);
    }
    public function user()
    {
    return $this->belongsTo(User::class);
    }

    public function contentIconFile() //used on exhibitor
    {
        return $this->hasOne(Drive::class, 'table_id', 'id')
            ->where('table_type', 'companies')
            ->where('file_type', 'content_icon');
    }

    public function quickLinkIconFile() //used on exhibitor
    {
        return $this->hasOne(Drive::class, 'table_id', 'id')
            ->where('table_type', 'companies')
            ->where('file_type', 'quick_link_icon');
    }
    public function logo() //used on sponsor
    {
        return $this->hasOne(Drive::class, 'table_id', 'id')
            ->where('table_type', 'companies')
            ->where('file_type', 'logo');
    }
    public function banner() //used on sponsor
    {
        return $this->hasOne(Drive::class, 'table_id', 'id')
            ->where('table_type', 'companies')
            ->where('file_type', 'banner');
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
    

   public function products()
   {
    return $this->belongsTo(Product::class,'company_id');
   }

    public function boothUsers()
    {
        return $this->hasMany(BoothUser::class);
    }
    public function booths()
    {
        return $this->hasManyThrough(
            Booth::class,   // Target
            BoothUser::class, // Pivot
            'company_id',    // Foreign key on BoothUser
            'id',            // Foreign key on Booth
            'id',            // Local key on Company
            'booth_id'       // Local key on BoothUser
        );
    }

    public function files()
    {
        return $this->hasMany(Drive::class, 'table_id', 'id')
            ->where('table_type', 'companies')
            ->where('file_type', 'files')
            ->whereNotNull('file_name');
    }
    
      public function Docs()
    {
        return $this->hasMany(Drive::class, 'table_id', 'id')
            ->where('table_type', 'companies')
            ->where('file_type', 'private_docs')
            ->whereNotNull('file_name');
    }
    public function sessions()
    {
    return $this->hasMany(Session::class);
    }

    public function category()
    {
    return $this->hasOne(Category::class,'slug','type');
    }


}
