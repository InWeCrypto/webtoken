<?php

namespace App\Http\Controllers\Api;


use App\Model\Coinmarketcapcurrent;
use App\Model\MarketCategory;
use App\Model\MarketNotification;
use App\Model\Pricecoinmarketcap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class MarketNotificationController
 * @package App\Http\Controllers\Api
 */
class MarketNotificationController extends BaseController
{
	/**
	 * 用户提醒列表
	 * @return array
	 */
	public function index()
	{
		$list = MarketCategory::with(['relationNotification' => function ($query) {
			$query->ofUserId($this->user->id);
		}])->whereHas('relationUser',function ($query){
			$query->ofUserId($this->user->id);
		})->withCount(['relationNotification' => function ($query) {
			$query->ofUserId($this->user->id);
		}])->get()->each(function ($v){
			$v->relationCapMin = $v->relationCap= [];
//			if($v->relationNotificationCount){
			$v->relationCapMin = Coinmarketcapcurrent::ofSymbol($v->flag)->where('_group','pricecoinmarketcap')->first();
			$v->relationCap = Pricecoinmarketcap::ofSymbol($v->flag)->orderBy('last_updated', 'desc')->first();
//			}
		});

		return success(compact('list'));
	}


	/**
	 * 添加提醒
	 * @param Request $request
	 * @return array
	 * @throws \Exception
	 */
	public function store(Request $request)
	{
		$this->validate($request, [
			'market_arr' => 'required|json',//格式:[{"market_id":11,"upper_limit":11,"lower_limit":222}]
			'currency'=>'required',
		], [
			'market_arr.required' => '请填写用户所选行情ID',
			'market_arr.json' => '用户所选行情ID必须为json格式',
			'currency.required' => '请填写需要添加提醒的币种',
		]);

		$marketArr = json_decode($request->get('market_arr'), true);
		if (!is_array($marketArr)) {
			throw new \Exception('用户所选行情ID必须为json格式');
		}
		DB::transaction(function () use ($marketArr,$request) {
			//删除原来已有记录
			MarketNotification::ofUserId($this->user->id)->delete();
			foreach ($marketArr as $v) {
				$data[] = new MarketNotification($v+['currency'=>strtolower($request->get('currency'))]);
			}
			$this->user->userMarketNotification()->saveMany($data);
		});
		return success();
	}

	/**
	 * @param Request $request
	 * @param $id
	 * @return array
	 */
	public function update(Request $request, $id)
	{
		DB::transaction(function () use ($request, $id) {
			$record = MarketNotification::ofUserId($this->user->id)->findOrFail($id);
			$record->fill($request->all())->save();
			//todo:推送监听
		});
		return success();
	}

	/**
	 * @param $id
	 * @return array
	 */
	public function destroy($id)
	{
		return MarketNotification::ofUserId($this->user->id)->findOrFail($id)->delete() ? success() : fail();
	}
}
