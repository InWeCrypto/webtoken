<?php

namespace App\Http\Controllers\Api;


use App\Model\Message;
use Illuminate\Http\Request;

/**
 * Class MessageController
 * @package App\Http\Controllers\Api
 */
class MessageController extends BaseController
{
	/**
	 * @param Request $request
	 * @return array
	 */
	public function index(Request $request)
	{
		$this->validate($request, [
			'type' => 'required',//0未读,1已读
		]);
		$list = Message::ofUserId($this->user->id)->ofType($request->get('type', 0))->latest()->simplePaginate($request->get('per_page'))->toArray();
		$list = $list['data'];

		return success(compact('list'));
	}

	/**
	 * @param Request $request
	 * @param $id
	 * @return array
	 */
	public function update(Request $request, $id)
	{
		return Message::ofUserId($this->user->id)->where('id', $id)->update(['type' => 1]) ? success() : fail();
	}

	/**
	 * @param $id
	 * @return array
	 */
	public function destroy($id)
	{
		return Message::ofUserId($this->user->id)->findOrFail($id)->delete() ? success() : fail();
	}
}
