<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'name',
        'price',
        'type',
        'expires_at',
    ];

    public function spinners()
    {
        return $this->hasMany(Spinner::class, 'coupon_id', 'id');
    }

    public function wallets()
    {
        return $this->hasMany(Wallet::class, 'table_id', 'id')
            ->where('table_type', 'coupons');
    }
}
