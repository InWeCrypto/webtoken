<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Coinmarketcapcurrent extends Model
{
    //
	protected $table='coinmarketcapcurrent';
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
