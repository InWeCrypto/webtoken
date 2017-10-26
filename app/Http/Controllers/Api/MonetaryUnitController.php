<?php

namespace App\Http\Controllers\Api;

use App\Model\MonetaryUnit;
use App\Model\UserMonetaryUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class MonetaryUnitController
 * @package App\Http\Controllers\Api
 */
class MonetaryUnitController extends BaseController
{
	/**
	 * 获取货币单位及用户选择情况
	 *
	 * @return array
	 */
	public function index()
	{
		$list = MonetaryUnit::withCount(['userUnit' => function ($query) {
			$query->ofUserId($this->user->id);
		}])->get();

		return success(compact('list'));
	}


	/**
	 * 变更用户选择的货币单位
	 *
	 * @param Request $request
	 * @return array
	 */
	public function store(Request $request)
	{
		$this->validate($request, [
			'monetary_unit_id' => 'required'
		], [
			'monetary_unit_id.required' => '请选择货币单位'
		]);

		try {
			DB::transaction(function () use ($request) {
				//删除原有的
				UserMonetaryUnit::ofUserId($this->user->id)->delete();
				UserMonetaryUnit::create(['user_id' => $this->user->id, 'monetary_unit_id' => $request->get('monetary_unit_id')]);
			});
			return success();
		} catch (\Exception $ex) {
			return fail('', $ex->getMessage());
		}
	}
}
