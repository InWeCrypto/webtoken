<?php

namespace App\Http\Controllers\Api;


use App\Model\Article;
use App\Model\IcoCategory;

/**
 * Class FindController
 * @package App\Http\Controllers\Api
 */
class FindController extends BaseController
{
	/**
	 * @return array
	 */
	public function index()
	{
		$banner = Article::getBanner();
		$ico = IcoCategory::getList();
		return success(compact('banner','ico'));
    }
}
