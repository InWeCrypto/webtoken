<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * Class Message
 * @package App\Model
 */
class Message extends Model
{
	/**
	 * @var array
	 */
	protected $fillable = [
		"user_id",
		"title",
		"content",
		"type",//0未读,1已读,default 0
		"ext",
		"resource_id",
		"resource_type",
		"hash",
	];

	/**
	 * Message constructor.
	 * @param array $attributes
	 */
	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);
		date_default_timezone_set('PRC');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function user()
	{
		return $this->belongsTo(User::class);
	}


	/**
	 * @param $query
	 * @param $userId
	 * @return mixed
	 */
	public function scopeOfUserId($query, $userId)
	{
		return $userId ? $query->where('user_id', $userId) : $query;
	}


	/**
	 * @param $query
	 * @param $type
	 * @return mixed
	 */
	public function scopeOfType($query, $type)
	{
		return $type != null ? $query->where('type', $type) : $query;
	}
}
