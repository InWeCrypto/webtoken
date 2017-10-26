<?php

namespace App\Http\Controllers\Api;


use App\Model\UserContact;
use Illuminate\Http\Request;

/**
 * Class UserContactController
 * @package App\Http\Controllers\Api
 */
class UserContactController extends BaseController
{
	/**
	 * @param Request $request
	 * @return array
	 */
	public function index(Request $request)
	{
		$list = UserContact::with('wallet.user')->ofUserId($this->user->id)->ofCategoryId($request->get('category_id'))->get();
		return success(compact('list'));
	}

	/**
	 * @param Request $request
	 * @return array
	 */
	public function store(Request $request)
	{
		$this->validate($request, [
			'category_id' => 'required',
			'name' => 'required',
			'address' => 'required',
		]);
		return UserContact::create(['user_id' => $this->user->id] + $request->all()) ? success() : fail();
	}

	/**
	 * @param $id
	 * @return array
	 */
	public function show($id)
	{
		$record = UserContact::ofUserId($this->user->id)->findOrFail($id);
		return success(compact('record'));
	}

	/**
	 * @param Request $request
	 * @param $id
	 * @return array
	 */
	public function update(Request $request, $id)
	{
		$this->validate($request, [
			'category_id' => 'required',
			'name' => 'required',
			'address' => 'required',
		]);
		return UserContact::ofUserId($this->user->id)->findOrFail($id)->fill($request->all())->save() ? success() : fail();
	}

	/**
	 * @param $id
	 * @return array
	 */
	public function destroy($id)
	{
		return UserContact::ofUserId($this->user->id)->findOrFail($id)->delete() ? success() : fail();
	}
}
