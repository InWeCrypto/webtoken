<?php

namespace App\Http\Controllers\Api;


use App\Model\Coinmarketcapcurrent;
use App\Model\MarketCategory;
use App\Model\Pricecoinmarketcap;
use App\Model\UserMarketCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class MarketCategoryController
 * @package App\Http\Controllers\Api
 */
class MarketCategoryController extends BaseController
{
	/**
	 * @param Request $request
	 * @return array
	 */
	public function index(Request $request)
	{
		if ($request->has('is_all')) {
			$data = MarketCategory::withCount(['relationUser' => function ($query) {
				$query->where('user_id', $this->user->id);
			}])->get()->groupBy('source');
		} else {
			$data = UserMarketCategory::ofUserId($this->user->id)->with('market')->orderBy('created_at', 'desc')->get()->pluck('market')->each(function ($v) {
				$v->relationCapMin = Coinmarketcapcurrent::ofSymbol($v->flag)->where('_group', 'pricecoinmarketcap')->first();
				$v->relationCap = Pricecoinmarketcap::ofSymbol($v->flag)->orderBy('last_updated', 'desc')->first();
			})->groupBy('source');
		}
		$list = [];
		$data->each(function ($val, $key) use (&$list) {
			$list[] = ['name' => $key, 'data' => $val];
		});
		return success(compact('list'));
	}


	/**
	 * 添加行情分类
	 * @param Request $request
	 * @return array
	 * @throws \Exception
	 */
	public function store(Request $request)
	{
		$this->validate($request, [
			'market_ids' => 'required|json'
		], [
			'market_ids.required' => '请填写用户所选行情ID',
			'market_ids.json' => '请用户所选行情ID必须为json格式',

		]);
		$marketIds = json_decode($request->get('market_ids'), true);
		if (!is_array($marketIds)) {
			throw new \Exception('请用户所选行情ID必须为json格式');
		}
		DB::transaction(function () use ($marketIds) {
			//删除原来已有记录
			UserMarketCategory::ofUserId($this->user->id)->delete();
			foreach ($marketIds as $marketId) {
				$data[] = new UserMarketCategory(['market_id' => $marketId]);
			}
			$this->user->userMarket()->saveMany($data);
		});
		return success();
	}

	/**
	 * @param Request $request
	 * @param $id
	 * @return array
	 */
	public function show(Request $request, $id)
	{
//		$currency = $request->get('currency', 'cny');//币种
//		if(!in_array($currency,['cny','usd','btc'])){
//			return fail('','不支持的币种,目前仅支持,cny,usd,btc');
//		}
		//1 min,2 hour,3 day,4 week
		$type = $request->get('type', 1);
		$time = Carbon::now();
		$capDb = 'coinmarketcap';
		switch ($type) {
			case 1:
				$dstart = (clone $time)->subHours(4)->timestamp;
				$capDb .= '5min';
				break;
			case 2:
				$dstart = (clone $time)->subHours(62)->timestamp;
				$capDb .= 'hour';
				break;
			case 3:
				$dstart = (clone $time)->subDays(62)->timestamp;
				$capDb .= 'day';
				break;
			case 4:
				$dstart = (clone $time)->subWeeks(62)->timestamp;
				$capDb .= 'week';
				break;
		}
		$start = $request->get('start', $dstart);
		$end = $request->get('end', $time->timestamp);
		$record = MarketCategory::findOrFail($id);
		$record->relationCapMin = Coinmarketcapcurrent::ofSymbol($record->flag)->where('_group', 'pricecoinmarketcap')->first();
		$record->relationCap = Pricecoinmarketcap::ofSymbol($record->flag)->orderBy('last_updated', 'desc')->first();
		$list = DB::table($capDb)->where('symbol', $record->flag)->whereBetween('timestamp', [$start, $end])->get();

		return success(compact('record', 'list'));
	}
}
