<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use App\Traits\AutoHtmlDecode;

class Setting extends Model
{   
    use  Auditable;
    use AutoHtmlDecode;
    protected $fillable = [
        'key',
        'value',
    ];

    public function photo()
    {
        return $this->hasOne(Drive::class, 'table_id', 'id')
            ->where('table_type', 'settings')
            ->where('file_type', 'photo')
            ->whereNotNull('file_name');
    }
}
