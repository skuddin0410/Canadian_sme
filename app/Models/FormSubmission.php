<?php

namespace App\Models;

use App\Models\Form;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormSubmission extends Model
{
    protected $table = 'form_submissions';
     protected $fillable = [
        'form_id',
        'submission_data',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'submission_data' => 'array'
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

}
