<?php

namespace App\Jobs;

use App\Model\IcoOrder;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

/**
 * ICO交易完成情况监听
 *
 * Class ListenWalletOrderStatus
 * @package App\Jobs
 */
class ListenIcoOrderStatus implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	/**
	 * @var WalletOrder
	 */
	protected $order;


	/**
	 * ListenWalletOrderStatus constructor.
	 * @param IcoOrder $order
	 */
	public function __construct(IcoOrder $order)
	{
		$this->order = $order;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		//检测transaction表是否有订单成功记录
		if ($record = DB::talble('transaction')->where('txHash', $this->order->hash)->first()) {
			$this->order->fill(['status' => 2, 'finished_at' => Carbon::createFromFormat($record->ttimestamp)])->save();

			//todo:mq推送信息

		} else {
			dispatch(new self($this->order));
		}
	}
}
