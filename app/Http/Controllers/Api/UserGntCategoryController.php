<?php

namespace App\Http\Controllers\Api;


use App\Model\GntCategory;
use App\Model\UserGntCategory;
use App\Model\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * 用户代币类型
 *
 * Class UserGntCategoryController
 * @package App\Http\Controllers\Api
 */
class UserGntCategoryController extends BaseController
{
	/**
	 * 代币列表
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
			'wallet_category_id.required' => '请选择钱包类型',
			'wallet_id.required' => '请选择钱包ID'
		]);
		$categoryId = $request->get('wallet_category_id');
		$list = UserGntCategory::whereHas('gntCategory', function ($query) use ($categoryId) {
			$query->ofCategoryId($categoryId);
		})->ofUserId($this->user->id)->ofWalletId($request->get('wallet_id'))->orderBy('updated_at', 'desc')->get();
		return success(compact('list'));
	}


	/**
	 * 添加代币类型
	 * @param Request $request
	 * @return array
	 */
	public function store(Request $request)
	{
		//流程:钱包类型=>钱包=>代币类型
		$this->validate($request, [
			'wallet_id' => 'required',
			'gnt_category_ids' => 'required'
		], [
			'wallet_id.required' => '请选择钱包ID',
			'gnt_category_ids.required' => '请选择代币类型'
		]);
		$gntCategoryIds = json_decode($request->get('gnt_category_ids'), true);
		$walletId = $request->get('wallet_id', 0);
		try {
			DB::transaction(function () use ($gntCategoryIds, $walletId) {
				if (!Wallet::ofUserId($this->user->id)->find($walletId)) {
					throw new \Exception('非法操作');
				}
//				UserGntCategory::where('user_id', $this->user->id)->where('wallet_id', $walletId)->delete();
				foreach ($gntCategoryIds as $categoryId) {
					//入库
					if (!$category = GntCategory::find($categoryId)) {
						throw new \Exception('非法代币类型');
					}
					$category->userGnt()->create(['user_id' => $this->user->id, 'name' => $category->name, 'wallet_id' => $walletId]);
				}
			});
			return success();
		} catch (\Exception $ex) {
			return fail('', $ex->getMessage());
		}
	}


	/**
	 * 置顶
	 * @param $id
	 * @return array
	 */
	public function update(Request $request, $id)
	{
		return UserGntCategory::ofUserId($this->user->id)->findOrFail($id)->update(['updated_at' => Carbon::now()]) ? success() : fail();
	}


	/**
	 * @param $id
	 * @return array
	 */
	public function destroy($id)
	{
		return UserGntCategory::ofUserId($this->user->id)->findOrFail($id)->delete() ? success() : fail();
	}
}
