<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * 代币类型
 * Class GntCategory
 * @package App\Model
 */
class GntCategory extends Model
{
	/**
	 * @var array
	 */
	protected $fillable = [
		"category_id",
		"name",
		"gas",
		"icon",
		"address"
	];


	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function walletCategory()
	{
		return $this->belongsTo(WalletCategory::class, 'category_id');
	}


	public function cap()
	{
		return $this->hasOne(Pricecoinmarketcap::class, 'symbol', 'name')->orderBy('last_updated', 'desc');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function userGnt()
	{
		return $this->hasMany(UserGntCategory::class, 'gnt_category_id');
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

	public function icoInfo()
	{
		return $this->hasOne(IcoList::class, 'symbol', 'name')->select('symbol','name');
	}
}
