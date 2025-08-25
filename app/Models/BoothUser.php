<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoothUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'booth_id',
    ];

    // Relationships
    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function booth()
    {
        return $this->belongsTo(Booth::class);
    }
}
