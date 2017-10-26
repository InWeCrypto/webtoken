<?php

namespace App\Http\Controllers\Back;


use App\Model\Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Class ConfigController
 * @package App\Http\Controllers\Back
 */
class ConfigController extends BaseController
{
	private $allow = [
		'min_block_num',
		'check_block_num'
	];

	/**
	 * @return array
	 */
	public function index()
	{
		$list = Config::get();
		return success($list);
	}

	/**
	 * @param Request $request
	 * @return array
	 */
	public function store(Request $request)
	{
		$this->validate($request, [
			'min_block_num' => 'required|integer|min:1',
			'check_block_num' => 'required|integer|min:1'
		]);
		foreach ($request->only($this->allow) as $k => $v) {
			if ($re = Config::where('name', $k)->first()) {
				$re->fill(['value' => $v])->save();
			} else {
				Config::create(['name' => $k, 'value' => $v]);
			}
		}
		Cache::forget('min_block_num');
		Cache::forget('check_block_num');
		return success();
	}
}
