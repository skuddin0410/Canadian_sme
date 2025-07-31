<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = [
        'user_id',
        'table_id',
        'table_type',
        'amount',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'table_id', 'id');
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'table_id', 'id');
    }

    public function giveaway()
    {
        return $this->belongsTo(Order::class, 'table_id', 'id')
            ->where('table_type', 'giveaways');
    }

    public function quiz()
    {
        return $this->belongsTo(Order::class, 'table_id', 'id')
            ->where('table_type', 'quizzes');
    }

    public function spinner()
    {
        return $this->belongsTo(Order::class, 'table_id', 'id')
            ->where('table_type', 'spinners');
    }
}
