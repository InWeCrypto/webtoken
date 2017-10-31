<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Ethtx
 * @package App\Model
 */
class Ethtx extends Model
{
	/**
	 * @var string
	 */
	protected $table = 'ethtx';

	/**
	 * @return string
	 */
	public function getAddrFromAttribute()
	{
		return strtolower($this->attributes['addr_from']);
	}

	/**
	 * @return string
	 */
	public function getAddrToAttribute()
	{
		return strtolower($this->attributes['addr_to']);
	}

	/**
	 * @return string
	 */
	public function getAddrTokenAttribute()
	{
		return strtolower($this->attributes['addr_token']);
	}
}
