<?php

namespace App\Http\Controllers\Api;


use App\Model\Article;

/**
 * 资讯文章
 *
 * Class ArticleController
 * @package App\Http\Controllers\Api
 */
class ArticleController extends BaseController
{

	public function index()
	{
		$list = Article::getList()->toArray()['data'];
		return success(compact('list'));
	}
	/**
	 * @param $id
	 * @return array
	 */
	public function show($id)
	{
		$record = Article::findOrFail($id);
		return view('app.article',['record' => $record]);
	}
}
