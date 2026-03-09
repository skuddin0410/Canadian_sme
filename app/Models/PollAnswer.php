<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\PollQuestion;
use App\Models\User;

class PollAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'poll_question_id',
        'user_id',
        'text_answer',
        'yes_no_answer',
        'rating_answer',
    ];

    protected $casts = [
        'yes_no_answer' => 'boolean',
        'rating_answer' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function question()
    {
        return $this->belongsTo(PollQuestion::class, 'poll_question_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
