<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * 用户联系人表
 *
 * Class UserContact
 * @package App\Model
 */
class UserContact extends Model
{
	/**
	 * @var array
	 */
	protected $fillable = [
		"user_id",
		"category_id",
		"name",
		"address",
		"remark",
	];


	/**
	 * @return mixed
	 */
	public function wallet()
	{
		return $this->hasOne(Wallet::class, 'address', 'address');
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
	 * @param $categoryId
	 * @return mixed
	 */
	public function scopeOfCategoryId($query, $categoryId)
	{
		return $categoryId ? $query->where('category_id', $categoryId) : $query;
	}

}
