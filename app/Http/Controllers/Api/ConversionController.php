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
		// \PriceCoinmarketcap::test();
		$this->validate($request, [
			'wallet_ids' => 'required|json'
		]);
		$wallets = json_decode($request->get('wallet_ids'), true);
		$list = Wallet::with('category.icoInfo')
			->ofUserId($this->user->id)
			->whereIn('id', $wallets)
			->get()->each(function ($val){
				if(! $ico_name = !empty($val->category->icoInfo) ? $val->category->icoInfo->name : null){
					\Log::info('获取'.$val->category->name.'的API名称失败,请检查ico_list表中是否存在!');
				}
				$val->category->cap = \PriceCoinmarketcap::getPrice($ico_name) ?: null;
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
		// $record = Wallet::with('gnt.gntCategory.icoInfo')->findOrFail($walletId);
		$record = Wallet::with('gnt.gntCategory')->ofUserId($this->user->id)->findOrFail($walletId);
		//测算价值
		switch(strtolower($record->category->name)){
			case 'eth':
				// dd($record->gnt->toArray());
				$list = $record->gnt->each(function ($val) use ($record) {
					if(! $ico_name = !empty($val->category->icoInfo) ? $val->category->icoInfo->name : null){
						\Log::info('获取'.$val->gntCategory->name.'的API名称失败,请检查ico_list表中是否存在!');
					}
					$val->gntCategory->cap = \PriceCoinmarketcap::getPrice($ico_name) ?: null;
					$uri   = env('API_URL',config('user_config.unichain_url')) . '/eth/tokens/balanceOf';
					$param = [
						'contract' => $val->gntCategory->address,
						'address' => $record->address
					];
					$res = sendCurl($uri, $param, null, 'POST');
					$val->balance = $res['value'];
				})->sortByDesc('updated_at')->values()->all();
			break;
			case 'neo':
				// neo 钱包没有代币,默认为gas
				unset($record->gnt);
				// neo 余额
				$record->balance = $this->getWalletBalance('neo', $record->address);
				$record->cap = \PriceCoinmarketcap::getPrice('neo') ?: null;
			case 'gas':
				$uri = env('TRADER_URL_NEO',config('user_config.unichain_url')) . '/extend';
				$param = [
					'jsonrpc' => '2.0',
					'method' => 'claim',
					'method' => 'claim',
					'params' => [$record->address],
					'id' => 0
				];
				$res = sendCurl($uri, $param, null, 'POST');
				// neo 默认代币 gas 
				$gnt = [
					'name' => 'gas',
					'unavailable' => $res['result']['Unavailable'] ?: 0,
					'available' => $res['result']['Available'] ?: 0,
					'balance' => $this->getWalletBalance('neo', $record->address, \Request::header('neo-gas-asset-id')),
					'cap' => \PriceCoinmarketcap::getPrice('gas') ?: null
				];
				$record->gnt = [collect($gnt)];
			break;
		}

		return success(compact('record','list'));
	}

	// 钱包余额
	public function getWalletBalance($category_name, $address, $asset_id = null){
		$return = 0;
		switch(strtolower($category_name)){
			case 'eth':
				$address= strtolower($address);
				$uri    = env('TRADER_URL_ETH', config('user_config.unichain_url')) . '/eth/getBalance';
				$res    = sendCurl($uri, compact('address'), null, 'POST');
				$return = $res['value'];
			break;
			case 'neo':
				$neo_asset_id = $asset_id ?: \Request::header('neo-asset-id');
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
