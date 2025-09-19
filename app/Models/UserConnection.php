<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use App\Traits\AutoHtmlDecode;

class UserConnection extends Model
{
    use HasFactory;
    use  Auditable;
    use AutoHtmlDecode;

    protected $fillable = ['user_id', 'connection_id', 'status','rating','note'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function connection()
    {
        return $this->belongsTo(User::class, 'connection_id');
    }

    public static function alreadyConnected($userId, $connectionId): bool
    {
        return self::where(function ($q) use ($userId, $connectionId) {
                $q->where('user_id', $userId)
                  ->where('connection_id', $connectionId);
            })
            ->orWhere(function ($q) use ($userId, $connectionId) {
                $q->where('user_id', $connectionId)
                  ->where('connection_id', $userId);
            })
            ->exists();
    }

}

