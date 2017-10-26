<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UserWalletCategory
 * @package App\Model
 */
class UserWalletCategory extends Model
{

	/**
	 * @var array
	 */
	protected $fillable = [
		"user_id",
		"category_id",
		"name",
	];

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
