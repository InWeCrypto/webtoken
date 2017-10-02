<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Pricecoinmarketcap
 * @package App\Model
 */
class Coinmarketcapmin extends Model
{
	/**
	 * @var string
	 */
	protected $table = 'coinmarketcapmin';
	/**
	 * @param $query
	 * @param $symbol
	 * @return mixed
	 */
	public function scopeOfSymbol($query, $symbol)
	{
		return $symbol ? $query->where('symbol', $symbol) : $query;
	}
}
