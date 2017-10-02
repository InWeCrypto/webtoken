<?php

namespace App\Http\Controllers\Back;

use App\Model\Pricecoinmarketcap;
use App\Model\WalletCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class WalletCategoryController
 * @package App\Http\Controllers\Back
 */
class WalletCategoryController extends BaseController
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$list = WalletCategory::get();
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
			'name' => 'required|unique:wallet_categories'
		]);
//		if(!Pricecoinmarketcap::ofSymbol($request->get('name'))->count()){
//			return fail('','该类型不合法');
//		}

		return WalletCategory::create($request->all()) ? success() : fail();

	}


	/**
	 * @param Request $request
	 * @param WalletCategory $walletCategory
	 * @return array
	 */
	public function update(WalletCategory $walletCategory,Request $request)
	{
		$this->validate($request, [
			'name' => 'required|unique:wallet_categories,name,' . $walletCategory->id
		]);
//		if(!Pricecoinmarketcap::ofSymbol($request->get('name'))->count()){
//			return fail('','该类型不合法');
//		}
		DB::transaction(function () use ($walletCategory, $request) {
			$walletCategory->fill($request->all())->save();
			$walletCategory->userWallet()->update(['name' => $request->get('name')]);
		});

		return success();
	}


	/**
	 * @param WalletCategory $walletCategory
	 * @return array
	 */
	public function destroy(WalletCategory $walletCategory)
	{
		if ($walletCategory->userWallet()->count()) {
			return fail('', '该钱包类型已有用户创建实例,禁止删除');
		}
		//TODO:检测代币
		return $walletCategory->delete() ? success() : fail();
	}
}
