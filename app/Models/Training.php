<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;
use App\Traits\AutoHtmlDecode;

class Training extends Model
{
    //
    use HasFactory;
    use Auditable;
    use AutoHtmlDecode;
    
    protected $table = "trainings";

    public function material()
    {
        return $this->hasOne(Drive::class, 'table_id', 'id')
            ->where('table_type', 'trainings')
            ->where('file_type', 'training_material')
            ->whereNotNull('file_name');
    }

   
}
