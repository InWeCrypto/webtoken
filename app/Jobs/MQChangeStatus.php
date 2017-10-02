<?php

namespace App\Jobs;

use App\Model\Wallet;
use App\Model\WalletCategory;
use App\Model\WalletOrder;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class MQChangeStatus
 * @package App\Jobs
 */
class MQChangeStatus implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	/**
	 * @var
	 */
	private $hash;

	/**
	 * MQChangeStatus constructor.
	 * @param $hash
	 */
	public function __construct($hash)
	{
		$this->hash = $hash;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		if (!$eth = DB::table('ethtx')->where('hash', $this->hash)->first()) {
			return;
		}
		if (!$wallet = Wallet::ofAddress($eth->addr_from)->orWhere('address', $eth->type ? $eth->addr_token : $eth->addr_to)->first()) {
			return;
		}
		$res = sendCurl(env('API_URL', config('user_config.api_url')) . '/eth/blockNumber', [], [], 'get');
		Log::info('start transaction:');
		DB::transaction(function () use ($eth, $wallet, $res) {
			//检测是否存在
			$orders = WalletOrder::where('hash', $eth->hash)->get();
			if ($orders->count()) {
				/**
				 * 存在记录可推断钱包内部流转
				 */
				//内部交易,直接改变状态和通知
				foreach ($orders as $v) {
					Log::info('执行内部流程订单,切换状态为 2:' . $v->hash);
					$v->fill(['status' => 2])->save();
				}
			} else {
				/**
				 * 非内部流转,可推断wallet表存在的记录一定是用户的记录
				 */
				$insert = [
					"user_id" => $wallet->user_id,
					"wallet_id" => $wallet->id,
					"trade_no" => $eth->hash,
					"block_number" => hexdec($res['value']),
					"handle_fee" => $eth->gas,
					"hash" => $eth->hash,
					"pay_address" => $eth->addr_from,
					"receive_address" => $eth->type ? $eth->addr_token : $eth->addr_to,
				];
				$changData = [
					"fee" => $eth->value,
					"flag" => 'ETH',
				];
				if ($eth->type) {
					//推导代币类型地址
					$tokenKey = Wallet::ofAddress($eth->addr_from)->count() ? $eth->addr_to : $eth->addr_from;
					$category = WalletCategory::where('address', $tokenKey)->first();
					if (!$category) {
						Log::info('记录:' . json_encode($eth) . '转入/转出,查询不到钱包类型');
						throw new \Exception('转入/转出,查询不到钱包类型');
					}
					$changData = strtoupper($category->name) == 'ETH' ? [
						"fee" => $eth->value,
						"flag" => 'ETH',
					] : [
						"fee" => $eth->token_value,
						"flag" => strtoupper($category->name),
					];
				}
				//不存在,添加
				WalletOrder::create($insert + $changData + ['status' => 2, 'own_address' => $wallet->address]);
			}
		});

	}

	/**
	 * @param \Exception $e
	 */
	public function fail(\Exception $e)
	{
		Log::info('roll back,err:' . $e->getMessage());
	}
}
