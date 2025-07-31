<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'table_id',
        'table_type',
        'amount',
        'winning_type',
        'winning',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function giveaway()
    {
        return $this->belongsTo(Giveaway::class, 'table_id', 'id');
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'table_id', 'id');
    }

    public function spinner()
    {
        return $this->belongsTo(Spinner::class, 'table_id', 'id')
            ->withTrashed();
    }
}
