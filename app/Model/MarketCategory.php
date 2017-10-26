<?php

namespace App\Model;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MarketCategory
 * @package App\Model
 */
class MarketCategory extends Model
{
	/**
	 * @var array
	 */
	protected $fillable = [
		"name",
		"flag",
		"token",
		"url",
		"source",
		"icon"
	];

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function relationUser()
	{
		return $this->hasMany(UserMarketCategory::class, 'market_id');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function relationNotification()
	{
		return $this->hasMany(MarketNotification::class, 'market_id');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function relationCurrent()
	{
		return $this->hasOne(Coinmarketcapcurrent::class, 'symbol', 'flag')->where('_group','pricecoinmarketcap');
	}

	/**
	 * 最新信息
	 * @return mixed
	 */
	public function relationCap()
	{
		return $this->hasMany(Pricecoinmarketcap::class, 'symbol', 'flag')->orderBy('last_updated', 'desc');
	}

	/**
	 * 最新信息min
	 * @return mixed
	 */
	public function relationCapMin()
	{
		return $this->hasOne(Coinmarketcapmin::class, 'symbol', 'flag')->orderBy('timestamp', 'desc');
	}

	/**
	 * 开盘价格
	 * @return mixed
	 */
	public function relationCapStart()
	{
		return $this->hasOne(Pricecoinmarketcap::class, 'symbol', 'flag')->orderBy('last_updated', 'asc')->select('symbol', 'price_usd', 'price_cny');
	}

	/**
	 * @return mixed
	 */
	public function relationCapCnyMax()
	{
		return $this->maxAndMinGenerator('price_cny', 'desc');
	}

	/**
	 * @return mixed
	 */
	public function relationCapCnyMin()
	{
		return $this->maxAndMinGenerator('price_cny', 'asc');
	}

	/**
	 * @return mixed
	 */
	public function relationCapUsdMax()
	{
		return $this->maxAndMinGenerator('price_usd', 'desc');
	}

	/**
	 * @return mixed
	 */
	public function relationCapUsdMin()
	{
		return $this->maxAndMinGenerator('price_usd', 'asc');
	}

	/**
	 * @param $columnName
	 * @param $order
	 * @return mixed
	 */
	public function maxAndMinGenerator($columnName, $order)
	{
		return $this->hasOne(Pricecoinmarketcap::class, 'symbol', 'flag')->where('last_updated', '>', Carbon::today()->getTimestamp())->orderBy($columnName, $order)->select('symbol', $columnName);
	}
}
