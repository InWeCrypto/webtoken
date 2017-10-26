<?php

namespace App\Http\Controllers\Back;


use App\Model\Article;
use Illuminate\Http\Request;

/**
 * Class ArticleController
 * @package App\Http\Controllers\Back
 */
class ArticleController extends BaseController
{
	/**
	 * @param Request $request
	 * @return array
	 */
	public function index(Request $request)
	{
		$list = Article::latest()->paginate($request->get('per_page'));
		return success(compact('list'));
	}


	/**
	 * @param Request $request
	 * @return array
	 */
	public function store(Request $request)
	{
		$this->check($request);
		return Article::create(['detail' => $request->only('title','img','desc','content','is_banner')]) ? success() : fail();
	}


	/**
	 * @param Article $article
	 * @return array
	 */
	public function show(Article $article)
	{
		return success(['record' => $article]);
	}

	/**
	 * @param Request $request
	 * @param Article $article
	 * @return array
	 */
	public function update(Request $request, Article $article)
	{
		$this->check($request);
		return $article->fill(['detail' => $request->only('title','img','desc','content','is_banner')])->save() ? success() : fail();
	}

	/**
	 * @param Article $article
	 * @return array
	 */
	public function destroy(Article $article)
	{
		return $article->delete() ? success() : fail();
	}

	/**
	 * @param Request $request
	 */
	public function check(Request $request)
	{
		$this->validate($request, [
			'img' => 'required',
			'title' => 'required',
			'desc' => 'required',
			'content' => 'required',
		], [
			'img.required' => '请上传封面图片',
			'title.required' => '请填写标题',
			'desc.required' => '请填写简介',
			'content.required' => '请填写详情',
		]);
	}
}
