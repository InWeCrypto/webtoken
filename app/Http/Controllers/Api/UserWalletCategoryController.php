<?php

namespace App\Http\Controllers\Api;


use App\Model\UserWalletCategory;
use App\Model\WalletCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * 用户钱包类型
 *
 * Class UserWalletCategoryController
 * @package App\Http\Controllers\Api
 */
class UserWalletCategoryController extends BaseController
{
	/**
	 * 添加钱包类型
	 * @param Request $request
	 * @return array
	 */
	public function store(Request $request)
	{
		$this->validate($request, [
			'category_ids' => 'required|json'
		], [
			'category_ids.required' => '请选择钱包类型'
		]);
		$categoryIds = json_decode($request->get('category_ids'), true);
		try {
			DB::transaction(function () use ($categoryIds) {
				foreach ($categoryIds as $categoryId) {
					//入库
					if (!$category = WalletCategory::find($categoryId)) {
						throw new \Exception('非法钱包类型');
					}
					$category->userWallet()->create(['user_id' => $this->user->id, 'name' => $category->name]);
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
	public function update($id)
	{
		return UserWalletCategory::ofUserId($this->user->id)->findOrFail($id)->update(['updated_at' => Carbon::now()]) ? success() : fail();
	}
}
