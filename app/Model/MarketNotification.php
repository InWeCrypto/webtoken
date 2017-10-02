<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MarketNotification
 * @package App\Model
 */
class MarketNotification extends Model
{
	/**
	 * @var array
	 */
	protected $fillable = [
		"market_id",
		"user_id",
		"upper_limit",
		"lower_limit",
		"currency",//币种
	];

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function market()
	{
		return $this->belongsTo(MarketCategory::class,'market_id');
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
}
