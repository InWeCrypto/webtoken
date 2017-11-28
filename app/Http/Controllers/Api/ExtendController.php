<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;


/**
 * Class ExtendController
 * @package App\Http\Controllers\Api
 */
class ExtendController extends BaseController
{
	/**
	 * @var string
	 */
	private $url;

	/**
	 * ExtendController constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->url = env('API_URL', config('user_config.api_url'));
	}

	/**
	 * 获取gas价格
	 * @return array
	 */
	public function getGasPrice()
	{
		return success($this->send('/eth/getGasPrice'));
	}


	/**
	 * @param Request $request
	 * @return array
	 */
	public function getEstimateGas(Request $request)
	{
		$this->validate($request, [
			'to' => 'required'
		]);
		return success($this->send('/eth/getEstimateGas ', $request->all(), [], 'POST'));
	}


	/**
	 * 获取用户交易次数
	 * @param Request $request
	 * @return array
	 */
	public function getTransactionCount(Request $request)
	{
		$this->validate($request, [
			'address' => 'required'
		]);
		return success($this->send('/eth/getTransactionCount', $request->all(), [], 'POST'));
	}


	/**
	 * 发送签名后的交易
	 * @param Request $request
	 * @return array
	 */
	public function sendRawTransaction(Request $request)
	{
		$this->validate($request, [
			'data' => 'required'
		]);
		return success($this->send('/eth/sendRawTransaction', $request->all(), [], 'POST'));
	}

	/**
	 * 获得交易详情
	 * @param Request $request
	 * @return array
	 */
	public function getTransaction(Request $request)
	{
		$this->validate($request, [
			'txHash' => 'required'
		]);
		return success($this->send('/eth/getTransaction', $request->all(), [], 'POST'));
	}

	/**
	 * 获得账户余额(ETH)
	 * @param Request $request
	 * @return array
	 */
	public function getBalance(Request $request)
	{
		$this->validate($request, [
			'address' => 'required'
		]);
		return success($this->send('/eth/getBalance', $request->all(), [], 'POST'));
	}

	/**
	 * 获得代币余额(Tokens)
	 * @param Request $request
	 * @return array
	 */
	public function balanceOf(Request $request)
	{
		$this->validate($request, [
			'contract' => 'required',
			'address' => 'required'
		]);
		return success($this->send('/eth/tokens/balanceOf', $request->all(), [], 'POST'));
	}

	/**
	 * 获得代币总发行量
	 * @param Request $request
	 * @return array
	 */
	public function totalSupply(Request $request)
	{
		$this->validate($request, [
			'contract' => 'required',
		]);
		return success($this->send('/eth/tokens/totalSupply', $request->all(), [], 'POST'));
	}

	/**
	 * 代币转账ABI
	 * @param Request $request
	 * @return array
	 */
	public function transferABI(Request $request)
	{
		$this->validate($request, [
			'contract' => 'required',
			'to' => 'required',
			'value' => 'required',
		]);
		return success($this->send('/eth/tokens/transferABI', $request->all(), [], 'POST'));
	}

	/**
	 * 交易市场价格
	 * @param Request $request
	 * @return array
	 */
	public function priceList(Request $request)
	{
		return success($this->send('/market/priceList', $request->all(), [], 'get'));
	}

	/**
	 * 当前最大块号
	 * @param Request $request
	 * @return array
	 */
	public function blockNumber(Request $request)
	{
		return success($this->send('/eth/blockNumber', $request->all(), [], 'get'));
	}

	/**
	 * 获得当前块的发生速度（每秒多少块）,用于评估众筹开始时间
	 * @param Request $request
	 * @return array
	 */
	public function blockPerSecond(Request $request)
	{
		return success($this->send('/eth/blockPerSecond', $request->all(), [], 'get'));
	}

	/**
	 * 获取事务
	 * @param Request $request
	 * @return array
	 */
	public function getTransactions(Request $request)
	{
		$this->validate($request, [
			'from' => 'required',
			'to' => 'required',
			'address' => 'required',
		]);
		return success($this->send('/btc/getTransactions', $request->all(), [], 'POST'));
	}

	/**
	 * 获取事务详情
	 * @param Request $request
	 * @return array
	 */
	public function getTransactionById(Request $request)
	{
		$this->validate($request, [
			'txid' => 'required',
		]);
		return success($this->send('/btc/getTransactionById', $request->all(), [], 'POST'));
	}

	/**
	 * 查询费率
	 * @param Request $request
	 * @return array
	 */
	public function estimateFee(Request $request)
	{
		$this->validate($request, [
			'nbBlocks' => 'required',
		]);
		return success($this->send('/btc/estimatefee', $request->all(), [], 'GET'));
	}

	/**
	 * 查询余额
	 * @param Request $request
	 * @return array
	 */
	public function address(Request $request)
	{
		$this->validate($request, [
			'address' => 'required',
		]);
		return success($this->send('/btc/address', $request->all(), [], 'POST'));
	}

	/**
	 * 获取neo的claim utxo
	 * @param  Request $request
	 * @return array
	 */
	public function getNeoClaimUtxo(Request $request)
	{
		$this->validate($request, [
			'address' => 'required',
		]);
		$uri = env('TRADER_URL_NEO', config('user_config.api_url')) . '/extend';
		$param = [
			"jsonrpc" => "2.0",
			"method" => "claim",
			"params" => [$request->get('address')],
			"id" => 0
		];
		return success(sendCurl($uri, $param, null, 'POST'));
	}


	/**
	 * @param $uri
	 * @param array $body
	 * @param array $header
	 * @param string $type
	 * @return mixed
	 * @throws \Exception
	 */
	private function send($uri, $body = [], $header = [], $type = "GET")
	{
		return sendCurl($this->url . $uri, $body, $header, $type);
	}
}
