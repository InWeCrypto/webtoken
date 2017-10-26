<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Ico
 * @package App\Model
 */
class Ico extends Model
{
	/**
	 * @var string
	 */
	protected $table = 'ico';
	/**
	 * @var array
	 */
	protected $fillable = [
		"title",
		"img",
		"intro",
		"start_at",
		"end_at",
		"cny",
		"block_net",
		"address",
		"url",
		"is_valid",//0审核不过,1审核中,2审核通过
		"is_show",
	];
	/**
	 * @var array
	 */
	protected $dates = ['start_at', 'end_at'];

	/**
	 * @param $query
	 * @return mixed
	 */
	public function scopePass($query)
	{
		return $query->where('is_valid', 2);
	}

	/**
	 * @param $query
	 * @return mixed
	 */
	public function scopeShow($query)
	{
		return $query->where('is_show', 1);
	}

	/**
	 * @param $query
	 * @param $keyword
	 * @return mixed
	 */
	public function scopeOfKeyword($query, $keyword)
	{
		return $keyword ? $query->where('title', 'like', "%$keyword%") : $query;
	}

	/**
	 * @param $query
	 * @param $time
	 * @return mixed
	 */
	public function scopeOfTime($query, $time)
	{
		return $time[0]?$query->whereBetween('start_at',$time):$query;
	}
}
