<?php

namespace App\Http\Controllers\Api;


use App\Model\Pricecoinmarketcap;
use App\Model\Wallet;
use Illuminate\Http\Request;

/**
 * Class IndexController
 * @package App\Http\Controllers\Api
 */
class ConversionController extends BaseController
{
	/**
	 * 获取钱包余额
	 *
	 * @param Request $request
	 * @return array
	 */
	public function index(Request $request)
	{
		$this->validate($request, [
			'wallet_ids' => 'required|json'
		]);
		$wallets = json_decode($request->get('wallet_ids'), true);
		$uri = env('API_URL',config('user_config.unichain_url'));
		$list = Wallet::with('category')->ofUserId($this->user->id)->whereIn('id', $wallets)->get()->each(function ($val) use ($uri) {
			$val->category->cap = Pricecoinmarketcap::ofSymbol($val->category->name)->orderBy('last_updated', 'desc')->first();
			//钱包余额
			$res = sendCurl($uri . '/eth/getBalance', ['address' => $val->address], null, 'POST');
			$val->balance = $res['value'];
		});

		return success(compact('list'));
	}

	/**
	 * @param $walletId
	 * @return array
	 */
	public function show($walletId)
	{
		$record = Wallet::with('gnt.gntCategory')->ofUserId($this->user->id)->findOrFail($walletId);
		$uri = env('API_URL',config('user_config.unichain_url'));
		//测算价值
		$list = $record->gnt->each(function ($val) use ($uri, $record) {
			$val->gntCategory->cap = Pricecoinmarketcap::ofSymbol($val->gntCategory->name)->orderBy('last_updated', 'desc')->first();
			$res = sendCurl($uri . '/eth/tokens/balanceOf', ['contract' => $val->gntCategory->address, 'address' => $record->address], null, 'POST');
			$val->balance = $res['value'];
		})->sortByDesc('updated_at')->values()->all();

		return success(compact('record','list'));
	}
}
