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

    protected $fillable = ['user_id', 'connection_id', 'status','rating','note', 'event_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function connection()
    {
        return $this->belongsTo(User::class, 'connection_id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public static function alreadyConnected($userId, $connectionId, $eventId = 1): bool
    {
        return self::where('event_id', $eventId)
            ->where(function ($q) use ($userId, $connectionId) {
                $q->where(function ($q2) use ($userId, $connectionId) {
                    $q2->where('user_id', $userId)
                       ->where('connection_id', $connectionId);
                })
                ->orWhere(function ($q2) use ($userId, $connectionId) {
                    $q2->where('user_id', $connectionId)
                       ->where('connection_id', $userId);
                });
            })
            ->exists();
    }
}

