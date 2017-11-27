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
		$list = Wallet::with('category')
			->ofUserId($this->user->id)
			->whereIn('id', $wallets)
			->get()->each(function ($val) use ($uri) {
				$val->category->cap = Pricecoinmarketcap::ofSymbol($val->category->name)
										->orderBy('last_updated', 'desc')
										->first();
				//钱包余额
				$val->balance = $this->getWalletBalance($val->category->name, $val->address);
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

	// 钱包余额
	public function getWalletBalance($category_name, $address){
		$return = 0;
		switch(strtolower($category_name)){
			case 'eth':
				$uri    = env('TRADER_URL_ETH', config('user_config.unichain_url')) . '/eth/getBalance';
				$res    = sendCurl($uri, compact('address'), null, 'POST');
				$return = $res['value'];
			break;
			case 'neo':
				$neo_asset_id = \Request::header('neo-asset-id');
				$uri          = env('TRADER_URL_NEO', config('user_config.unichain_url'));
				$param        = [
					'jsonrpc' => '2.0',
					'method' => 'getaccountstate',
					'params' => [$address],
					'id' => 1
				];
				$res = sendCurl($uri, $param, null, 'POST');
				if(!empty($res['result']['balances'])){
					foreach($res['result']['balances'] as $val){
						if(strcasecmp($val['asset'], $neo_asset_id) == 0){
							$return = $val['value'];
							break;
						}
					}
				}
			break;
		}
		return $return;
	}
}
