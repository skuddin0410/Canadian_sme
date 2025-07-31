<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'table_id',
        'table_type',
        'purpose',
        'journal_type',
        'amount',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class, 'table_id', 'id');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'table_id', 'id');
    }

    public function withdrawal()
    {
        return $this->belongsTo(Withdrawal::class, 'table_id', 'id');
    }

    public function scopeSummary($query)
    {
        return $query->selectRaw("
            SUM(CASE WHEN `purpose` = 'referral' AND `journal_type` = 'credit' THEN `amount` ELSE 0 END) AS `total_referrals`,
            SUM(CASE WHEN `purpose` = 'redeem' AND `journal_type` = 'credit' THEN `amount` ELSE 0 END) AS `total_redeems`,
            SUM(CASE WHEN `purpose` = 'winning' AND `journal_type` = 'credit' THEN `amount` ELSE 0 END) AS `total_winnings`,
            SUM(CASE WHEN `purpose` = 'deposit' AND `journal_type` = 'credit' THEN `amount` ELSE 0 END) AS `total_deposits`,
            SUM(CASE WHEN `purpose` = 'withdraw' AND `journal_type` = 'debit' THEN `amount` ELSE 0 END) AS `total_withdrawals`,
            SUM(CASE WHEN `journal_type` = 'credit' THEN `amount` ELSE 0 END) AS `total_credits`,
            SUM(CASE WHEN `journal_type` = 'debit' THEN `amount` ELSE 0 END) AS `total_debits`,
            (SUM(CASE WHEN `journal_type` = 'credit' THEN `amount` ELSE 0 END) -
             SUM(CASE WHEN `journal_type` = 'debit' THEN `amount` ELSE 0 END)) AS `balance`
        ");
    }
}
