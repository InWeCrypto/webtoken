<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * 货币单位
 *
 * Class MonetaryUnit
 * @package App\Model
 */
class MonetaryUnit extends Model
{
	/**
	 * @var array
	 */
	protected $fillable=[
		'name'
	];

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function userUnit()
	{
		return $this->hasMany(UserMonetaryUnit::class,'monetary_unit_id');
	}

}
