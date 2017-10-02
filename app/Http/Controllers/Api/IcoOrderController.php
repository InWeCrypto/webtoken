<?php

namespace App\Http\Controllers\Api;

use App\Model\IcoOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class WalletOrderController
 * @package App\Http\Controllers\Api
 */
class IcoOrderController extends BaseController
{
	/**
	 * @param Request $request
	 * @return array
	 */
	public function index(Request $request)
	{
		/**
		 * 更新订单状态
		 */
		updateOrderStatus();
		$list = IcoOrder::with('ico')->whereHas('relationWallet',function ($query){
			$query->ofUserId($this->user->id);
		})->simplePaginate($request->get('per_page'))['data'];

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
			"ico_id" => 'required',
			"trade_no" => 'required',
			"pay_address" => 'required',
			"receive_address" => 'required',
//			"remark"=>'required',
			"fee" => 'required',
			"handle_fee" => 'required',
			"hash" => 'required',
		], [
			"wallet_id.required" => '请填写钱包ID',
			"ico_id.required" => '请填写icoID',
			"trade_no.required" => '请填写交易单号',
			"pay_address.required" => '请填写支付地址',
			"receive_address.required" => '请填写收款地址',
			"fee.required" => '请填写支付金额',
			"handle_fee.required" => '请填写手续费用',
			"hash.required" => '请填写txHash',
		]);
		$res = sendCurl(env('API_URL', config('user_config.api_url')) . '/eth/blockNumber', [], [], 'get');
		$request['block_number'] = hexdec($res['value']);
		DB::transaction(function () use ($request) {
			IcoOrder::create(['user_id' => $this->user->id, 'own_address' => $request->get('pay_address')] + $request->all());
			IcoOrder::create(['user_id' => $this->user->id, 'own_address' => $request->get('receive_address')] + $request->all());
		});
		return success();

	}


	/**
	 * @param $id
	 * @return array
	 */
	public function show($id)
	{
		$record = IcoOrder::with('ico')->ofUserId($this->user->id)->findOrFail($id);

		return success(compact('record'));
	}
}
