<?php

namespace App\Http\Controllers\Back;


use App\Model\MarketCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MarketCategoryController extends BaseController
{

	/**
	 * @return array
	 */
	public function index()
	{
		$list = MarketCategory::get();
		return success(compact('list'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		$this->validate($request, [
			'name' => 'required|unique:ico_categories,name',
			'flag' => 'required',
		]);
		return MarketCategory::create($request->only('name', 'flag')) ? success() : fail();
	}


	/**
	 * @param Request $request
	 * @param MarketCategory $marketCategory
	 * @return array
	 */
	public function update(Request $request, MarketCategory $marketCategory)
	{
		$this->validate($request, [
			'name' => 'required|unique:ico_categories,name,' . $marketCategory->id . ',id',
			'flag' => 'required',
		]);
		return $marketCategory->fill($request->only('name', 'flag'))->save()?success():fail();

	}


	/**
	 * @param  MarketCategory $marketCategory
	 * @return array
	 */
	public function destroy(MarketCategory $marketCategory)
	{
		if($marketCategory->relationUser()->count()){
			return fail('', '该分类已有用户关联,禁止删除');
		}

		return $marketCategory->delete() ? success() : fail();
	}
}
