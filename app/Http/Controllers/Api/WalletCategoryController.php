<?php

namespace App\Http\Controllers\Api;


use App\Model\WalletCategory;

/**
 * 用户未选择钱包类型
 *
 * Class WalletCategoryController
 * @package App\Http\Controllers\Api
 */
class WalletCategoryController extends BaseController
{
	/**
	 * @return array
	 */
	public function index()
	{
		if (!$this->user) {
			$list = WalletCategory::get();
		} else {
			$list = WalletCategory::whereDoesntHave('userWallet', function ($query) {
				$query->ofUserId($this->user->id);
			})->get();
		}

		return success(compact('list'));
	}
}
