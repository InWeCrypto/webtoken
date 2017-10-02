<?php

namespace App\Jobs;

use App\Model\Message;
use App\Model\WalletOrder;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

/**
 * 钱包交易完成情况监听
 *
 * Class ListenWalletOrderStatus
 * @package App\Jobs
 */
class ListenWalletOrderStatus implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	/**
	 * @var WalletOrder
	 */
	protected $hash;
	/**
	 * @var
	 */
	protected $blockNumber;


	/**
	 * ListenWalletOrderStatus constructor.
	 * @param $hash
	 * @param $blockNumber
	 */
	public function __construct($hash, $blockNumber)
	{
		$this->hash = $hash;
		$this->blockNumber = $blockNumber;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		if ($record = WalletOrder::where('txHash', $this->hash)->first()) {
			$status = (Cache::get('min_block_num') + 12 < hexdec($this->blockNumber)) ? 3 : 2;
			$data = [
				'status' => $status,
				'block_number' => $this->blockNumber,

			];
			$status == 3 and $data['finished_at'] = Carbon::now();
			$record->fill($data)->save();
			//todo:mq推送信息
			if ($status == 3) {
				Message::create(['user_id' => $record->user_id, 'title' => '订单完成', 'content' => '订单:' . $record->trade_no . '已交易完成']);
			}
		}
	}
}
