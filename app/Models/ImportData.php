<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class ImportData extends Model
{
    use HasFactory, Auditable;

    protected $table = 'imported_data';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'mobile',
        'added_by'
    ];
}
