<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\Ico;
use Illuminate\Http\Request;

/**
 * Class IcoController
 * @package App\Http\Controllers\Api
 */
class IcoController extends Controller
{
	/**
	 * 详情页面,直接请求返回字段中的url
	 *
	 * @param Request $request
	 * @return array
	 */
	public function index(Request $request)
	{
		$list = Ico::pass()->show()->latest()->simplePaginate($request->get('per_page'))->toArray();
		$list = $list['data'];

		return success(compact('list'));
	}

}
