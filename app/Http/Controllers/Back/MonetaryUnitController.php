<?php

namespace App\Http\Controllers\Back;


use App\Model\MonetaryUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonetaryUnitController extends BaseController
{

	/**
	 * @return array
	 */
	public function index()
	{
		$list = MonetaryUnit::get();
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
			'name' => 'required|unique:monetary_units,name',
		]);
		return MonetaryUnit::create($request->all()) ? success() : fail();
	}


	/**
	 * @param Request $request
	 * @param MonetaryUnit $monetaryUnit
	 * @return array
	 */
	public function update(Request $request, MonetaryUnit $monetaryUnit)
	{
		$this->validate($request, [
			'name' => 'required|unique:monetary_units,name,' . $monetaryUnit->id . ',id',
		]);
		DB::transaction(function () use ($monetaryUnit, $request) {
			$name = $request->get('name');
			$monetaryUnit->fill($request->all())->save();
		});

		return success();

	}


	/**
	 * @param  MonetaryUnit $monetaryUnit
	 * @return array
	 */
	public function destroy( MonetaryUnit $monetaryUnit)
	{
		if ($monetaryUnit->userUnit()->count()) {
			return fail('', '该货币单位已有用户关联,禁止删除');
		}
		return $monetaryUnit->delete() ? success() : fail();
	}
}
