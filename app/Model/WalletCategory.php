<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Class WalletCategory
 * @package App\Model
 */
class WalletCategory extends Model
{
	/**
	 * @var array
	 */
	protected $fillable = [
		"name",
		"gas",
		"icon",
		"address"
	];


	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function userWallet()
	{
		return $this->hasMany(UserWalletCategory::class, 'category_id');
	}


	public function cap()
	{
		return $this->hasOne(Pricecoinmarketcap::class, 'symbol', 'name')->orderBy('last_updated', 'desc');
	}

	public function icoInfo()
	{
		return $this->hasOne(IcoList::class, 'symbol', 'name')->select('symbol','name');
	}
}
