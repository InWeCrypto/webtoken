<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Pricecoinmarketcap
 * @package App\Model
 */
class Pricecoinmarketcap extends Model
{
	/**
	 * @var string
	 */
	protected $table = 'pricecoinmarketcap';

	/**
	 * @var array
	 */
	protected $fillable = [
		"asset_id",
		"name",
		"symbol",
		"rank",
		"price_usd",
		"price_btc",
		"volume_usd_24h",
		"market_cap_usd",
		"available_supply",
		"total_supply",
		"percent_change_1h",
		"percent_change_24h",
		"percent_change_7d",
		"last_updated",
		"price_cny",
		"volume_cny_24h",
		"market_cap_cny"
	];

	/**
	 * @var bool
	 */
	public $timestamps = false;

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
