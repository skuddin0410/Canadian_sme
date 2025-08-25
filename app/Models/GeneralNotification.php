<?php
	namespace App\Models;


	use Illuminate\Database\Eloquent\Factories\HasFactory;
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\Relations\MorphTo;
	use Illuminate\Database\Eloquent\Relations\BelongsTo;


	class GeneralNotification extends Model
	{
	 use HasFactory;


	protected $fillable = [
	'user_id','title','body',
	'related_type','related_id','related_name',
	'scheduled_at','delivered_at','read_at','meta',
	];


	protected $casts = [
		'scheduled_at' => 'datetime',
		'delivered_at' => 'datetime',
		'read_at' => 'datetime',
		'meta' => 'array',
	];


	public function user(): BelongsTo { 
		return $this->belongsTo(User::class); 
	}


	public function related(): MorphTo
	{
	  return $this->morphTo(__FUNCTION__, 'related_type', 'related_id');
	}
}