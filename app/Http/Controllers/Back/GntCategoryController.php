<?php

namespace App\Http\Controllers\Back;

use App\Model\GntCategory;
use App\Model\Pricecoinmarketcap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class GntCategoryController
 * @package App\Http\Controllers\Back
 */
class GntCategoryController extends BaseController
{

	/**
	 * @param Request $request
	 * @return array
	 */
	public function index(Request $request)
	{
//		$this->validate($request, [
//			'category_id' => 'required'
//		]);
		$list = GntCategory::ofCategoryId($request->get('category_id', 0))->with('walletCategory')->get();
		return success(compact('list'));
	}


	/**
	 * @param Request $request
	 * @return array
	 */
	public function store(Request $request)
	{
		$this->validate($request, [
			'category_id' => 'required',
			'name' => 'required|unique:gnt_categories,name,null,id,category_id,' . $request->get('category_id'),
		]);
//		if(!Pricecoinmarketcap::ofSymbol($request->get('name'))->count()){
//			return fail('','该类型不合法');
//		}
		return GntCategory::create($request->all()) ? success() : fail();
	}


	/**
	 * @param Request $request
	 * @param GntCategory $gntCategory
	 * @return array
	 */
	public function update(Request $request, GntCategory $gntCategory)
	{
		$this->validate($request, [
			'name' => 'required|unique:gnt_categories,name,' . $gntCategory->id . ',id,category_id,' . $gntCategory->category_id,
		]);
//		if(!Pricecoinmarketcap::ofSymbol($request->get('name'))->count()){
//			return fail('','该类型不合法');
//		}
		DB::transaction(function () use ($gntCategory, $request) {
			$name = $request->get('name');
			$gntCategory->fill($request->all())->save();
			$gntCategory->userGnt()->update(['name' => $name]);
		});

		return success();

	}


	/**
	 * @param GntCategory $gntCategory
	 * @return array
	 */
	public function destroy(GntCategory $gntCategory)
	{
		if ($gntCategory->userGnt()->count()) {
			return fail('', '该代币类型已有用户创建实例,禁止删除');
		}
		return $gntCategory->delete() ? success() : fail();
	}
}
