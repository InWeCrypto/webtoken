<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class Article
 * @package App\Model
 */
class Article extends Model
{
	/**
	 * @var array
	 */
	protected $fillable = [
		'detail',//详细内容,jsonb
	];

	/**
	 * @var array
	 */
	protected $casts = [
		'detail' => 'array',
	];


	/**
	 * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
	 */
	public static function getList()
	{
		return self::query()->latest()->paginate(request('per_page', 5));
	}


	/**
	 * @return \Illuminate\Support\Collection
	 */
	public static function getBanner()
	{
		return self::query()->where('detail->is_banner','true')->latest()->limit(4)->get();
	}
}
