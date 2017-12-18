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
		$walletId = $request->get('wallet_id');
		$flag     = strtoupper($request->get('flag', 'ETH'));
		switch(strtolower($flag)){
			case 'eth':
				$wallet = Wallet::ofUserId($this->user->id)->findOrFail($walletId);
				if(!$asset_id = $request->get('asset_id')){
					throw new \Exception('请填写 NEO ASSET ID');
				}
				$wallet = Wallet::ofUserId($this->user->id)->findOrFail($walletId);
				$url = env('TRADER_WALLET_URL_ETH', config('user_config.api_url')) . '/orders';
				$url.= '/'.$wallet->address;
				$url.= '/'.$asset_id;
				$url.= '/'.$request->get('offset', $request->get('page', 0));
				$url.= '/'.$request->get('size', 10);
				$res = sendCurl($url);

				$list = [];

				foreach($res as $v){
					$temp = [
						'trade_no' => $v['tx'],
						'hash' => $v['tx'],
						'pay_address' => $v['from'],
						'receive_address' => $v['to'],
						'block_number' => $v['blocks'],
						'fee' => $v['value'],
						'status' => empty($v['confirmTime']) ? 0 : 1,
						'created_at' => $v['createTime']
					];

                    $cont = !empty($v['context']) ? json_decode($v['context'], true) : [
                        "remark" => "",
                        "handle_fee" => "1925730000000000"
                    ];

					$list[] = array_merge($cont, $temp);
				}




				// 下面代码是之前订单存取在数据库的代码
				// /**
				//  * 更新订单状态
				//  */
				// updateOrderStatus();
				// // $type = $request->get('type', 1);
				// // if (1 == $type) {
				// // 	$query = WalletOrder::ofFlag($request->get('flag','ETH'))->ofUserId($this->user->id)->ofWalletId($walletId);
				// // } else {
				// // 	$query = WalletOrder::ofFlag($request->get('flag','ETH'))->whereHas('relationReceiveWallet', function ($query) use ($walletId) {
				// // 		$query->ofUserId($this->user->id)->where('id', $walletId);
				// // 	});
				// // }
				// // $query = WalletOrder::ofFlag($request->get('flag', 'ETH'))->where(function ($query) use ($walletId) {
				// // 	$query->where(function ($query) use ($walletId) {
				// // 		$query->ofUserId($this->user->id)->ofWalletId($walletId);
				// // 	})->orWhere(function ($query) use ($walletId) {
				// // 		$query->whereHas('relationReceiveWallet', function ($query) use ($walletId) {
				// // 			$query->ofUserId($this->user->id)->where('id', $walletId);
				// // 		});
				// // 	});
				// // });
				// $query = WalletOrder::ofFlag($flag)->whereHas('relationWallet', function ($query) use ($walletId) {
				// 	$query->ofUserId($this->user->id)->where('id', $walletId);
				// });
                //
				// $list = $query->latest()->simplePaginate($request->get('per_page'))->toArray();
				// $list = $list['data'];
			break;
			case 'neo':
				if(!$asset_id = $request->get('asset_id')){
					throw new \Exception('请填写 NEO ASSET ID');
				}
				$wallet = Wallet::ofUserId($this->user->id)->findOrFail($walletId);
				$url = env('TRADER_WALLET_URL_NEO', config('user_config.api_url')) . '/orders';
				$url.= '/'.$wallet->address;
				$url.= '/'.$asset_id;
				$url.= '/'.$request->get('offset', $request->get('page', 0));
				$url.= '/'.$request->get('size', 10);
				$list = sendCurl($url);
			break;
		}

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
			switch(strtolower($request->get('flag'))){
				case 'eth':
					if(!$asset_id = $request->get('asset_id')){
						throw new \Exception('ETH 请求接口,请传入转账资产类型ID!');
					}
					// $block_number_uri = env('API_URL', config('user_config.api_url')) . '/eth/blockNumber';
					// $res              = sendCurl($block_number_uri, [], [], 'get');
					// $request['block_number'] = hexdec($res['value']);
					// //hash
					$send_raw_transaction_uri   = env('API_URL', config('user_config.api_url')) . '/eth/sendRawTransaction';
					$send_raw_transaction_param = [
						'data' => $request->get('data')
					];
					$res = sendCurl($send_raw_transaction_uri, $send_raw_transaction_param, [], 'post');
					if(empty($res['txHash'])){
						throw new \Exception('ETH 请求借口,发起交易失败!'. implode('|', $res));
					}
					$trade_no = $res['txHash'];
					$context = [
						'remark' => $request->get('remark'),
						'handle_fee' => $request->get('handle_fee')
					];
					// 调用eth创建订单接口
					$order_uri   = env('TRADER_WALLET_URL_ETH') . '/order';
					$order_param = [
						'tx' => $trade_no,
						'asset' => $asset_id,
						'from' => $request->get('pay_address'),
						'to' => $request->get('receive_address'),
						'value' => $request->get('fee'),
						'context' => json_encode($context),
					];
					// 返回200就算成功
					// 失败就直接throw
					try{
						sendCurl($order_uri, $order_param, null, 'POST');
					} catch (\Exception $e) {
						throw new \Exception('调用'.$order_uri.'接口失败!');
					}

					// $request['trade_no'] = $request['hash'] = $res['txHash'];
					// $sec = Wallet::where('address', $request->get('receive_address'))->first();
					// DB::transaction(function () use ($request, $sec) {
					// 	WalletOrder::create([
					// 		'user_id' => $this->user->id,
					// 		'own_address' => $request->get('pay_address')
					// 		] + $request->all()
					// 	);
					// 	if ($sec) {
					// 		WalletOrder::create([
					// 			'user_id' => $sec->user_id,
					// 			'own_address' => $request->get('receive_address'),
					// 			'wallet_id' => $sec->id
					// 			] + $request->except('wallet_id')
					// 		);
					// 	}
					// });
				break;
				case 'neo':
					if(!$trade_no = $request->get('trade_no')){
						throw new \Exception('NEO 请求接口,请填写订单号!');
					}
					if(!$asset_id = $request->get('asset_id')){
						throw new \Exception('NEO 请求接口,请传入转账资产类型ID!');
					}

					// 发起交易
					$send_raw_transaction_uri   = env('TRADER_URL_NEO', config('user_config.api_url'));
					$send_raw_transaction_param = [
						'jsonrpc' => '2.0',
						'method' => 'sendrawtransaction',
						'params' => [$request->get('data')],
						'id' => '1'
					];

					$res = sendCurl($send_raw_transaction_uri, $send_raw_transaction_param, [], 'post');
					// $res['result'] == false 表示失败
					if(empty($res['result'])){
						throw new \Exception('NEO 请求接口,发起交易失败!' .implode("|",$res));
					}

					// 调用Neo创建订单接口
					$order_uri   = env('TRADER_WALLET_URL_NEO', config('user_config.api_url')) . '/order';
					$order_param = [
						'tx' => $trade_no,
						'asset' => $asset_id,
						'from' => $request->get('pay_address'),
						'to' => $request->get('receive_address'),
						'value' => $request->get('fee'),
						'remark' => $request->get('remark'),
					];
					// 返回200就算成功
					// 失败就直接throw
					try{
						sendCurl($order_uri, $order_param, null, 'POST');
					} catch (\Exception $e) {
						throw new \Exception('调用'.$order_uri.'接口失败!');
					}
				break;
			}
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
