<?php

namespace App\Http\Controllers\Api;

use App\Model\Wallet;
use App\Model\WalletOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class WalletOrderController
 * @package App\Http\Controllers\Api
 */
class WalletOrderController extends BaseController
{
	/**
	 * @param Request $request
	 * @return array
	 */
	public function index(Request $request)
	{
		$this->validate($request, [
			'wallet_id' => 'required',
//			'type' => 'required',
			'flag' => 'required',
		], [
			"wallet_id.required" => '请填写钱包ID',
//			"type.required" => '请选择交易类型,1转账,2收款',
			"flag.required" => '请填写钱包/代币类型:如eth',
		]);
		/**
		 * 更新订单状态
		 */
		updateOrderStatus();
//		$type = $request->get('type', 1);
		$walletId = $request->get('wallet_id');
//		if (1 == $type) {
//			$query = WalletOrder::ofFlag($request->get('flag','ETH'))->ofUserId($this->user->id)->ofWalletId($walletId);
//		} else {
//			$query = WalletOrder::ofFlag($request->get('flag','ETH'))->whereHas('relationReceiveWallet', function ($query) use ($walletId) {
//				$query->ofUserId($this->user->id)->where('id', $walletId);
//			});
//		}
//		$query = WalletOrder::ofFlag($request->get('flag', 'ETH'))->where(function ($query) use ($walletId) {
//			$query->where(function ($query) use ($walletId) {
//				$query->ofUserId($this->user->id)->ofWalletId($walletId);
//			})->orWhere(function ($query) use ($walletId) {
//				$query->whereHas('relationReceiveWallet', function ($query) use ($walletId) {
//					$query->ofUserId($this->user->id)->where('id', $walletId);
//				});
//			});
//		});
		$query = WalletOrder::ofFlag($request->get('flag', 'ETH'))->whereHas('relationWallet', function ($query) use ($walletId) {
			$query->ofUserId($this->user->id)->where('id', $walletId);
		});
		$list = $query->latest()->simplePaginate($request->get('per_page'))->toArray();
		$list = $list['data'];

		return success(compact('list'));
	}


	/**
	 * @param Request $request
	 * @return array
	 */
	public function store(Request $request)
	{
		$this->validate($request, [
			"wallet_id" => 'required',
//			"trade_no" => 'required',
			"pay_address" => 'required',
			"receive_address" => 'required',
//			"remark"=>'required',
			"fee" => 'required',
			"handle_fee" => 'required',
			"data" => 'required',
			"flag" => 'required'
		], [
			"wallet_id.required" => '请填写钱包ID',
//			"trade_no.required" => '请填写交易单号',
			"pay_address.required" => '请填写支付地址',
			"receive_address.required" => '请填写收款地址',
			"fee.required" => '请填写支付金额',
			"handle_fee.required" => '请填写手续费用',
			"data.required" => '请填写data',
			"flag.required" => '请填写钱包/代币类型:如eth',
		]);
		try {
			$res = sendCurl(env('API_URL', config('user_config.api_url')) . '/eth/blockNumber', [], [], 'get');
			$request['block_number'] = hexdec($res['value']);
			//hash
			$res = sendCurl(env('API_URL', config('user_config.api_url')) . '/eth/sendRawTransaction', ['data' => $request->get('data')], [], 'post');
			$request['trade_no'] = $request['hash'] = $res['txHash'];
			$sec = Wallet::where('address', $request->get('receive_address'))->first();
			DB::transaction(function () use ($request, $sec) {
				WalletOrder::create(['user_id' => $this->user->id, 'own_address' => $request->get('pay_address')] + $request->all());
				if ($sec) {
					WalletOrder::create(['user_id' => $sec->user_id, 'own_address' => $request->get('receive_address'), 'wallet_id' => $sec->id] + $request->except('wallet_id'));
				}
			});
			return success();

		} catch (\Exception $e) {
			Log::info('创建订单失败!'. '订单原始数据:' . json_encode($request->all()).',错误原因:' . $e->getMessage() );
			throw $e;
		}

//		try {
//			DB::beginTransaction();
//			WalletOrder::create(['user_id' => $this->user->id, 'own_address' => $request->get('pay_address')] + $request->all());
//			if ($sec = Wallet::where('address', $request->get('receive_address'))->first()) {
//				WalletOrder::create(['user_id' => $sec->user_id, 'own_address' => $request->get('receive_address'), 'wallet_id' => $sec->id] + $request->except('wallet_id'));
//
//			}
//			DB::commit();
//			return success();
//		} catch (\Exception $e) {
//			Log::error('add order err:' . $e->getMessage());
//			DB::rollBack();
//			throw $e;
//		}

	}


	/**
	 * @param $id
	 * @return array
	 */
	public function show($id)
	{
		$record = WalletOrder::where(function ($query) {
			$query->ofUserId($this->user->id)->orWhereHas('relationReceiveWallet', function ($query) {
				$query->ofUserId($this->user->id);
			});
		})->findOrFail($id);

		return success(compact('record'));
	}
}
