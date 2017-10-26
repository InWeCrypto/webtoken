<?php

namespace App\Http\Controllers\Back;


use App\Model\IcoCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IcoCategoryController extends BaseController
{

	/**
	 * @return array
	 */
	public function index()
	{
		$list = IcoCategory::get();
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
		]);
		return IcoCategory::create($request->all()) ? success() : fail();
	}


	/**
	 * @param Request $request
	 * @param IcoCategory $icoCategory
	 * @return array
	 */
	public function update(Request $request, IcoCategory $icoCategory)
	{
		$this->validate($request, [
			'name' => 'required|unique:ico_categories,name,' . $icoCategory->id . ',id',
		]);
		DB::transaction(function () use ($icoCategory, $request) {
			$icoCategory->fill($request->all())->save();
		});

		return success();

	}


	/**
	 * @param  IcoCategory $icoCategory
	 * @return array
	 */
	public function destroy( IcoCategory $icoCategory)
	{
		//TODO:检测ICO分类是否被使用
		return $icoCategory->delete() ? success() : fail();
	}
}
