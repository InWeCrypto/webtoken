<?php

namespace App\Http\Controllers\Api;


use App\Model\GntCategory;
use App\Model\WalletCategory;
use Illuminate\Http\Request;

/**
 * Class GasController
 * @package App\Http\Controllers\Api
 */
class GasController extends BaseController
{
	/**
	 * @param Request $request
	 * @return array
	 */
	public function index(Request $request)
	{
		$this->validate($request, [
			'category_id' => 'required',
			'type' => 'required',//0钱包类型,1代币类型
		]);
		$record = $request->get('type', 0) ? GntCategory::findOrFail($request->get('category_id')) : WalletCategory::findOrFail($request->get('category_id'));

		return success(compact('record'));
	}
}
