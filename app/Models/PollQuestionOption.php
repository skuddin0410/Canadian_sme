<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\PollQuestion;
use App\Models\PollAnswer;


class PollQuestionOption extends Model
{
    protected $table = 'poll_question_options';
    protected $fillable = [
        'poll_question_id',
        'option_text',
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

    public function answers()
    {
        return $this->hasMany(PollAnswer::class, 'option_id');
    }
}
