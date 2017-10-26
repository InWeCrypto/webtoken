<?php

namespace App\Http\Controllers\Api;


use App\Model\GntCategory;
use Illuminate\Http\Request;

/**
 * Class GntCategoryController
 * @package App\Http\Controllers\Api
 */
class GntCategoryController extends BaseController
{
	/**
	 * 用户未选择钱包类型
	 *
	 * @param Request $request
	 * @return array
	 */
	public function index(Request $request)
	{
		$this->validate($request, [
			'wallet_category_id' => 'required',
			'wallet_id' => 'required'
		], [
			'wallet_id.required' => '请选择钱包ID',
			'wallet_category_id.required' => '请选择钱包类型ID'
		]);
		$list = GntCategory::whereDoesntHave('userGnt', function ($query) use($request) {
			$query->ofUserId($this->user->id)->ofWalletId($request->get('wallet_id'));
		})->where('category_id', $request->get('wallet_category_id'))->get();
		return success(compact('list'));
	}
}
