<?php

namespace App\Http\Controllers\Back;


use App\Model\GntCategory;
use App\Model\Ico;
use App\Model\WalletCategory;
use Illuminate\Http\Request;

/**
 * Class IcoController
 * @package App\Http\Controllers\Back
 */
class IcoController extends BaseController
{
	/**
	 * @param Request $request
	 * @return array
	 */
	public function index(Request $request)
	{
		$list = Ico::ofKeyword($request->get('keyword'))->ofTime($request->get('time'))->paginate($request->get('per_page'));
		return success(compact('list'));
	}

	public function store(Request $request)
	{
		$this->validate($request, [
			"title" => 'required',
			"img" => 'required',
			"intro" => 'required',
			"cny" => 'required',
			"block_net" => 'required',
			"address" => 'required',
			"url" => 'required',
		]);

		return Ico::create($request->all() + ['is_valid' => 2]) ? success() : fail();
	}

	public function update(Request $request, Ico $ico)
	{
		$this->validate($request, [
			"title" => 'required',
			"img" => 'required',
			"intro" => 'required',
			"cny" => 'required',
			"block_net" => 'required',
			"address" => 'required',
			"url" => 'required',
		]);

		return $ico->fill($request->all() + ['is_valid' => 2])->save() ? success() : fail();
	}


	/**
	 * @return array
	 */
	public function getCategory()
	{
		$wCategory = WalletCategory::select('name');
		$list = GntCategory::select('name')->union($wCategory)->get();
		return success(compact('list'));
	}
}
