<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UserMonetaryUnit
 * @package App\Model
 */
class UserMonetaryUnit extends Model
{
	/**
	 * @var array
	 */
	protected $fillable = [
		'user_id',
		'monetary_unit_id',
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
