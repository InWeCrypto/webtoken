<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Class IcoCategory
 * @package App\Model
 */
class IcoCategory extends Model
{
	/**
	 * @var array
	 */
	protected $fillable = [
		"name",
		"img",
		"type",//type：1.native 2.web
		"param",// url/params
	];

	/**
	 * 获取分类信息
	 *
	 * @return mixed
	 */
	public static function getList()
	{
		return self::latest()->get();
	}
}
