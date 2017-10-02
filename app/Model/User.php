<?php

namespace App\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject as AuthenticatableUserContract;

/**
 * Class User
 * @package App\Model
 */
class User extends Authenticatable implements AuthenticatableUserContract
{

	/**
	 * @var array
	 */
	protected $hidden = [
		"password",
	];
	/**
	 * @var array
	 */
	protected $fillable = [
		"open_id",
		"password",
		"nickname",
		"sex",
		"img",
		"province",
		"city",
		"country",
	];

	/**
	 *  监听事件
	 */
	protected static function boot()
	{
		static::created(function ($user) {
			if ($markets = MarketCategory::where('flag','ETH')->get()) {
				//自动添加所有行情
				$marketData = [];
				foreach ($markets as $market) {
					$marketData[] = new UserMarketCategory(['market_id' => $market->id]);
				}
				$user->userMarket()->saveMany($marketData);
			}
		});
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function userMarket()
	{
		return $this->hasMany(UserMarketCategory::class, 'user_id');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function userMarketNotification()
	{
		return $this->hasMany(MarketNotification::class, 'user_id');
	}

	/**
	 * @return mixed
	 */
	public function getJWTIdentifier()
	{
		return $this->getKey(); // Eloquent model method
	}

	/**
	 * @return array
	 */
	public function getJWTCustomClaims()
	{
		return [];
	}
}
