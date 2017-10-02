<?php

namespace App\Console\Commands;

use App\Model\Coinmarketcapcurrent;
use App\Model\MarketNotification;
use App\Model\Message;
use App\Services\Push;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Class RunNotification
 * @package App\Console\Commands
 */
class RunNotification extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'run:notification';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'run consumer for notification to listen notification status change';


	/**
	 * RunNotification constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$timestamp = time();
		while (true) {
			try {
				$list = Coinmarketcapcurrent::where('timestamp', '>', $timestamp)->get();
				foreach ($list as $v) {
					$pushList = MarketNotification::whereHas('market', function ($query) use ($v) {
						$query->where('flag', $v->symbol);
					})->with(['market', 'user'])->where(function ($query) use ($v) {
						$query->where(function ($query) use ($v) {
							$query->where('lower_limit', '<', $v->price_usd_last)->orWhere('upper_limit', '>', $v->price_usd_last);
						})->orWhere(function ($query) use ($v) {
							$query->where('lower_limit', '<', $v->price_cny_last)->orWhere('upper_limit', '>', $v->price_cny_last);
						});
					})->get();
					foreach ($pushList as $item) {
						$content = "您关注的{$item->market->name}已达到{$item->upper_limit}上限，请密切关注该行情。";
						if (($item->currency == 'usd' && $item->lower_limit > $v->price_usd_last) || ($item->currency == 'cny' && $item->lower_limit > $v->price_cny_last)) {
							$content = "您关注的{$item->market->name}已达到{$item->lower_limit}下限，请密切关注该行情。";
						}
						Push::doAction('行情提醒信息', $content, ['resource_type' => 'MARKET', 'resource_id' => $item->market_id], 'ACCOUNT', $item->user->open_id);
						Message::create(['user_id' => $item->user_id, 'title' => '系统消息', 'content' => $content, 'resource_type' => 'MARKET', 'resource_id' => $item->market_id]);
						$item->delete();
					}
				}
				sleep(10);
				$timestamp += 10;
			} catch (\Exception $e) {
				Log::info($e->getMessage());
			}
		}
	}
}
