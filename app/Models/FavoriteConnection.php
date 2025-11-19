<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FavoriteConnection extends Model
{
    use HasFactory;

    protected $table = 'favorite_connections';

    protected $fillable = [
        'user_id',
        'connection_id',
    ];

    /**
     * The user who added the favorite.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * The user who is marked as favorite.
     */
    public function connection()
    {
        return $this->belongsTo(User::class, 'connection_id');
    }
}
