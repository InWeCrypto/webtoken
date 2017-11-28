<?php

namespace App\Http\Controllers\Api;


use App\Model\Wallet;
use Illuminate\Http\Request;

/**
 * Class WalletController
 * @package App\Http\Controllers\Api
 */
class WalletController extends BaseController
{

	/**
	 * @return array
	 */
	public function index()
	{
		$list = Wallet::ofUserId($this->user->id)->with('category')->get();
		return success(compact('list'));
	}

	/**
	 * 添加钱包
	 * @param Request $request
	 * @return array
	 */
	public function store(Request $request)
	{
		$this->validate($request, [
			'category_id' => 'required',
			'name' => 'required',
			'address' => 'required|unique:wallets,address,NULL,id,user_id,'.$this->user->id.',deleted_at,NULL',
		], [
			'category_id.required' => '请填写钱包类型',
			'name.required' => '请填写钱包名称',
			'address.required' => '请填写钱包地址',
		]);

		if (Wallet::ofUserId($this->user->id)->ofAddress($request->get('address'))->count()) {
			return fail('', '该钱包地址已存在');
		}
		try {
			$this->checkAddress($request->get('category_id'), $request->get('address'));
		} catch (\Exception $e) {
			return fail('', '该钱包地址不是合法的地址');
		}
		$record = Wallet::create(['user_id' => $this->user->id] + $request->all());
		return $record ? success(compact('record')) : fail();
	}


	/**
	 * @param Request $request
	 * @param $id
	 * @return array
	 */
	public function update(Request $request, $id)
	{
		$this->validate($request, [
			'name' => 'required',
		], [
			'name.required' => '请填写钱包名称',
		]);
		$wallet = Wallet::ofUserId($this->user->id)->findOrFail($id);
		return $wallet->fill($request->all())->save() ? success() : fail();
	}


	/**
	 * @param $id
	 * @return array
	 */
	public function destroy($id)
	{
		$wallet  = Wallet::ofUserId($this->user->id)->findOrFail($id);
		$address = $wallet->address;
		
		$wallet_message_url = env('TRADER_WALLET_URL_NEO', config('user_config.unichain_url')) . '/wallet/' .$this->user->open_id . '/' . $address;
		sendCurl($wallet_message_url, [], null, 'DELETE');
		return $wallet->delete() ? success() : fail();
	}

	/**
	 * 
	 * @return bool
	 */
	private function checkAddress($category_id, $address)
	{
		$result = '';
		$walletCategory = \App\Model\WalletCategory::findOrFail($category_id,['name']);
		switch (strtolower($walletCategory->name)) {
			case 'eth':
				$result = sendCurl(
							env('TRADER_URL_ETH', config('user_config.unichain_url')) . '/eth/getBalance',
							compact('address'),
							null,
							'POST');
				break;
			case 'neo':
				$result = sendCurl(
								env('TRADER_URL_NEO', config('user_config.unichain_url')),
								[
									'jsonrpc' => '2.0',
									'method' => 'getaccountstate',
									'params' => [$address],
								    'id' => 1
								],
								null,
								'POST');
				if(!empty($result['error'])){
					throw new \Exception('不是有效的neo钱包地址!');
				}
				// 推送消息接口
				$wallet_message_url = env('TRADER_WALLET_URL_NEO', config('user_config.unichain_url')) . '/wallet/' .$this->user->open_id . '/' . $address;
				sendCurl($wallet_message_url, [], null, 'POST');
				break;
			default:
				throw new \Exception('未知钱包类型');
				break;
		}
		return true;
	}

}
